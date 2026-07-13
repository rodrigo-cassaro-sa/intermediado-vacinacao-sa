<?php
// ============================================================================
// api/v1/interno/clientes.php
// Função: cadastro/listagem de clientes B2B (tenants). Grupo interno (sessão+CSRF).
// Base: docs/09, docs/08. Somente perfis internos gerenciam clientes.
// ============================================================================

/** POST /api/v1/interno/clientes — cria cliente B2B (tenant). */
function rota_criar_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['razao_social', 'cnpj']);

    $cnpj = so_digitos($dados['cnpj'] ?? '');
    if ($cnpj !== '' && strlen($cnpj) !== 14) {
        $erros[] = ['field' => 'cnpj', 'code' => 'CNPJ_INVALIDO', 'message' => 'CNPJ deve ter 14 dígitos.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }

    // Evita duplicidade de CNPJ.
    $existe = db_primeiro("SELECT id FROM cliente_b2b WHERE cnpj = :cnpj LIMIT 1", [':cnpj' => $cnpj]);
    if ($existe !== null) {
        responder_erro('CNPJ já cadastrado.', 409, [
            ['field' => 'cnpj', 'code' => 'CNPJ_DUPLICADO', 'message' => 'Já existe cliente com este CNPJ.'],
        ]);
    }

    db_executar(
        "INSERT INTO cliente_b2b (razao_social, cnpj, status) VALUES (:razao, :cnpj, 'ativo')",
        [':razao' => trim($dados['razao_social']), ':cnpj' => $cnpj]
    );
    $id = (int) db_ultimo_id();

    registrar_auditoria('cliente.criado', [
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'cliente_b2b',
        'entidade_id'   => $id,
        'metadata'      => ['cnpj' => $cnpj],
    ]);

    responder_sucesso(['cliente_b2b_id' => $id], 'Cliente B2B criado.', 201);
}

/** GET /api/v1/interno/clientes — lista clientes B2B do escopo do usuário. */
function rota_listar_clientes(array $params): void
{
    $usuario = exigir_login();

    $acessiveis = clientes_acessiveis_pelo_usuario($usuario);
    $where = 'excluido_em IS NULL';
    $bind = [];
    if ($acessiveis !== ['*']) {
        if (!$acessiveis) {
            responder_sucesso(['itens' => []], 'OK.');
        }
        $ph = [];
        foreach ($acessiveis as $i => $cid) { $ph[] = ":c_$i"; $bind[":c_$i"] = (int) $cid; }
        $where .= ' AND id IN (' . implode(',', $ph) . ')';
    }

    $itens = db_todos(
        "SELECT id, razao_social, cnpj, status, criado_em FROM cliente_b2b WHERE $where ORDER BY razao_social",
        $bind
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}
