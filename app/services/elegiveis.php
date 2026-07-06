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

    foreach ($lista as $i => $item) {
        $recebidos++;
        $linha = $i + 1;
        $cpf  = so_digitos($item['cpf'] ?? '');
        $nome = trim((string) ($item['nome'] ?? ''));
        $nasc = $item['data_nascimento'] ?? null;

        if (!validar_cpf($cpf)) {
            $rejeitados++;
            if (count($erros) < 50) {
                $erros[] = ['linha' => $linha, 'cpf' => mascarar_cpf($cpf), 'code' => 'CPF_INVALIDO'];
            }
            continue;
        }
        if ($nome === '') {
            $rejeitados++;
            if (count($erros) < 50) {
                $erros[] = ['linha' => $linha, 'cpf' => mascarar_cpf($cpf), 'code' => 'NOME_OBRIGATORIO'];
            }
            continue;
        }
        if ($nasc !== null && $nasc !== '' && !validar_data($nasc)) {
            $nasc = null; // data inválida é ignorada, não rejeita o registro
        }
        if ($nasc === '') {
            $nasc = null;
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
                "INSERT INTO elegivel (tenant_id, campanha_id, paciente_id, origem, status, importacao_id)
                 VALUES (:tenant, :campanha, :paciente, :origem, 'pendente', :imp)",
                [
                    ':tenant'   => $tenantId,
                    ':campanha' => $campanhaId,
                    ':paciente' => $pacienteId,
                    ':origem'   => $origem,
                    ':imp'      => $importacaoId,
                ]
            );
            $criados++;
        } else {
            $atualizados++; // já elegível nesta campanha (dedup) — não duplica
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

    $idxCpf = 0; $idxNome = 1; $idxNasc = 2;
    if ($temCabecalho) {
        $idxCpf  = array_search('cpf', $cabecalho, true);
        $idxNome = array_search('nome', $cabecalho, true);
        $idxNasc = array_search('data_nascimento', $cabecalho, true);
    }

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
            'data_nascimento' => ($idxNasc !== false && isset($col[$idxNasc])) ? $col[$idxNasc] : null,
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
        ];
    }
    return $lista;
}
