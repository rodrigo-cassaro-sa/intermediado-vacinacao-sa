<?php
// ============================================================================
// app/middlewares/auth_sessao.php
// Função: autenticação de HUMANOS por sessão + CSRF (grupo /api/v1/interno).
// Base: docs/10 §2. Cookie HttpOnly/Secure/SameSite; expiração por inatividade.
// ============================================================================

/** Inicia a sessão com cookie seguro. Chamar cedo na requisição interna. */
function iniciar_sessao(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => APP_ENV === 'producao',
        'samesite' => 'Lax',
    ]);
    session_name('imz_sessao');
    session_start();
}

/** Cria a sessão do usuário autenticado (após validar credenciais). */
function login_sessao(array $usuario): void
{
    iniciar_sessao();
    session_regenerate_id(true); // evita fixation
    $_SESSION['usuario'] = [
        'id'        => (int) $usuario['id'],
        'perfil'    => $usuario['perfil'],
        'nome'      => $usuario['nome'],
        'tenant_id' => isset($usuario['tenant_id']) ? (int) $usuario['tenant_id'] : null,
    ];
    $_SESSION['criada_em']    = time();
    $_SESSION['ultimo_uso']   = time();
    $_SESSION['csrf_token']   = bin2hex(random_bytes(32));
}

/** Encerra a sessão. */
function logout_sessao(): void
{
    iniciar_sessao();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Retorna o usuário logado ou null, respeitando expiração. */
function usuario_sessao(): ?array
{
    iniciar_sessao();
    if (empty($_SESSION['usuario'])) {
        return null;
    }
    $inatividade = (int) env('SESSAO_INATIVIDADE', 1800);
    $absoluta    = (int) env('SESSAO_ABSOLUTA', 28800);
    $agora = time();
    if (($agora - ($_SESSION['ultimo_uso'] ?? 0)) > $inatividade
        || ($agora - ($_SESSION['criada_em'] ?? 0)) > $absoluta) {
        logout_sessao();
        return null;
    }
    $_SESSION['ultimo_uso'] = $agora;
    return $_SESSION['usuario'];
}

/** Exige usuário logado; responde 401 se não houver. Devolve o usuário. */
function exigir_login(): array
{
    $usuario = usuario_sessao();
    if ($usuario === null) {
        responder_erro('Autenticação necessária.', 401, [
            ['field' => null, 'code' => 'NAO_AUTENTICADO', 'message' => 'Faça login para continuar.'],
        ]);
    }
    return $usuario;
}

/** Exige que o perfil esteja na lista; responde 403 caso contrário. */
function exigir_perfil(array $usuario, array $perfis): void
{
    if (!in_array($usuario['perfil'], $perfis, true)) {
        registrar_auditoria('permissao.negada', [
            'tenant_id' => $usuario['tenant_id'],
            'ator_tipo' => 'usuario',
            'ator_id'   => $usuario['id'],
            'origem'    => 'admin',
            'metadata'  => ['perfil' => $usuario['perfil'], 'perfis_exigidos' => $perfis],
        ]);
        responder_erro('Sem permissão para esta ação.', 403, [
            ['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Seu perfil não permite esta ação.'],
        ]);
    }
}

/** Valida o token CSRF em mutações. Header X-CSRF-Token. */
function exigir_csrf(): void
{
    iniciar_sessao();
    $enviado = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $esperado = $_SESSION['csrf_token'] ?? '';
    if ($esperado === '' || !hash_equals($esperado, (string) $enviado)) {
        responder_erro('Requisição inválida (CSRF).', 403, [
            ['field' => null, 'code' => 'CSRF_INVALIDO', 'message' => 'Token de segurança ausente ou inválido.'],
        ]);
    }
}
