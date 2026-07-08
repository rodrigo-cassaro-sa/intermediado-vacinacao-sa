<?php
// ============================================================================
// api/v1/interno/precos.php
// Item 4: tabela de preços — cobrança do cliente (por modalidade/vacina) e
// pagamento à clínica (por vacina). Grupo interno (super_admin/operador).
// ============================================================================

const PRECO_MODALIDADES = ['in_company', 'rede_credenciada'];

/** POST /api/v1/interno/clientes/{id}/precos  {modalidade, vacina_id, valor} */
function rota_definir_preco_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $clienteId = (int) ($params['id'] ?? 0);
    $dados = corpo_json();
    $erros = exigir_campos($dados, ['modalidade', 'vacina_id', 'valor']);
    if (isset($dados['modalidade']) && !in_array($dados['modalidade'], PRECO_MODALIDADES, true)) {
        $erros[] = ['field' => 'modalidade', 'code' => 'MODALIDADE_INVALIDA', 'message' => 'Use in_company ou rede_credenciada.'];
    }
    if (isset($dados['valor']) && (!is_numeric($dados['valor']) || (float) $dados['valor'] < 0)) {
        $erros[] = ['field' => 'valor', 'code' => 'VALOR_INVALIDO', 'message' => 'Informe um valor válido.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }
    if (db_primeiro("SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $clienteId]) === null) {
        responder_erro('Cliente inexistente.', 404, [['field' => 'id', 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente não encontrado.']]);
    }
    if (db_primeiro("SELECT id FROM vacina WHERE id = :id LIMIT 1", [':id' => (int) $dados['vacina_id']]) === null) {
        responder_erro('Vacina inexistente.', 422, [['field' => 'vacina_id', 'code' => 'VACINA_INVALIDA', 'message' => 'Vacina inválida.']]);
    }

    db_executar(
        "INSERT INTO preco_cliente (cliente_b2b_id, modalidade, vacina_id, valor)
         VALUES (:c, :m, :v, :valor)
         ON DUPLICATE KEY UPDATE valor = VALUES(valor)",
        [':c' => $clienteId, ':m' => $dados['modalidade'], ':v' => (int) $dados['vacina_id'], ':valor' => (float) $dados['valor']]
    );
    registrar_auditoria('preco_cliente.definido', [
        'tenant_id' => $clienteId, 'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'],
        'origem' => 'admin', 'entidade_tipo' => 'cliente_b2b', 'entidade_id' => $clienteId,
        'metadata' => ['modalidade' => $dados['modalidade'], 'vacina_id' => (int) $dados['vacina_id'], 'valor' => (float) $dados['valor']],
    ]);
    responder_sucesso([], 'Preço do cliente definido.', 201);
}

/** GET /api/v1/interno/clientes/{id}/precos */
function rota_listar_precos_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $clienteId = (int) ($params['id'] ?? 0);
    $itens = db_todos(
        "SELECT pc.modalidade, pc.vacina_id, v.nome AS vacina, pc.valor
           FROM preco_cliente pc JOIN vacina v ON v.id = pc.vacina_id
          WHERE pc.cliente_b2b_id = :c ORDER BY pc.modalidade, v.nome",
        [':c' => $clienteId]
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/** POST /api/v1/interno/clinicas/{id}/precos  {vacina_id, valor} */
function rota_definir_preco_clinica(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $clinicaId = (int) ($params['id'] ?? 0);
    $dados = corpo_json();
    $erros = exigir_campos($dados, ['vacina_id', 'valor']);
    if (isset($dados['valor']) && (!is_numeric($dados['valor']) || (float) $dados['valor'] < 0)) {
        $erros[] = ['field' => 'valor', 'code' => 'VALOR_INVALIDO', 'message' => 'Informe um valor válido.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }
    if (db_primeiro("SELECT id FROM clinica_credenciada WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $clinicaId]) === null) {
        responder_erro('Clínica inexistente.', 404, [['field' => 'id', 'code' => 'CLINICA_NAO_ENCONTRADA', 'message' => 'Clínica não encontrada.']]);
    }
    if (db_primeiro("SELECT id FROM vacina WHERE id = :id LIMIT 1", [':id' => (int) $dados['vacina_id']]) === null) {
        responder_erro('Vacina inexistente.', 422, [['field' => 'vacina_id', 'code' => 'VACINA_INVALIDA', 'message' => 'Vacina inválida.']]);
    }

    db_executar(
        "INSERT INTO preco_clinica (clinica_id, vacina_id, valor)
         VALUES (:c, :v, :valor) ON DUPLICATE KEY UPDATE valor = VALUES(valor)",
        [':c' => $clinicaId, ':v' => (int) $dados['vacina_id'], ':valor' => (float) $dados['valor']]
    );
    registrar_auditoria('preco_clinica.definido', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'clinica_credenciada', 'entidade_id' => $clinicaId,
        'metadata' => ['vacina_id' => (int) $dados['vacina_id'], 'valor' => (float) $dados['valor']],
    ]);
    responder_sucesso([], 'Preço da clínica definido.', 201);
}

/** GET /api/v1/interno/clinicas/{id}/precos */
function rota_listar_precos_clinica(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $clinicaId = (int) ($params['id'] ?? 0);
    $itens = db_todos(
        "SELECT pc.vacina_id, v.nome AS vacina, pc.valor
           FROM preco_clinica pc JOIN vacina v ON v.id = pc.vacina_id
          WHERE pc.clinica_id = :c ORDER BY v.nome",
        [':c' => $clinicaId]
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}
