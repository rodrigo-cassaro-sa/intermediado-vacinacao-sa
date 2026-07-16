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

/** PUT /api/v1/interno/clientes/{id} — edita razão social, CNPJ, sigla, status e grupo. */
function rota_editar_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    if (db_primeiro("SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $id]) === null) {
        responder_erro('Cliente não encontrado.', 404, [['field' => null, 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente inexistente.']]);
    }

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['razao_social', 'cnpj']);
    $cnpj = so_digitos($dados['cnpj'] ?? '');
    if ($cnpj !== '' && strlen($cnpj) !== 14) {
        $erros[] = ['field' => 'cnpj', 'code' => 'CNPJ_INVALIDO', 'message' => 'CNPJ deve ter 14 dígitos.'];
    }
    // sigla (opcional; string vazia => limpa)
    $temSigla = array_key_exists('sigla', $dados);
    $sigla = null;
    if ($temSigla) {
        $s = trim((string) ($dados['sigla'] ?? ''));
        if ($s !== '') {
            $sigla = normalizar_sigla($s);
            if ($sigla === null) $erros[] = ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'];
        }
    }
    // status
    $status = null;
    if (isset($dados['status'])) {
        $status = (string) $dados['status'];
        if (!in_array($status, ['ativo', 'inativo'], true)) $erros[] = ['field' => 'status', 'code' => 'STATUS_INVALIDO', 'message' => 'Use ativo ou inativo.'];
    }
    // grupo (opcional; null/'' desvincula)
    $temGrupo = array_key_exists('grupo_empresarial_id', $dados);
    $grupoId = null;
    if ($temGrupo) {
        $g = $dados['grupo_empresarial_id'];
        $grupoId = ($g === null || $g === '' || (int) $g === 0) ? null : (int) $g;
    }
    if ($erros) erro_validacao($erros);

    if ($cnpj !== '' && db_primeiro("SELECT id FROM cliente_b2b WHERE cnpj = :cnpj AND id <> :id LIMIT 1", [':cnpj' => $cnpj, ':id' => $id]) !== null) {
        responder_erro('CNPJ já cadastrado.', 409, [['field' => 'cnpj', 'code' => 'CNPJ_DUPLICADO', 'message' => 'Outro cliente já usa este CNPJ.']]);
    }
    if ($temSigla && $sigla !== null && db_primeiro("SELECT id FROM cliente_b2b WHERE sigla = :s AND id <> :id LIMIT 1", [':s' => $sigla, ':id' => $id]) !== null) {
        responder_erro('Sigla já usada por outro cliente.', 409, [['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.']]);
    }
    if ($temGrupo && $grupoId !== null && db_primeiro("SELECT id FROM grupo_empresarial WHERE id = :g AND excluido_em IS NULL LIMIT 1", [':g' => $grupoId]) === null) {
        responder_erro('Grupo não encontrado.', 404, [['field' => 'grupo_empresarial_id', 'code' => 'GRUPO_NAO_ENCONTRADO', 'message' => 'Grupo inexistente.']]);
    }

    $sets = ['razao_social = :razao', 'cnpj = :cnpj'];
    $bind = [':razao' => trim($dados['razao_social']), ':cnpj' => $cnpj, ':id' => $id];
    if ($temSigla) { $sets[] = 'sigla = :sigla'; $bind[':sigla'] = $sigla; }
    if ($status !== null) { $sets[] = 'status = :status'; $bind[':status'] = $status; }
    if ($temGrupo) { $sets[] = 'grupo_empresarial_id = :grupo'; $bind[':grupo'] = $grupoId; }
    db_executar('UPDATE cliente_b2b SET ' . implode(', ', $sets) . ' WHERE id = :id', $bind);

    registrar_auditoria('cliente.editado', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'cliente_b2b', 'entidade_id' => $id, 'metadata' => ['cnpj' => $cnpj],
    ]);
    responder_sucesso(['cliente_b2b_id' => $id], 'Cliente atualizado.');
}

/**
 * POST /api/v1/interno/clientes/importar  { clientes: [{razao_social, cnpj, sigla?, grupo_sigla?}] }
 * Upsert por CNPJ. Vincula grupo pela sigla (se existir). Relatório por linha.
 */
function rota_importar_clientes(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $lista = corpo_json()['clientes'] ?? null;
    if (!is_array($lista) || !$lista) {
        erro_validacao([['field' => 'clientes', 'code' => 'LISTA_VAZIA', 'message' => 'Envie ao menos uma linha.']]);
    }

    // Índice de grupos por sigla (para vincular).
    $gruposPorSigla = [];
    foreach (db_todos("SELECT id, sigla FROM grupo_empresarial WHERE excluido_em IS NULL AND sigla IS NOT NULL") as $g) {
        $gruposPorSigla[strtoupper((string) $g['sigla'])] = (int) $g['id'];
    }

    $inseridos = 0; $atualizados = 0; $erros = []; $linha = 0;
    foreach ($lista as $it) {
        $linha++;
        $razao = trim((string) ($it['razao_social'] ?? ''));
        $cnpj = so_digitos((string) ($it['cnpj'] ?? ''));
        if ($razao === '' || $cnpj === '') { $erros[] = ['linha' => $linha, 'motivo' => 'Razão social e CNPJ são obrigatórios.']; continue; }
        if (strlen($cnpj) !== 14) { $erros[] = ['linha' => $linha, 'motivo' => 'CNPJ deve ter 14 dígitos.']; continue; }

        $sigla = null;
        $sRaw = trim((string) ($it['sigla'] ?? ''));
        if ($sRaw !== '') {
            $sigla = normalizar_sigla($sRaw);
            if ($sigla === null) { $erros[] = ['linha' => $linha, 'motivo' => 'Sigla inválida (use 3 caracteres A-Z/0-9).']; continue; }
        }
        $grupoId = null;
        $gRaw = trim((string) ($it['grupo_sigla'] ?? ''));
        if ($gRaw !== '') {
            $key = strtoupper($gRaw);
            if (isset($gruposPorSigla[$key])) $grupoId = $gruposPorSigla[$key];
            else $erros[] = ['linha' => $linha, 'motivo' => 'Grupo (sigla "' . $gRaw . '") não encontrado — cliente novo recebe um grupo próprio; cliente existente mantém o grupo atual.'];
        }
        // Sigla não pode colidir com outro cliente (de CNPJ diferente).
        if ($sigla !== null) {
            $dup = db_primeiro("SELECT id FROM cliente_b2b WHERE sigla = :s AND cnpj <> :cnpj LIMIT 1", [':s' => $sigla, ':cnpj' => $cnpj]);
            if ($dup !== null) { $erros[] = ['linha' => $linha, 'motivo' => 'Sigla já usada por outro cliente.']; continue; }
        }

        $exist = db_primeiro("SELECT id FROM cliente_b2b WHERE cnpj = :cnpj LIMIT 1", [':cnpj' => $cnpj]);
        if ($exist !== null) {
            $cid = (int) $exist['id'];
            $sets = ['razao_social = :razao'];
            $bind = [':razao' => $razao, ':id' => $cid];
            if ($sigla !== null) { $sets[] = 'sigla = :sigla'; $bind[':sigla'] = $sigla; }
            if ($grupoId !== null) { $sets[] = 'grupo_empresarial_id = :grupo'; $bind[':grupo'] = $grupoId; }
            db_executar('UPDATE cliente_b2b SET ' . implode(', ', $sets) . ' WHERE id = :id', $bind);
            $atualizados++;
        } else {
            // Todo cliente precisa de grupo: se a linha não trouxe um, cria um
            // grupo repetindo o cliente (mesmo nome/sigla) e vincula.
            if ($grupoId === null) {
                $gSigla = $sigla;
                if ($gSigla !== null && db_primeiro("SELECT id FROM grupo_empresarial WHERE sigla = :s LIMIT 1", [':s' => $gSigla]) !== null) {
                    $gSigla = null; // evita conflito de sigla de grupo
                }
                db_executar("INSERT INTO grupo_empresarial (nome, sigla) VALUES (:n, :s)", [':n' => $razao, ':s' => $gSigla]);
                $grupoId = (int) db_ultimo_id();
            }
            db_executar(
                "INSERT INTO cliente_b2b (razao_social, sigla, cnpj, status, grupo_empresarial_id) VALUES (:razao, :sigla, :cnpj, 'ativo', :grupo)",
                [':razao' => $razao, ':sigla' => $sigla, ':cnpj' => $cnpj, ':grupo' => $grupoId]
            );
            $inseridos++;
        }
    }

    registrar_auditoria('clientes.importados', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'cliente_b2b', 'metadata' => ['inseridos' => $inseridos, 'atualizados' => $atualizados, 'erros' => count($erros)],
    ]);
    responder_sucesso(['inseridos' => $inseridos, 'atualizados' => $atualizados, 'erros' => $erros, 'total' => count($lista)], 'Importação concluída.');
}
