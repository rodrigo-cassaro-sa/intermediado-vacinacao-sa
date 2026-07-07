<?php
// ============================================================================
// api/v1/interno/observabilidade.php
// Item 13: métricas operacionais e consulta dos últimos eventos de auditoria.
// Grupo interno (super_admin/operador). Sem dados sensíveis crus.
// ============================================================================

/** GET /api/v1/interno/metricas — números operacionais para acompanhamento. */
function rota_metricas(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);

    $t0 = microtime(true);
    db_primeiro('SELECT 1');
    $bancoMs = round((microtime(true) - $t0) * 1000, 1);

    $conta = fn(string $sql, array $b = []) => (int) (db_primeiro($sql, $b)['c'] ?? 0);

    // Importações por status nas últimas 24h.
    $impStatus = [];
    foreach (db_todos("SELECT status, COUNT(*) AS c FROM importacao_elegiveis WHERE criado_em >= NOW() - INTERVAL 1 DAY GROUP BY status") as $r) {
        $impStatus[$r['status']] = (int) $r['c'];
    }

    responder_sucesso([
        'versao'   => APP_VERSION,
        'ambiente' => APP_ENV,
        'agora'    => date('c'),
        'banco_ms' => $bancoMs,
        'importacoes' => [
            'pendentes'        => $conta("SELECT COUNT(*) c FROM importacao_elegiveis WHERE status = 'pendente'"),
            'processando'      => $conta("SELECT COUNT(*) c FROM importacao_elegiveis WHERE status = 'processando'"),
            'falhas_24h'       => $impStatus['falha'] ?? 0,
            'concluidas_24h'   => $impStatus['concluida'] ?? 0,
        ],
        'aplicacoes' => [
            'confirmadas_hoje' => $conta("SELECT COUNT(*) c FROM aplicacao WHERE status = 'confirmada' AND aplicado_em >= CURDATE()"),
            'lancadas_24h'     => $conta("SELECT COUNT(*) c FROM aplicacao WHERE criado_em >= NOW() - INTERVAL 1 DAY"),
        ],
        'campanhas_ativas'   => $conta("SELECT COUNT(*) c FROM campanha WHERE status = 'ativa' AND excluido_em IS NULL"),
        'seguranca_24h' => [
            'login_falha'      => $conta("SELECT COUNT(*) c FROM log_auditoria WHERE evento = 'login.falha' AND data_hora >= NOW() - INTERVAL 1 DAY"),
            'permissao_negada' => $conta("SELECT COUNT(*) c FROM log_auditoria WHERE evento = 'permissao.negada' AND data_hora >= NOW() - INTERVAL 1 DAY"),
        ],
    ], 'OK.');
}

/** GET /api/v1/interno/auditoria?evento=&limit= — últimos eventos (sem metadata). */
function rota_auditoria_recente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin']);

    $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? min(100, max(1, (int) $_GET['limit'])) : 50;
    $where = '1=1';
    $bind = [];
    if (!empty($_GET['evento'])) {
        $where .= ' AND evento = :e';
        $bind[':e'] = (string) $_GET['evento'];
    }

    $itens = db_todos(
        "SELECT id, tenant_id, ator_tipo, ator_id, evento, origem, entidade_tipo, entidade_id, request_id, ip, data_hora
           FROM log_auditoria WHERE $where ORDER BY id DESC LIMIT $limit",
        $bind
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}
