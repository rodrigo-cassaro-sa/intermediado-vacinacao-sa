<?php
// ============================================================================
// api/v1/health.php
// Função: health check simples (app + banco). Usado por deploy/monitoramento.
// ============================================================================

function rota_health(array $params): void
{
    $bancoOk = false;
    try {
        db_primeiro('SELECT 1 AS ok');
        $bancoOk = true;
    } catch (Throwable $e) {
        $bancoOk = false;
    }

    responder_sucesso([
        'app'    => 'ok',
        'banco'  => $bancoOk ? 'ok' : 'indisponivel',
        'ambiente' => APP_ENV,
        'agora'  => date('c'),
    ], 'Serviço no ar.');
}
