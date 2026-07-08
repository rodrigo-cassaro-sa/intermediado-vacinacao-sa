<?php
// ============================================================================
// api/v1/interno/webhooks.php
// Painel de integrações: criar/listar/desativar assinaturas de webhook, ver
// entregas e enviar um teste. Grupo interno (super_admin/operador). Fase A.
// ============================================================================

/** POST /api/v1/interno/webhooks  {evento, url, tenant_id?} */
function rota_criar_webhook(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['evento', 'url']);
    if (isset($dados['evento']) && !in_array($dados['evento'], WEBHOOK_EVENTOS, true)) {
        $erros[] = ['field' => 'evento', 'code' => 'EVENTO_INVALIDO', 'message' => 'Evento não suportado. Ver docs/11.'];
    }
    if (isset($dados['url']) && !filter_var($dados['url'], FILTER_VALIDATE_URL)) {
        $erros[] = ['field' => 'url', 'code' => 'URL_INVALIDA', 'message' => 'Informe uma URL válida (https).'];
    }
    if ($erros) {
        erro_validacao($erros);
    }

    $tenantId = isset($dados['tenant_id']) && is_numeric($dados['tenant_id']) ? (int) $dados['tenant_id'] : null;
    $segredo = bin2hex(random_bytes(24));

    db_executar(
        "INSERT INTO webhook_assinatura (tenant_id, evento, url, segredo, ativo)
         VALUES (:t, :e, :u, :s, 1)",
        [':t' => $tenantId, ':e' => $dados['evento'], ':u' => $dados['url'], ':s' => $segredo]
    );
    $id = (int) db_ultimo_id();

    registrar_auditoria('webhook.criado', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'webhook_assinatura', 'entidade_id' => $id,
        'metadata' => ['evento' => $dados['evento'], 'url' => $dados['url']],
    ]);

    // O segredo (HMAC) é mostrado uma vez; guarde-o para validar a assinatura.
    responder_sucesso([
        'webhook_id' => $id,
        'segredo'    => $segredo,
        'aviso'      => 'Guarde o segredo; ele assina os webhooks (header X-Assinatura = HMAC-SHA256 do corpo).',
    ], 'Webhook criado.', 201);
}

/** GET /api/v1/interno/webhooks — lista assinaturas (sem segredo). */
function rota_listar_webhooks(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $itens = db_todos(
        "SELECT id, tenant_id, evento, url, ativo, criado_em FROM webhook_assinatura ORDER BY id DESC"
    );
    responder_sucesso(['itens' => $itens, 'eventos_disponiveis' => WEBHOOK_EVENTOS], 'OK.');
}

/** POST /api/v1/interno/webhooks/{id}/desativar */
function rota_desativar_webhook(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();
    $id = (int) ($params['id'] ?? 0);
    if (db_primeiro("SELECT id FROM webhook_assinatura WHERE id = :id", [':id' => $id]) === null) {
        responder_erro('Webhook inexistente.', 404, [['field' => null, 'code' => 'WEBHOOK_NAO_ENCONTRADO', 'message' => 'Não encontrado.']]);
    }
    db_executar("UPDATE webhook_assinatura SET ativo = 0 WHERE id = :id", [':id' => $id]);
    registrar_auditoria('webhook.desativado', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'webhook_assinatura', 'entidade_id' => $id,
    ]);
    responder_sucesso(['webhook_id' => $id], 'Webhook desativado.');
}

/** GET /api/v1/interno/webhooks/{id}/entregas — log de entregas recentes. */
function rota_entregas_webhook(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $id = (int) ($params['id'] ?? 0);
    $itens = db_todos(
        "SELECT id, evento, status, tentativas, ultimo_status_code, proxima_tentativa_em, criado_em, entregue_em
           FROM webhook_entrega WHERE assinatura_id = :id ORDER BY id DESC LIMIT 50",
        [':id' => $id]
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/** POST /api/v1/interno/webhooks/{id}/testar — enfileira uma entrega de teste. */
function rota_testar_webhook(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();
    $id = (int) ($params['id'] ?? 0);
    $wh = db_primeiro("SELECT id, evento FROM webhook_assinatura WHERE id = :id AND ativo = 1", [':id' => $id]);
    if ($wh === null) {
        responder_erro('Webhook inexistente ou inativo.', 404, [['field' => null, 'code' => 'WEBHOOK_NAO_ENCONTRADO', 'message' => 'Não encontrado.']]);
    }
    $payload = json_encode([
        'evento' => 'webhook.teste', 'ocorrido_em' => date('c'),
        'dados' => ['mensagem' => 'Entrega de teste do painel de integrações.'],
    ], JSON_UNESCAPED_UNICODE);
    db_executar(
        "INSERT INTO webhook_entrega (assinatura_id, evento, payload, status, proxima_tentativa_em)
         VALUES (:a, 'webhook.teste', :p, 'pendente', NOW())",
        [':a' => $id, ':p' => $payload]
    );
    responder_sucesso(['webhook_id' => $id, 'entrega_id' => (int) db_ultimo_id()],
        'Teste enfileirado; será entregue pelo worker em instantes.', 201);
}
