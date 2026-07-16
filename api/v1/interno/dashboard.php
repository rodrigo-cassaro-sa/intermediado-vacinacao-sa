<?php
// ============================================================================
// api/v1/interno/dashboard.php
// Função: visão geral consolidada (dashboard admin) — métricas agregadas de
//         TODAS as campanhas do escopo do usuário, série mensal de vacinações e
//         a lista de campanhas ativas/planejadas. Grupo interno (sessão).
// Escopo: interno vê tudo; gestores (negócio/grupo) veem só os clientes que
//         gerem (isolamento LGPD/financeiro, igual ao restante do portal).
// ============================================================================

/** GET /api/v1/interno/dashboard — números para a tela "Visão Geral". */
function rota_dashboard_visao_geral(array $params): void
{
    $usuario = exigir_login();

    // --- Escopo de clientes (tenants) que o usuário pode ver ----------------
    $geridos = clientes_geridos_pelo_usuario($usuario);
    if ($geridos !== ['*'] && !$geridos) {
        // Usuário sem gestão (ex.: nível LOCAL/unidade) enxerga os clientes a
        // que tem acesso; as LISTAS detalhadas (elegíveis/vacinados) continuam
        // restritas à unidade via filtro_unidade_sql.
        $geridos = clientes_acessiveis_pelo_usuario($usuario);
    }
    if ($geridos !== ['*'] && !$geridos) {
        responder_erro('Sem clientes no seu escopo de gestão.', 403, [
            ['field' => null, 'code' => 'SEM_ESCOPO', 'message' => 'Seu usuário não gere nenhum cliente.'],
        ]);
    }
    // Fragmento SQL reutilizável: filtra por c.tenant_id (alias 'c' = campanha).
    $tFiltro = '';
    $tBind   = [];
    if ($geridos !== ['*']) {
        $ph = [];
        foreach ($geridos as $i => $cid) { $ph[] = ":t_$i"; $tBind[":t_$i"] = (int) $cid; }
        $tFiltro = ' AND c.tenant_id IN (' . implode(',', $ph) . ')';
    }

    // --- Restrição por UNIDADE (usuário só-local) ---------------------------
    // Se TODO cliente do escopo restringe por unidade, filtra elegíveis/aplicações
    // pelas unidades do usuário. Se algum cliente é gerido por inteiro, sem filtro.
    // IN com inteiros vindos do banco (sem placeholders → sem risco de reuso HY093).
    $uEleg = '';
    $uAplic = '';
    if ($geridos !== ['*']) {
        $unidades = [];
        $semRestricao = false;
        foreach ($geridos as $cid) {
            $r = unidades_restritas_do_usuario($usuario, (int) $cid);
            if ($r === null) { $semRestricao = true; break; }
            foreach ($r as $uid) { $unidades[(int) $uid] = true; }
        }
        if (!$semRestricao && $unidades) {
            $inU = implode(',', array_map('intval', array_keys($unidades)));
            $uEleg  = " AND e.unidade_id IN ($inU)";
            $uAplic = " AND a.elegivel_id IN (SELECT id FROM elegivel WHERE unidade_id IN ($inU))";
        }
    }

    // --- Filtro opcional por campanha ---------------------------------------
    // Sem ?campanha_id => agrega TODAS as campanhas ativas do escopo.
    // Com ?campanha_id => valida o acesso e restringe tudo àquela campanha.
    $campanhaId = null;
    if (isset($_GET['campanha_id']) && is_numeric($_GET['campanha_id'])) {
        $campanhaId = (int) $_GET['campanha_id'];
        exigir_campanha_do_usuario($usuario, $campanhaId); // 403/404 se fora do escopo
    }
    $modoUnico = $campanhaId !== null;

    // --- 1) Métricas: elegíveis (da campanha, ou de todas as ATIVAS) --------
    $porStatus = ['pendente' => 0, 'aplicado' => 0, 'recusado' => 0, 'inelegivel' => 0, 'ausente' => 0, 'expirado' => 0, 'removido' => 0];
    if ($modoUnico) {
        $sqlMetrica = "SELECT e.status, COUNT(*) AS c FROM elegivel e WHERE e.campanha_id = :camp$uEleg GROUP BY e.status";
        $bindMetrica = [':camp' => $campanhaId];
    } else {
        $sqlMetrica = "SELECT e.status, COUNT(*) AS c
                         FROM elegivel e
                         JOIN campanha c ON c.id = e.campanha_id
                        WHERE c.status = 'ativa' AND c.excluido_em IS NULL$tFiltro$uEleg
                        GROUP BY e.status";
        $bindMetrica = $tBind;
    }
    foreach (db_todos($sqlMetrica, $bindMetrica) as $r) {
        $porStatus[$r['status']] = (int) $r['c'];
    }
    $totalElegiveis = array_sum($porStatus);
    $vacinados = $porStatus['aplicado'];
    $pendentes = $porStatus['pendente'];
    $cobertura = $totalElegiveis > 0 ? round($vacinados * 100 / $totalElegiveis, 1) : 0;

    // --- 2) Evolução: aplicações confirmadas por mês (últimos 6 meses) ------
    $rotulos = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $meses = [];
    for ($i = 5; $i >= 0; $i--) {
        $ts = strtotime("first day of -$i month");
        $ym = date('Y-m', $ts);
        $meses[$ym] = ['ym' => $ym, 'mes' => $rotulos[(int) date('n', $ts)], 'aplicacoes' => 0];
    }
    $inicioSerie = array_key_first($meses) . '-01';
    if ($modoUnico) {
        $sqlEvo = "SELECT DATE_FORMAT(a.aplicado_em, '%Y-%m') AS ym, COUNT(*) AS c
                     FROM aplicacao a
                    WHERE a.status = 'confirmada' AND a.aplicado_em >= :ini AND a.campanha_id = :camp$uAplic
                    GROUP BY ym";
        $bindEvo = [':ini' => $inicioSerie, ':camp' => $campanhaId];
    } else {
        $sqlEvo = "SELECT DATE_FORMAT(a.aplicado_em, '%Y-%m') AS ym, COUNT(*) AS c
                     FROM aplicacao a
                     JOIN campanha c ON c.id = a.campanha_id
                    WHERE a.status = 'confirmada' AND a.aplicado_em >= :ini$tFiltro$uAplic
                    GROUP BY ym";
        $bindEvo = array_merge([':ini' => $inicioSerie], $tBind);
    }
    foreach (db_todos($sqlEvo, $bindEvo) as $r) {
        if (isset($meses[$r['ym']])) {
            $meses[$r['ym']]['aplicacoes'] = (int) $r['c'];
        }
    }
    $evolucao = array_values($meses);

    // Delta real de vacinações: mês corrente vs. mês anterior.
    $n = count($evolucao);
    $mesAtual     = $n >= 1 ? $evolucao[$n - 1]['aplicacoes'] : 0;
    $mesAnterior  = $n >= 2 ? $evolucao[$n - 2]['aplicacoes'] : 0;
    $deltaPct     = $mesAnterior > 0 ? round(($mesAtual - $mesAnterior) * 100 / $mesAnterior, 1) : null;

    // --- 3) Lista de campanhas (a selecionada, ou as ativas/planejadas) -----
    if ($modoUnico) {
        $sqlCamp = "SELECT c.id, c.codigo, c.temporada, c.nome, c.status, c.periodo_inicio, c.periodo_fim, cb.razao_social AS cliente,
                           (SELECT COUNT(*) FROM elegivel e WHERE e.campanha_id = c.id$uEleg) AS elegiveis,
                           (SELECT COUNT(*) FROM elegivel e WHERE e.campanha_id = c.id AND e.status = 'aplicado'$uEleg) AS aplicados
                      FROM campanha c
                      JOIN cliente_b2b cb ON cb.id = c.tenant_id
                     WHERE c.id = :camp";
        $bindCamp = [':camp' => $campanhaId];
    } else {
        $sqlCamp = "SELECT c.id, c.codigo, c.temporada, c.nome, c.status, c.periodo_inicio, c.periodo_fim, cb.razao_social AS cliente,
                           (SELECT COUNT(*) FROM elegivel e WHERE e.campanha_id = c.id$uEleg) AS elegiveis,
                           (SELECT COUNT(*) FROM elegivel e WHERE e.campanha_id = c.id AND e.status = 'aplicado'$uEleg) AS aplicados
                      FROM campanha c
                      JOIN cliente_b2b cb ON cb.id = c.tenant_id
                     WHERE c.excluido_em IS NULL AND c.status IN ('ativa', 'rascunho')$tFiltro
                     ORDER BY FIELD(c.status, 'ativa', 'rascunho'), c.periodo_fim ASC
                     LIMIT 8";
        $bindCamp = $tBind;
    }
    $campanhas = db_todos($sqlCamp, $bindCamp);
    $hoje = date('Y-m-d');
    $ativas = 0;
    foreach ($campanhas as &$c) {
        $eleg = (int) $c['elegiveis'];
        $c['elegiveis'] = $eleg;
        $c['aplicados'] = (int) $c['aplicados'];
        $c['cobertura'] = $eleg > 0 ? round($c['aplicados'] * 100 / $eleg, 1) : 0;
        if ($c['status'] === 'rascunho') {
            $c['situacao'] = 'planejada';
        } elseif ($c['status'] === 'encerrada') {
            $c['situacao'] = 'encerrada';
        } elseif ($c['periodo_fim'] < $hoje) {
            $c['situacao'] = 'atrasada';
            $ativas++;
        } else {
            $c['situacao'] = 'em_andamento';
            $ativas++;
        }
    }
    unset($c);

    responder_sucesso([
        'campanha_id'            => $campanhaId,
        'total_elegiveis'        => $totalElegiveis,
        'total_vacinados'        => $vacinados,
        'pendentes'              => $pendentes,
        'cobertura_percentual'   => $cobertura,
        'vacinados_mes'          => $mesAtual,
        'vacinados_mes_anterior' => $mesAnterior,
        'vacinados_delta_pct'    => $deltaPct,
        'campanhas_ativas'       => $ativas,
        'evolucao'               => $evolucao,
        'campanhas'              => $campanhas,
        'atualizado_em'          => date('c'),
    ], 'OK.');
}
