<?php
// ============================================================================
// api/v1/interno/tabela_verdade.php
// Função: tabela verdade (RN-005, via vw_tabela_verdade) e dashboard da campanha.
// Grupo interno. Leitura sempre filtrada por tenant_id + campanha_id.
// ============================================================================

/** GET /api/v1/interno/campanhas/{id}/tabela-verdade */
function rota_tabela_verdade(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_campanha_do_usuario($usuario, $id);

    [$page, $porPagina, $offset] = paginacao();

    $resumo = resumo_situacao($id);
    $total = array_sum($resumo);

    $itens = db_todos(
        "SELECT cpf, nome, situacao_elegivel, total_aplicacoes, ultima_aplicacao_em
           FROM vw_tabela_verdade
          WHERE campanha_id = :id
          ORDER BY nome
          LIMIT $porPagina OFFSET $offset",
        [':id' => $id]
    );
    foreach ($itens as &$it) {
        $it['cpf'] = mascarar_cpf($it['cpf']);
    }
    unset($it);

    responder_sucesso(['resumo' => $resumo, 'itens' => $itens], 'OK.', 200, [
        'page' => $page, 'por_pagina' => $porPagina, 'total' => $total,
    ]);
}

/** GET /api/v1/interno/campanhas/{id}/dashboard — métricas consolidadas. */
function rota_dashboard(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_campanha_do_usuario($usuario, $id);

    $resumo = resumo_situacao($id);
    $totalElegiveis = array_sum($resumo);
    $aplicados = $resumo['aplicado'] ?? 0;
    $cobertura = $totalElegiveis > 0 ? round($aplicados * 100 / $totalElegiveis, 1) : 0;

    // Aplicações confirmadas por vacina.
    $porVacina = db_todos(
        "SELECT v.nome, COUNT(a.id) AS aplicacoes
           FROM aplicacao a
           JOIN vacina v ON v.id = a.vacina_id
          WHERE a.campanha_id = :id AND a.status = 'confirmada'
          GROUP BY v.id, v.nome
          ORDER BY aplicacoes DESC",
        [':id' => $id]
    );

    responder_sucesso([
        'total_elegiveis'      => $totalElegiveis,
        'aplicados'            => $aplicados,
        'pendentes'            => $resumo['pendente'] ?? 0,
        'recusados'            => $resumo['recusado'] ?? 0,
        'cobertura_percentual' => $cobertura,
        'aplicacoes_por_vacina' => $porVacina,
    ], 'OK.');
}

/**
 * GET /api/v1/interno/campanhas/{id}/exportar — CSV da tabela verdade.
 * Retorno analítico ao cliente B2B (dado da própria campanha). Acesso auditado.
 */
function rota_exportar_tabela_verdade(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $linhas = db_todos(
        "SELECT p.cpf, p.nome, e.status AS situacao,
                COUNT(a.id) AS total_aplicacoes,
                MAX(a.aplicado_em) AS ultima_aplicacao,
                GROUP_CONCAT(DISTINCT v.nome ORDER BY v.nome SEPARATOR ' | ') AS vacinas
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
      LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
      LEFT JOIN vacina v ON v.id = a.vacina_id
          WHERE e.campanha_id = :id
       GROUP BY e.id, p.cpf, p.nome, e.status
       ORDER BY p.nome",
        [':id' => $id]
    );

    registrar_auditoria('tabela_verdade.exportada', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['linhas' => count($linhas)],
    ]);

    // Saída CSV (UTF-8 com BOM p/ Excel; delimitador ; comum em pt-BR).
    if (!headers_sent()) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="tabela-verdade-campanha-' . $id . '.csv"');
        header('X-Request-Id: ' . request_id());
    }
    echo "\xEF\xBB\xBF"; // BOM
    $out = fopen('php://output', 'w');
    fputcsv($out, ['cpf', 'nome', 'situacao', 'total_aplicacoes', 'ultima_aplicacao', 'vacinas'], ';');
    foreach ($linhas as $l) {
        fputcsv($out, [
            $l['cpf'], $l['nome'], $l['situacao'],
            $l['total_aplicacoes'], $l['ultima_aplicacao'] ?? '', $l['vacinas'] ?? '',
        ], ';');
    }
    fclose($out);
    exit;
}

/** Contagem de elegíveis por situação (base da tabela verdade). */
function resumo_situacao(int $campanhaId): array
{
    $linhas = db_todos(
        "SELECT status, COUNT(*) AS total FROM elegivel WHERE campanha_id = :id GROUP BY status",
        [':id' => $campanhaId]
    );
    $resumo = ['pendente' => 0, 'aplicado' => 0, 'recusado' => 0, 'inelegivel' => 0, 'ausente' => 0, 'expirado' => 0];
    foreach ($linhas as $r) {
        $resumo[$r['status']] = (int) $r['total'];
    }
    return $resumo;
}
