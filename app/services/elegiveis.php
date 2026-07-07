<?php
// ============================================================================
// app/services/elegiveis.php
// Regra de negócio de elegíveis: ingestão com validação/dedup por CPF (RN-002,
// RN-008) e parsing de CSV. Reutilizado pelo upload interno e pela API do parceiro.
// ============================================================================

/**
 * Ingere uma lista de elegíveis numa campanha.
 * $lista: itens [ ['cpf'=>, 'nome'=>, 'data_nascimento'=>?] ].
 * $origem: 'upload' | 'api' | 'autoelegivel'.
 * Cria/reutiliza paciente por CPF e cria elegivel (UNIQUE campanha+paciente).
 * Devolve contagens e os primeiros erros por item.
 */
function ingerir_elegiveis(int $campanhaId, int $tenantId, array $lista, string $origem, ?int $importacaoId, array $ator = ['tipo' => 'usuario', 'id' => null], ?array $colaboradoresArquivo = null, int $offsetLinha = 0): array
{
    $recebidos = 0; $criados = 0; $atualizados = 0; $rejeitados = 0;
    $erros = [];
    $tipos = ['colaborador', 'dependente', 'terceiro'];

    // Persiste o rejeitado (relatório de erros ao cliente) e acumula amostra.
    $rejeita = function (int $linha, string $cpf, string $nome, string $code) use (&$rejeitados, &$erros, $importacaoId) {
        $rejeitados++;
        if (count($erros) < 50) {
            $erros[] = ['linha' => $linha, 'cpf' => mascarar_cpf($cpf), 'code' => $code];
        }
        if ($importacaoId !== null) {
            try {
                db_executar(
                    "INSERT INTO importacao_erro (importacao_id, linha, cpf, nome, codigo)
                     VALUES (:imp, :linha, :cpf, :nome, :code)",
                    [':imp' => $importacaoId, ':linha' => $linha, ':cpf' => $cpf ?: null,
                     ':nome' => $nome !== '' ? $nome : null, ':code' => $code]
                );
            } catch (Throwable $e) {
                error_log('importacao_erro falhou: ' . $e->getMessage());
            }
        }
    };

    // RN-017/RN-018 (item 6): mapa dos colaboradores para validar o titular dos
    // dependentes. Pode vir pré-calculado (worker) ou ser montado a partir do lote.
    if ($colaboradoresArquivo === null) {
        $colaboradoresArquivo = [];
        foreach ($lista as $it) {
            if (strtolower(trim((string) ($it['tipo_vinculo'] ?? ''))) === 'colaborador') {
                $colaboradoresArquivo[so_digitos($it['cpf'] ?? '')] = true;
            }
        }
    }

    foreach ($lista as $i => $item) {
        $recebidos++;
        $linha = $offsetLinha + $i + 1;
        $cpf   = so_digitos($item['cpf'] ?? '');
        $identificador = trim((string) ($item['identificador'] ?? ''));
        $nome  = trim((string) ($item['nome'] ?? ''));
        $nasc  = trim((string) ($item['data_nascimento'] ?? ''));
        $tipo  = strtolower(trim((string) ($item['tipo_vinculo'] ?? '')));
        $cpfTitular = so_digitos($item['cpf_titular'] ?? '');
        $codLotacao = trim((string) ($item['codigo_lotacao'] ?? ''));
        $codRh      = trim((string) ($item['codigo_rh'] ?? ''));
        $temCpf = $cpf !== '';
        $idErro = $temCpf ? $cpf : $identificador;  // valor mostrado no relatório de erros

        // RN-028: identidade por CPF (validado) OU por voucher/identificador (sem CPF).
        if ($temCpf) {
            if (!validar_cpf($cpf)) { $rejeita($linha, $idErro, $nome, 'CPF_INVALIDO'); continue; }
        } elseif ($identificador === '') {
            $rejeita($linha, $idErro, $nome, 'SEM_IDENTIDADE'); continue;
        }
        // Nome obrigatório.
        if ($nome === '') { $rejeita($linha, $idErro, $nome, 'NOME_OBRIGATORIO'); continue; }
        // RN-016: data de nascimento OPCIONAL, mas se vier tem de ser válida.
        if ($nasc !== '' && !validar_data($nasc)) { $rejeita($linha, $idErro, $nome, 'DATA_NASCIMENTO_INVALIDA'); continue; }
        $nasc = $nasc === '' ? null : $nasc;
        // RN-016: tipo de vínculo obrigatório (colaborador|dependente|terceiro).
        if (!in_array($tipo, $tipos, true)) { $rejeita($linha, $idErro, $nome, 'TIPO_VINCULO_INVALIDO'); continue; }
        // RN-017: dependente exige CPF do titular válido e que seja COLABORADOR
        // elegível na mesma campanha (no arquivo ou já cadastrado). Item 6.
        if ($tipo === 'dependente') {
            if (!validar_cpf($cpfTitular)) { $rejeita($linha, $idErro, $nome, 'CPF_TITULAR_INVALIDO'); continue; }
            $titularOk = isset($colaboradoresArquivo[$cpfTitular]);
            if (!$titularOk) {
                $ex = db_primeiro(
                    "SELECT e.id FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
                      WHERE e.campanha_id = :c AND p.cpf = :cpf AND e.tipo_vinculo = 'colaborador' LIMIT 1",
                    [':c' => $campanhaId, ':cpf' => $cpfTitular]
                );
                $titularOk = $ex !== null;
            }
            if (!$titularOk) { $rejeita($linha, $idErro, $nome, 'CPF_TITULAR_NAO_ELEGIVEL'); continue; }
        } else {
            $cpfTitular = null;
        }
        // RN-018: códigos do cliente obrigatórios.
        if ($codLotacao === '') { $rejeita($linha, $idErro, $nome, 'CODIGO_LOTACAO_OBRIGATORIO'); continue; }
        if ($codRh === '')      { $rejeita($linha, $idErro, $nome, 'CODIGO_RH_OBRIGATORIO'); continue; }

        // Identidade global: por CPF (RN-008) ou por identificador/voucher (RN-028).
        if ($temCpf) {
            $paciente = db_primeiro("SELECT id FROM paciente WHERE cpf = :v LIMIT 1", [':v' => $cpf]);
        } else {
            $paciente = db_primeiro("SELECT id FROM paciente WHERE identificador = :v LIMIT 1", [':v' => $identificador]);
        }
        if ($paciente === null) {
            db_executar(
                "INSERT INTO paciente (cpf, identificador, nome, data_nascimento) VALUES (:cpf, :idf, :nome, :nasc)",
                [':cpf' => $temCpf ? $cpf : null, ':idf' => $temCpf ? null : $identificador, ':nome' => $nome, ':nasc' => $nasc]
            );
            $pacienteId = (int) db_ultimo_id();
        } else {
            $pacienteId = (int) $paciente['id'];
        }

        // Elegível por campanha (UNIQUE campanha+paciente).
        $eleg = db_primeiro(
            "SELECT id FROM elegivel WHERE campanha_id = :c AND paciente_id = :p LIMIT 1",
            [':c' => $campanhaId, ':p' => $pacienteId]
        );
        if ($eleg === null) {
            db_executar(
                "INSERT INTO elegivel (tenant_id, campanha_id, paciente_id, nome, data_nascimento, origem, tipo_vinculo, cpf_titular, codigo_lotacao, codigo_rh, status, importacao_id)
                 VALUES (:tenant, :campanha, :paciente, :enome, :enasc, :origem, :tipo, :titular, :lotacao, :rh, 'pendente', :imp)",
                [
                    ':tenant'   => $tenantId,
                    ':campanha' => $campanhaId,
                    ':paciente' => $pacienteId,
                    ':enome'    => $nome,
                    ':enasc'    => $nasc,
                    ':origem'   => $origem,
                    ':tipo'     => $tipo,
                    ':titular'  => $cpfTitular,
                    ':lotacao'  => $codLotacao,
                    ':rh'       => $codRh,
                    ':imp'      => $importacaoId,
                ]
            );
            $novoId = (int) db_ultimo_id();
            $criados++;
            // RN-021: evento de origem no histórico do elegível.
            historico_elegivel($novoId, 'criado', $ator, null, [
                'identidade' => $temCpf ? mascarar_cpf($cpf) : ('voucher:' . $identificador),
                'nome' => $nome, 'tipo_vinculo' => $tipo,
                'origem' => $origem, 'codigo_lotacao' => $codLotacao, 'codigo_rh' => $codRh,
            ]);
        } else {
            // Já elegível nesta campanha (dedup) — atualiza dados sem duplicar.
            db_executar(
                "UPDATE elegivel SET nome = :enome, data_nascimento = :enasc, tipo_vinculo = :tipo,
                        cpf_titular = :titular, codigo_lotacao = :lotacao, codigo_rh = :rh WHERE id = :id",
                [':enome' => $nome, ':enasc' => $nasc, ':tipo' => $tipo, ':titular' => $cpfTitular,
                 ':lotacao' => $codLotacao, ':rh' => $codRh, ':id' => (int) $eleg['id']]
            );
            $atualizados++;
            historico_elegivel((int) $eleg['id'], 'reingerido', $ator, null, [
                'tipo_vinculo' => $tipo, 'origem' => $origem,
                'codigo_lotacao' => $codLotacao, 'codigo_rh' => $codRh,
            ]);
        }
    }

    return [
        'recebidos'   => $recebidos,
        'criados'     => $criados,
        'atualizados' => $atualizados,
        'rejeitados'  => $rejeitados,
        'erros'       => $erros,
    ];
}

/**
 * Converte CSV (com ou sem cabeçalho) em lista de itens.
 * Aceita delimitador vírgula ou ponto e vírgula. Colunas: cpf, nome, data_nascimento.
 */
function parsear_csv_elegiveis(string $conteudo): array
{
    $linhas = preg_split('/\r\n|\r|\n/', trim($conteudo));
    if (!$linhas || $linhas[0] === '') {
        return [];
    }
    $delim = substr_count($linhas[0], ';') > substr_count($linhas[0], ',') ? ';' : ',';

    $cabecalho = array_map(fn($h) => strtolower(trim($h)), str_getcsv($linhas[0], $delim));
    $temCabecalho = in_array('cpf', $cabecalho, true);

    $idx = ['cpf' => 0, 'nome' => 1, 'data_nascimento' => 2, 'tipo_vinculo' => 3,
            'cpf_titular' => 4, 'codigo_lotacao' => 5, 'codigo_rh' => 6, 'identificador' => 7];
    if ($temCabecalho) {
        foreach ($idx as $nome => $_) {
            $idx[$nome] = array_search($nome, $cabecalho, true);
        }
    }
    $val = fn($col, $i) => ($i !== false && isset($col[$i])) ? $col[$i] : null;

    $lista = [];
    $inicio = $temCabecalho ? 1 : 0;
    for ($i = $inicio; $i < count($linhas); $i++) {
        if (trim($linhas[$i]) === '') {
            continue;
        }
        $col = str_getcsv($linhas[$i], $delim);
        $lista[] = [
            'cpf'             => $col[$idx['cpf']] ?? '',
            'nome'            => $col[$idx['nome']] ?? '',
            'data_nascimento' => $val($col, $idx['data_nascimento']),
            'tipo_vinculo'    => $val($col, $idx['tipo_vinculo']),
            'cpf_titular'     => $val($col, $idx['cpf_titular']),
            'codigo_lotacao'  => $val($col, $idx['codigo_lotacao']),
            'codigo_rh'       => $val($col, $idx['codigo_rh']),
            'identificador'   => $val($col, $idx['identificador']),
        ];
    }
    return $lista;
}

/**
 * RN-020: altera a situação de um elegível que NÃO foi vacinado, com motivo.
 * status permitido: pendente | recusado | ausente | inelegivel. Motivo obrigatório
 * quando != pendente. Não permite alterar quem já está 'aplicado'.
 * Devolve ['ok'=>true] ou ['ok'=>false,'http','code','message']. Escopo é do chamador.
 */
function alterar_situacao_elegivel(int $elegId, string $status, string $motivo): array
{
    $permitidos = ['pendente', 'recusado', 'ausente', 'inelegivel'];
    if (!in_array($status, $permitidos, true)) {
        return ['ok' => false, 'http' => 400, 'code' => 'STATUS_INVALIDO', 'message' => 'Situação inválida.'];
    }
    if ($status !== 'pendente' && trim($motivo) === '') {
        return ['ok' => false, 'http' => 400, 'code' => 'MOTIVO_OBRIGATORIO', 'message' => 'Informe o motivo.'];
    }
    $eleg = db_primeiro("SELECT status FROM elegivel WHERE id = :id LIMIT 1", [':id' => $elegId]);
    if ($eleg === null) {
        return ['ok' => false, 'http' => 404, 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível inexistente.'];
    }
    if ($eleg['status'] === 'aplicado') {
        return ['ok' => false, 'http' => 409, 'code' => 'JA_VACINADO', 'message' => 'Elegível já vacinado; use retificação da aplicação.'];
    }

    db_executar(
        "UPDATE elegivel SET status = :s, motivo_situacao = :m WHERE id = :id",
        [':s' => $status, ':m' => $status === 'pendente' ? null : trim($motivo), ':id' => $elegId]
    );
    return ['ok' => true];
}

/** Normaliza itens vindos de JSON [{cpf,nome,data_nascimento}]. */
function normalizar_elegiveis_json($itens): array
{
    if (!is_array($itens)) {
        return [];
    }
    $lista = [];
    foreach ($itens as $it) {
        if (!is_array($it)) {
            continue;
        }
        $lista[] = [
            'cpf'             => $it['cpf'] ?? '',
            'nome'            => $it['nome'] ?? '',
            'data_nascimento' => $it['data_nascimento'] ?? null,
            'tipo_vinculo'    => $it['tipo_vinculo'] ?? null,
            'cpf_titular'     => $it['cpf_titular'] ?? null,
            'codigo_lotacao'  => $it['codigo_lotacao'] ?? null,
            'codigo_rh'       => $it['codigo_rh'] ?? null,
            'identificador'   => $it['identificador'] ?? null,
        ];
    }
    return $lista;
}
