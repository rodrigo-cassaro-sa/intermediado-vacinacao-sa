<?php
// ============================================================================
// api/v1/interno/clinicas.php
// Função: cadastro/listagem de clínicas da rede credenciada e ATRIBUIÇÃO de
// elegíveis a uma clínica (RN-012). Grupo interno (sessão + CSRF).
// ============================================================================

/** POST /api/v1/interno/clinicas — cadastra uma clínica da rede. */
function rota_criar_clinica(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['nome', 'cnpj']);
    $cnpj = so_digitos($dados['cnpj'] ?? '');
    if ($cnpj !== '' && strlen($cnpj) !== 14) {
        $erros[] = ['field' => 'cnpj', 'code' => 'CNPJ_INVALIDO', 'message' => 'CNPJ deve ter 14 dígitos.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }

    $existe = db_primeiro("SELECT id FROM clinica_credenciada WHERE cnpj = :cnpj LIMIT 1", [':cnpj' => $cnpj]);
    if ($existe !== null) {
        responder_erro('CNPJ já cadastrado.', 409, [
            ['field' => 'cnpj', 'code' => 'CNPJ_DUPLICADO', 'message' => 'Já existe clínica com este CNPJ.'],
        ]);
    }

    db_executar(
        "INSERT INTO clinica_credenciada (nome, cnpj, status) VALUES (:nome, :cnpj, 'ativa')",
        [':nome' => trim($dados['nome']), ':cnpj' => $cnpj]
    );
    $id = (int) db_ultimo_id();

    registrar_auditoria('clinica.criada', [
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'clinica_credenciada',
        'entidade_id'   => $id,
        'metadata'      => ['cnpj' => $cnpj],
    ]);

    responder_sucesso(['clinica_id' => $id], 'Clínica cadastrada.', 201);
}

/** GET /api/v1/interno/clinicas — lista clínicas ativas. */
function rota_listar_clinicas(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);

    $itens = db_todos(
        "SELECT id, nome, cnpj, status, criado_em
           FROM clinica_credenciada WHERE excluido_em IS NULL ORDER BY nome"
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/**
 * POST /api/v1/interno/campanhas/{id}/atribuir-clinica
 * Atribui elegíveis (por CPF) a uma clínica dentro da campanha (RN-012).
 * Body: { clinica_id, cpfs: ["...", ...] }
 */
function rota_atribuir_clinica(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['clinica_id']);
    if (empty($dados['cpfs']) || !is_array($dados['cpfs'])) {
        $erros[] = ['field' => 'cpfs', 'code' => 'CPFS_OBRIGATORIOS', 'message' => 'Envie a lista de CPFs.'];
    }
    if ($erros) {
        erro_validacao($erros);
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

    $atribuidos = 0;
    $naoEncontrados = [];
    foreach ($dados['cpfs'] as $cpfBruto) {
        $cpf = so_digitos((string) $cpfBruto);
        $eleg = db_primeiro(
            "SELECT e.id FROM elegivel e
                JOIN paciente p ON p.id = e.paciente_id
              WHERE e.campanha_id = :campanha AND p.cpf = :cpf LIMIT 1",
            [':campanha' => $id, ':cpf' => $cpf]
        );
        if ($eleg === null) {
            $naoEncontrados[] = mascarar_cpf($cpf);
            continue;
        }
        $anterior = db_primeiro("SELECT clinica_id FROM elegivel WHERE id = :id", [':id' => (int) $eleg['id']]);
        db_executar(
            "UPDATE elegivel SET clinica_id = :clinica WHERE id = :id",
            [':clinica' => (int) $dados['clinica_id'], ':id' => (int) $eleg['id']]
        );
        historico_elegivel((int) $eleg['id'], 'clinica_alterada', ator_usuario($usuario),
            ['clinica_id' => $anterior['clinica_id'] ?? null], ['clinica_id' => (int) $dados['clinica_id']]);
        $atribuidos++;
    }

    registrar_auditoria('elegiveis.atribuidos_clinica', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['clinica_id' => (int) $dados['clinica_id'], 'atribuidos' => $atribuidos],
    ]);

    responder_sucesso([
        'atribuidos'      => $atribuidos,
        'nao_encontrados' => $naoEncontrados,
    ], 'Atribuição concluída.');
}
