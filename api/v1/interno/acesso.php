<?php
// ============================================================================
// api/v1/interno/acesso.php
// PORTAL D0: gestão de grupos, unidades, usuários e atribuições (hierarquia).
// Grupo interno (sessão). "acima gere abaixo" (doc 04 §4.1).
// ============================================================================

const NIVEIS_ATRIBUICAO = ['gestao_interna', 'grupo', 'negocio', 'local'];

/** GET /api/v1/interno/acesso/eu — atribuições e capacidades do usuário logado. */
function rota_acesso_eu(array $params): void
{
    $usuario = exigir_login();
    responder_sucesso([
        'usuario'      => ['id' => $usuario['id'], 'nome' => $usuario['nome'], 'perfil' => $usuario['perfil']],
        'interno'      => usuario_eh_interno($usuario),
        'atribuicoes'  => atribuicoes_do_usuario((int) $usuario['id']),
        'clientes_geridos' => clientes_geridos_pelo_usuario($usuario),
    ], 'OK.');
}

/** POST /api/v1/interno/grupos  {nome} — só gestão interna. */
function rota_criar_grupo(array $params): void
{
    $usuario = exigir_login();
    if (!usuario_eh_interno($usuario)) {
        responder_erro('Sem permissão.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Apenas gestão interna.']]);
    }
    exigir_csrf();
    $dados = corpo_json();
    $erros = exigir_campos($dados, ['nome']);
    $sigla = null;
    if (isset($dados['sigla']) && trim((string) $dados['sigla']) !== '') {
        $sigla = normalizar_sigla($dados['sigla']);
        if ($sigla === null) $erros[] = ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'];
    }
    if ($erros) erro_validacao($erros);
    if ($sigla !== null && db_primeiro("SELECT id FROM grupo_empresarial WHERE sigla = :s LIMIT 1", [':s' => $sigla]) !== null) {
        responder_erro('Sigla já usada por outro grupo.', 409, [['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.']]);
    }
    db_executar("INSERT INTO grupo_empresarial (nome, sigla) VALUES (:n, :s)", [':n' => trim($dados['nome']), ':s' => $sigla]);
    $id = (int) db_ultimo_id();
    registrar_auditoria('grupo.criado', ['ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin', 'entidade_tipo' => 'grupo_empresarial', 'entidade_id' => $id]);
    responder_sucesso(['grupo_empresarial_id' => $id], 'Grupo criado.', 201);
}

/** POST /api/v1/interno/grupos/{id}/sigla — define/atualiza a sigla do grupo. */
function rota_definir_sigla_grupo(array $params): void
{
    $usuario = exigir_login();
    if (!usuario_eh_interno($usuario)) {
        responder_erro('Sem permissão.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Apenas gestão interna.']]);
    }
    exigir_csrf();
    $id = (int) ($params['id'] ?? 0);
    $sigla = normalizar_sigla(corpo_json()['sigla'] ?? '');
    if ($sigla === null) {
        responder_erro('Sigla inválida.', 422, [['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).']]);
    }
    if (db_primeiro("SELECT id FROM grupo_empresarial WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $id]) === null) {
        responder_erro('Grupo não encontrado.', 404, [['field' => null, 'code' => 'GRUPO_NAO_ENCONTRADO', 'message' => 'Grupo inexistente.']]);
    }
    if (db_primeiro("SELECT id FROM grupo_empresarial WHERE sigla = :s AND id <> :id LIMIT 1", [':s' => $sigla, ':id' => $id]) !== null) {
        responder_erro('Sigla já usada por outro grupo.', 409, [['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.']]);
    }
    db_executar("UPDATE grupo_empresarial SET sigla = :s WHERE id = :id", [':s' => $sigla, ':id' => $id]);
    responder_sucesso(['grupo_empresarial_id' => $id, 'sigla' => $sigla], 'Sigla atualizada.');
}

/** GET /api/v1/interno/grupos */
function rota_listar_grupos(array $params): void
{
    $usuario = exigir_login();
    if (!usuario_eh_interno($usuario)) {
        responder_erro('Sem permissão.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Apenas gestão interna.']]);
    }
    responder_sucesso(['itens' => db_todos("SELECT id, nome, sigla, status, criado_em FROM grupo_empresarial WHERE excluido_em IS NULL ORDER BY nome")], 'OK.');
}

/** POST /api/v1/interno/clientes/{id}/grupo  {grupo_empresarial_id} — vincula cliente a grupo. */
function rota_vincular_cliente_grupo(array $params): void
{
    $usuario = exigir_login();
    if (!usuario_eh_interno($usuario)) {
        responder_erro('Sem permissão.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Apenas gestão interna.']]);
    }
    exigir_csrf();
    $clienteId = (int) ($params['id'] ?? 0);
    $dados = corpo_json();
    $grupoId = isset($dados['grupo_empresarial_id']) && is_numeric($dados['grupo_empresarial_id']) ? (int) $dados['grupo_empresarial_id'] : null;
    db_executar("UPDATE cliente_b2b SET grupo_empresarial_id = :g WHERE id = :c", [':g' => $grupoId, ':c' => $clienteId]);
    responder_sucesso(['cliente_b2b_id' => $clienteId, 'grupo_empresarial_id' => $grupoId], 'Vínculo atualizado.');
}

/** POST /api/v1/interno/clientes/{id}/unidades  {nome, codigo_lotacao?, cidade?, uf?} */
function rota_criar_unidade(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();
    $clienteId = (int) ($params['id'] ?? 0);
    if (!usuario_pode_cliente($usuario, $clienteId, true)) {
        responder_erro('Sem acesso a este cliente.', 403, [['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Acesso negado.']]);
    }
    $dados = corpo_json();
    $erros = exigir_campos($dados, ['nome']);
    if ($erros) erro_validacao($erros);
    db_executar(
        "INSERT INTO unidade (cliente_b2b_id, nome, codigo_lotacao, cidade, uf) VALUES (:c, :n, :cod, :cid, :uf)",
        [':c' => $clienteId, ':n' => trim($dados['nome']), ':cod' => $dados['codigo_lotacao'] ?? null,
         ':cid' => $dados['cidade'] ?? null, ':uf' => isset($dados['uf']) ? strtoupper(substr($dados['uf'], 0, 2)) : null]
    );
    $id = (int) db_ultimo_id();
    registrar_auditoria('unidade.criada', ['tenant_id' => $clienteId, 'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin', 'entidade_tipo' => 'unidade', 'entidade_id' => $id]);
    responder_sucesso(['unidade_id' => $id], 'Unidade criada.', 201);
}

/** GET /api/v1/interno/clientes/{id}/unidades */
function rota_listar_unidades(array $params): void
{
    $usuario = exigir_login();
    $clienteId = (int) ($params['id'] ?? 0);
    if (!usuario_pode_cliente($usuario, $clienteId)) {
        responder_erro('Sem acesso a este cliente.', 403, [['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Acesso negado.']]);
    }
    responder_sucesso(['itens' => db_todos("SELECT id, nome, codigo_lotacao, cidade, uf, status FROM unidade WHERE cliente_b2b_id = :c AND excluido_em IS NULL ORDER BY nome", [':c' => $clienteId])], 'OK.');
}

/**
 * POST /api/v1/interno/usuarios  {nome, email, senha, nivel, escopo_tipo, escopo_id}
 * Cria usuário do portal + 1 atribuição. Ator precisa poder gerir o nível/escopo.
 */
function rota_criar_usuario_portal(array $params): void
{
    $ator = exigir_login();
    exigir_csrf();
    $dados = corpo_json();
    $erros = exigir_campos($dados, ['nome', 'email', 'senha', 'nivel']);
    if (isset($dados['nivel']) && !in_array($dados['nivel'], NIVEIS_ATRIBUICAO, true)) {
        $erros[] = ['field' => 'nivel', 'code' => 'NIVEL_INVALIDO', 'message' => 'Nível inválido.'];
    }
    if (isset($dados['senha']) && strlen((string) $dados['senha']) < 8) {
        $erros[] = ['field' => 'senha', 'code' => 'SENHA_CURTA', 'message' => 'Mínimo 8 caracteres.'];
    }
    if (!validar_email($dados['email'] ?? '')) {
        $erros[] = ['field' => 'email', 'code' => 'EMAIL_INVALIDO', 'message' => 'E-mail inválido.'];
    }
    if ($erros) erro_validacao($erros);

    $nivel = $dados['nivel'];
    [$escopoTipo, $escopoId] = _escopo_do_nivel($nivel, $dados);
    if (!usuario_pode_gerir($ator, $nivel, $escopoTipo, $escopoId)) {
        responder_erro('Sem permissão para criar usuário neste nível/escopo.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Você só gere níveis iguais/abaixo no seu escopo.']]);
    }
    if (db_primeiro("SELECT id FROM usuario WHERE email = :e AND excluido_em IS NULL", [':e' => $dados['email']]) !== null) {
        responder_erro('E-mail já cadastrado.', 409, [['field' => 'email', 'code' => 'EMAIL_DUPLICADO', 'message' => 'Já existe usuário com este e-mail.']]);
    }

    // perfil/tenant derivados (compat com endpoints atuais).
    $perfil = $nivel === 'gestao_interna' ? 'operador_interno' : ($nivel === 'negocio' ? 'cliente_b2b' : 'gestor');
    $tenant = $nivel === 'negocio' ? $escopoId : ($nivel === 'local' ? cliente_da_unidade($escopoId) : null);

    try {
        pdo()->beginTransaction();
        db_executar(
            "INSERT INTO usuario (tenant_id, perfil, nome, email, senha_hash, status) VALUES (:t, :p, :n, :e, :h, 'ativo')",
            [':t' => $tenant, ':p' => $perfil, ':n' => trim($dados['nome']), ':e' => $dados['email'], ':h' => password_hash($dados['senha'], PASSWORD_DEFAULT)]
        );
        $uid = (int) db_ultimo_id();
        db_executar(
            "INSERT INTO usuario_atribuicao (usuario_id, nivel, escopo_tipo, escopo_id, criado_por) VALUES (:u, :nv, :et, :ei, :cp)",
            [':u' => $uid, ':nv' => $nivel, ':et' => $escopoTipo, ':ei' => $escopoId, ':cp' => (int) $ator['id']]
        );
        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) pdo()->rollBack();
        throw $e;
    }

    registrar_auditoria('usuario.criado', ['ator_tipo' => 'usuario', 'ator_id' => (int) $ator['id'], 'origem' => 'admin', 'entidade_tipo' => 'usuario', 'entidade_id' => $uid, 'metadata' => ['nivel' => $nivel, 'escopo_tipo' => $escopoTipo, 'escopo_id' => $escopoId]]);
    responder_sucesso(['usuario_id' => $uid], 'Usuário criado.', 201);
}

/** POST /api/v1/interno/usuarios/{id}/atribuicoes  {nivel, escopo_tipo?, escopo_id?/cliente_b2b_id/unidade_id/grupo_empresarial_id} */
function rota_adicionar_atribuicao(array $params): void
{
    $ator = exigir_login();
    exigir_csrf();
    $uid = (int) ($params['id'] ?? 0);
    if (db_primeiro("SELECT id FROM usuario WHERE id = :id AND excluido_em IS NULL", [':id' => $uid]) === null) {
        responder_erro('Usuário inexistente.', 404, [['field' => null, 'code' => 'USUARIO_NAO_ENCONTRADO', 'message' => 'Não encontrado.']]);
    }
    $dados = corpo_json();
    if (empty($dados['nivel']) || !in_array($dados['nivel'], NIVEIS_ATRIBUICAO, true)) {
        erro_validacao([['field' => 'nivel', 'code' => 'NIVEL_INVALIDO', 'message' => 'Nível inválido.']]);
    }
    [$escopoTipo, $escopoId] = _escopo_do_nivel($dados['nivel'], $dados);
    if (!usuario_pode_gerir($ator, $dados['nivel'], $escopoTipo, $escopoId)) {
        responder_erro('Sem permissão para esta atribuição.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Fora do seu escopo/nível.']]);
    }
    db_executar(
        "INSERT IGNORE INTO usuario_atribuicao (usuario_id, nivel, escopo_tipo, escopo_id, criado_por) VALUES (:u, :nv, :et, :ei, :cp)",
        [':u' => $uid, ':nv' => $dados['nivel'], ':et' => $escopoTipo, ':ei' => $escopoId, ':cp' => (int) $ator['id']]
    );
    registrar_auditoria('usuario.atribuicao_add', ['ator_tipo' => 'usuario', 'ator_id' => (int) $ator['id'], 'origem' => 'admin', 'entidade_tipo' => 'usuario', 'entidade_id' => $uid, 'metadata' => ['nivel' => $dados['nivel'], 'escopo_tipo' => $escopoTipo, 'escopo_id' => $escopoId]]);
    responder_sucesso(['usuario_id' => $uid], 'Atribuição adicionada.', 201);
}

/** GET /api/v1/interno/usuarios — lista usuários do escopo gerido pelo ator. */
function rota_listar_usuarios(array $params): void
{
    $ator = exigir_login();
    $managed = clientes_geridos_pelo_usuario($ator);
    if ($managed === ['*']) {
        $itens = db_todos("SELECT id, nome, email, perfil, status, criado_em FROM usuario WHERE excluido_em IS NULL ORDER BY nome LIMIT 200");
        responder_sucesso(['itens' => $itens], 'OK.');
    }
    if (!$managed) {
        responder_sucesso(['itens' => []], 'OK.');
    }
    $ph = []; $bind = [];
    foreach ($managed as $i => $c) { $ph[] = ":c_$i"; $bind[":c_$i"] = (int) $c; }
    $in = implode(',', $ph);
    $itens = db_todos(
        "SELECT DISTINCT u.id, u.nome, u.email, u.perfil, u.status, u.criado_em
           FROM usuario u
           JOIN usuario_atribuicao ua ON ua.usuario_id = u.id
      LEFT JOIN unidade un ON ua.escopo_tipo = 'unidade' AND un.id = ua.escopo_id
          WHERE u.excluido_em IS NULL AND (
                (ua.escopo_tipo = 'cliente_b2b' AND ua.escopo_id IN ($in)) OR
                (ua.escopo_tipo = 'unidade' AND un.cliente_b2b_id IN ($in))
          )
          ORDER BY u.nome LIMIT 200",
        $bind
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/** GET /api/v1/interno/usuarios/{id}/atribuicoes */
function rota_listar_atribuicoes(array $params): void
{
    $ator = exigir_login();
    $uid = (int) ($params['id'] ?? 0);
    responder_sucesso(['itens' => db_todos("SELECT id, nivel, escopo_tipo, escopo_id, criado_em FROM usuario_atribuicao WHERE usuario_id = :u ORDER BY id", [':u' => $uid])], 'OK.');
}

/** Resolve escopo_tipo/escopo_id a partir do nível e do corpo. */
function _escopo_do_nivel(string $nivel, array $dados): array
{
    switch ($nivel) {
        case 'gestao_interna': return ['global', 0];
        case 'grupo':   return ['grupo_empresarial', (int) ($dados['grupo_empresarial_id'] ?? $dados['escopo_id'] ?? 0)];
        case 'negocio': return ['cliente_b2b', (int) ($dados['cliente_b2b_id'] ?? $dados['escopo_id'] ?? 0)];
        case 'local':   return ['unidade', (int) ($dados['unidade_id'] ?? $dados['escopo_id'] ?? 0)];
        default:        return ['global', 0];
    }
}
