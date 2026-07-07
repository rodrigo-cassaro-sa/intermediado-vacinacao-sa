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

        // RN: CPF válido.
        if (!validar_cpf($cpf)) { $rejeita($linha, $cpf, 'CPF_INVALIDO'); continue; }
        // Nome obrigatório.
        if ($nome === '') { $rejeita($linha, $cpf, 'NOME_OBRIGATORIO'); continue; }
        // RN: data de nascimento obrigatória e válida (AAAA-MM-DD).
        if (!validar_data($nasc)) { $rejeita($linha, $cpf, 'DATA_NASCIMENTO_INVALIDA'); continue; }
        // RN-016: tipo de vínculo obrigatório (colaborador|dependente|terceiro).
        if (!in_array($tipo, $tipos, true)) { $rejeita($linha, $cpf, 'TIPO_VINCULO_INVALIDO'); continue; }
        // RN-017: dependente exige CPF do titular válido; demais não têm titular.
        if ($tipo === 'dependente') {
            if (!validar_cpf($cpfTitular)) { $rejeita($linha, $cpf, 'CPF_TITULAR_INVALIDO'); continue; }
        } else {
            $cpfTitular = null;
        }

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
                "INSERT INTO elegivel (tenant_id, campanha_id, paciente_id, origem, tipo_vinculo, cpf_titular, status, importacao_id)
                 VALUES (:tenant, :campanha, :paciente, :origem, :tipo, :titular, 'pendente', :imp)",
                [
                    ':tenant'   => $tenantId,
                    ':campanha' => $campanhaId,
                    ':paciente' => $pacienteId,
                    ':origem'   => $origem,
                    ':tipo'     => $tipo,
                    ':titular'  => $cpfTitular,
                    ':imp'      => $importacaoId,
                ]
            );
            $criados++;
        } else {
            // Já elegível nesta campanha (dedup) — atualiza tipo/titular sem duplicar.
            db_executar(
                "UPDATE elegivel SET tipo_vinculo = :tipo, cpf_titular = :titular WHERE id = :id",
                [':tipo' => $tipo, ':titular' => $cpfTitular, ':id' => (int) $eleg['id']]
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

    $idxCpf = 0; $idxNome = 1; $idxNasc = 2; $idxTipo = 3; $idxTitular = 4;
    if ($temCabecalho) {
        $idxCpf     = array_search('cpf', $cabecalho, true);
        $idxNome    = array_search('nome', $cabecalho, true);
        $idxNasc    = array_search('data_nascimento', $cabecalho, true);
        $idxTipo    = array_search('tipo_vinculo', $cabecalho, true);
        $idxTitular = array_search('cpf_titular', $cabecalho, true);
    }
    $val = fn($col, $idx) => ($idx !== false && isset($col[$idx])) ? $col[$idx] : null;

    $lista = [];
    $inicio = $temCabecalho ? 1 : 0;
    for ($i = $inicio; $i < count($linhas); $i++) {
        if (trim($linhas[$i]) === '') {
            continue;
        }
        $col = str_getcsv($linhas[$i], $delim);
        $lista[] = [
            'cpf'             => $col[$idxCpf] ?? '',
            'nome'            => $col[$idxNome] ?? '',
            'data_nascimento' => $val($col, $idxNasc),
            'tipo_vinculo'    => $val($col, $idxTipo),
            'cpf_titular'     => $val($col, $idxTitular),
        ];
    }
    return $lista;
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
        ];
    }
    return $lista;
}
