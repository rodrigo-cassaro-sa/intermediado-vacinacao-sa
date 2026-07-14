<?php
// ============================================================================
// scripts/backfill_codigo_campanha.php
// Função: gerar o CÓDIGO automático (migration 026) para campanhas antigas que
//         ainda estão sem código. Usa temporada = ano do período de início e a
//         1ª vacina vinculada. Onde faltar sigla (vacina/cliente) ou a modalidade
//         não mapear (ex.: 'historico'), a campanha é PULADA e relatada.
// Uso (no container, após as siglas estarem preenchidas):
//         php scripts/backfill_codigo_campanha.php
// Seguro reexecutar: só toca em campanhas com codigo IS NULL.
// ============================================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';
require_once __DIR__ . '/../app/services/codigo_campanha.php';

$campanhas = db_todos(
    "SELECT c.id, c.tenant_id, c.modalidade, c.periodo_inicio,
            (SELECT cv.vacina_id FROM campanha_vacina cv WHERE cv.campanha_id = c.id ORDER BY cv.vacina_id LIMIT 1) AS vacina_id
       FROM campanha c
      WHERE c.codigo IS NULL AND c.excluido_em IS NULL
      ORDER BY c.id"
);

$gerados = 0;
$pulados = [];

foreach ($campanhas as $c) {
    $mod = sigla_modalidade((string) $c['modalidade']);
    if ($mod === null) {
        $pulados[] = "#{$c['id']} (modalidade '{$c['modalidade']}' sem mapeamento)";
        continue;
    }
    if (empty($c['vacina_id'])) {
        $pulados[] = "#{$c['id']} (sem vacina vinculada)";
        continue;
    }
    $temporada = (int) substr((string) $c['periodo_inicio'], 0, 4);

    $vac = db_primeiro("SELECT sigla FROM vacina WHERE id = :id LIMIT 1", [':id' => (int) $c['vacina_id']]);
    $cli = db_primeiro(
        "SELECT c.sigla AS cli_sigla, g.sigla AS grp_sigla
           FROM cliente_b2b c LEFT JOIN grupo_empresarial g ON g.id = c.grupo_empresarial_id
          WHERE c.id = :id LIMIT 1",
        [':id' => (int) $c['tenant_id']]
    );

    $vacSigla = $vac ? normalizar_sigla($vac['sigla']) : null;
    $cliSigla = $cli ? normalizar_sigla($cli['cli_sigla']) : null;
    if ($vacSigla === null || $cliSigla === null) {
        $faltam = [];
        if ($vacSigla === null) $faltam[] = 'vacina';
        if ($cliSigla === null) $faltam[] = 'cliente';
        $pulados[] = "#{$c['id']} (sem sigla: " . implode(', ', $faltam) . ')';
        continue;
    }
    $grpSigla = ($cli && normalizar_sigla($cli['grp_sigla'])) ? normalizar_sigla($cli['grp_sigla']) : $cliSigla;

    $prefixo = $vacSigla . '.' . $temporada . '.' . $mod . '.' . $grpSigla . '.' . $cliSigla;
    $seq = proxima_seq_codigo($prefixo);
    $codigo = $prefixo . '.' . $seq;

    db_executar(
        "UPDATE campanha SET codigo = :cod, temporada = :temp, seq = :seq WHERE id = :id",
        [':cod' => $codigo, ':temp' => $temporada, ':seq' => $seq, ':id' => (int) $c['id']]
    );
    echo "OK  #{$c['id']} => $codigo\n";
    $gerados++;
}

echo "\nCampanhas com código gerado: $gerados\n";
if ($pulados) {
    echo "Puladas (" . count($pulados) . "):\n  - " . implode("\n  - ", $pulados) . "\n";
}
