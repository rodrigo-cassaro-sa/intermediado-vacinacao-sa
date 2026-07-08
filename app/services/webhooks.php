<?php
// ============================================================================
// app/services/webhooks.php
// Fase A: webhooks de saída. Enfileira entregas por evento e as processa com
// retry/backoff + assinatura HMAC. Base: docs/11. Falha nunca derruba a operação.
// ============================================================================

// Eventos que geram webhook (whitelist). Disparados automaticamente pela auditoria.
const WEBHOOK_EVENTOS = [
    'aplicacao.registrada', 'aplicacao.estornada', 'aplicacao.retificada',
    'elegivel.situacao_definida', 'importacao.concluida', 'campanha.encerrada',
    'elegiveis.importados',
];

// Backoff entre tentativas (segundos). Após esgotar, a entrega vira 'dead'.
const WEBHOOK_BACKOFF = [60, 300, 1800, 7200, 21600]; // 1m, 5m, 30m, 2h, 6h

/**
 * Enfileira entregas do evento para todas as assinaturas ativas que casam
 * (evento + tenant global ou do próprio tenant). Seguro (fail-safe).
 */
function disparar_evento(string $evento, array $dados, ?int $tenantId = null): void
{
    if (!in_array($evento, WEBHOOK_EVENTOS, true)) {
        return;
    }
    try {
        $assinaturas = db_todos(
            "SELECT id FROM webhook_assinatura
              WHERE ativo = 1 AND evento = :e AND (tenant_id IS NULL OR tenant_id = :t)",
            [':e' => $evento, ':t' => $tenantId]
        );
        if (!$assinaturas) {
            return;
        }
        $payload = json_encode([
            'evento'      => $evento,
            'ocorrido_em' => date('c'),
            'dados'       => $dados,
        ], JSON_UNESCAPED_UNICODE);

        foreach ($assinaturas as $a) {
            db_executar(
                "INSERT INTO webhook_entrega (assinatura_id, evento, payload, status, proxima_tentativa_em)
                 VALUES (:a, :e, :p, 'pendente', NOW())",
                [':a' => (int) $a['id'], ':e' => $evento, ':p' => $payload]
            );
        }
    } catch (Throwable $e) {
        error_log('disparar_evento falhou: ' . $e->getMessage());
    }
}

/** Processa entregas pendentes vencidas (worker/cron). */
function processar_entregas_webhook(int $limite = 50): int
{
    $pend = db_todos(
        "SELECT we.id, we.evento, we.payload, we.tentativas, wa.url, wa.segredo
           FROM webhook_entrega we
           JOIN webhook_assinatura wa ON wa.id = we.assinatura_id
          WHERE we.status = 'pendente' AND we.proxima_tentativa_em <= NOW()
          ORDER BY we.id LIMIT $limite"
    );
    $processadas = 0;
    foreach ($pend as $e) {
        [$ok, $code] = enviar_webhook($e['url'], $e['payload'], $e['segredo'], $e['evento'], (int) $e['id']);
        $tent = (int) $e['tentativas'] + 1;

        if ($ok) {
            db_executar("UPDATE webhook_entrega SET status='entregue', ultimo_status_code=:c, tentativas=:t, entregue_em=NOW() WHERE id=:id",
                [':c' => $code, ':t' => $tent, ':id' => (int) $e['id']]);
        } elseif ($tent > count(WEBHOOK_BACKOFF)) {
            db_executar("UPDATE webhook_entrega SET status='dead', ultimo_status_code=:c, tentativas=:t WHERE id=:id",
                [':c' => $code, ':t' => $tent, ':id' => (int) $e['id']]);
        } else {
            $espera = WEBHOOK_BACKOFF[$tent - 1];
            db_executar("UPDATE webhook_entrega SET ultimo_status_code=:c, tentativas=:t, proxima_tentativa_em = NOW() + INTERVAL :s SECOND WHERE id=:id",
                [':c' => $code, ':t' => $tent, ':s' => $espera, ':id' => (int) $e['id']]);
        }
        $processadas++;
    }
    return $processadas;
}

/** POST assinado (HMAC-SHA256) via stream. Devolve [ok(bool), status_code(int)]. */
function enviar_webhook(string $url, string $payload, string $segredo, string $evento, int $entregaId): array
{
    $assinatura = hash_hmac('sha256', $payload, $segredo);
    $headers = implode("\r\n", [
        'Content-Type: application/json',
        'X-Evento: ' . $evento,
        'X-Entrega-Id: whd_' . $entregaId,
        'X-Assinatura: ' . $assinatura,
    ]);
    $ctx = stream_context_create(['http' => [
        'method'        => 'POST',
        'header'        => $headers,
        'content'       => $payload,
        'timeout'       => 10,
        'ignore_errors' => true,
    ]]);

    $resp = @file_get_contents($url, false, $ctx);
    $code = 0;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
        $code = (int) $m[1];
    }
    $ok = $resp !== false && $code >= 200 && $code < 300;
    return [$ok, $code];
}
