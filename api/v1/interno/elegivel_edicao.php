<?php
// ============================================================================
// api/v1/interno/elegivel_edicao.php
// Função: editar (corrigir) dados do elegível/paciente com integridade e
// histórico (RN-021), e consultar o histórico do elegível. Grupo interno.
// Campos: nome, cpf, data_nascimento (paciente); tipo_vinculo, cpf_titular,
// codigo_lotacao, codigo_rh, clinica_id (elegível).
// ============================================================================

/** PUT /api/v1/interno/elegiveis/{id} — corrige dados do elegível/paciente. */
function rota_editar_elegivel(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $atual = db_primeiro(
        "SELECT e.id, e.campanha_id, e.tipo_vinculo, e.cpf_titular, e.codigo_lotacao, e.codigo_rh, e.clinica_id,
                e.paciente_id, p.cpf, p.nome, p.data_nascimento
           FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
          WHERE e.id = :id LIMIT 1",
        [':id' => $id]
    );
    if ($atual === null) {
        responder_erro('Elegível inexistente.', 404, [
            ['field' => null, 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível não encontrado.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $atual['campanha_id']);

    $dados = corpo_json();
    $erros = [];

    // ---- Paciente (nome, cpf, data_nascimento) ----
    $novoNome = array_key_exists('nome', $dados) ? trim((string) $dados['nome']) : $atual['nome'];
    if ($novoNome === '') {
        $erros[] = ['field' => 'nome', 'code' => 'NOME_OBRIGATORIO', 'message' => 'Nome não pode ficar vazio.'];
    }

    $novoCpf = $atual['cpf'];
    if (array_key_exists('cpf', $dados)) {
        $c = so_digitos($dados['cpf']);
        if (!validar_cpf($c)) {
            $erros[] = ['field' => 'cpf', 'code' => 'CPF_INVALIDO', 'message' => 'CPF inválido.'];
        } else {
            // Não pode colidir com outro paciente.
            $colide = db_primeiro("SELECT id FROM paciente WHERE cpf = :c AND id <> :pid LIMIT 1",
                [':c' => $c, ':pid' => (int) $atual['paciente_id']]);
            if ($colide !== null) {
                $erros[] = ['field' => 'cpf', 'code' => 'CPF_EM_USO', 'message' => 'CPF já pertence a outro paciente.'];
            } else {
                $novoCpf = $c;
            }
        }
    }

    $novoNasc = $atual['data_nascimento'];
    if (array_key_exists('data_nascimento', $dados)) {
        $n = trim((string) $dados['data_nascimento']);
        if ($n !== '' && !validar_data($n)) {
            $erros[] = ['field' => 'data_nascimento', 'code' => 'DATA_NASCIMENTO_INVALIDA', 'message' => 'Data inválida.'];
        } else {
            $novoNasc = $n === '' ? null : $n;
        }
    }

    // ---- Elegível (tipo, titular, códigos, clínica) ----
    $novoTipo = $atual['tipo_vinculo'];
    if (array_key_exists('tipo_vinculo', $dados)) {
        $t = strtolower(trim((string) $dados['tipo_vinculo']));
        if (!in_array($t, ['colaborador', 'dependente', 'terceiro'], true)) {
            $erros[] = ['field' => 'tipo_vinculo', 'code' => 'TIPO_VINCULO_INVALIDO', 'message' => 'Tipo inválido.'];
        } else {
            $novoTipo = $t;
        }
    }
    $novoTitular = $atual['cpf_titular'];
    if (array_key_exists('cpf_titular', $dados)) {
        $novoTitular = so_digitos($dados['cpf_titular']) ?: null;
    }
    if ($novoTipo === 'dependente') {
        if (!$novoTitular || !validar_cpf($novoTitular)) {
            $erros[] = ['field' => 'cpf_titular', 'code' => 'CPF_TITULAR_INVALIDO', 'message' => 'Dependente exige CPF do titular válido.'];
        }
    } else {
        $novoTitular = null;
    }
    $novoLotacao = array_key_exists('codigo_lotacao', $dados) ? trim((string) $dados['codigo_lotacao']) : $atual['codigo_lotacao'];
    $novoRh      = array_key_exists('codigo_rh', $dados) ? trim((string) $dados['codigo_rh']) : $atual['codigo_rh'];

    $novaClinica = $atual['clinica_id'];
    if (array_key_exists('clinica_id', $dados)) {
        if ($dados['clinica_id'] === null || $dados['clinica_id'] === '') {
            $novaClinica = null; // volta para in company / sem clínica
        } else {
            $cl = db_primeiro("SELECT id FROM clinica_credenciada WHERE id = :id AND status = 'ativa' AND excluido_em IS NULL LIMIT 1",
                [':id' => (int) $dados['clinica_id']]);
            if ($cl === null) {
                $erros[] = ['field' => 'clinica_id', 'code' => 'CLINICA_NAO_ENCONTRADA', 'message' => 'Clínica inválida.'];
            } else {
                $novaClinica = (int) $cl['id'];
            }
        }
    }

    if ($erros) {
        erro_validacao($erros);
    }

    $antes = [
        'nome' => $atual['nome'], 'cpf' => mascarar_cpf($atual['cpf']), 'data_nascimento' => $atual['data_nascimento'],
        'tipo_vinculo' => $atual['tipo_vinculo'], 'cpf_titular' => $atual['cpf_titular'] ? mascarar_cpf($atual['cpf_titular']) : null,
        'codigo_lotacao' => $atual['codigo_lotacao'], 'codigo_rh' => $atual['codigo_rh'], 'clinica_id' => $atual['clinica_id'],
    ];
    $depois = [
        'nome' => $novoNome, 'cpf' => mascarar_cpf($novoCpf), 'data_nascimento' => $novoNasc,
        'tipo_vinculo' => $novoTipo, 'cpf_titular' => $novoTitular ? mascarar_cpf($novoTitular) : null,
        'codigo_lotacao' => $novoLotacao, 'codigo_rh' => $novoRh, 'clinica_id' => $novaClinica,
    ];

    try {
        pdo()->beginTransaction();

        db_executar(
            "UPDATE paciente SET nome = :nome, cpf = :cpf, data_nascimento = :nasc WHERE id = :id",
            [':nome' => $novoNome, ':cpf' => $novoCpf, ':nasc' => $novoNasc, ':id' => (int) $atual['paciente_id']]
        );
        db_executar(
            "UPDATE elegivel SET tipo_vinculo = :tipo, cpf_titular = :titular, codigo_lotacao = :lot,
                    codigo_rh = :rh, clinica_id = :clinica WHERE id = :id",
            [':tipo' => $novoTipo, ':titular' => $novoTitular, ':lot' => $novoLotacao, ':rh' => $novoRh,
             ':clinica' => $novaClinica, ':id' => $id]
        );

        // Histórico: evento geral 'editado' e, se mudou a clínica, também 'clinica_alterada'.
        historico_elegivel($id, 'editado', ator_usuario($usuario), $antes, $depois);
        if ((int) ($atual['clinica_id'] ?? 0) !== (int) ($novaClinica ?? 0)) {
            historico_elegivel($id, 'clinica_alterada', ator_usuario($usuario),
                ['clinica_id' => $atual['clinica_id']], ['clinica_id' => $novaClinica]);
        }

        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('elegivel.editado', [
        'tenant_id'     => null,
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'elegivel',
        'entidade_id'   => $id,
        'metadata'      => ['campos' => array_keys($dados)],
    ]);

    responder_sucesso(['elegivel_id' => $id], 'Elegível atualizado.');
}

/** GET /api/v1/interno/elegiveis/{id}/historico — trilha de mudanças. */
function rota_historico_elegivel(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);

    $id = (int) ($params['id'] ?? 0);
    $eleg = db_primeiro("SELECT campanha_id FROM elegivel WHERE id = :id LIMIT 1", [':id' => $id]);
    if ($eleg === null) {
        responder_erro('Elegível inexistente.', 404, [
            ['field' => null, 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível não encontrado.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $eleg['campanha_id']);

    $itens = db_todos(
        "SELECT id, evento, ator_tipo, ator_id, dados_antes, dados_depois, observacao, criado_em
           FROM elegivel_historico WHERE elegivel_id = :id ORDER BY id DESC",
        [':id' => $id]
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}
