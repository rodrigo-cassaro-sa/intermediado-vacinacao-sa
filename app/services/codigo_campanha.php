<?php
// ============================================================================
// app/services/codigo_campanha.php
// Codificação automática de campanha (migration 026):
//   <VAC>.<TEMP>.<MOD>.<GRP>.<CLI>.<SEQ>   ex.: IF3.2026.IC.TES.TES.1
// VAC = vacina.sigla · TEMP = temporada (ano) · MOD = IC|RC ·
// GRP = grupo.sigla (ou sigla do cliente, se sem grupo) · CLI = cliente.sigla ·
// SEQ = MAX(seq)+1 das campanhas com o mesmo prefixo VAC.TEMP.MOD.GRP.CLI.
// ============================================================================

/** Normaliza uma sigla: MAIÚSCULA e exatamente 3 caracteres A-Z/0-9, ou null. */
function normalizar_sigla($valor): ?string
{
    $s = strtoupper(trim((string) $valor));
    return preg_match('/^[A-Z0-9]{3}$/', $s) === 1 ? $s : null;
}

/** Mapeia a modalidade da campanha para a sigla do código (IC|RC), ou null. */
function sigla_modalidade(string $modalidade): ?string
{
    return $modalidade === 'in_company' ? 'IC' : ($modalidade === 'rede_credenciada' ? 'RC' : null);
}

/**
 * Monta o prefixo VAC.TEMP.MOD.GRP.CLI a partir dos cadastros.
 * Responde 422 (e encerra) se faltar sigla ou algo for inválido — o chamador
 * já deve ter aberto/gerenciado a transação conforme necessário.
 */
function prefixo_codigo_campanha(int $clienteId, int $vacinaId, string $modalidade, int $temporada): string
{
    $mod = sigla_modalidade($modalidade);
    if ($mod === null) {
        responder_erro('Modalidade inválida para o código.', 422, [
            ['field' => 'modalidade', 'code' => 'MODALIDADE_INVALIDA', 'message' => 'Use in_company ou rede_credenciada.'],
        ]);
    }
    if ($temporada < 2000 || $temporada > 2100) {
        responder_erro('Temporada inválida.', 422, [
            ['field' => 'temporada', 'code' => 'TEMPORADA_INVALIDA', 'message' => 'Informe um ano entre 2000 e 2100.'],
        ]);
    }

    $vac = db_primeiro("SELECT sigla FROM vacina WHERE id = :id LIMIT 1", [':id' => $vacinaId]);
    $cli = db_primeiro(
        "SELECT c.sigla AS cli_sigla, g.sigla AS grp_sigla
           FROM cliente_b2b c
           LEFT JOIN grupo_empresarial g ON g.id = c.grupo_empresarial_id
          WHERE c.id = :id LIMIT 1",
        [':id' => $clienteId]
    );

    $faltando = [];
    if ($vac === null || normalizar_sigla($vac['sigla']) === null) { $faltando[] = 'vacina'; }
    if ($cli === null || normalizar_sigla($cli['cli_sigla']) === null) { $faltando[] = 'cliente'; }
    if ($faltando) {
        responder_erro('Faltam siglas para gerar o código: ' . implode(', ', $faltando) . '.', 422, [
            ['field' => 'sigla', 'code' => 'SIGLA_AUSENTE', 'message' => 'Cadastre a sigla de: ' . implode(', ', $faltando) . '.'],
        ]);
    }

    $vacSigla = normalizar_sigla($vac['sigla']);
    $cliSigla = normalizar_sigla($cli['cli_sigla']);
    // Sem grupo (ou grupo sem sigla) => repete a sigla do cliente.
    $grpSigla = normalizar_sigla($cli['grp_sigla']) ?? $cliSigla;

    return $vacSigla . '.' . $temporada . '.' . $mod . '.' . $grpSigla . '.' . $cliSigla;
}

/** Próxima sequência (SEQ) para um prefixo: MAX(seq)+1 das campanhas com aquele prefixo. */
function proxima_seq_codigo(string $prefixo): int
{
    $r = db_primeiro(
        "SELECT COALESCE(MAX(seq), 0) + 1 AS prox FROM campanha WHERE codigo LIKE :p",
        [':p' => $prefixo . '.%']
    );
    return max(1, (int) ($r['prox'] ?? 1));
}
