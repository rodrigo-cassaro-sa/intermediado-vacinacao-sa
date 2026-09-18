<?php
// ============================================================================
// app/services/senha.php
// Função: regras do fluxo "esqueci minha senha" (token de uso único com prazo).
// Base: docs/10 §3. O token puro só existe em memória e dentro do e-mail.
//
// Decisões que valem ler antes de mexer:
//  - O token é sorteado com random_bytes (CSPRNG). Guardamos sha256(token), do
//    mesmo jeito que a senha: um dump do banco não permite trocar senha alheia.
//  - A busca é pelo índice UNIQUE do hash, então não há comparação de string
//    sensível a timing aqui.
//  - Pedir um link novo invalida os anteriores. Sem isso, um link antigo que
//    vazou continuaria valendo até expirar.
//  - Usar um link invalida todos os outros do mesmo usuário.
// ============================================================================

/** Minutos de validade do link (padrão 60). */
function senha_validade_minutos(): int
{
    $v = (int) env('SENHA_RESET_MINUTOS', 60);
    return $v > 0 ? $v : 60;
}

/**
 * Cria um token para o usuário e invalida os pendentes.
 * Devolve o token PURO (só aqui e no e-mail ele existe em claro).
 */
function senha_criar_token(int $usuarioId, string $origem = 'portal'): string
{
    db_executar(
        "UPDATE senha_redefinicao SET invalidado_em = NOW()
          WHERE usuario_id = :u AND usado_em IS NULL AND invalidado_em IS NULL",
        [':u' => $usuarioId]
    );

    $token = bin2hex(random_bytes(32));   // 64 hex = 256 bits

    db_executar(
        "INSERT INTO senha_redefinicao (usuario_id, token_hash, expira_em, ip_solicitante, origem)
         VALUES (:u, :h, DATE_ADD(NOW(), INTERVAL :min MINUTE), :ip, :origem)",
        [
            ':u'      => $usuarioId,
            ':h'      => hash('sha256', $token),
            ':min'    => senha_validade_minutos(),
            ':ip'     => $_SERVER['REMOTE_ADDR'] ?? null,
            ':origem' => $origem === 'admin' ? 'admin' : 'portal',
        ]
    );

    return $token;
}

/**
 * Busca o pedido válido para o token. Devolve o registro + usuário, ou null.
 * "Válido" = existe, não usado, não invalidado, não expirado e usuário ativo.
 */
function senha_pedido_valido(string $token): ?array
{
    if ($token === '' || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;   // formato errado nem chega ao banco
    }

    return db_primeiro(
        "SELECT r.id, r.usuario_id, r.expira_em, r.origem,
                u.nome, u.email, u.status, u.tenant_id, u.perfil
           FROM senha_redefinicao r
           JOIN usuario u ON u.id = r.usuario_id
          WHERE r.token_hash = :h
            AND r.usado_em IS NULL
            AND r.invalidado_em IS NULL
            AND r.expira_em > NOW()
            AND u.excluido_em IS NULL
            AND u.status = 'ativo'
          LIMIT 1",
        [':h' => hash('sha256', $token)]
    );
}

/**
 * Consome o pedido e grava a senha nova numa transação: ou as duas coisas
 * acontecem, ou nenhuma. Sem isso, uma falha no meio deixaria o token queimado
 * com a senha antiga — e o usuário sem link e sem senha.
 */
function senha_aplicar(array $pedido, string $senhaNova): void
{
    try {
        pdo()->beginTransaction();

        db_executar(
            "UPDATE usuario SET senha_hash = :h WHERE id = :u",
            [':h' => password_hash($senhaNova, PASSWORD_DEFAULT), ':u' => $pedido['usuario_id']]
        );
        db_executar(
            "UPDATE senha_redefinicao SET usado_em = NOW() WHERE id = :id",
            [':id' => $pedido['id']]
        );
        // Qualquer outro link pendente do mesmo usuário morre junto.
        db_executar(
            "UPDATE senha_redefinicao SET invalidado_em = NOW()
              WHERE usuario_id = :u AND id <> :id AND usado_em IS NULL AND invalidado_em IS NULL",
            [':u' => $pedido['usuario_id'], ':id' => $pedido['id']]
        );

        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }
}

/** Regra de senha aceitável. Devolve lista de erros no formato da API. */
function senha_validar_forca(string $senha): array
{
    $erros = [];
    if (strlen($senha) < 8) {
        $erros[] = ['field' => 'senha', 'code' => 'SENHA_CURTA', 'message' => 'Mínimo de 8 caracteres.'];
    }
    if (strlen($senha) > 200) {
        $erros[] = ['field' => 'senha', 'code' => 'SENHA_LONGA', 'message' => 'Máximo de 200 caracteres.'];
    }
    return $erros;
}

/**
 * Chaves de rate limit. Ficam aqui (e não soltas no endpoint) para caberem num
 * teste: a coluna `rate_limite.chave` é VARCHAR(80) e uma chave maior faz o
 * controle falhar em silêncio, porque o helper é fail-open.
 *
 * O e-mail nunca entra em claro: a tabela de rate limit não é lugar de guardar
 * quem pediu o quê. 32 hex = 128 bits, de sobra para não colidir.
 */
function senha_chave_rate_email(string $email): string
{
    return 'senha_email:' . substr(hash('sha256', strtolower(trim($email))), 0, 32);
}

function senha_chave_rate_ip(string $acao, ?string $ip): string
{
    return 'senha_' . $acao . ':' . substr((string) ($ip ?: 'desconhecido'), 0, 45);
}

/** URL da tela de redefinição, com o token. */
function senha_link(string $token): string
{
    $base = rtrim((string) env('APP_URL', ''), '/');
    return $base . '/redefinir-senha.html?token=' . $token;
}

/**
 * Monta e dispara o e-mail. Devolve [ok, detalhe] só para o log — o endpoint
 * responde a mesma coisa de qualquer jeito.
 */
function senha_enviar_email(array $usuario, string $token): array
{
    $link  = senha_link($token);
    $min   = senha_validade_minutos();
    $nome  = htmlspecialchars((string) $usuario['nome'], ENT_QUOTES, 'UTF-8');
    $linkH = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

    $assunto = 'Redefinição de senha — S&A Imunizações';

    $html = '<!DOCTYPE html>'
        . '<html lang="pt-BR"><head><meta charset="utf-8" /></head>'
        . '<body style="margin:0;padding:24px;background:#eef3f9;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#12233b">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;margin:0 auto;background:#ffffff;border:1px solid #dbe4f0;border-radius:12px">'
        . '<tr><td style="padding:28px">'
        . '<p style="margin:0 0 4px;font-size:18px;font-weight:700;color:#1666c4">S&amp;A Imunizações</p>'
        . '<p style="margin:0 0 20px;font-size:13px;color:#5c6e86">Redefinição de senha</p>'
        . '<p style="margin:0 0 14px;font-size:15px;line-height:1.6">Olá, ' . $nome . '.</p>'
        . '<p style="margin:0 0 20px;font-size:15px;line-height:1.6">'
        . 'Recebemos um pedido para redefinir a senha da sua conta. Clique no botão abaixo para '
        . 'escolher uma senha nova. O link vale por <strong>' . $min . ' minutos</strong> e só pode ser usado uma vez.'
        . '</p>'
        . '<p style="margin:0 0 24px">'
        . '<a href="' . $linkH . '" style="display:inline-block;background:#1666c4;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px;padding:12px 22px;border-radius:8px">Redefinir minha senha</a>'
        . '</p>'
        . '<p style="margin:0 0 20px;font-size:13px;line-height:1.6;color:#5c6e86">'
        . 'Se o botão não funcionar, copie e cole este endereço no navegador:<br />'
        . '<span style="word-break:break-all;color:#1666c4">' . $linkH . '</span>'
        . '</p>'
        . '<p style="margin:0;padding-top:18px;border-top:1px solid #dbe4f0;font-size:13px;line-height:1.6;color:#5c6e86">'
        . '<strong>Não foi você?</strong> Ignore este e-mail — sua senha atual continua valendo e nada '
        . 'muda. Por segurança, o pedido fica registrado na auditoria da plataforma.'
        . '</p>'
        . '</td></tr></table></body></html>';

    $texto = "Olá, {$usuario['nome']}.\n\n"
        . "Recebemos um pedido para redefinir a senha da sua conta na S&A Imunizações.\n"
        . "Abra o endereço abaixo para escolher uma senha nova. Ele vale por {$min} minutos e só pode ser usado uma vez.\n\n"
        . $link . "\n\n"
        . "Não foi você? Ignore este e-mail — sua senha atual continua valendo.\n";

    return email_enviar((string) $usuario['email'], $assunto, $html, $texto);
}
