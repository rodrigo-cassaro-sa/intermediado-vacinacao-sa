<?php
// ============================================================================
// api/v1/health.php
// Função: health check simples (app + banco). Usado por deploy/monitoramento.
// ============================================================================

function rota_health(array $params): void
{
    $bancoOk = false;
    $diagnostico = null;
    $bancoMs = null;
    try {
        $t0 = microtime(true);
        db_primeiro('SELECT 1 AS ok');
        $bancoMs = round((microtime(true) - $t0) * 1000, 1);
        $bancoOk = true;
    } catch (Throwable $e) {
        $bancoOk = false;
        error_log('[health] banco indisponivel: ' . $e->getMessage());
        // Diagnóstico seguro: o texto de erro de conexão do MySQL NÃO contém a senha,
        // mas ajuda a identificar host/credencial/banco. Só exposto fora de produção.
        if (APP_ENV !== 'producao') {
            $diagnostico = mascarar_diagnostico_banco($e->getMessage());
        }
    }

    $data = [
        'app'      => 'ok',
        'banco'    => $bancoOk ? 'ok' : 'indisponivel',
        'banco_ms' => $bancoMs,
        'versao'   => APP_VERSION,
        'ambiente' => APP_ENV,
        'agora'    => date('c'),
    ];
    if ($diagnostico !== null) {
        $data['banco_diagnostico'] = $diagnostico;
    }

    responder_sucesso($data, 'Serviço no ar.');
}

/** Remove qualquer trecho sensível do texto de erro antes de exibir. */
function mascarar_diagnostico_banco(string $mensagem): string
{
    // Por segurança, corta a partir de possíveis pares chave=valor com credenciais.
    $mensagem = preg_replace('/(password|senha|pwd)=\S+/i', '$1=***', $mensagem);
    return mb_substr($mensagem, 0, 200);
}
