<?php
// ============================================================================
// app/services/historico_import.php
// Importação RETROCOMPATÍVEL de vacinados de anos anteriores (carteira perpétua,
// RN-027). Uso INTERNO (onboarding de cliente recorrente). Burla as regras de
// campanha ativa/período/elegibilidade de propósito — por isso é interno-only.
//
// Para cada linha resolve/cria: campanha "Histórico — {vacina} {ano}" (por
// cliente+vacina+ano), paciente (CPF ou identificador), elegível (status
// 'aplicado') e insere a APLICAÇÃO direto (origem 'historico'). Deduplica por
// (elegível, vacina, dose) — a UNIQUE do banco é a trava final.
// ============================================================================

require_once BASE_PATH . '/app/services/elegiveis.php'; // unidade_por_lotacao()

/**
 * Importa uma lista de vacinados históricos para um cliente.
 * $linhas: [ ['cpf'|'identificador','nome','data_nascimento'?,'vacina','dose'?,
 *             'lote'?,'aplicado_em','codigo_lotacao'?,'cidade'?,'uf'?] ]
 * $ator: ['tipo'=>'usuario','id'=>int]  (quem está migrando).
 * Devolve contagens + amostra de erros (não persiste — retorno inline).
 */
function importar_vacinados_historico(int $tenantId, array $linhas, array $ator): array
{
    $recebidos = 0; $aplicacoes = 0; $duplicados = 0; $rejeitados = 0;
    $campanhasCriadas = 0; $erros = [];
    $cacheVacina = [];   // nome normalizado -> id
    $cacheCampanha = []; // "vacina|ano" -> campanha_id
    $atorId = (int) ($ator['id'] ?? 0);

    $rejeita = function (int $linha, string $code) use (&$rejeitados, &$erros) {
        $rejeitados++;
        if (count($erros) < 100) {
            $erros[] = ['linha' => $linha, 'code' => $code];
        }
    };

    foreach ($linhas as $i => $item) {
        $recebidos++;
        $linha = $i + 1;

        $cpf   = so_digitos($item['cpf'] ?? '');
        $identificador = trim((string) ($item['identificador'] ?? ''));
        $nome  = trim((string) ($item['nome'] ?? ''));
        $nasc  = trim((string) ($item['data_nascimento'] ?? ''));
        $vacinaNome = trim((string) ($item['vacina'] ?? ''));
        $dose  = (int) ($item['dose'] ?? 1); if ($dose < 1) { $dose = 1; }
        $lote  = trim((string) ($item['lote'] ?? '')); if ($lote === '') { $lote = 'HISTORICO'; }
        $quando = trim((string) ($item['aplicado_em'] ?? ''));
        $codLotacao = trim((string) ($item['codigo_lotacao'] ?? ''));
        $cidade = trim((string) ($item['cidade'] ?? '')) ?: null;
        $uf     = strtoupper(trim((string) ($item['uf'] ?? '')));
        $uf     = preg_match('/^[A-Z]{2}$/', $uf) ? $uf : null;
        $temCpf = $cpf !== '';

        // ---- Validações mínimas (retrocompatível: tolera lote/prof/cidade ausentes) ----
        if ($temCpf) {
            if (!validar_cpf($cpf)) { $rejeita($linha, 'CPF_INVALIDO'); continue; }
        } elseif ($identificador === '') {
            $rejeita($linha, 'SEM_IDENTIDADE'); continue;
        }
        if ($nome === '') { $rejeita($linha, 'NOME_OBRIGATORIO'); continue; }
        if ($nasc !== '' && !validar_data($nasc)) { $rejeita($linha, 'DATA_NASCIMENTO_INVALIDA'); continue; }
        $nasc = $nasc === '' ? null : $nasc;
        if ($vacinaNome === '') { $rejeita($linha, 'VACINA_OBRIGATORIA'); continue; }

        // Data da aplicação: aceita AAAA-MM-DD (ou com hora) OU só o ANO (AAAA).
        $ano = null; $aplicadoEm = null;
        if (preg_match('/^\d{4}$/', $quando)) {
            $ano = (int) $quando;
            $aplicadoEm = sprintf('%04d-07-01 00:00:00', $ano); // meio do ano quando só há o ano
        } elseif (($ts = strtotime($quando)) !== false && $quando !== '') {
            $ano = (int) date('Y', $ts);
            $aplicadoEm = date('Y-m-d H:i:s', $ts);
        } else {
            $rejeita($linha, 'DATA_APLICACAO_INVALIDA'); continue;
        }
        if ($ano < 1990 || $ano > (int) date('Y')) { $rejeita($linha, 'ANO_FORA_DO_INTERVALO'); continue; }

        // ---- Vacina: precisa existir no catálogo (evita duplicar catálogo com typos).
        // A coluna é utf8mb4_unicode_ci (case-insensitive), então a comparação já ignora
        // maiúsculas/minúsculas sem precisar de LOWER()/mbstring.
        $chaveV = strtolower($vacinaNome); // só p/ chave de cache (ASCII-fold basta)
        if (!array_key_exists($chaveV, $cacheVacina)) {
            $v = db_primeiro("SELECT id FROM vacina WHERE nome = :n AND status = 'ativa' LIMIT 1", [':n' => $vacinaNome]);
            $cacheVacina[$chaveV] = $v ? (int) $v['id'] : null;
        }
        $vacinaId = $cacheVacina[$chaveV];
        if ($vacinaId === null) { $rejeita($linha, 'VACINA_DESCONHECIDA'); continue; }

        // ---- Campanha histórica por (cliente, vacina, ano) ----
        // Resolvida/criada FORA da transação da linha (autocommit): assim persiste
        // mesmo que a linha falhe depois, e o cache nunca aponta p/ id revertido.
        $chaveC = $vacinaId . '|' . $ano;
        if (!array_key_exists($chaveC, $cacheCampanha)) {
            $nomeCamp = "Histórico — {$vacinaNome} {$ano}";
            $existe = db_primeiro(
                "SELECT id FROM campanha
                  WHERE tenant_id = :t AND modalidade = 'historico' AND nome = :n AND excluido_em IS NULL LIMIT 1",
                [':t' => $tenantId, ':n' => $nomeCamp]
            );
            if ($existe !== null) {
                $cacheCampanha[$chaveC] = (int) $existe['id'];
            } else {
                db_executar(
                    "INSERT INTO campanha (tenant_id, nome, modalidade, periodo_inicio, periodo_fim, status, criado_por)
                     VALUES (:t, :n, 'historico', :ini, :fim, 'encerrada', :u)",
                    [':t' => $tenantId, ':n' => $nomeCamp,
                     ':ini' => sprintf('%04d-01-01', $ano), ':fim' => sprintf('%04d-12-31', $ano),
                     ':u' => $atorId]
                );
                $cacheCampanha[$chaveC] = (int) db_ultimo_id();
                $campanhasCriadas++;
            }
            // Garante a vacina vinculada à campanha (para detalhe/relatórios).
            db_executar(
                "INSERT INTO campanha_vacina (tenant_id, campanha_id, vacina_id, doses_previstas)
                 VALUES (:t, :c, :v, 1) ON DUPLICATE KEY UPDATE doses_previstas = doses_previstas",
                [':t' => $tenantId, ':c' => $cacheCampanha[$chaveC], ':v' => $vacinaId]
            );
        }
        $campanhaId = $cacheCampanha[$chaveC];

        try {
            pdo()->beginTransaction();

            // ---- Paciente (identidade global por CPF ou identificador) ----
            if ($temCpf) {
                $pac = db_primeiro("SELECT id FROM paciente WHERE cpf = :v LIMIT 1", [':v' => $cpf]);
            } else {
                $pac = db_primeiro("SELECT id FROM paciente WHERE identificador = :v LIMIT 1", [':v' => $identificador]);
            }
            if ($pac === null) {
                db_executar(
                    "INSERT INTO paciente (cpf, identificador, nome, data_nascimento) VALUES (:cpf, :idf, :nome, :nasc)",
                    [':cpf' => $temCpf ? $cpf : null, ':idf' => $temCpf ? null : $identificador, ':nome' => $nome, ':nasc' => $nasc]
                );
                $pacienteId = (int) db_ultimo_id();
            } else {
                $pacienteId = (int) $pac['id'];
            }

            // ---- Unidade (opcional) pelo código de lotação ----
            $unidadeId = $codLotacao !== '' ? unidade_por_lotacao($tenantId, $codLotacao) : null;

            // ---- Elegível (status 'aplicado') — dedup por campanha+paciente ----
            $eleg = db_primeiro("SELECT id FROM elegivel WHERE campanha_id = :c AND paciente_id = :p LIMIT 1",
                [':c' => $campanhaId, ':p' => $pacienteId]);
            if ($eleg === null) {
                db_executar(
                    "INSERT INTO elegivel (tenant_id, campanha_id, unidade_id, paciente_id, nome, data_nascimento, origem, codigo_lotacao, status)
                     VALUES (:t, :c, :u, :p, :nome, :nasc, 'historico', :lot, 'aplicado')",
                    [':t' => $tenantId, ':c' => $campanhaId, ':u' => $unidadeId, ':p' => $pacienteId,
                     ':nome' => $nome, ':nasc' => $nasc, ':lot' => $codLotacao ?: null]
                );
                $elegivelId = (int) db_ultimo_id();
                historico_elegivel($elegivelId, 'importado_historico', $ator, null, [
                    'identidade' => $temCpf ? mascarar_cpf($cpf) : ('id:' . $identificador),
                    'campanha' => "Histórico {$vacinaNome} {$ano}",
                ]);
            } else {
                $elegivelId = (int) $eleg['id'];
                if ($unidadeId !== null) {
                    db_executar("UPDATE elegivel SET status = 'aplicado', unidade_id = COALESCE(unidade_id, :u) WHERE id = :id",
                        [':u' => $unidadeId, ':id' => $elegivelId]);
                } else {
                    db_executar("UPDATE elegivel SET status = 'aplicado' WHERE id = :id", [':id' => $elegivelId]);
                }
            }

            // ---- Aplicação (imutável, origem 'historico') ----
            db_executar(
                "INSERT INTO aplicacao
                    (tenant_id, campanha_id, elegivel_id, paciente_id, vacina_id, dose, lote,
                     cidade, uf, executor_tipo, executor_id, origem, status, aplicado_em, criado_por)
                 VALUES
                    (:t, :c, :e, :p, :v, :dose, :lote, :cidade, :uf,
                     'importacao_historica', :ator_exec, 'historico', 'confirmada', :quando, :ator_criado)",
                [':t' => $tenantId, ':c' => $campanhaId, ':e' => $elegivelId, ':p' => $pacienteId,
                 ':v' => $vacinaId, ':dose' => $dose, ':lote' => $lote,
                 ':cidade' => $cidade, ':uf' => $uf, ':ator_exec' => $atorId,
                 ':ator_criado' => $atorId, ':quando' => $aplicadoEm]
            );
            $aplicacoes++;

            pdo()->commit();
        } catch (Throwable $e) {
            if (pdo()->inTransaction()) { pdo()->rollBack(); }
            // Dose repetida (UNIQUE uq_aplicacao_confirmada) = já constava; conta como duplicado.
            if ($e instanceof PDOException && $e->getCode() === '23000') {
                $duplicados++;
                continue;
            }
            $rejeita($linha, 'ERRO_INTERNO');
            error_log('import historico linha ' . $linha . ': ' . $e->getMessage());
        }
    }

    return [
        'recebidos'          => $recebidos,
        'aplicacoes_criadas' => $aplicacoes,
        'duplicados'         => $duplicados,
        'rejeitados'         => $rejeitados,
        'campanhas_criadas'  => $campanhasCriadas,
        'erros'              => $erros,
    ];
}

/**
 * Converte CSV do histórico (com ou sem cabeçalho) em lista de itens.
 * Colunas: cpf, nome, data_nascimento, vacina, dose, lote, aplicado_em,
 *          codigo_lotacao, cidade, uf, identificador
 */
function parsear_csv_vacinados_historico(string $conteudo): array
{
    $linhas = preg_split('/\r\n|\r|\n/', trim($conteudo));
    if (!$linhas || $linhas[0] === '') {
        return [];
    }
    $delim = substr_count($linhas[0], ';') > substr_count($linhas[0], ',') ? ';' : ',';
    $cabecalho = array_map(fn($h) => strtolower(trim($h)), str_getcsv($linhas[0], $delim));

    $ordem = ['cpf', 'nome', 'data_nascimento', 'vacina', 'dose', 'lote', 'aplicado_em', 'codigo_lotacao', 'cidade', 'uf', 'identificador'];
    $idx = array_flip($ordem);           // posição padrão (sem cabeçalho)
    $temCabecalho = in_array('vacina', $cabecalho, true) || in_array('aplicado_em', $cabecalho, true);
    if ($temCabecalho) {
        foreach ($ordem as $campo) {
            $p = array_search($campo, $cabecalho, true);
            $idx[$campo] = $p === false ? null : $p;
        }
    }
    $val = fn($col, $campo) => (isset($idx[$campo]) && $idx[$campo] !== null && isset($col[$idx[$campo]])) ? $col[$idx[$campo]] : null;

    $lista = [];
    for ($i = $temCabecalho ? 1 : 0; $i < count($linhas); $i++) {
        if (trim($linhas[$i]) === '') { continue; }
        $col = str_getcsv($linhas[$i], $delim);
        $lista[] = [
            'cpf'             => $val($col, 'cpf') ?? '',
            'nome'            => $val($col, 'nome') ?? '',
            'data_nascimento' => $val($col, 'data_nascimento'),
            'vacina'          => $val($col, 'vacina') ?? '',
            'dose'            => $val($col, 'dose'),
            'lote'            => $val($col, 'lote'),
            'aplicado_em'     => $val($col, 'aplicado_em') ?? '',
            'codigo_lotacao'  => $val($col, 'codigo_lotacao'),
            'cidade'          => $val($col, 'cidade'),
            'uf'              => $val($col, 'uf'),
            'identificador'   => $val($col, 'identificador'),
        ];
    }
    return $lista;
}
