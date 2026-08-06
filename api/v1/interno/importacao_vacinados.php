<?php
// ============================================================================
// api/v1/interno/importacao_vacinados.php
// RN-031: importar vacinados em MASSA numa campanha ativa (CSV).
// Grupo interno (sessão + CSRF). Fluxo: upload -> SIMULAÇÃO -> confirmação.
//
// Quem enxerga o paciente pode registrar a vacinação dele (mesma regra do
// registro unitário). O estorno do lote é restrito ao interno, com justificativa.
// ============================================================================

require_once BASE_PATH . '/app/services/importacao_aplicacoes.php';

/**
 * POST /api/v1/interno/campanhas/{id}/vacinados/importar
 * Recebe o CSV + os dados comuns do lote e devolve a SIMULAÇÃO. Nada é gravado.
 */
function rota_importar_vacinados(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);
    if ($campanha['status'] !== 'ativa') {
        responder_erro('A campanha precisa estar ativa.', 422, [
            ['field' => null, 'code' => 'CAMPANHA_INATIVA', 'message' => 'Só é possível importar vacinados em campanha ativa.'],
        ]);
    }

    // Arquivo (multipart) ou conteúdo colado em JSON {csv: "..."}.
    if (!empty($_FILES['arquivo']['tmp_name']) && is_uploaded_file($_FILES['arquivo']['tmp_name'])) {
        if (($_FILES['arquivo']['size'] ?? 0) > 20 * 1024 * 1024) {
            responder_erro('Arquivo muito grande (máx. 20MB).', 400, [
                ['field' => 'arquivo', 'code' => 'ARQUIVO_GRANDE', 'message' => 'Envie um arquivo de até 20MB.'],
            ]);
        }
        $ext = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'txt'], true)) {
            responder_erro('Formato inválido. Envie CSV.', 400, [
                ['field' => 'arquivo', 'code' => 'ARQUIVO_INVALIDO', 'message' => 'Apenas .csv ou .txt.'],
            ]);
        }
        $conteudo = (string) file_get_contents($_FILES['arquivo']['tmp_name']);
        $padroes = json_decode((string) ($_POST['padroes'] ?? '{}'), true) ?: [];
        $criarElegivel = ($_POST['criar_elegivel'] ?? '') === '1';
    } else {
        $dados = corpo_json();
        $conteudo = (string) ($dados['csv'] ?? '');
        $padroes = is_array($dados['padroes'] ?? null) ? $dados['padroes'] : [];
        $criarElegivel = !empty($dados['criar_elegivel']);
    }

    if (trim($conteudo) === '') {
        responder_erro('Envie um arquivo CSV ou o conteúdo em "csv".', 400, [
            ['field' => 'csv', 'code' => 'SEM_DADOS', 'message' => 'Nenhuma linha informada.'],
        ]);
    }

    // Cabeçalho conferido antes de gastar uma simulação inteira para descobrir que
    // o arquivo não identifica ninguém (BUG-004).
    $chk = csv_conferir($conteudo, csv_ordem_vacinacao(), csv_alias_vacinacao(),
        [], [['cpf', 'identificador']]);
    if ($chk['erro'] !== null) {
        responder_erro($chk['erro'], 422, [
            ['field' => 'arquivo', 'code' => 'CABECALHO_INVALIDO', 'message' => $chk['erro']],
        ]);
    }

    // Só os campos previstos viram padrão do lote (evita injetar chave estranha no ctx).
    $limpos = [];
    foreach (imp_aplic_campos_padrao() as $campo) {
        if (isset($padroes[$campo]) && $padroes[$campo] !== '' && $padroes[$campo] !== null) {
            $limpos[$campo] = $padroes[$campo];
        }
    }

    $r = imp_aplic_iniciar((int) $campanha['tenant_id'], $id, $conteudo, $limpos,
        $criarElegivel, ator_usuario($usuario));

    responder_sucesso(
        imp_aplic_resumo((int) $r['importacao_id']),
        $r['status'] === 'simulada'
            ? 'Simulação concluída. Confira o resultado antes de confirmar.'
            : 'Arquivo recebido; a simulação está sendo processada em segundo plano.',
        201
    );
}

/**
 * GET /api/v1/interno/campanhas/{id}/importacoes-vacinados
 * Histórico dos lotes da campanha. O cliente enxerga para conferir o que enviou;
 * só o interno recebe `pode_estornar` (a autoridade continua sendo o endpoint de
 * estorno, que valida o perfil de novo).
 */
function rota_listar_importacoes_vacinados(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_campanha_do_usuario($usuario, $id);

    $itens = db_todos(
        "SELECT i.id, i.status, i.criar_elegivel, i.total_linhas, i.total_aplicacoes,
                i.total_elegiveis, i.total_rejeitados, i.total_estornados,
                i.motivo_estorno, i.criado_em, i.finalizado_em, i.estornado_em,
                u.nome  AS criado_por_nome,
                ue.nome AS estornado_por_nome
           FROM importacao_aplicacoes i
           LEFT JOIN usuario u  ON u.id = i.criado_por
           LEFT JOIN usuario ue ON ue.id = i.estornado_por
          WHERE i.campanha_id = :c
          ORDER BY i.id DESC
          LIMIT 50",
        [':c' => $id]
    );

    responder_sucesso([
        'itens'         => $itens,
        // Mesma lista de perfis exigida por rota_estornar_importacao_vacinados.
        'pode_estornar' => in_array($usuario['perfil'] ?? '', ['super_admin', 'operador_interno'], true),
    ], 'OK.');
}

/** GET /api/v1/interno/importacoes-vacinados/{id} — status/resumo do lote. */
function rota_status_importacao_vacinados(array $params): void
{
    $usuario = exigir_login();
    $imp = imp_vacinados_do_usuario($usuario, (int) ($params['id'] ?? 0));
    responder_sucesso(imp_aplic_resumo((int) $imp['id']), 'OK.');
}

/**
 * POST /api/v1/interno/importacoes-vacinados/{id}/confirmar
 * Grava de verdade o que a simulação mostrou. Ação explícita e auditada.
 */
function rota_confirmar_importacao_vacinados(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();
    $imp = imp_vacinados_do_usuario($usuario, (int) ($params['id'] ?? 0));

    $r = imp_aplic_confirmar((int) $imp['id'], ator_usuario($usuario));
    if (!$r['ok']) {
        responder_erro($r['message'], $r['http'], [
            ['field' => null, 'code' => $r['code'], 'message' => $r['message']],
        ]);
    }

    responder_sucesso(imp_aplic_resumo((int) $imp['id']),
        $r['status'] === 'concluida' ? 'Vacinados registrados.' : 'Confirmado; gravando em segundo plano.', 201);
}

/**
 * POST /api/v1/interno/importacoes-vacinados/{id}/estornar
 * Desfaz o LOTE inteiro. Interno-only e exige justificativa: dose aplicada é
 * dose faturada, então desfazer em massa é ação de alto impacto.
 */
function rota_estornar_importacao_vacinados(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $dados = corpo_json();
    $motivo = trim((string) ($dados['motivo'] ?? ''));
    if ($motivo === '') {
        responder_erro('Informe a justificativa do estorno.', 400, [
            ['field' => 'motivo', 'code' => 'MOTIVO_OBRIGATORIO', 'message' => 'A justificativa é obrigatória.'],
        ]);
    }

    $imp = imp_vacinados_do_usuario($usuario, (int) ($params['id'] ?? 0));
    $r = imp_aplic_estornar((int) $imp['id'], $motivo, ator_usuario($usuario));
    if (!$r['ok']) {
        responder_erro($r['message'], $r['http'], [
            ['field' => null, 'code' => $r['code'], 'message' => $r['message']],
        ]);
    }

    responder_sucesso(['importacao_id' => (int) $imp['id'], 'estornados' => $r['estornados']],
        $r['estornados'] . ' aplicação(ões) estornada(s).');
}

/** GET /api/v1/interno/importacoes-vacinados/{id}/erros/exportar — CSV dos rejeitados. */
function rota_exportar_erros_importacao_vacinados(array $params): void
{
    $usuario = exigir_login();
    $imp = imp_vacinados_do_usuario($usuario, (int) ($params['id'] ?? 0));

    $linhas = db_todos(
        "SELECT linha, cpf, nome, codigo FROM importacao_aplicacao_erro
          WHERE importacao_id = :id ORDER BY linha",
        [':id' => (int) $imp['id']]
    );

    registrar_auditoria('vacinados.importacao_erros_exportados', [
        'tenant_id' => (int) $imp['tenant_id'], 'ator_tipo' => 'usuario',
        'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'importacao_aplicacoes', 'entidade_id' => (int) $imp['id'],
    ]);

    if (!headers_sent()) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="erros-vacinados-' . (int) $imp['id'] . '.csv"');
        header('X-Request-Id: ' . request_id());
    }
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, ['linha', 'cpf', 'nome', 'codigo_erro', 'motivo'], ';');
    foreach ($linhas as $l) {
        fputcsv($out, [$l['linha'], $l['cpf'], $l['nome'], $l['codigo'], motivo_erro_vacinacao($l['codigo'])], ';');
    }
    fclose($out);
    exit;
}

/** Resumo do lote, com amostra dos erros (o suficiente para decidir na tela). */
function imp_aplic_resumo(int $impId): array
{
    $imp = db_primeiro("SELECT * FROM importacao_aplicacoes WHERE id = :id LIMIT 1", [':id' => $impId]);
    if ($imp === null) {
        return [];
    }
    $amostra = db_todos(
        "SELECT linha, cpf, nome, codigo FROM importacao_aplicacao_erro
          WHERE importacao_id = :id ORDER BY linha LIMIT 20",
        [':id' => $impId]
    );
    foreach ($amostra as &$a) {
        $a['motivo'] = motivo_erro_vacinacao($a['codigo']);
    }
    unset($a);

    return [
        'importacao_id'     => (int) $imp['id'],
        'campanha_id'       => (int) $imp['campanha_id'],
        'status'            => $imp['status'],
        'criar_elegivel'    => (int) $imp['criar_elegivel'] === 1,
        'total_linhas'      => (int) $imp['total_linhas'],
        'total_processados' => (int) $imp['total_processados'],
        'total_aplicacoes'  => (int) $imp['total_aplicacoes'],
        'total_elegiveis'   => (int) $imp['total_elegiveis'],
        'total_rejeitados'  => (int) $imp['total_rejeitados'],
        'total_estornados'  => (int) $imp['total_estornados'],
        'motivo_estorno'    => $imp['motivo_estorno'],
        'mensagem_erro'     => $imp['mensagem_erro'],
        'criado_em'         => $imp['criado_em'],
        'finalizado_em'     => $imp['finalizado_em'],
        'erros_amostra'     => $amostra,
    ];
}

/** Busca o lote e valida o escopo do usuário (pela campanha). */
function imp_vacinados_do_usuario(array $usuario, int $impId): array
{
    $imp = db_primeiro("SELECT * FROM importacao_aplicacoes WHERE id = :id LIMIT 1", [':id' => $impId]);
    if ($imp === null) {
        responder_erro('Importação inexistente.', 404, [
            ['field' => null, 'code' => 'IMPORTACAO_NAO_ENCONTRADA', 'message' => 'Importação não encontrada.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $imp['campanha_id']);
    return $imp;
}

/** Texto amigável do código de erro (relatório ao cliente). */
function motivo_erro_vacinacao(string $codigo): string
{
    static $mapa = [
        'CPF_INVALIDO'             => 'CPF inválido',
        'SEM_IDENTIDADE'           => 'Sem CPF e sem identificador/voucher',
        'NOME_OBRIGATORIO'         => 'Nome não informado (necessário para criar o elegível)',
        'NAO_ELEGIVEL'             => 'Pessoa não está na lista de elegíveis da campanha',
        'ELEGIVEL_NAO_CRIADO'      => 'Não foi possível criar o elegível (confira CPF, nome e data de nascimento)',
        'VACINA_OBRIGATORIA'       => 'Vacina não informada na linha nem nos dados do lote',
        'VACINA_FORA_DA_CAMPANHA'  => 'Vacina não prevista nesta campanha (use o nome ou a sigla do catálogo)',
        'VACINADO_DUPLICADO'       => 'Esta dose desta vacina já consta para o paciente',
        'FORA_DO_PERIODO'          => 'Data de aplicação fora do período da campanha',
        'DATA_INVALIDA'            => 'Data de aplicação inválida (use AAAA-MM-DD)',
        'CAMPANHA_INATIVA'         => 'A campanha não está ativa',
        'CAMPO_OBRIGATORIO'        => 'Falta um campo obrigatório (lote, data, profissional, cidade ou UF)',
        'CPF_PROFISSIONAL_INVALIDO' => 'CPF do profissional que aplicou é inválido',
        'UF_INVALIDA'              => 'UF deve ter 2 letras',
    ];
    return $mapa[$codigo] ?? $codigo;
}
