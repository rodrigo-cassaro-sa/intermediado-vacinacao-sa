<?php
// ============================================================================
// api/v1/interno/campanhas.php
// Função: CRUD de campanhas (RN-001) e gestão das vacinas da campanha.
// Grupo interno (sessão + CSRF). Base: docs/09, docs/08. tenant_id = cliente_b2b.
// ============================================================================

const CAMPANHA_MODALIDADES = ['rede_credenciada', 'in_company'];
const CAMPANHA_STATUS      = ['rascunho', 'ativa', 'encerrada'];
const PERFIS_INTERNOS      = ['super_admin', 'operador_interno'];

/** POST /api/v1/interno/campanhas — cria campanha (opcionalmente com vacinas). */
function rota_criar_campanha(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['cliente_b2b_id', 'nome', 'modalidade', 'periodo_inicio', 'periodo_fim']);
    $erros = array_merge($erros, validar_dados_campanha($dados));
    if ($erros) {
        erro_validacao($erros);
    }

    // Cliente (tenant) precisa existir e estar ativo.
    $cliente = db_primeiro(
        "SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL AND status = 'ativo' LIMIT 1",
        [':id' => (int) $dados['cliente_b2b_id']]
    );
    if ($cliente === null) {
        responder_erro('Cliente B2B inexistente ou inativo.', 422, [
            ['field' => 'cliente_b2b_id', 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente B2B inválido.'],
        ]);
    }

    $vacinas = normalizar_vacinas($dados['vacinas'] ?? []);

    try {
        pdo()->beginTransaction();

        db_executar(
            "INSERT INTO campanha (tenant_id, nome, modalidade, periodo_inicio, periodo_fim, status, criado_por)
             VALUES (:tenant_id, :nome, :modalidade, :inicio, :fim, :status, :criado_por)",
            [
                ':tenant_id'  => (int) $dados['cliente_b2b_id'],
                ':nome'       => trim($dados['nome']),
                ':modalidade' => $dados['modalidade'],
                ':inicio'     => $dados['periodo_inicio'],
                ':fim'        => $dados['periodo_fim'],
                ':status'     => $dados['status'] ?? 'rascunho',
                ':criado_por' => (int) $usuario['id'],
            ]
        );
        $campanhaId = (int) db_ultimo_id();

        gravar_vacinas_campanha($campanhaId, (int) $dados['cliente_b2b_id'], $vacinas);

        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('campanha.criada', [
        'tenant_id'     => (int) $dados['cliente_b2b_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $campanhaId,
    ]);

    responder_sucesso(
        ['campanha_id' => $campanhaId, 'vacinas_vinculadas' => count($vacinas)],
        'Campanha criada.',
        201
    );
}

/** GET /api/v1/interno/campanhas — lista campanhas do escopo. */
function rota_listar_campanhas(array $params): void
{
    $usuario = exigir_login();

    [$page, $porPagina, $offset] = paginacao();

    $where = 'c.excluido_em IS NULL';
    $bind  = [];

    if (in_array($usuario['perfil'], PERFIS_INTERNOS, true)) {
        // Interno pode filtrar por cliente opcionalmente.
        if (!empty($_GET['cliente_b2b_id']) && is_numeric($_GET['cliente_b2b_id'])) {
            $where .= ' AND c.tenant_id = :tenant';
            $bind[':tenant'] = (int) $_GET['cliente_b2b_id'];
        }
    } else {
        // Demais perfis: só o próprio tenant.
        if ($usuario['tenant_id'] === null) {
            responder_sucesso(['itens' => []], 'OK.', 200, ['page' => $page, 'por_pagina' => $porPagina, 'total' => 0]);
        }
        $where .= ' AND c.tenant_id = :tenant';
        $bind[':tenant'] = (int) $usuario['tenant_id'];
    }

    $total = (int) (db_primeiro("SELECT COUNT(*) AS t FROM campanha c WHERE $where", $bind)['t'] ?? 0);

    $itens = db_todos(
        "SELECT c.id, c.tenant_id AS cliente_b2b_id, cb.razao_social AS cliente,
                c.nome, c.modalidade, c.periodo_inicio, c.periodo_fim, c.status, c.criado_em
           FROM campanha c
           JOIN cliente_b2b cb ON cb.id = c.tenant_id
          WHERE $where
          ORDER BY c.id DESC
          LIMIT $porPagina OFFSET $offset",
        $bind
    );

    responder_sucesso(['itens' => $itens], 'OK.', 200, [
        'page' => $page, 'por_pagina' => $porPagina, 'total' => $total,
    ]);
}

/** GET /api/v1/interno/campanhas/{id} — detalhe + vacinas. */
function rota_obter_campanha(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $vacinas = db_todos(
        "SELECT cv.vacina_id, v.nome, cv.doses_previstas
           FROM campanha_vacina cv
           JOIN vacina v ON v.id = cv.vacina_id
          WHERE cv.campanha_id = :id
          ORDER BY v.nome",
        [':id' => $id]
    );

    responder_sucesso([
        'campanha' => [
            'id'             => (int) $campanha['id'],
            'cliente_b2b_id' => (int) $campanha['tenant_id'],
            'nome'           => $campanha['nome'],
            'modalidade'     => $campanha['modalidade'],
            'periodo_inicio' => $campanha['periodo_inicio'],
            'periodo_fim'    => $campanha['periodo_fim'],
            'status'         => $campanha['status'],
        ],
        'vacinas' => $vacinas,
    ], 'OK.');
}

/** PUT /api/v1/interno/campanhas/{id} — edita campos permitidos. */
function rota_editar_campanha(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $dados = corpo_json();

    // Monta atualização apenas com campos enviados.
    $campos = [];
    $bind   = [':id' => $id];
    $erros  = [];

    if (isset($dados['nome']) && trim($dados['nome']) !== '') {
        $campos[] = 'nome = :nome';
        $bind[':nome'] = trim($dados['nome']);
    }
    if (isset($dados['modalidade'])) {
        if (!in_array($dados['modalidade'], CAMPANHA_MODALIDADES, true)) {
            $erros[] = ['field' => 'modalidade', 'code' => 'MODALIDADE_INVALIDA', 'message' => 'Modalidade inválida.'];
        } else {
            $campos[] = 'modalidade = :modalidade';
            $bind[':modalidade'] = $dados['modalidade'];
        }
    }
    if (isset($dados['periodo_inicio'])) {
        if (!validar_data($dados['periodo_inicio'])) {
            $erros[] = ['field' => 'periodo_inicio', 'code' => 'DATA_INVALIDA', 'message' => 'Use o formato AAAA-MM-DD.'];
        } else {
            $campos[] = 'periodo_inicio = :inicio';
            $bind[':inicio'] = $dados['periodo_inicio'];
        }
    }
    if (isset($dados['periodo_fim'])) {
        if (!validar_data($dados['periodo_fim'])) {
            $erros[] = ['field' => 'periodo_fim', 'code' => 'DATA_INVALIDA', 'message' => 'Use o formato AAAA-MM-DD.'];
        } else {
            $campos[] = 'periodo_fim = :fim';
            $bind[':fim'] = $dados['periodo_fim'];
        }
    }
    if (isset($dados['status'])) {
        if (!in_array($dados['status'], CAMPANHA_STATUS, true)) {
            $erros[] = ['field' => 'status', 'code' => 'STATUS_INVALIDO', 'message' => 'Status inválido.'];
        } else {
            $campos[] = 'status = :status';
            $bind[':status'] = $dados['status'];
        }
    }

    if ($erros) {
        erro_validacao($erros);
    }
    if (!$campos) {
        responder_erro('Nada para atualizar.', 400, [
            ['field' => null, 'code' => 'SEM_ALTERACAO', 'message' => 'Informe ao menos um campo.'],
        ]);
    }

    db_executar('UPDATE campanha SET ' . implode(', ', $campos) . ' WHERE id = :id', $bind);

    registrar_auditoria('campanha.editada', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['campos' => array_keys($dados)],
    ]);

    responder_sucesso(['campanha_id' => $id], 'Campanha atualizada.');
}

/** POST /api/v1/interno/campanhas/{id}/vacinas — redefine as vacinas da campanha. */
function rota_definir_vacinas_campanha(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $dados = corpo_json();
    $vacinas = normalizar_vacinas($dados['vacinas'] ?? []);
    if (!$vacinas) {
        responder_erro('Informe ao menos uma vacina.', 400, [
            ['field' => 'vacinas', 'code' => 'VACINAS_OBRIGATORIAS', 'message' => 'Lista de vacinas vazia.'],
        ]);
    }

    try {
        pdo()->beginTransaction();
        db_executar('DELETE FROM campanha_vacina WHERE campanha_id = :id', [':id' => $id]);
        gravar_vacinas_campanha($id, (int) $campanha['tenant_id'], $vacinas);
        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('campanha.vacinas_definidas', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
    ]);

    responder_sucesso(['campanha_id' => $id, 'vacinas_vinculadas' => count($vacinas)], 'Vacinas atualizadas.');
}

// ------------------------------- Helpers -----------------------------------

/** Valida modalidade, datas e status (quando presentes) na criação. */
function validar_dados_campanha(array $dados): array
{
    $erros = [];
    if (isset($dados['modalidade']) && !in_array($dados['modalidade'], CAMPANHA_MODALIDADES, true)) {
        $erros[] = ['field' => 'modalidade', 'code' => 'MODALIDADE_INVALIDA', 'message' => 'Use rede_credenciada ou in_company.'];
    }
    if (isset($dados['periodo_inicio']) && !validar_data($dados['periodo_inicio'])) {
        $erros[] = ['field' => 'periodo_inicio', 'code' => 'DATA_INVALIDA', 'message' => 'Use o formato AAAA-MM-DD.'];
    }
    if (isset($dados['periodo_fim']) && !validar_data($dados['periodo_fim'])) {
        $erros[] = ['field' => 'periodo_fim', 'code' => 'DATA_INVALIDA', 'message' => 'Use o formato AAAA-MM-DD.'];
    }
    if (empty($erros) && isset($dados['periodo_inicio'], $dados['periodo_fim'])
        && $dados['periodo_fim'] < $dados['periodo_inicio']) {
        $erros[] = ['field' => 'periodo_fim', 'code' => 'PERIODO_INVALIDO', 'message' => 'A data fim não pode ser anterior ao início.'];
    }
    if (isset($dados['status']) && !in_array($dados['status'], CAMPANHA_STATUS, true)) {
        $erros[] = ['field' => 'status', 'code' => 'STATUS_INVALIDO', 'message' => 'Status inválido.'];
    }
    return $erros;
}

/** Normaliza a lista de vacinas do payload: [{vacina_id, doses_previstas}]. */
function normalizar_vacinas($vacinas): array
{
    if (!is_array($vacinas)) {
        return [];
    }
    $saida = [];
    foreach ($vacinas as $v) {
        if (!is_array($v) || empty($v['vacina_id']) || !is_numeric($v['vacina_id'])) {
            continue;
        }
        $saida[(int) $v['vacina_id']] = [
            'vacina_id'       => (int) $v['vacina_id'],
            'doses_previstas' => isset($v['doses_previstas']) && is_numeric($v['doses_previstas'])
                ? max(1, (int) $v['doses_previstas']) : 1,
        ];
    }
    return array_values($saida); // dedup por vacina_id
}

/** Insere as vacinas da campanha, validando que cada vacina existe e está ativa. */
function gravar_vacinas_campanha(int $campanhaId, int $tenantId, array $vacinas): void
{
    foreach ($vacinas as $v) {
        $existe = db_primeiro(
            "SELECT id FROM vacina WHERE id = :id AND status = 'ativa' LIMIT 1",
            [':id' => $v['vacina_id']]
        );
        if ($existe === null) {
            // Rollback será feito pelo chamador (transação aberta).
            responder_erro('Vacina inválida na lista.', 422, [
                ['field' => 'vacinas', 'code' => 'VACINA_INVALIDA', 'message' => "Vacina {$v['vacina_id']} inexistente ou inativa."],
            ]);
        }
        db_executar(
            "INSERT INTO campanha_vacina (tenant_id, campanha_id, vacina_id, doses_previstas)
             VALUES (:tenant, :campanha, :vacina, :doses)",
            [
                ':tenant'   => $tenantId,
                ':campanha' => $campanhaId,
                ':vacina'   => $v['vacina_id'],
                ':doses'    => $v['doses_previstas'],
            ]
        );
    }
}

/** Lê page/por_pagina do querystring com limites seguros. Devolve [page, porPagina, offset]. */
function paginacao(): array
{
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $porPagina = isset($_GET['por_pagina']) && is_numeric($_GET['por_pagina'])
        ? min(100, max(1, (int) $_GET['por_pagina'])) : 50;
    $offset = ($page - 1) * $porPagina;
    return [$page, $porPagina, $offset];
}
