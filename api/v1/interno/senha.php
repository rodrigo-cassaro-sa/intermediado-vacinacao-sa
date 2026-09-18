<?php
// ============================================================================
// api/v1/interno/senha.php
// Função: endpoints públicos (sem login) do fluxo "esqueci minha senha".
// Base: docs/09 §3.10 e docs/10 §3.
//
// Três rotas, todas anônimas por natureza — quem esqueceu a senha não consegue
// autenticar:
//   POST /interno/auth/senha/esqueci    {email}          -> manda o link
//   GET  /interno/auth/senha/validar?token=...           -> o link ainda vale?
//   POST /interno/auth/senha/redefinir  {token, senha}   -> grava a senha nova
//
// REGRA QUE ATRAVESSA O ARQUIVO: nenhuma resposta pode revelar se um e-mail
// existe na base. Um endpoint que responde "e-mail não cadastrado" entrega a
// lista de clientes da empresa para quem quiser sondar. Por isso /esqueci
// responde exatamente a mesma coisa nos dois casos, inclusive quando o envio
// falha. O que aconteceu de verdade fica na auditoria e no log do container.
// ============================================================================

/**
 * POST /api/v1/interno/auth/senha/esqueci
 * Corpo: { email, origem? }
 */
function rota_senha_esqueci(array $params): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';

    // Dois limites: um contra varredura de e-mails a partir de um IP, outro para
    // não transformar a caixa de entrada de alguém em alvo de flood.
    rate_limit_ou_429(senha_chave_rate_ip('esqueci_ip', $ip), (int) env('RATE_LIMIT_SENHA_IP', 10));

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['email']);
    if ($erros) {
        erro_validacao($erros);
    }

    $email  = strtolower(trim((string) $dados['email']));
    $origem = ($dados['origem'] ?? 'portal') === 'admin' ? 'admin' : 'portal';

    if (!validar_email($email)) {
        erro_validacao([
            ['field' => 'email', 'code' => 'EMAIL_INVALIDO', 'message' => 'Informe um e-mail válido.'],
        ]);
    }

    rate_limit_ou_429(senha_chave_rate_email($email), (int) env('RATE_LIMIT_SENHA_EMAIL', 3));

    // Resposta única, montada uma vez e usada em todos os caminhos.
    $responderSempreIgual = function () {
        responder_sucesso(
            ['validade_minutos' => senha_validade_minutos()],
            'Se houver uma conta com esse e-mail, o link de redefinição foi enviado.'
        );
    };

    $usuario = db_primeiro(
        "SELECT id, nome, email, status, tenant_id, perfil
           FROM usuario
          WHERE email = :email AND excluido_em IS NULL
          LIMIT 1",
        [':email' => $email]
    );

    if ($usuario === null || $usuario['status'] !== 'ativo') {
        registrar_auditoria('senha.reset_solicitado_invalido', [
            'ator_tipo' => 'usuario',
            'origem'    => $origem,
            'metadata'  => ['email' => $email, 'motivo' => $usuario === null ? 'inexistente' : 'inativo'],
        ]);
        $responderSempreIgual();
    }

    $token = senha_criar_token((int) $usuario['id'], $origem);
    [$enviado, $detalhe] = senha_enviar_email($usuario, $token);

    // O token NÃO entra na auditoria: quem lê a trilha não pode trocar a senha
    // de ninguém. O helper já mascara a chave 'token', mas aqui nem chega.
    registrar_auditoria('senha.reset_solicitado', [
        'tenant_id'     => $usuario['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => $origem,
        'entidade_tipo' => 'usuario',
        'entidade_id'   => (int) $usuario['id'],
        'metadata'      => ['email_enviado' => $enviado, 'detalhe' => $detalhe],
    ]);

    if (!$enviado) {
        // Falha de envio é problema nosso, não do visitante — e contar para ele
        // que "o e-mail existe mas não conseguimos enviar" já seria vazamento.
        error_log("[senha] link gerado para usuario {$usuario['id']} mas o envio falhou ({$detalhe})");
        if (!email_configurado()) {
            // Sem provedor configurado: o link vai para o log do container, para
            // dar para testar o fluxo em homologação antes de contratar o envio.
            error_log('[senha] EMAIL NAO CONFIGURADO — link de teste: ' . senha_link($token));
        }
    }

    $responderSempreIgual();
}

/**
 * GET /api/v1/interno/auth/senha/validar?token=...
 * Serve para a tela dizer "link expirado" ANTES de a pessoa digitar a senha nova.
 * Não devolve e-mail nem id — só o primeiro nome, para a tela cumprimentar.
 */
function rota_senha_validar(array $params): void
{
    rate_limit_ou_429(senha_chave_rate_ip('validar_ip', $_SERVER['REMOTE_ADDR'] ?? null),
        (int) env('RATE_LIMIT_SENHA_IP', 10) * 3);

    $pedido = senha_pedido_valido((string) ($_GET['token'] ?? ''));

    if ($pedido === null) {
        responder_erro('Link inválido ou expirado.', 400, [
            ['field' => 'token', 'code' => 'TOKEN_INVALIDO',
             'message' => 'Peça um link novo na tela de acesso.'],
        ]);
    }

    $primeiroNome = trim(explode(' ', trim((string) $pedido['nome']))[0]);

    responder_sucesso([
        'valido'           => true,
        'nome'             => $primeiroNome,
        'origem'           => $pedido['origem'],
        'expira_em'        => $pedido['expira_em'],
    ], 'Link válido.');
}

/**
 * POST /api/v1/interno/auth/senha/redefinir
 * Corpo: { token, senha }
 */
function rota_senha_redefinir(array $params): void
{
    rate_limit_ou_429(senha_chave_rate_ip('redefinir_ip', $_SERVER['REMOTE_ADDR'] ?? null),
        (int) env('RATE_LIMIT_SENHA_IP', 10));

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['token', 'senha']);
    if ($erros) {
        erro_validacao($erros);
    }

    $senha = (string) $dados['senha'];
    $erros = senha_validar_forca($senha);
    if ($erros) {
        erro_validacao($erros);
    }

    $pedido = senha_pedido_valido((string) $dados['token']);
    if ($pedido === null) {
        registrar_auditoria('senha.reset_token_recusado', [
            'ator_tipo' => 'usuario',
            'origem'    => 'portal',
        ]);
        responder_erro('Link inválido ou expirado.', 400, [
            ['field' => 'token', 'code' => 'TOKEN_INVALIDO',
             'message' => 'Peça um link novo na tela de acesso.'],
        ]);
    }

    senha_aplicar($pedido, $senha);

    registrar_auditoria('senha.redefinida', [
        'tenant_id'     => $pedido['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $pedido['usuario_id'],
        'origem'        => $pedido['origem'],
        'entidade_tipo' => 'usuario',
        'entidade_id'   => (int) $pedido['usuario_id'],
    ]);

    responder_sucesso([
        'destino' => $pedido['origem'] === 'admin' ? '/admin/' : '/portal/',
    ], 'Senha redefinida. Já pode entrar com a senha nova.');
}
