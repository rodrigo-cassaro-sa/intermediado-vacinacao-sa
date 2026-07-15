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
    $campanha = exigir_campanha_do_usuario($usuario, $id);
    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'v');

    // Keyset por paciente_id (único por campanha na VIEW) — escala em milhões (item 10).
    [$apos, $porPagina] = paginacao_keyset();

    $resumo = resumo_situacao($id, $usuario, (int) $campanha['tenant_id']);
    $total = array_sum($resumo);

    $where = 'v.campanha_id = :id' . $fUni;
    $bind  = array_merge([':id' => $id], $bUni);
    if ($apos > 0) {
        $where .= ' AND v.paciente_id > :apos';
        $bind[':apos'] = $apos;
    }

    $itens = db_todos(
        "SELECT v.paciente_id, v.cpf, v.nome, v.situacao_elegivel, v.total_aplicacoes, v.ultima_aplicacao_em
           FROM vw_tabela_verdade v
          WHERE $where
          ORDER BY v.paciente_id ASC
          LIMIT $porPagina",
        $bind
    );
    foreach ($itens as &$it) {
        $it['cpf'] = cpf_para_usuario($it['cpf'], $usuario);
    }
    unset($it);

    $proximo = count($itens) === $porPagina ? (int) end($itens)['paciente_id'] : null;

    responder_sucesso(['resumo' => $resumo, 'itens' => $itens], 'OK.', 200, [
        'por_pagina' => $porPagina, 'total' => $total, 'proximo_cursor' => $proximo,
    ]);
}

/**
 * GET /api/v1/interno/campanhas/{id}/vacinados
 * Lista os elegíveis da campanha com a aplicação CONFIRMADA mais recente (se
 * houver) — base da tela de vacinados. Traz elegivel_id (para registrar) e
 * aplicacao_id (para estornar). Filtros: ?status, ?tipo, ?q. Keyset por e.id.
 */
function rota_listar_vacinados(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);
    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'e');
    [$apos, $porPagina] = paginacao_keyset();

    $resumo = resumo_situacao($id, $usuario, (int) $campanha['tenant_id']);
    $total = array_sum($resumo);

    $where = 'e.campanha_id = :id' . $fUni;
    $bind  = array_merge([':id' => $id], $bUni);
    if ($apos > 0) {
        $where .= ' AND e.id < :apos';
        $bind[':apos'] = $apos;
    }
    if (!empty($_GET['status'])) {
        $where .= ' AND e.status = :st';
        $bind[':st'] = (string) $_GET['status'];
    }
    if (!empty($_GET['tipo'])) {
        $where .= ' AND e.tipo_vinculo = :tp';
        $bind[':tp'] = (string) $_GET['tipo'];
    }
    if (isset($_GET['q']) && trim((string) $_GET['q']) !== '') {
        $q = trim((string) $_GET['q']);
        $partes = ['COALESCE(e.nome, p.nome) LIKE :q', 'e.codigo_rh LIKE :q'];
        $bind[':q'] = '%' . $q . '%';
        $qd = so_digitos($q);
        if ($qd !== '') { $partes[] = 'p.cpf LIKE :qd'; $bind[':qd'] = '%' . $qd . '%'; }
        $where .= ' AND (' . implode(' OR ', $partes) . ')';
    }

    $itens = db_todos(
        "SELECT e.id, p.cpf, COALESCE(e.nome, p.nome) AS nome, e.tipo_vinculo, e.status,
                a.id AS aplicacao_id, a.aplicado_em, a.dose, a.lote, vv.nome AS vacina,
                a.profissional_nome, a.profissional_cpf, a.cidade, a.uf, a.unidade,
                a.executor_tipo, a.clinica_id, cl.nome AS clinica
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
      LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
                AND a.id = (SELECT MAX(a2.id) FROM aplicacao a2 WHERE a2.elegivel_id = e.id AND a2.status = 'confirmada')
      LEFT JOIN vacina vv ON vv.id = a.vacina_id
      LEFT JOIN clinica_credenciada cl ON cl.id = a.clinica_id
          WHERE $where
          ORDER BY e.id DESC
          LIMIT $porPagina",
        $bind
    );
    foreach ($itens as &$it) {
        $it['cpf'] = cpf_para_usuario($it['cpf'], $usuario);
        if (!empty($it['profissional_cpf'])) {
            $it['profissional_cpf'] = cpf_para_usuario($it['profissional_cpf'], $usuario);
        }
    }
    unset($it);
    $proximo = count($itens) === $porPagina ? (int) end($itens)['id'] : null;

    responder_sucesso(['resumo' => $resumo, 'itens' => $itens], 'OK.', 200, [
        'por_pagina' => $porPagina, 'total' => $total, 'proximo_cursor' => $proximo,
    ]);
}

/**
 * GET /api/v1/interno/campanhas/{id}/vacinados/exportar
 * CSV completo dos vacinados (lastro RN-019): paciente, vacina, lote, data,
 * clínica, profissional (nome/CPF), cidade/UF e unidade. Respeita os filtros
 * ?status/?tipo/?q. Acesso auditado (dado sensível de saúde).
 */
function rota_exportar_vacinados(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);
    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'e');

    $where = 'e.campanha_id = :id' . $fUni;
    $bind  = array_merge([':id' => $id], $bUni);
    if (!empty($_GET['status'])) { $where .= ' AND e.status = :st'; $bind[':st'] = (string) $_GET['status']; }
    if (!empty($_GET['tipo']))   { $where .= ' AND e.tipo_vinculo = :tp'; $bind[':tp'] = (string) $_GET['tipo']; }
    if (isset($_GET['q']) && trim((string) $_GET['q']) !== '') {
        $q = trim((string) $_GET['q']);
        $partes = ['COALESCE(e.nome, p.nome) LIKE :q', 'e.codigo_rh LIKE :q'];
        $bind[':q'] = '%' . $q . '%';
        $qd = so_digitos($q);
        if ($qd !== '') { $partes[] = 'p.cpf LIKE :qd'; $bind[':qd'] = '%' . $qd . '%'; }
        $where .= ' AND (' . implode(' OR ', $partes) . ')';
    }

    $linhas = db_todos(
        "SELECT p.cpf, COALESCE(e.nome, p.nome) AS nome, e.tipo_vinculo, e.status,
                e.codigo_lotacao, e.codigo_rh,
                vv.nome AS vacina, a.dose, a.lote, a.aplicado_em,
                cl.nome AS clinica, a.executor_tipo, a.profissional_nome, a.profissional_cpf,
                a.cidade, a.uf, a.unidade
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
      LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
                AND a.id = (SELECT MAX(a2.id) FROM aplicacao a2 WHERE a2.elegivel_id = e.id AND a2.status = 'confirmada')
      LEFT JOIN vacina vv ON vv.id = a.vacina_id
      LEFT JOIN clinica_credenciada cl ON cl.id = a.clinica_id
          WHERE $where
          ORDER BY p.nome",
        $bind
    );

    registrar_auditoria('vacinados.exportados', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['linhas' => count($linhas), 'filtro_status' => $_GET['status'] ?? ''],
    ]);

    if (!headers_sent()) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="vacinados-campanha-' . $id . '.csv"');
        header('X-Request-Id: ' . request_id());
    }
    echo "\xEF\xBB\xBF"; // BOM (Excel)
    $out = fopen('php://output', 'w');
    fputcsv($out, [
        'cpf', 'nome', 'tipo_vinculo', 'situacao', 'codigo_lotacao', 'codigo_rh',
        'vacina', 'dose', 'lote', 'aplicado_em',
        'clinica', 'executor', 'profissional_nome', 'profissional_cpf', 'cidade', 'uf', 'unidade',
    ], ';');
    foreach ($linhas as $l) {
        fputcsv($out, [
            cpf_para_usuario($l['cpf'], $usuario), $l['nome'], $l['tipo_vinculo'] ?? '', $l['status'],
            $l['codigo_lotacao'] ?? '', $l['codigo_rh'] ?? '',
            $l['vacina'] ?? '', $l['dose'] ?? '', $l['lote'] ?? '', $l['aplicado_em'] ?? '',
            $l['clinica'] ?? '', $l['executor_tipo'] ?? '', $l['profissional_nome'] ?? '',
            cpf_para_usuario($l['profissional_cpf'] ?? '', $usuario), $l['cidade'] ?? '', $l['uf'] ?? '', $l['unidade'] ?? '',
        ], ';');
    }
    fclose($out);
    exit;
}

/** GET /api/v1/interno/campanhas/{id}/dashboard — métricas consolidadas. */
function rota_dashboard(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $resumo = resumo_situacao($id, $usuario, (int) $campanha['tenant_id']);
    $totalElegiveis = array_sum($resumo);
    $aplicados = $resumo['aplicado'] ?? 0;
    $cobertura = $totalElegiveis > 0 ? round($aplicados * 100 / $totalElegiveis, 1) : 0;

    // Aplicações confirmadas por vacina (respeita a restrição de unidade).
    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'e');
    $porVacina = db_todos(
        "SELECT vc.nome, COUNT(a.id) AS aplicacoes
           FROM aplicacao a
           JOIN elegivel e ON e.id = a.elegivel_id
           JOIN vacina vc ON vc.id = a.vacina_id
          WHERE a.campanha_id = :id AND a.status = 'confirmada'$fUni
          GROUP BY vc.id, vc.nome
          ORDER BY aplicacoes DESC",
        array_merge([':id' => $id], $bUni)
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

    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'e');
    $linhas = db_todos(
        "SELECT p.cpf, COALESCE(e.nome, p.nome) AS nome, e.status AS situacao,
                COUNT(a.id) AS total_aplicacoes,
                MAX(a.aplicado_em) AS ultima_aplicacao,
                GROUP_CONCAT(DISTINCT v.nome ORDER BY v.nome SEPARATOR ' | ') AS vacinas
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
      LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
      LEFT JOIN vacina v ON v.id = a.vacina_id
          WHERE e.campanha_id = :id$fUni
       GROUP BY e.id, p.cpf, p.nome, e.status
       ORDER BY p.nome",
        array_merge([':id' => $id], $bUni)
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
            cpf_para_usuario($l['cpf'], $usuario), $l['nome'], $l['situacao'],
            $l['total_aplicacoes'], $l['ultima_aplicacao'] ?? '', $l['vacinas'] ?? '',
        ], ';');
    }
    fclose($out);
    exit;
}

/** Contagem de elegíveis por situação (base da tabela verdade), com restrição de unidade. */
function resumo_situacao(int $campanhaId, ?array $usuario = null, ?int $clienteId = null): array
{
    $fUni = ''; $bUni = [];
    if ($usuario !== null && $clienteId !== null) {
        [$fUni, $bUni] = filtro_unidade_sql($usuario, $clienteId, 'e');
    }
    $linhas = db_todos(
        "SELECT e.status, COUNT(*) AS total FROM elegivel e WHERE e.campanha_id = :id$fUni GROUP BY e.status",
        array_merge([':id' => $campanhaId], $bUni)
    );
    $resumo = ['pendente' => 0, 'aplicado' => 0, 'recusado' => 0, 'inelegivel' => 0, 'ausente' => 0, 'expirado' => 0, 'removido' => 0];
    foreach ($linhas as $r) {
        $resumo[$r['status']] = (int) $r['total'];
    }
    return $resumo;
}
