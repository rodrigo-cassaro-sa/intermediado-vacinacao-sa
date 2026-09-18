<?php
// ============================================================================
// app/helpers/email.php
// Função: enviar e-mail transacional por API HTTP de provedor (Resend, Brevo
//         ou SendGrid). Base: docs/11.
//
// Por que API e não SMTP: o projeto não tem Composer nem vendor/, então não há
// PHPMailer; falar SMTP na mão exigiria implementar STARTTLS + AUTH + o diálogo
// inteiro. Os três provedores aqui são um POST JSON — o que muda entre eles é o
// endpoint, o cabeçalho de autenticação e o formato do corpo.
//
// Por que streams e não cURL: o Dockerfile instala o BINÁRIO curl (para o
// healthcheck), não a extensão curl do PHP. file_get_contents com contexto HTTP
// resolve sem mexer na imagem.
//
// Nunca lançamos exceção para o chamador: e-mail que falha não pode derrubar a
// requisição nem revelar ao visitante que o endereço existe.
// ============================================================================

/** O envio está configurado? Usado para decidir entre enviar e cair no log. */
function email_configurado(): bool
{
    return env('EMAIL_PROVEDOR', '') !== '' && env('EMAIL_API_KEY', '') !== '';
}

/**
 * Envia um e-mail. Devolve [ok(bool), detalhe(string)] — o detalhe é para o log
 * técnico, nunca para a resposta HTTP.
 */
function email_enviar(string $para, string $assunto, string $html, string $texto = ''): array
{
    $provedor = strtolower(trim((string) env('EMAIL_PROVEDOR', '')));
    $chave    = (string) env('EMAIL_API_KEY', '');
    $de       = (string) env('EMAIL_REMETENTE', 'nao-responda@saimunizacoes.com.br');
    $deNome   = (string) env('EMAIL_REMETENTE_NOME', 'S&A Imunizações');

    if ($provedor === '' || $chave === '') {
        // Sem provedor configurado: registra e segue. Em homologação isso permite
        // testar o fluxo inteiro lendo o link no log do container.
        error_log("[email] SEM PROVEDOR CONFIGURADO — e-mail não enviado para {$para} | assunto: {$assunto}");
        if (APP_DEBUG) {
            error_log("[email] corpo (texto): {$texto}");
        }
        return [false, 'provedor_nao_configurado'];
    }

    if ($texto === '') {
        $texto = trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
    }

    switch ($provedor) {
        case 'resend':
            $url = 'https://api.resend.com/emails';
            $cabecalhos = ['Authorization: Bearer ' . $chave];
            $corpo = [
                'from'    => "{$deNome} <{$de}>",
                'to'      => [$para],
                'subject' => $assunto,
                'html'    => $html,
                'text'    => $texto,
            ];
            break;

        case 'brevo':
            $url = 'https://api.brevo.com/v3/smtp/email';
            $cabecalhos = ['api-key: ' . $chave];
            $corpo = [
                'sender'      => ['email' => $de, 'name' => $deNome],
                'to'          => [['email' => $para]],
                'subject'     => $assunto,
                'htmlContent' => $html,
                'textContent' => $texto,
            ];
            break;

        case 'sendgrid':
            $url = 'https://api.sendgrid.com/v3/mail/send';
            $cabecalhos = ['Authorization: Bearer ' . $chave];
            $corpo = [
                'personalizations' => [['to' => [['email' => $para]]]],
                'from'             => ['email' => $de, 'name' => $deNome],
                'subject'          => $assunto,
                'content'          => [
                    ['type' => 'text/plain', 'value' => $texto],
                    ['type' => 'text/html',  'value' => $html],
                ],
            ];
            break;

        default:
            error_log("[email] EMAIL_PROVEDOR desconhecido: '{$provedor}' (use resend, brevo ou sendgrid)");
            return [false, 'provedor_desconhecido'];
    }

    return email_post_json($url, $cabecalhos, $corpo);
}

/**
 * POST JSON via stream. Devolve [ok, detalhe].
 * Não usa o handler global de erros: um provedor fora do ar não é erro nosso.
 */
function email_post_json(string $url, array $cabecalhos, array $corpo): array
{
    $json = json_encode($corpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $contexto = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => implode("\r\n", array_merge(
                ['Content-Type: application/json', 'Accept: application/json'],
                $cabecalhos
            )),
            'content'       => $json,
            'timeout'       => (int) env('EMAIL_TIMEOUT', 10),
            'ignore_errors' => true,   // queremos ler o corpo do 4xx/5xx, não um warning
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    // O set_error_handler do bootstrap transforma warning em exceção; um provedor
    // fora do ar viraria 500 para o usuário. Por isso o try/catch.
    try {
        $resposta = @file_get_contents($url, false, $contexto);
    } catch (Throwable $e) {
        error_log('[email] falha de rede: ' . $e->getMessage());
        return [false, 'falha_rede'];
    }

    $status = 0;
    if (isset($http_response_header[0]) && preg_match('#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $m)) {
        $status = (int) $m[1];
    }

    if ($resposta === false || $status < 200 || $status >= 300) {
        // O corpo do erro do provedor pode conter o endereço de destino; fica só
        // no log técnico do container, nunca na resposta HTTP.
        error_log("[email] provedor recusou (HTTP {$status}): " . substr((string) $resposta, 0, 500));
        return [false, "http_{$status}"];
    }

    return [true, "http_{$status}"];
}
