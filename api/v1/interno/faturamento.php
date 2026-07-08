<?php
// ============================================================================
// api/v1/interno/faturamento.php
// Item 4: relatórios de faturamento por campanha.
//  - a COBRAR do cliente: rede credenciada = por elegível indicado;
//                         in company = por vacinado. Preço [cliente, modalidade, vacina].
//  - a PAGAR às clínicas: por vacinado, preço [clínica, vacina]. Só rede credenciada.
// Grupo interno (super_admin/operador). Base: RN-029.
// ============================================================================

/** GET /api/v1/interno/campanhas/{id}/faturamento-cliente */
function rota_faturamento_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $tenant = (int) $campanha['tenant_id'];
    $modalidade = $campanha['modalidade'];
    $itens = [];
    $total = 0.0;
    $semPreco = [];

    if ($modalidade === 'in_company') {
        // Por vacinado (aplicação confirmada), preço [cliente, in_company, vacina].
        $linhas = db_todos(
            "SELECT a.vacina_id, v.nome AS vacina, COUNT(*) AS quantidade, pc.valor
               FROM aplicacao a
               JOIN vacina v ON v.id = a.vacina_id
          LEFT JOIN preco_cliente pc ON pc.cliente_b2b_id = :t AND pc.modalidade = 'in_company' AND pc.vacina_id = a.vacina_id
              WHERE a.campanha_id = :id AND a.status = 'confirmada'
           GROUP BY a.vacina_id, v.nome, pc.valor",
            [':t' => $tenant, ':id' => $id]
        );
        $baseLabel = 'vacinados';
    } else {
        // Rede credenciada: por elegível indicado × cada vacina da campanha.
        $qtdElegiveis = (int) (db_primeiro(
            "SELECT COUNT(*) AS c FROM elegivel WHERE campanha_id = :id AND status <> 'removido'", [':id' => $id]
        )['c'] ?? 0);
        $linhas = db_todos(
            "SELECT cv.vacina_id, v.nome AS vacina, :q AS quantidade, pc.valor
               FROM campanha_vacina cv
               JOIN vacina v ON v.id = cv.vacina_id
          LEFT JOIN preco_cliente pc ON pc.cliente_b2b_id = :t AND pc.modalidade = 'rede_credenciada' AND pc.vacina_id = cv.vacina_id
              WHERE cv.campanha_id = :id",
            [':q' => $qtdElegiveis, ':t' => $tenant, ':id' => $id]
        );
        $baseLabel = 'elegíveis indicados';
    }

    foreach ($linhas as $l) {
        $qtd = (int) $l['quantidade'];
        $valor = $l['valor'] !== null ? (float) $l['valor'] : null;
        $subtotal = $valor !== null ? round($qtd * $valor, 2) : 0.0;
        if ($valor === null) {
            $semPreco[] = $l['vacina'];
        } else {
            $total += $subtotal;
        }
        $itens[] = [
            'vacina' => $l['vacina'], 'quantidade' => $qtd,
            'valor_unitario' => $valor, 'subtotal' => $subtotal,
        ];
    }

    responder_sucesso([
        'campanha'   => ['id' => $id, 'nome' => $campanha['nome'], 'modalidade' => $modalidade],
        'base'       => $baseLabel,
        'itens'      => $itens,
        'total'      => round($total, 2),
        'sem_preco'  => $semPreco,   // vacinas sem preço cadastrado (não somadas)
    ], 'OK.');
}

/** GET /api/v1/interno/campanhas/{id}/faturamento-clinicas — a pagar às clínicas. */
function rota_faturamento_clinicas(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    if ($campanha['modalidade'] !== 'rede_credenciada') {
        responder_sucesso(['campanha' => ['id' => $id, 'modalidade' => $campanha['modalidade']],
            'itens' => [], 'total' => 0, 'observacao' => 'Sem pagamento a clínicas (modalidade in company).'], 'OK.');
    }

    $linhas = db_todos(
        "SELECT a.executor_id AS clinica_id, cc.nome AS clinica, a.vacina_id, v.nome AS vacina,
                COUNT(*) AS quantidade, pcl.valor
           FROM aplicacao a
           JOIN vacina v ON v.id = a.vacina_id
           JOIN clinica_credenciada cc ON cc.id = a.executor_id
      LEFT JOIN preco_clinica pcl ON pcl.clinica_id = a.executor_id AND pcl.vacina_id = a.vacina_id
          WHERE a.campanha_id = :id AND a.status = 'confirmada' AND a.executor_tipo = 'clinica_credenciada'
       GROUP BY a.executor_id, cc.nome, a.vacina_id, v.nome, pcl.valor
       ORDER BY cc.nome, v.nome",
        [':id' => $id]
    );

    $porClinica = [];
    $totalGeral = 0.0;
    $semPreco = [];
    foreach ($linhas as $l) {
        $cid = (int) $l['clinica_id'];
        $qtd = (int) $l['quantidade'];
        $valor = $l['valor'] !== null ? (float) $l['valor'] : null;
        $subtotal = $valor !== null ? round($qtd * $valor, 2) : 0.0;
        if ($valor === null) {
            $semPreco[] = $l['clinica'] . ' / ' . $l['vacina'];
        } else {
            $totalGeral += $subtotal;
        }
        if (!isset($porClinica[$cid])) {
            $porClinica[$cid] = ['clinica' => $l['clinica'], 'itens' => [], 'total' => 0.0];
        }
        $porClinica[$cid]['itens'][] = ['vacina' => $l['vacina'], 'quantidade' => $qtd, 'valor_unitario' => $valor, 'subtotal' => $subtotal];
        $porClinica[$cid]['total'] = round($porClinica[$cid]['total'] + $subtotal, 2);
    }

    responder_sucesso([
        'campanha'    => ['id' => $id, 'nome' => $campanha['nome']],
        'clinicas'    => array_values($porClinica),
        'total_geral' => round($totalGeral, 2),
        'sem_preco'   => $semPreco,
    ], 'OK.');
}
