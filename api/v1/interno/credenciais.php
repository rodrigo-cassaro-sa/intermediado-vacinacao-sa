<?php
// ============================================================================
// api/v1/interno/credenciais.php
// Função: emitir/listar/revogar credenciais de API (token de máquina) com escopo
// por campanha (RN-009). Grupo interno (sessão + CSRF). Base: docs/09, docs/10.
// O token cru só aparece UMA vez, na emissão; guardamos apenas o hash.
// ============================================================================

// Escopo por campanha: ingestao_b2b, rede_credenciada, app_in_company.
// Escopo por tenant (cliente): consulta.
const CREDENCIAL_TIPOS = ['ingestao_b2b', 'rede_credenciada', 'consulta', 'app_in_company'];

/** POST /api/v1/interno/credenciais — emite um token de máquina. */
function rota_emitir_credencial(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();
    $ehInterno = usuario_eh_interno($usuario);

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['tipo']);
    if (isset($dados['tipo']) && !in_array($dados['tipo'], CREDENCIAL_TIPOS, true)) {
        $erros[] = ['field' => 'tipo', 'code' => 'TIPO_INVALIDO', 'message' => 'Use ingestao_b2b, app_in_company, consulta ou rede_credenciada.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }

    $tipo = $dados['tipo'];
    // Portal (não interno) não emite token de rede credenciada (relação interna com clínica).
    if (!$ehInterno && $tipo === 'rede_credenciada') {
        responder_erro('Tipo não permitido no portal.', 403, [
            ['field' => 'tipo', 'code' => 'TIPO_NAO_PERMITIDO', 'message' => 'Token de rede credenciada é emitido pela gestão interna.'],
        ]);
    }
    $tenantId = null;
    $escopoCampanha = null;

    if ($tipo === 'consulta') {
        // Token de leitura do CLIENTE (RH/sistema de carteira): escopo = tenant, sem campanha.
        if (empty($dados['cliente_b2b_id']) || !is_numeric($dados['cliente_b2b_id'])) {
            responder_erro('Informe o cliente.', 422, [
                ['field' => 'cliente_b2b_id', 'code' => 'CLIENTE_OBRIGATORIO', 'message' => 'cliente_b2b_id é obrigatório para consulta.'],
            ]);
        }
        $cliente = db_primeiro("SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL AND status = 'ativo' LIMIT 1",
            [':id' => (int) $dados['cliente_b2b_id']]);
        if ($cliente === null) {
            responder_erro('Cliente inexistente ou inativo.', 422, [
                ['field' => 'cliente_b2b_id', 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente inválido.'],
            ]);
        }
        if (!$ehInterno && !usuario_pode_cliente($usuario, (int) $cliente['id'], true)) {
            responder_erro('Cliente fora do seu escopo.', 403, [['field' => 'cliente_b2b_id', 'code' => 'FORA_DO_ESCOPO', 'message' => 'Acesso negado.']]);
        }
        $titularTipo = 'cliente_b2b';
        $titularId   = (int) $cliente['id'];
        $tenantId    = (int) $cliente['id'];
    } else {
        // ingestao_b2b / rede_credenciada: escopo por campanha.
        if (empty($dados['campanha_id']) || !is_numeric($dados['campanha_id'])) {
            responder_erro('Informe a campanha.', 422, [
                ['field' => 'campanha_id', 'code' => 'CAMPANHA_OBRIGATORIA', 'message' => 'campanha_id é obrigatório para este tipo.'],
            ]);
        }
        $campanha = db_primeiro("SELECT id, tenant_id FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1",
            [':id' => (int) $dados['campanha_id']]);
        if ($campanha === null) {
            responder_erro('Campanha inexistente.', 404, [
                ['field' => 'campanha_id', 'code' => 'CAMPANHA_NAO_ENCONTRADA', 'message' => 'Campanha não encontrada.'],
            ]);
        }
        $escopoCampanha = (int) $campanha['id'];
        $tenantId = (int) $campanha['tenant_id'];
        if (!$ehInterno && !usuario_pode_cliente($usuario, $tenantId, true)) {
            responder_erro('Campanha fora do seu escopo.', 403, [['field' => 'campanha_id', 'code' => 'FORA_DO_ESCOPO', 'message' => 'Acesso negado.']]);
        }

        if (in_array($tipo, ['ingestao_b2b', 'app_in_company'], true)) {
            // Titular é o cliente B2B dono da campanha (app in company: PWA/app/terceiro).
            $titularTipo = 'cliente_b2b';
            $titularId   = (int) $campanha['tenant_id'];
        } else {
            if (empty($dados['clinica_id']) || !is_numeric($dados['clinica_id'])) {
                responder_erro('Informe a clínica.', 422, [
                    ['field' => 'clinica_id', 'code' => 'CLINICA_OBRIGATORIA', 'message' => 'clinica_id é obrigatório para rede_credenciada.'],
                ]);
            }
            $clinica = db_primeiro("SELECT id FROM clinica_credenciada WHERE id = :id AND excluido_em IS NULL AND status = 'ativa' LIMIT 1",
                [':id' => (int) $dados['clinica_id']]);
            if ($clinica === null) {
                responder_erro('Clínica inexistente ou inativa.', 422, [
                    ['field' => 'clinica_id', 'code' => 'CLINICA_NAO_ENCONTRADA', 'message' => 'Clínica inválida.'],
                ]);
            }
            $titularTipo = 'clinica_credenciada';
            $titularId   = (int) $clinica['id'];
        }
    }

    [$tokenCru, $tokenHash] = gerar_token_credencial();

    db_executar(
        "INSERT INTO credencial_api (tipo, titular_tipo, titular_id, token_hash, escopo_campanha_id, ativo)
         VALUES (:tipo, :ttipo, :tid, :hash, :escopo, 1)",
        [':tipo' => $tipo, ':ttipo' => $titularTipo, ':tid' => $titularId, ':hash' => $tokenHash, ':escopo' => $escopoCampanha]
    );
    $credId = (int) db_ultimo_id();

    registrar_auditoria('credencial.emitida', [
        'tenant_id'     => $tenantId,
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'credencial_api',
        'entidade_id'   => $credId,
        'metadata'      => ['tipo' => $tipo, 'escopo_campanha_id' => $escopoCampanha, 'titular_id' => $titularId],
    ]);

    responder_sucesso([
        'credencial_id'      => $credId,
        'tipo'               => $tipo,
        'escopo_campanha_id' => $escopoCampanha,
        'token'              => $tokenCru,
        'aviso'              => 'Guarde este token; ele não será exibido novamente.',
    ], 'Credencial emitida.', 201);
}

/** GET /api/v1/interno/credenciais — lista credenciais (sem o token), no escopo do usuário. */
function rota_listar_credenciais(array $params): void
{
    $usuario = exigir_login();
    $base = "SELECT id, tipo, titular_tipo, titular_id, escopo_campanha_id, ativo, criado_em, revogado_em FROM credencial_api";
    if (usuario_eh_interno($usuario)) {
        $itens = db_todos("$base ORDER BY id DESC");
    } else {
        $managed = clientes_geridos_pelo_usuario($usuario);
        if (!$managed) {
            responder_sucesso(['itens' => []], 'OK.');
        }
        $ph = []; $bind = [];
        foreach ($managed as $i => $c) { $ph[] = ":c_$i"; $bind[":c_$i"] = (int) $c; }
        // Portal vê credenciais cujo titular é um cliente que ele gere.
        $itens = db_todos("$base WHERE titular_tipo = 'cliente_b2b' AND titular_id IN (" . implode(',', $ph) . ") ORDER BY id DESC", $bind);
    }
    responder_sucesso(['itens' => $itens], 'OK.');
}

/** POST /api/v1/interno/credenciais/{id}/revogar — revoga um token. */
function rota_revogar_credencial(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $cred = db_primeiro("SELECT id, titular_tipo, titular_id FROM credencial_api WHERE id = :id LIMIT 1", [':id' => $id]);
    if ($cred === null) {
        responder_erro('Credencial inexistente.', 404, [
            ['field' => null, 'code' => 'CREDENCIAL_NAO_ENCONTRADA', 'message' => 'Não encontrada.'],
        ]);
    }
    if (!usuario_eh_interno($usuario)) {
        if ($cred['titular_tipo'] !== 'cliente_b2b' || !usuario_pode_cliente($usuario, (int) $cred['titular_id'], true)) {
            responder_erro('Sem acesso a esta credencial.', 403, [['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Acesso negado.']]);
        }
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
