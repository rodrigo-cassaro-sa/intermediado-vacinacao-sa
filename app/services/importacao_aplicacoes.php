<?php
// ============================================================================
// app/services/importacao_aplicacoes.php
// RN-031: importação em MASSA de vacinados numa campanha ATIVA.
//
// Diferente da importação histórica (RN-027, interno-only, cria campanha
// 'historico'), aqui a aplicação entra na campanha corrente e passa por TODAS as
// regras normais — período, vacina prevista, dose duplicada, lastro do
// profissional. A validação é a mesma função usada pelo registro unitário
// (validar_aplicacao), para a simulação não prometer o que a gravação não faz.
//
// Fluxo obrigatório:  upload -> SIMULAÇÃO -> confirmação -> fila -> worker
// Dose aplicada é dose faturada (RN-013/018): por isso ninguém grava sem antes
// ver o relatório, e todo lote pode ser estornado inteiro pelo interno.
//
// Requer: app/services/aplicacoes.php e app/services/elegiveis.php carregados.
// ============================================================================

require_once BASE_PATH . '/app/helpers/csv.php';
require_once BASE_PATH . '/app/services/aplicacoes.php';
require_once BASE_PATH . '/app/services/elegiveis.php';
require_once BASE_PATH . '/app/services/importacao.php';  // importacao_salvar_arquivo()

const IMP_APLIC_LIMITE_SINCRONO = 2000;  // acima disso, simulação e gravação vão para a fila
const IMP_APLIC_CHUNK           = 500;   // linhas por transação no worker

/** Campos que podem vir como padrão do lote (formulário) e ser sobrepostos pela linha. */
function imp_aplic_campos_padrao(): array
{
    return ['vacina_id', 'dose', 'lote', 'aplicado_em', 'profissional_nome',
            'profissional_cpf', 'cidade', 'uf', 'unidade', 'clinica_id'];
}

/**
 * Resolve a vacina DENTRO da campanha, por nome ou sigla (decisão: aceitar os dois).
 * Não cria vacina no catálogo — campanha ativa tem vacinas definidas de propósito.
 */
function imp_aplic_vacina(int $campanhaId, string $termo): ?int
{
    static $cache = [];
    $termo = trim($termo);
    if ($termo === '') {
        return null;
    }
    $k = $campanhaId . '|' . strtolower($termo);
    if (!array_key_exists($k, $cache)) {
        // Dois placeholders distintos de propósito: a conexão usa prepares reais
        // (EMULATE_PREPARES=false), que não aceitam repetir o mesmo nome.
        $r = db_primeiro(
            "SELECT v.id
               FROM campanha_vacina cv
               JOIN vacina v ON v.id = cv.vacina_id
              WHERE cv.campanha_id = :c AND (v.nome = :nome OR v.sigla = :sigla)
              LIMIT 1",
            [':c' => $campanhaId, ':nome' => $termo, ':sigla' => $termo]
        );
        $cache[$k] = $r ? (int) $r['id'] : null;
    }
    return $cache[$k];
}

/** Acha o elegível da campanha pelo CPF (ou identificador/voucher). */
function imp_aplic_elegivel(int $campanhaId, string $cpf, string $identificador): ?array
{
    if ($cpf !== '') {
        return db_primeiro(
            "SELECT e.id, e.status
               FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
              WHERE e.campanha_id = :c AND p.cpf = :v AND e.status <> 'removido' LIMIT 1",
            [':c' => $campanhaId, ':v' => $cpf]
        );
    }
    if ($identificador !== '') {
        return db_primeiro(
            "SELECT e.id, e.status
               FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
              WHERE e.campanha_id = :c AND p.identificador = :v AND e.status <> 'removido' LIMIT 1",
            [':c' => $campanhaId, ':v' => $identificador]
        );
    }
    return null;
}

/**
 * Um elegível criado na hora continua sujeito a RN-016 (tipo de vínculo) e
 * RN-018 (códigos do cliente) — a importação de vacinados não é atalho para
 * furar a regra da lista. Devolve o código do erro ou null se estiver ok.
 *
 * Usada TAMBÉM na simulação: sem isso ela prometeria registros que a gravação
 * depois rejeitaria, que é justamente o que a simulação existe para evitar.
 */
function imp_aplic_erro_novo_elegivel(array $item): ?string
{
    if (trim((string) ($item['nome'] ?? '')) === '') {
        return 'NOME_OBRIGATORIO';
    }
    $tipo = strtolower(trim((string) ($item['tipo_vinculo'] ?? '')));
    if (!in_array($tipo, ['colaborador', 'dependente', 'terceiro'], true)) {
        return 'TIPO_VINCULO_INVALIDO';
    }
    if ($tipo === 'dependente' && !validar_cpf(so_digitos($item['cpf_titular'] ?? ''))) {
        return 'CPF_TITULAR_INVALIDO';
    }
    if (trim((string) ($item['codigo_lotacao'] ?? '')) === '') {
        return 'CODIGO_LOTACAO_OBRIGATORIO';
    }
    if (trim((string) ($item['codigo_rh'] ?? '')) === '') {
        return 'CODIGO_RH_OBRIGATORIO';
    }
    $nasc = trim((string) ($item['data_nascimento'] ?? ''));
    if ($nasc !== '' && !validar_data($nasc)) {
        return 'DATA_NASCIMENTO_INVALIDA';
    }
    return null;
}

/** Contexto da campanha usado na simulação de um elegível que ainda não existe. */
function imp_aplic_campanha_ctx(int $campanhaId): ?array
{
    static $cache = [];
    if (!array_key_exists($campanhaId, $cache)) {
        $c = db_primeiro(
            "SELECT id, tenant_id, status, periodo_inicio, periodo_fim
               FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1",
            [':id' => $campanhaId]
        );
        $cache[$campanhaId] = $c === null ? null : [
            'id' => 0, 'campanha_id' => (int) $c['id'], 'tenant_id' => (int) $c['tenant_id'],
            'campanha_status' => $c['status'],
            'periodo_inicio' => $c['periodo_inicio'], 'periodo_fim' => $c['periodo_fim'],
        ];
    }
    return $cache[$campanhaId];
}

/**
 * Processa um bloco de linhas. Em modo simulação nada é gravado (nem aplicação,
 * nem elegível) — só o relatório de erros, que é o produto da conferência.
 *
 * Devolve ['recebidos','aplicacoes','elegiveis','rejeitados'].
 */
function imp_aplic_processar_linhas(array $imp, array $linhas, bool $simular, array $ator, int $offsetLinha = 0): array
{
    $campanhaId = (int) $imp['campanha_id'];
    $tenantId   = (int) $imp['tenant_id'];
    $impId      = (int) $imp['id'];
    $criarEleg  = (int) ($imp['criar_elegivel'] ?? 0) === 1;
    $padroes    = json_decode((string) ($imp['padroes'] ?? '{}'), true) ?: [];

    $recebidos = 0; $aplicacoes = 0; $elegiveisCriados = 0; $rejeitados = 0;

    $rejeita = function (int $linha, string $cpf, string $nome, string $code) use (&$rejeitados, $impId) {
        $rejeitados++;
        try {
            db_executar(
                "INSERT INTO importacao_aplicacao_erro (importacao_id, linha, cpf, nome, codigo)
                 VALUES (:i, :l, :c, :n, :cod)",
                [':i' => $impId, ':l' => $linha, ':c' => $cpf !== '' ? mascarar_cpf($cpf) : null,
                 ':n' => $nome !== '' ? $nome : null, ':cod' => $code]
            );
        } catch (Throwable $e) {
            error_log('importacao_aplicacao_erro falhou: ' . $e->getMessage());
        }
    };

    foreach ($linhas as $i => $item) {
        $recebidos++;
        $linha = $offsetLinha + $i + 1;

        $cpf           = so_digitos($item['cpf'] ?? '');
        $identificador = trim((string) ($item['identificador'] ?? ''));
        $nome          = trim((string) ($item['nome'] ?? ''));

        // Identidade: CPF válido OU identificador/voucher (RN-028).
        if ($cpf !== '') {
            if (!validar_cpf($cpf)) { $rejeita($linha, $cpf, $nome, 'CPF_INVALIDO'); continue; }
        } elseif ($identificador === '') {
            $rejeita($linha, '', $nome, 'SEM_IDENTIDADE'); continue;
        }

        // Vacina: da linha (nome ou sigla) ou o padrão do lote.
        $vacinaTermo = trim((string) ($item['vacina'] ?? ''));
        if ($vacinaTermo !== '') {
            $vacinaId = imp_aplic_vacina($campanhaId, $vacinaTermo);
            if ($vacinaId === null) { $rejeita($linha, $cpf, $nome, 'VACINA_FORA_DA_CAMPANHA'); continue; }
        } else {
            $vacinaId = (int) ($padroes['vacina_id'] ?? 0);
            if ($vacinaId <= 0) { $rejeita($linha, $cpf, $nome, 'VACINA_OBRIGATORIA'); continue; }
        }

        // Elegível na campanha; se faltar, criar (decisão do usuário) ou rejeitar.
        $eleg = imp_aplic_elegivel($campanhaId, $cpf, $identificador);
        $elegPre = null;
        if ($eleg === null) {
            if (!$criarEleg) { $rejeita($linha, $cpf, $nome, 'NAO_ELEGIVEL'); continue; }
            // Mesmas exigências na simulação e na gravação.
            $faltando = imp_aplic_erro_novo_elegivel($item);
            if ($faltando !== null) { $rejeita($linha, $cpf, $nome, $faltando); continue; }

            if ($simular) {
                // Nada é criado na conferência: valida contra o contexto da campanha.
                $elegPre = imp_aplic_campanha_ctx($campanhaId);
                if ($elegPre === null) { $rejeita($linha, $cpf, $nome, 'CAMPANHA_NAO_ENCONTRADA'); continue; }
                $elegivelId = 0;
                $elegiveisCriados++;
            } else {
                $res = ingerir_elegiveis($campanhaId, $tenantId, [[
                    'cpf'             => $cpf,
                    'nome'            => $nome,
                    'data_nascimento' => $item['data_nascimento'] ?? null,
                    'tipo_vinculo'    => $item['tipo_vinculo'] ?? null,
                    'cpf_titular'     => $item['cpf_titular'] ?? null,
                    'codigo_lotacao'  => $item['codigo_lotacao'] ?? null,
                    'codigo_rh'       => $item['codigo_rh'] ?? null,
                    'identificador'   => $identificador !== '' ? $identificador : null,
                ]], 'upload', null, $ator);
                $eleg = imp_aplic_elegivel($campanhaId, $cpf, $identificador);
                if ($eleg === null) {
                    // Devolve o motivo REAL da recusa (ex.: titular não elegível),
                    // não um genérico que o cliente não sabe corrigir.
                    $motivo = $res['erros'][0]['code'] ?? 'ELEGIVEL_NAO_CRIADO';
                    $rejeita($linha, $cpf, $nome, $motivo); continue;
                }
                $elegivelId = (int) $eleg['id'];
                $elegiveisCriados++;
            }
        } else {
            $elegivelId = (int) $eleg['id'];
        }

        // Contexto da aplicação: padrão do lote + o que a linha sobrescrever.
        $ctx = [
            'elegivel_id'              => $elegivelId,
            'vacina_id'                => $vacinaId,
            'executor_tipo'            => !empty($padroes['clinica_id']) ? 'clinica_credenciada' : 'profissional_saude',
            'executor_id'              => !empty($padroes['clinica_id']) ? (int) $padroes['clinica_id'] : (int) ($ator['id'] ?? 0),
            'clinica_id'               => $padroes['clinica_id'] ?? null,
            'origem'                   => 'importacao',
            'importacao_aplicacoes_id' => $impId,
            'criado_por'               => (int) ($ator['id'] ?? 0),
        ];
        foreach (imp_aplic_campos_padrao() as $campo) {
            if ($campo === 'vacina_id' || $campo === 'clinica_id') {
                continue;
            }
            $daLinha = trim((string) ($item[$campo] ?? ''));
            $ctx[$campo] = $daLinha !== '' ? $daLinha : ($padroes[$campo] ?? null);
        }
        if ((int) ($ctx['dose'] ?? 0) < 1) {
            $ctx['dose'] = 1;
        }

        if ($simular) {
            $v = validar_aplicacao($ctx, $elegPre);
            if ($v['ok']) { $aplicacoes++; } else { $rejeita($linha, $cpf, $nome, $v['code']); }
            continue;
        }

        $res = processar_aplicacao($ctx);
        if ($res['ok']) {
            $aplicacoes++;
            historico_aplicacao($res['aplicacao_id'], 'registrada', $ator, 'importacao_massa');
            historico_elegivel($elegivelId, 'vacinado', $ator, null,
                ['aplicacao_id' => $res['aplicacao_id'], 'importacao_id' => $impId]);
        } else {
            $rejeita($linha, $cpf, $nome, $res['code']);
        }
    }

    return ['recebidos' => $recebidos, 'aplicacoes' => $aplicacoes,
            'elegiveis' => $elegiveisCriados, 'rejeitados' => $rejeitados];
}

/**
 * Recebe o arquivo, cria o lote e roda a SIMULAÇÃO (inline se pequeno, senão fila).
 * Devolve ['importacao_id', 'status'] — nada de aplicação é gravado aqui.
 */
function imp_aplic_iniciar(int $tenantId, int $campanhaId, string $conteudo, array $padroes,
                           bool $criarElegivel, array $ator): array
{
    $linhas = csv_linhas($conteudo);
    $qtd = count(array_filter($linhas, fn($l) => trim($l) !== ''));
    if ($qtd > 0 && csv_tem_cabecalho($conteudo, csv_alias_vacinacao())) {
        $qtd--;
    }

    $arquivo = importacao_salvar_arquivo($conteudo, 'csv');
    db_executar(
        "INSERT INTO importacao_aplicacoes
            (tenant_id, campanha_id, arquivo, status, criar_elegivel, padroes, total_linhas, criado_por, criado_em)
         VALUES (:t, :c, :a, 'simulando', :ce, :p, :tl, :u, NOW())",
        [':t' => $tenantId, ':c' => $campanhaId, ':a' => $arquivo,
         ':ce' => $criarElegivel ? 1 : 0, ':p' => json_encode($padroes, JSON_UNESCAPED_UNICODE),
         ':tl' => $qtd, ':u' => (int) ($ator['id'] ?? 0)]
    );
    $impId = (int) db_ultimo_id();

    if ($qtd <= IMP_APLIC_LIMITE_SINCRONO) {
        imp_aplic_executar($impId, true, $ator);
        return ['importacao_id' => $impId, 'status' => 'simulada'];
    }
    return ['importacao_id' => $impId, 'status' => 'simulando'];
}

/**
 * Roda a passada completa sobre o arquivo do lote (simulação ou gravação).
 * Idempotente por status: o worker só pega o que está 'simulando'/'pendente'.
 */
function imp_aplic_executar(int $impId, bool $simular, array $ator): void
{
    $imp = db_primeiro("SELECT * FROM importacao_aplicacoes WHERE id = :id LIMIT 1", [':id' => $impId]);
    if ($imp === null) {
        return;
    }
    db_executar("UPDATE importacao_aplicacoes SET status = :s, iniciado_em = NOW() WHERE id = :id",
        [':s' => $simular ? 'simulando' : 'processando', ':id' => $impId]);

    // Cada passada substitui o relatório anterior (simular de novo, ou confirmar).
    db_executar("DELETE FROM importacao_aplicacao_erro WHERE importacao_id = :id", [':id' => $impId]);

    $caminho = BASE_PATH . '/storage/uploads/' . $imp['arquivo'];
    $conteudo = is_file($caminho) ? (string) file_get_contents($caminho) : '';
    if ($conteudo === '') {
        db_executar("UPDATE importacao_aplicacoes SET status='falha', mensagem_erro='arquivo não encontrado', finalizado_em=NOW() WHERE id=:id",
            [':id' => $impId]);
        return;
    }

    $lista = csv_parsear($conteudo, csv_ordem_vacinacao(), csv_alias_vacinacao());
    $tot = ['recebidos' => 0, 'aplicacoes' => 0, 'elegiveis' => 0, 'rejeitados' => 0];

    // Sem transação por bloco de propósito: processar_aplicacao() já é atômica por
    // linha (e abre a própria transação — PDO não aninha). Assim uma linha ruim
    // não desfaz as boas, e o relatório de erros reflete linha a linha.
    foreach (array_chunk($lista, IMP_APLIC_CHUNK, true) as $chunk) {
        $offset = (int) array_key_first($chunk);
        try {
            $r = imp_aplic_processar_linhas($imp, array_values($chunk), $simular, $ator, $offset);
        } catch (Throwable $e) {
            if (pdo()->inTransaction()) { pdo()->rollBack(); }
            db_executar("UPDATE importacao_aplicacoes SET status='falha', mensagem_erro=:m, finalizado_em=NOW() WHERE id=:id",
                [':m' => substr($e->getMessage(), 0, 250), ':id' => $impId]);
            return;
        }
        foreach ($tot as $k => $_) { $tot[$k] += $r[$k]; }
        db_executar("UPDATE importacao_aplicacoes SET total_processados = :p WHERE id = :id",
            [':p' => $tot['recebidos'], ':id' => $impId]);
    }

    db_executar(
        "UPDATE importacao_aplicacoes
            SET status = :s, total_linhas = :tl, total_processados = :tp, total_aplicacoes = :ap,
                total_elegiveis = :el, total_rejeitados = :re, finalizado_em = NOW()
          WHERE id = :id",
        [':s' => $simular ? 'simulada' : 'concluida', ':tl' => $tot['recebidos'], ':tp' => $tot['recebidos'],
         ':ap' => $tot['aplicacoes'], ':el' => $tot['elegiveis'], ':re' => $tot['rejeitados'], ':id' => $impId]
    );

    registrar_auditoria($simular ? 'vacinados.importacao_simulada' : 'vacinados.importados_massa', [
        'tenant_id' => (int) $imp['tenant_id'], 'ator_tipo' => $ator['tipo'] ?? 'usuario',
        'ator_id' => $ator['id'] ?? null, 'origem' => 'admin',
        'entidade_tipo' => 'importacao_aplicacoes', 'entidade_id' => $impId,
        'metadata' => $tot + ['campanha_id' => (int) $imp['campanha_id']],
    ]);
}

/**
 * Confirma o lote simulado: coloca na fila (ou grava na hora, se pequeno).
 * Só sai da simulação por ação explícita — nunca automaticamente.
 */
function imp_aplic_confirmar(int $impId, array $ator): array
{
    $imp = db_primeiro("SELECT * FROM importacao_aplicacoes WHERE id = :id LIMIT 1", [':id' => $impId]);
    if ($imp === null || $imp['status'] !== 'simulada') {
        return ['ok' => false, 'http' => 409, 'code' => 'LOTE_NAO_SIMULADO',
                'message' => 'Só é possível confirmar um lote já simulado.'];
    }
    if ((int) $imp['total_aplicacoes'] === 0) {
        return ['ok' => false, 'http' => 422, 'code' => 'NADA_A_IMPORTAR',
                'message' => 'A simulação não encontrou nenhuma linha válida.'];
    }

    if ((int) $imp['total_linhas'] <= IMP_APLIC_LIMITE_SINCRONO) {
        imp_aplic_executar($impId, false, $ator);
        return ['ok' => true, 'status' => 'concluida'];
    }
    db_executar("UPDATE importacao_aplicacoes SET status='pendente' WHERE id=:id", [':id' => $impId]);
    return ['ok' => true, 'status' => 'pendente'];
}

/**
 * Estorna o LOTE inteiro (decisão: interno-only, com justificativa).
 * Marca as aplicações como estornadas e devolve os elegíveis para 'pendente' —
 * mesma semântica do estorno unitário (RN-022), preservando a imutabilidade.
 */
function imp_aplic_estornar(int $impId, string $motivo, array $ator): array
{
    $imp = db_primeiro("SELECT * FROM importacao_aplicacoes WHERE id = :id LIMIT 1", [':id' => $impId]);
    if ($imp === null) {
        return ['ok' => false, 'http' => 404, 'code' => 'IMPORTACAO_NAO_ENCONTRADA',
                'message' => 'Importação não encontrada.'];
    }
    if ($imp['status'] === 'estornada') {
        return ['ok' => false, 'http' => 409, 'code' => 'LOTE_JA_ESTORNADO',
                'message' => 'Este lote já foi estornado.'];
    }
    if ($imp['status'] !== 'concluida') {
        return ['ok' => false, 'http' => 409, 'code' => 'LOTE_NAO_CONCLUIDO',
                'message' => 'Só é possível estornar um lote já gravado.'];
    }

    $alvos = db_todos(
        "SELECT id, elegivel_id FROM aplicacao
          WHERE importacao_aplicacoes_id = :id AND status = 'confirmada'",
        [':id' => $impId]
    );

    try {
        pdo()->beginTransaction();
        db_executar(
            "UPDATE aplicacao SET status = 'estornada'
              WHERE importacao_aplicacoes_id = :id AND status = 'confirmada'",
            [':id' => $impId]
        );
        foreach ($alvos as $a) {
            db_executar(
                "UPDATE elegivel SET status = 'pendente', motivo_situacao = NULL WHERE id = :id",
                [':id' => (int) $a['elegivel_id']]
            );
        }
        db_executar(
            "UPDATE importacao_aplicacoes
                SET status='estornada', total_estornados=:n, motivo_estorno=:m,
                    estornado_por=:u, estornado_em=NOW()
              WHERE id=:id",
            [':n' => count($alvos), ':m' => mb_substr($motivo, 0, 255), ':u' => (int) ($ator['id'] ?? 0), ':id' => $impId]
        );
        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) { pdo()->rollBack(); }
        throw $e;
    }

    foreach ($alvos as $a) {
        historico_aplicacao((int) $a['id'], 'estornada', $ator, $motivo);
    }
    registrar_auditoria('vacinados.lote_estornado', [
        'tenant_id' => (int) $imp['tenant_id'], 'ator_tipo' => $ator['tipo'] ?? 'usuario',
        'ator_id' => $ator['id'] ?? null, 'origem' => 'admin',
        'entidade_tipo' => 'importacao_aplicacoes', 'entidade_id' => $impId,
        'metadata' => ['estornados' => count($alvos), 'motivo' => $motivo,
                       'campanha_id' => (int) $imp['campanha_id']],
    ]);

    return ['ok' => true, 'estornados' => count($alvos)];
}
