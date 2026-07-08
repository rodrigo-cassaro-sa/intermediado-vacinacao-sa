<?php
// ============================================================================
// app/bootstrap.php
// Função: carregar config, conexão, helpers e middlewares em ordem, e instalar
//         o handler global de erros (nunca vaza stack trace — docs/09/10).
// ============================================================================

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/conexao.php';

require_once __DIR__ . '/helpers/resposta.php';
require_once __DIR__ . '/helpers/validacao.php';
require_once __DIR__ . '/helpers/auditoria.php';
require_once __DIR__ . '/helpers/idempotencia.php';
require_once __DIR__ . '/helpers/rate_limit.php';
require_once __DIR__ . '/services/historico.php';
require_once __DIR__ . '/services/webhooks.php';

require_once __DIR__ . '/middlewares/auth_sessao.php';
require_once __DIR__ . '/middlewares/auth_api.php';
require_once __DIR__ . '/middlewares/tenant.php';

// Handler global: erro técnico vira 500 padronizado, com request_id, sem detalhes.
set_exception_handler(function (Throwable $e): void {
    error_log('[' . request_id() . '] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    $errors = APP_DEBUG
        ? [['field' => null, 'code' => 'ERRO_INTERNO', 'message' => $e->getMessage()]]
        : [['field' => null, 'code' => 'ERRO_INTERNO', 'message' => 'Erro interno. Tente novamente.']];
    responder_erro('Erro interno.', 500, $errors);
});

// Converte erros PHP em exceção (para caírem no handler acima).
set_error_handler(function (int $severidade, string $mensagem, string $arquivo, int $linha): bool {
    if (!(error_reporting() & $severidade)) {
        return false;
    }
    throw new ErrorException($mensagem, 0, $severidade, $arquivo, $linha);
});
