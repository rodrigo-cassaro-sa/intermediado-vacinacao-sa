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
function ingerir_elegiveis(int $campanhaId, int $tenantId, array $lista, string $origem, ?int $importacaoId): array
{
    $recebidos = 0; $criados = 0; $atualizados = 0; $rejeitados = 0;
    $erros = [];
    $tipos = ['colaborador', 'dependente', 'terceiro'];

    $rejeita = function (int $linha, string $cpf, string $code) use (&$rejeitados, &$erros) {
        $rejeitados++;
        if (count($erros) < 50) {
            $erros[] = ['linha' => $linha, 'cpf' => mascarar_cpf($cpf), 'code' => $code];
        }
    };

    foreach ($lista as $i => $item) {
        $recebidos++;
        $linha = $i + 1;
        $cpf   = so_digitos($item['cpf'] ?? '');
        $nome  = trim((string) ($item['nome'] ?? ''));
        $nasc  = trim((string) ($item['data_nascimento'] ?? ''));
        $tipo  = strtolower(trim((string) ($item['tipo_vinculo'] ?? '')));
        $cpfTitular = so_digitos($item['cpf_titular'] ?? '');
        $codLotacao = trim((string) ($item['codigo_lotacao'] ?? ''));
        $codRh      = trim((string) ($item['codigo_rh'] ?? ''));

        // RN: CPF válido.
        if (!validar_cpf($cpf)) { $rejeita($linha, $cpf, 'CPF_INVALIDO'); continue; }
        // Nome obrigatório.
        if ($nome === '') { $rejeita($linha, $cpf, 'NOME_OBRIGATORIO'); continue; }
        // RN-016: data de nascimento OPCIONAL, mas se vier tem de ser válida.
        if ($nasc !== '' && !validar_data($nasc)) { $rejeita($linha, $cpf, 'DATA_NASCIMENTO_INVALIDA'); continue; }
        $nasc = $nasc === '' ? null : $nasc;
        // RN-016: tipo de vínculo obrigatório (colaborador|dependente|terceiro).
        if (!in_array($tipo, $tipos, true)) { $rejeita($linha, $cpf, 'TIPO_VINCULO_INVALIDO'); continue; }
        // RN-017: dependente exige CPF do titular válido; demais não têm titular.
        if ($tipo === 'dependente') {
            if (!validar_cpf($cpfTitular)) { $rejeita($linha, $cpf, 'CPF_TITULAR_INVALIDO'); continue; }
        } else {
            $cpfTitular = null;
        }
        // RN-018: códigos do cliente obrigatórios.
        if ($codLotacao === '') { $rejeita($linha, $cpf, 'CODIGO_LOTACAO_OBRIGATORIO'); continue; }
        if ($codRh === '')      { $rejeita($linha, $cpf, 'CODIGO_RH_OBRIGATORIO'); continue; }

        // Paciente por CPF (identidade global — RN-008).
        $paciente = db_primeiro("SELECT id FROM paciente WHERE cpf = :cpf LIMIT 1", [':cpf' => $cpf]);
        if ($paciente === null) {
            db_executar(
                "INSERT INTO paciente (cpf, nome, data_nascimento) VALUES (:cpf, :nome, :nasc)",
                [':cpf' => $cpf, ':nome' => $nome, ':nasc' => $nasc]
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
                "INSERT INTO elegivel (tenant_id, campanha_id, paciente_id, origem, tipo_vinculo, cpf_titular, codigo_lotacao, codigo_rh, status, importacao_id)
                 VALUES (:tenant, :campanha, :paciente, :origem, :tipo, :titular, :lotacao, :rh, 'pendente', :imp)",
                [
                    ':tenant'   => $tenantId,
                    ':campanha' => $campanhaId,
                    ':paciente' => $pacienteId,
                    ':origem'   => $origem,
                    ':tipo'     => $tipo,
                    ':titular'  => $cpfTitular,
                    ':lotacao'  => $codLotacao,
                    ':rh'       => $codRh,
                    ':imp'      => $importacaoId,
                ]
            );
            $criados++;
        } else {
            // Já elegível nesta campanha (dedup) — atualiza dados sem duplicar.
            db_executar(
                "UPDATE elegivel SET tipo_vinculo = :tipo, cpf_titular = :titular,
                        codigo_lotacao = :lotacao, codigo_rh = :rh WHERE id = :id",
                [':tipo' => $tipo, ':titular' => $cpfTitular, ':lotacao' => $codLotacao, ':rh' => $codRh, ':id' => (int) $eleg['id']]
            );
            $atualizados++;
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
            'cpf_titular' => 4, 'codigo_lotacao' => 5, 'codigo_rh' => 6];
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
        ];
    }
    return $lista;
}
