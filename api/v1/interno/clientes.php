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
    // Sigla (opcional na criação, mas necessária para gerar o código da campanha).
    $sigla = null;
    if (isset($dados['sigla']) && trim((string) $dados['sigla']) !== '') {
        $sigla = normalizar_sigla($dados['sigla']);
        if ($sigla === null) {
            $erros[] = ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'];
        }
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
    if ($sigla !== null) {
        $dupSigla = db_primeiro("SELECT id FROM cliente_b2b WHERE sigla = :s LIMIT 1", [':s' => $sigla]);
        if ($dupSigla !== null) {
            responder_erro('Sigla já usada por outro cliente.', 409, [
                ['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.'],
            ]);
        }
    }

    db_executar(
        "INSERT INTO cliente_b2b (razao_social, sigla, cnpj, status) VALUES (:razao, :sigla, :cnpj, 'ativo')",
        [':razao' => trim($dados['razao_social']), ':sigla' => $sigla, ':cnpj' => $cnpj]
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
    $where = 'c.excluido_em IS NULL';
    $bind = [];
    if ($acessiveis !== ['*']) {
        if (!$acessiveis) {
            responder_sucesso(['itens' => []], 'OK.');
        }
        $ph = [];
        foreach ($acessiveis as $i => $cid) { $ph[] = ":c_$i"; $bind[":c_$i"] = (int) $cid; }
        $where .= ' AND c.id IN (' . implode(',', $ph) . ')';
    }

    $itens = db_todos(
        "SELECT c.id, c.razao_social, c.sigla, c.cnpj, c.status, c.criado_em,
                c.grupo_empresarial_id, g.sigla AS grupo_sigla, g.nome AS grupo_nome
           FROM cliente_b2b c
           LEFT JOIN grupo_empresarial g ON g.id = c.grupo_empresarial_id
          WHERE $where
          ORDER BY c.razao_social",
        $bind
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/** POST /api/v1/interno/clientes/{id}/sigla — define/atualiza a sigla do cliente. */
function rota_definir_sigla_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $sigla = normalizar_sigla(corpo_json()['sigla'] ?? '');
    if ($sigla === null) {
        responder_erro('Sigla inválida.', 422, [
            ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'],
        ]);
    }
    if (db_primeiro("SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $id]) === null) {
        responder_erro('Cliente não encontrado.', 404, [
            ['field' => null, 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente inexistente.'],
        ]);
    }
    if (db_primeiro("SELECT id FROM cliente_b2b WHERE sigla = :s AND id <> :id LIMIT 1", [':s' => $sigla, ':id' => $id]) !== null) {
        responder_erro('Sigla já usada por outro cliente.', 409, [
            ['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.'],
        ]);
    }
    db_executar("UPDATE cliente_b2b SET sigla = :s WHERE id = :id", [':s' => $sigla, ':id' => $id]);
    responder_sucesso(['cliente_b2b_id' => $id, 'sigla' => $sigla], 'Sigla atualizada.');
}
