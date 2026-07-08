<?php
// ============================================================================
// scripts/processar_webhooks.php
// Worker de entrega de webhooks (Fase A). Rodar por CRON (ex.: a cada minuto).
// Envia as entregas pendentes vencidas, com HMAC e retry/backoff.
// Uso (container):  php scripts/processar_webhooks.php
// ============================================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';
require_once __DIR__ . '/../app/services/webhooks.php';

$n = processar_entregas_webhook(100);
echo "Entregas processadas: $n\n";
