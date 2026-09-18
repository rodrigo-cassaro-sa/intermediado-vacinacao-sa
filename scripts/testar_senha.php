<?php
// ============================================================================
// scripts/testar_senha.php
// Teste do fluxo "esqueci minha senha" (token de uso único com prazo).
//
// Roda em dois modos, no mesmo espírito do testar_csv.php:
//   php scripts/testar_senha.php          -> só o que não depende de banco
//   php scripts/testar_senha.php --banco  -> + o ciclo completo contra o MySQL
//
// O modo --banco cria um usuário descartável (email @teste.invalido, que não
// existe como domínio real), exercita o ciclo e apaga tudo no final. Não envia
// e-mail: o envio é testado à parte, configurando EMAIL_PROVEDOR.
//
// Sai com código 1 se algum caso falhar (serve para CI/pós-deploy).
// ============================================================================

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

$comBanco = in_array('--banco', $argv, true);

$ok = 0;
$falhou = 0;

function checar(string $nome, $real, $esperado): void
{
    global $ok, $falhou;
    $a = json_encode($real, JSON_UNESCAPED_UNICODE);
    $b = json_encode($esperado, JSON_UNESCAPED_UNICODE);
    if ($a === $b) {
        $ok++;
        echo "  PASS  {$nome}\n";
    } else {
        $falhou++;
        echo "  FAIL  {$nome}\n        esperado: {$b}\n        obtido:   {$a}\n";
    }
}

function titulo(string $t): void { echo "\n== {$t} ==\n"; }

// ---------------------------------------------------------------------------
// Parte 1: sem banco. Carregamos só o que declara função pura.
// ---------------------------------------------------------------------------
require_once BASE_PATH . '/app/config/config.php';   // env(), APP_DEBUG
require_once BASE_PATH . '/app/helpers/email.php';

titulo('1. Configuração de envio');

putenv('EMAIL_PROVEDOR=');
putenv('EMAIL_API_KEY=');
checar('sem provedor -> não configurado', email_configurado(), false);

putenv('EMAIL_PROVEDOR=resend');
checar('provedor sem chave -> não configurado', email_configurado(), false);

putenv('EMAIL_API_KEY=re_chave_de_teste');
checar('provedor + chave -> configurado', email_configurado(), true);

putenv('EMAIL_PROVEDOR=provedor_que_nao_existe');
[$enviou, $motivo] = email_enviar('alguem@teste.invalido', 'x', '<p>x</p>');
checar('provedor desconhecido não envia', $enviou, false);
checar('provedor desconhecido diz o porquê', $motivo, 'provedor_desconhecido');

putenv('EMAIL_PROVEDOR=');
putenv('EMAIL_API_KEY=');
[$enviou2, $motivo2] = email_enviar('alguem@teste.invalido', 'x', '<p>x</p>');
checar('sem provedor não envia', $enviou2, false);
checar('sem provedor diz o porquê', $motivo2, 'provedor_nao_configurado');

titulo('2. Validade do link');

putenv('SENHA_RESET_MINUTOS=');
// O serviço precisa da conexão declarada (não conectada) para carregar.
require_once BASE_PATH . '/app/config/conexao.php';
require_once BASE_PATH . '/app/services/senha.php';

checar('padrão de 60 minutos', senha_validade_minutos(), 60);
putenv('SENHA_RESET_MINUTOS=15');
checar('respeita o .env', senha_validade_minutos(), 15);
putenv('SENHA_RESET_MINUTOS=0');
checar('zero cai no padrão (link eterno seria falha de segurança)', senha_validade_minutos(), 60);
putenv('SENHA_RESET_MINUTOS=-5');
checar('negativo cai no padrão', senha_validade_minutos(), 60);
putenv('SENHA_RESET_MINUTOS=');

titulo('3. Força da senha');

checar('7 caracteres recusada', count(senha_validar_forca('1234567')), 1);
checar('8 caracteres aceita', senha_validar_forca('12345678'), []);
checar('vazia recusada', count(senha_validar_forca('')), 1);
checar('código do erro de curta', senha_validar_forca('abc')[0]['code'], 'SENHA_CURTA');
checar('201 caracteres recusada', senha_validar_forca(str_repeat('a', 201))[0]['code'], 'SENHA_LONGA');
checar('200 caracteres aceita', senha_validar_forca(str_repeat('a', 200)), []);
checar('acentuada de 8 letras aceita', senha_validar_forca('imunizaç'), []);

titulo('4. Formato do token recusado antes do banco');

// senha_pedido_valido devolve null sem consultar o banco quando o formato é
// impossível. É o que protege a rota pública de servir de sonda.
checar('vazio', senha_pedido_valido(''), null);
checar('curto demais', senha_pedido_valido('abc123'), null);
checar('com maiúscula (hex é minúsculo)', senha_pedido_valido(str_repeat('A', 64)), null);
checar('63 caracteres', senha_pedido_valido(str_repeat('a', 63)), null);
checar('65 caracteres', senha_pedido_valido(str_repeat('a', 65)), null);
checar('tentativa de SQL injection', senha_pedido_valido("' OR '1'='1"), null);
checar('caractere não-hex', senha_pedido_valido(str_repeat('a', 63) . 'z'), null);

titulo('5. Chaves de rate limit cabem na coluna');

// A coluna rate_limite.chave e VARCHAR(80). Chave maior faz o INSERT falhar, e
// como rate_limit_ou_429 e fail-open o limite simplesmente NAO VALE, em silencio.
// Foi exatamente o que aconteceu com 'senha_esqueci_email:<sha256>' (85 chars).
require_once BASE_PATH . '/app/helpers/rate_limit.php';

$emailLongo = str_repeat('a', 180) . '@' . str_repeat('b', 60) . '.com.br';
checar('chave de e-mail cabe em 80', strlen(senha_chave_rate_email('rodrigo@saimunizacoes.com.br')) <= 80, true);
checar('chave de e-mail LONGO cabe em 80', strlen(senha_chave_rate_email($emailLongo)) <= 80, true);
checar('chave de IPv4 cabe em 80', strlen(senha_chave_rate_ip('esqueci_ip', '203.0.113.42')) <= 80, true);
checar('chave de IPv6 cabe em 80', strlen(senha_chave_rate_ip('redefinir_ip', '2001:0db8:85a3:0000:0000:8a2e:0370:7334')) <= 80, true);
checar('IP ausente não quebra', strlen(senha_chave_rate_ip('validar_ip', null)) <= 80, true);

checar('e-mails diferentes -> chaves diferentes',
    senha_chave_rate_email('a@x.com') !== senha_chave_rate_email('b@x.com'), true);
checar('maiúsculas não criam chave nova (senão o limite seria burlável)',
    senha_chave_rate_email('Rodrigo@X.com'), senha_chave_rate_email('rodrigo@x.com'));
checar('e-mail não aparece em claro na chave',
    strpos(senha_chave_rate_email('rodrigo@saimunizacoes.com.br'), 'rodrigo') === false, true);

// A rede de seguranca do proprio helper, para qualquer chamador futuro.
checar('helper encurta chave gigante', strlen(rate_limit_chave(str_repeat('z', 500))), 80);
checar('helper não mexe em chave curta', rate_limit_chave('login:1.2.3.4'), 'login:1.2.3.4');
checar('chaves longas distintas não colidem',
    rate_limit_chave(str_repeat('z', 200) . 'A') !== rate_limit_chave(str_repeat('z', 200) . 'B'), true);

titulo('6. Link de redefinição');

putenv('APP_URL=https://imu.saimunizacoes.com.br');
$t = str_repeat('a', 64);
checar('monta a URL da tela', senha_link($t), 'https://imu.saimunizacoes.com.br/redefinir-senha.html?token=' . $t);
putenv('APP_URL=https://imu.saimunizacoes.com.br/');
checar('barra sobrando no APP_URL não duplica', senha_link($t), 'https://imu.saimunizacoes.com.br/redefinir-senha.html?token=' . $t);

// ---------------------------------------------------------------------------
// Parte 2: ciclo completo contra o banco (só com --banco).
// ---------------------------------------------------------------------------
if (!$comBanco) {
    echo "\n(ciclo com banco pulado — rode com --banco dentro do container)\n";
} else {
    titulo('7. Ciclo completo contra o MySQL');

    $email = 'teste-senha-' . bin2hex(random_bytes(4)) . '@teste.invalido';
    db_executar(
        "INSERT INTO usuario (tenant_id, perfil, nome, email, senha_hash, status)
         VALUES (NULL, 'operador_interno', 'Usuario De Teste', :e, :h, 'ativo')",
        [':e' => $email, ':h' => password_hash('senha-antiga-123', PASSWORD_DEFAULT)]
    );
    $uid = (int) db_ultimo_id();
    echo "  (usuário de teste id={$uid})\n";

    try {
        $token = senha_criar_token($uid, 'portal');
        checar('token tem 64 hex', (bool) preg_match('/^[a-f0-9]{64}$/', $token), true);

        $pedido = senha_pedido_valido($token);
        checar('token recém-criado é válido', $pedido !== null, true);
        checar('pedido aponta para o usuário certo', (int) $pedido['usuario_id'], $uid);

        // O que está gravado é o hash, não o token.
        $linha = db_primeiro("SELECT token_hash FROM senha_redefinicao WHERE usuario_id = :u ORDER BY id DESC LIMIT 1", [':u' => $uid]);
        checar('banco guarda o sha256, não o token', $linha['token_hash'], hash('sha256', $token));
        checar('token puro não está no banco', $linha['token_hash'] === $token, false);

        // Pedir de novo invalida o anterior.
        $token2 = senha_criar_token($uid, 'portal');
        checar('pedido novo invalida o anterior', senha_pedido_valido($token) === null, true);
        checar('o novo vale', senha_pedido_valido($token2) !== null, true);

        // Aplicar troca a senha e queima o token.
        $pedido2 = senha_pedido_valido($token2);
        senha_aplicar($pedido2, 'senha-nova-456');

        $u = db_primeiro("SELECT senha_hash FROM usuario WHERE id = :u", [':u' => $uid]);
        checar('senha nova confere', password_verify('senha-nova-456', $u['senha_hash']), true);
        checar('senha antiga não vale mais', password_verify('senha-antiga-123', $u['senha_hash']), false);
        checar('token usado não serve de novo', senha_pedido_valido($token2) === null, true);

        // Usuário inativo não pode redefinir.
        $token3 = senha_criar_token($uid, 'portal');
        db_executar("UPDATE usuario SET status = 'bloqueado' WHERE id = :u", [':u' => $uid]);
        checar('usuário bloqueado invalida o link', senha_pedido_valido($token3) === null, true);
        db_executar("UPDATE usuario SET status = 'ativo' WHERE id = :u", [':u' => $uid]);
        checar('reativado, o link volta a valer', senha_pedido_valido($token3) !== null, true);

        // Usuário excluído (soft delete) não pode redefinir.
        db_executar("UPDATE usuario SET excluido_em = NOW() WHERE id = :u", [':u' => $uid]);
        checar('usuário excluído invalida o link', senha_pedido_valido($token3) === null, true);
        db_executar("UPDATE usuario SET excluido_em = NULL WHERE id = :u", [':u' => $uid]);

        // Token expirado.
        $token4 = senha_criar_token($uid, 'portal');
        db_executar(
            "UPDATE senha_redefinicao SET expira_em = DATE_SUB(NOW(), INTERVAL 1 MINUTE)
              WHERE usuario_id = :u AND usado_em IS NULL AND invalidado_em IS NULL",
            [':u' => $uid]
        );
        checar('token expirado não vale', senha_pedido_valido($token4) === null, true);

        // Token de outro usuário não vaza.
        checar('token inventado não casa', senha_pedido_valido(bin2hex(random_bytes(32))) === null, true);

    } finally {
        // ON DELETE CASCADE leva os tokens junto.
        db_executar("DELETE FROM usuario WHERE id = :u", [':u' => $uid]);
        echo "  (usuário de teste removido)\n";
    }
}

echo "\n----------------------------------------\n";
echo "{$ok} passaram, {$falhou} falharam\n";
exit($falhou > 0 ? 1 : 0);
