<?php
// ============================================================================
// api/v1/interno/relatorios_gestao.php
// Relatório gerencial por cliente/campanha: doses previstas (meta), elegíveis,
// vacinados, disponíveis (não vacinados) e cobertura, com alerta/crítico quando
// os elegíveis ficam abaixo da meta (crítico < 70%). Inclui contagem de problemas
// de qualidade (sem CPF, sem nome, nascimento inválido) para gestão da base.
// Grupo interno (sessão). Escopo pelos clientes geridos.
// ============================================================================

/** Fragmento SQL que identifica nascimento inválido (nulo, <=1900 ou futuro). */
function _sql_nasc_invalido(): string
{
    return "(COALESCE(e.data_nascimento, p.data_nascimento) IS NULL"
         . " OR YEAR(COALESCE(e.data_nascimento, p.data_nascimento)) <= 1900"
         . " OR COALESCE(e.data_nascimento, p.data_nascimento) > CURDATE())";
}

/** GET /api/v1/interno/relatorios/gestao — visão gerencial de todos os clientes. */
function rota_relatorio_gestao(array $params): void
{
    $usuario = exigir_login();

    $geridos = clientes_geridos_pelo_usuario($usuario);
    if ($geridos !== ['*'] && !$geridos) {
        // Nível local/unidade: cai nos clientes acessíveis (senão a tela fica vazia).
        $geridos = clientes_acessiveis_pelo_usuario($usuario);
    }
    if ($geridos !== ['*'] && !$geridos) {
        responder_sucesso(['itens' => [], 'resumo' => []], 'OK.');
    }
    $where = 'c.excluido_em IS NULL';
    $bind  = [];
    if ($geridos !== ['*']) {
        $ph = [];
        foreach ($geridos as $i => $cid) { $ph[] = ":t_$i"; $bind[":t_$i"] = (int) $cid; }
        $where .= ' AND c.tenant_id IN (' . implode(',', $ph) . ')';
    }
    if (!empty($_GET['cliente_b2b_id']) && is_numeric($_GET['cliente_b2b_id'])) {
        $where .= ' AND c.tenant_id = :cli';
        $bind[':cli'] = (int) $_GET['cliente_b2b_id'];
    }
    if (!empty($_GET['status'])) {
        $where .= ' AND c.status = :st';
        $bind[':st'] = (string) $_GET['status'];
    }
    if (isset($_GET['q']) && trim((string) $_GET['q']) !== '') {
        $q = trim((string) $_GET['q']);
        $where .= ' AND (c.codigo LIKE :q OR c.nome LIKE :q OR cb.razao_social LIKE :q)';
        $bind[':q'] = '%' . $q . '%';
    }

    $nascInv = _sql_nasc_invalido();
    $linhas = db_todos(
        "SELECT c.id, c.codigo, c.temporada, c.nome, c.modalidade, c.status,
                c.periodo_inicio, c.periodo_fim, c.doses_previstas,
                c.tenant_id AS cliente_b2b_id, cb.razao_social AS cliente,
                COUNT(e.id) AS elegiveis,
                SUM(e.status = 'aplicado') AS vacinados,
                SUM(CASE WHEN (p.cpf IS NULL OR p.cpf = '') THEN 1 ELSE 0 END) AS sem_cpf,
                SUM(CASE WHEN (p.cpf IS NULL OR p.cpf = '') AND (p.identificador IS NULL OR p.identificador = '') THEN 1 ELSE 0 END) AS sem_documento,
                SUM(CASE WHEN COALESCE(e.nome, p.nome, '') = '' THEN 1 ELSE 0 END) AS sem_nome,
                SUM(CASE WHEN $nascInv THEN 1 ELSE 0 END) AS nascimento_invalido,
                SUM(CASE WHEN COALESCE(e.nome, p.nome, '') = ''
                          OR ((p.cpf IS NULL OR p.cpf = '') AND (p.identificador IS NULL OR p.identificador = ''))
                          OR $nascInv THEN 1 ELSE 0 END) AS com_problema
           FROM campanha c
           JOIN cliente_b2b cb ON cb.id = c.tenant_id
      LEFT JOIN elegivel e ON e.campanha_id = c.id AND e.status <> 'removido'
      LEFT JOIN paciente p ON p.id = e.paciente_id
          WHERE $where
       GROUP BY c.id, c.codigo, c.temporada, c.nome, c.modalidade, c.status,
                c.periodo_inicio, c.periodo_fim, c.doses_previstas, c.tenant_id, cb.razao_social
       ORDER BY cb.razao_social, c.id DESC",
        $bind
    );

    $itens = [];
    $resumo = ['campanhas' => 0, 'criticos' => 0, 'alertas' => 0, 'com_problema' => 0];
    foreach ($linhas as $l) {
        $eleg = (int) $l['elegiveis'];
        $vac  = (int) $l['vacinados'];
        $meta = $l['doses_previstas'] !== null ? (int) $l['doses_previstas'] : null;
        $disp = max(0, $eleg - $vac); // disponíveis = elegíveis não vacinados
        $cobertura = $eleg > 0 ? round($vac * 100 / $eleg, 1) : 0;

        // Situação pela META (elegíveis x doses previstas).
        $situacao = 'sem_meta';
        $pctMeta = null;
        if ($meta !== null && $meta > 0) {
            $pctMeta = round($eleg * 100 / $meta, 1);
            if ($eleg < 0.7 * $meta) { $situacao = 'critico'; }
            elseif ($eleg < $meta)   { $situacao = 'alerta'; }
            else                     { $situacao = 'ok'; }
        }
        $problema = (int) $l['com_problema'];

        $itens[] = [
            'campanha_id'         => (int) $l['id'],
            'codigo'              => $l['codigo'],
            'nome'                => $l['nome'],
            'temporada'           => $l['temporada'] !== null ? (int) $l['temporada'] : null,
            'modalidade'          => $l['modalidade'],
            'status'              => $l['status'],
            'periodo_inicio'      => $l['periodo_inicio'],
            'periodo_fim'         => $l['periodo_fim'],
            'cliente_b2b_id'      => (int) $l['cliente_b2b_id'],
            'cliente'             => $l['cliente'],
            'doses_previstas'     => $meta,
            'elegiveis'           => $eleg,
            'vacinados'           => $vac,
            'disponiveis'         => $disp,
            'cobertura'           => $cobertura,
            'pct_meta'            => $pctMeta,
            'situacao'            => $situacao,
            'sem_cpf'             => (int) $l['sem_cpf'],
            'sem_documento'       => (int) $l['sem_documento'],
            'sem_nome'            => (int) $l['sem_nome'],
            'nascimento_invalido' => (int) $l['nascimento_invalido'],
            'com_problema'        => $problema,
        ];
        $resumo['campanhas']++;
        if ($situacao === 'critico') { $resumo['criticos']++; }
        if ($situacao === 'alerta')  { $resumo['alertas']++; }
        $resumo['com_problema'] += $problema;
    }

    responder_sucesso(['itens' => $itens, 'resumo' => $resumo], 'OK.');
}

/**
 * GET /api/v1/interno/campanhas/{id}/qualidade
 * Lista os elegíveis com problema de qualidade (sem CPF/identificador, sem nome,
 * nascimento inválido) — drill-down do relatório. CPF conforme a permissão.
 */
function rota_qualidade_campanha(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);
    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'e');

    $nascInv = _sql_nasc_invalido();
    $itens = db_todos(
        "SELECT e.id, p.cpf, p.identificador, COALESCE(e.nome, p.nome) AS nome,
                COALESCE(e.data_nascimento, p.data_nascimento) AS data_nascimento,
                e.tipo_vinculo, e.codigo_rh, e.status,
                (p.cpf IS NULL OR p.cpf = '') AS sem_cpf,
                ((p.cpf IS NULL OR p.cpf = '') AND (p.identificador IS NULL OR p.identificador = '')) AS sem_documento,
                (COALESCE(e.nome, p.nome, '') = '') AS sem_nome,
                ($nascInv) AS nasc_invalido
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
          WHERE e.campanha_id = :id AND e.status <> 'removido'$fUni
            AND (COALESCE(e.nome, p.nome, '') = ''
                 OR ((p.cpf IS NULL OR p.cpf = '') AND (p.identificador IS NULL OR p.identificador = ''))
                 OR $nascInv)
          ORDER BY e.id DESC
          LIMIT 500",
        array_merge([':id' => $id], $bUni)
    );
    $cli = (int) $campanha['tenant_id'];
    foreach ($itens as &$it) {
        $it['cpf'] = cpf_para_usuario($it['cpf'], $usuario, $cli);
    }
    unset($it);

    responder_sucesso(['itens' => $itens], 'OK.');
}
