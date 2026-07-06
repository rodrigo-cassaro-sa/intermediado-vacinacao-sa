<?php
// ============================================================================
// api/v1/interno/credenciais.php
// Função: emitir/listar/revogar credenciais de API (token de máquina) com escopo
// por campanha (RN-009). Grupo interno (sessão + CSRF). Base: docs/09, docs/10.
// O token cru só aparece UMA vez, na emissão; guardamos apenas o hash.
// ============================================================================

const CREDENCIAL_TIPOS = ['ingestao_b2b', 'rede_credenciada'];

/** POST /api/v1/interno/credenciais — emite um token com escopo de campanha. */
function rota_emitir_credencial(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['tipo', 'campanha_id']);
    if (isset($dados['tipo']) && !in_array($dados['tipo'], CREDENCIAL_TIPOS, true)) {
        $erros[] = ['field' => 'tipo', 'code' => 'TIPO_INVALIDO', 'message' => 'Use ingestao_b2b ou rede_credenciada.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }

    $campanha = db_primeiro(
        "SELECT id, tenant_id FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1",
        [':id' => (int) $dados['campanha_id']]
    );
    if ($campanha === null) {
        responder_erro('Campanha inexistente.', 404, [
            ['field' => 'campanha_id', 'code' => 'CAMPANHA_NAO_ENCONTRADA', 'message' => 'Campanha não encontrada.'],
        ]);
    }

    // Define o titular conforme o tipo.
    if ($dados['tipo'] === 'ingestao_b2b') {
        // O titular é o cliente B2B dono da campanha.
        $titularTipo = 'cliente_b2b';
        $titularId   = (int) $campanha['tenant_id'];
    } else {
        // rede_credenciada: exige uma clínica existente.
        if (empty($dados['clinica_id']) || !is_numeric($dados['clinica_id'])) {
            responder_erro('Informe a clínica.', 422, [
                ['field' => 'clinica_id', 'code' => 'CLINICA_OBRIGATORIA', 'message' => 'clinica_id é obrigatório para rede_credenciada.'],
            ]);
        }
        $clinica = db_primeiro(
            "SELECT id FROM clinica_credenciada WHERE id = :id AND excluido_em IS NULL AND status = 'ativa' LIMIT 1",
            [':id' => (int) $dados['clinica_id']]
        );
        if ($clinica === null) {
            responder_erro('Clínica inexistente ou inativa.', 422, [
                ['field' => 'clinica_id', 'code' => 'CLINICA_NAO_ENCONTRADA', 'message' => 'Clínica inválida.'],
            ]);
        }
        $titularTipo = 'clinica_credenciada';
        $titularId   = (int) $clinica['id'];
    }

    [$tokenCru, $tokenHash] = gerar_token_credencial();

    db_executar(
        "INSERT INTO credencial_api (tipo, titular_tipo, titular_id, token_hash, escopo_campanha_id, ativo)
         VALUES (:tipo, :ttipo, :tid, :hash, :escopo, 1)",
        [
            ':tipo'   => $dados['tipo'],
            ':ttipo'  => $titularTipo,
            ':tid'    => $titularId,
            ':hash'   => $tokenHash,
            ':escopo' => (int) $campanha['id'],
        ]
    );
    $credId = (int) db_ultimo_id();

    registrar_auditoria('credencial.emitida', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'credencial_api',
        'entidade_id'   => $credId,
        'metadata'      => ['tipo' => $dados['tipo'], 'escopo_campanha_id' => (int) $campanha['id']],
    ]);

    // O token cru só é retornado aqui — não é recuperável depois.
    responder_sucesso([
        'credencial_id'      => $credId,
        'tipo'               => $dados['tipo'],
        'escopo_campanha_id' => (int) $campanha['id'],
        'token'              => $tokenCru,
        'aviso'              => 'Guarde este token; ele não será exibido novamente.',
    ], 'Credencial emitida.', 201);
}

/** GET /api/v1/interno/credenciais — lista credenciais (sem o token). */
function rota_listar_credenciais(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);

    $itens = db_todos(
        "SELECT id, tipo, titular_tipo, titular_id, escopo_campanha_id, ativo, criado_em, revogado_em
           FROM credencial_api
          ORDER BY id DESC"
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/** POST /api/v1/interno/credenciais/{id}/revogar — revoga um token. */
function rota_revogar_credencial(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $cred = db_primeiro("SELECT id FROM credencial_api WHERE id = :id LIMIT 1", [':id' => $id]);
    if ($cred === null) {
        responder_erro('Credencial inexistente.', 404, [
            ['field' => null, 'code' => 'CREDENCIAL_NAO_ENCONTRADA', 'message' => 'Não encontrada.'],
        ]);
    }

    db_executar("UPDATE credencial_api SET ativo = 0, revogado_em = NOW() WHERE id = :id", [':id' => $id]);

    registrar_auditoria('credencial.revogada', [
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'credencial_api',
        'entidade_id'   => $id,
    ]);

    responder_sucesso(['credencial_id' => $id], 'Credencial revogada.');
}
