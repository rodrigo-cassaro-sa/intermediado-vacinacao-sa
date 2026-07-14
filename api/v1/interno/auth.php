<?php
// ============================================================================
// api/v1/interno/auth.php
// Função: login, logout e "quem sou eu" (grupo interno, sessão). Base: docs/09/10.
// Valida credenciais no backend; senha via password_verify; audita login.
// ============================================================================

function rota_login(array $params): void
{
    // Anti-brute force: limita tentativas de login por IP.
    rate_limit_ou_429('login:' . ($_SERVER['REMOTE_ADDR'] ?? 'desconhecido'), (int) env('RATE_LIMIT_LOGIN', 20));

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['email', 'senha']);
    if ($erros) {
        erro_validacao($erros);
    }

    $email = trim((string) $dados['email']);
    $senha = (string) $dados['senha'];

    $usuario = db_primeiro(
        "SELECT id, tenant_id, perfil, nome, email, senha_hash, status
           FROM usuario
          WHERE email = :email AND excluido_em IS NULL
          LIMIT 1",
        [':email' => $email]
    );

    // Mensagem genérica para não revelar existência do e-mail.
    $credenciaisInvalidas = function () use ($email) {
        registrar_auditoria('login.falha', [
            'ator_tipo' => 'usuario',
            'origem'    => 'admin',
            'metadata'  => ['email' => $email],
        ]);
        responder_erro('Credenciais inválidas.', 401, [
            ['field' => null, 'code' => 'CREDENCIAIS_INVALIDAS', 'message' => 'E-mail ou senha incorretos.'],
        ]);
    };

    if ($usuario === null || $usuario['status'] !== 'ativo') {
        $credenciaisInvalidas();
    }
    if (!password_verify($senha, $usuario['senha_hash'])) {
        $credenciaisInvalidas();
    }

    // Atualiza último acesso e cria a sessão.
    db_executar("UPDATE usuario SET ultimo_acesso_em = NOW() WHERE id = :id", [':id' => $usuario['id']]);
    login_sessao($usuario);

    registrar_auditoria('login.sucesso', [
        'tenant_id' => $usuario['tenant_id'],
        'ator_tipo' => 'usuario',
        'ator_id'   => (int) $usuario['id'],
        'origem'    => 'admin',
    ]);

    responder_sucesso([
        'usuario' => [
            'id'     => (int) $usuario['id'],
            'nome'   => $usuario['nome'],
            'perfil' => $usuario['perfil'],
        ],
        'csrf_token' => $_SESSION['csrf_token'],
    ], 'Login realizado.');
}

function rota_logout(array $params): void
{
    $usuario = usuario_sessao();
    if ($usuario) {
        registrar_auditoria('logout', [
            'tenant_id' => $usuario['tenant_id'],
            'ator_tipo' => 'usuario',
            'ator_id'   => $usuario['id'],
            'origem'    => 'admin',
        ]);
    }
    logout_sessao();
    responder_sucesso([], 'Sessão encerrada.');
}

function rota_eu(array $params): void
{
    $usuario = exigir_login();
    // Devolve também o token CSRF da sessão para páginas que abriram com sessão
    // já ativa (sem passar pelo login) poderem fazer mutações. Same-origin: um
    // site externo não consegue ler esta resposta.
    responder_sucesso([
        'usuario'    => $usuario,
        'csrf_token' => $_SESSION['csrf_token'] ?? null,
    ], 'OK.');
}
