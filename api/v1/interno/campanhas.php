<?php
// ============================================================================
// api/v1/interno/campanhas.php
// Função: CRUD de campanhas (RN-001) e gestão das vacinas da campanha.
// Grupo interno (sessão + CSRF). Base: docs/09, docs/08. tenant_id = cliente_b2b.
// ============================================================================

const CAMPANHA_MODALIDADES = ['rede_credenciada', 'in_company'];
const CAMPANHA_STATUS      = ['rascunho', 'ativa', 'encerrada'];
const PERFIS_INTERNOS      = ['super_admin', 'operador_interno'];

/**
 * POST /api/v1/interno/campanhas — cria campanha com CÓDIGO automático (migration 026).
 * Regra: exatamente 1 vacina por campanha; o código é gerado no formato
 * VAC.TEMP.MOD.GRP.CLI.SEQ. O campo nome é opcional (rótulo humano).
 */
function rota_criar_campanha(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['cliente_b2b_id', 'modalidade', 'periodo_inicio', 'periodo_fim', 'temporada']);
    $erros = array_merge($erros, validar_dados_campanha($dados));
    if (!isset($dados['temporada']) || !is_numeric($dados['temporada'])
        || (int) $dados['temporada'] < 2000 || (int) $dados['temporada'] > 2100) {
        $erros[] = ['field' => 'temporada', 'code' => 'TEMPORADA_INVALIDA', 'message' => 'Informe um ano entre 2000 e 2100.'];
    }
    if ($erros) {
        erro_validacao($erros);
    }

    $clienteId = (int) $dados['cliente_b2b_id'];
    $temporada = (int) $dados['temporada'];

    // Cliente (tenant) precisa existir e estar ativo.
    $cliente = db_primeiro(
        "SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL AND status = 'ativo' LIMIT 1",
        [':id' => $clienteId]
    );
    if ($cliente === null) {
        responder_erro('Cliente B2B inexistente ou inativo.', 422, [
            ['field' => 'cliente_b2b_id', 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente B2B inválido.'],
        ]);
    }

    // Exatamente 1 vacina (aceita vacina_id direto ou uma lista de tamanho 1).
    $vacinas = normalizar_vacinas($dados['vacinas'] ?? []);
    if (!$vacinas && !empty($dados['vacina_id']) && is_numeric($dados['vacina_id'])) {
        $vacinas = [['vacina_id' => (int) $dados['vacina_id'], 'doses_previstas' => 1]];
    }
    if (count($vacinas) !== 1) {
        responder_erro('Informe exatamente uma vacina.', 422, [
            ['field' => 'vacina_id', 'code' => 'VACINA_UNICA', 'message' => 'A campanha deve ter exatamente uma vacina.'],
        ]);
    }
    $vacinaId = $vacinas[0]['vacina_id'];

    // Monta o prefixo do código (valida presença das siglas — 422 se faltar).
    $prefixo = prefixo_codigo_campanha($clienteId, $vacinaId, $dados['modalidade'], $temporada);
    $nome = isset($dados['nome']) && trim((string) $dados['nome']) !== '' ? trim($dados['nome']) : null;

    // Grava com retry: o índice único uq_campanha_codigo protege contra corrida.
    $campanhaId = 0;
    $codigo = '';
    for ($tentativa = 0; $tentativa < 5; $tentativa++) {
        $seq = proxima_seq_codigo($prefixo);
        $codigo = $prefixo . '.' . $seq;
        try {
            pdo()->beginTransaction();
            db_executar(
                "INSERT INTO campanha (tenant_id, nome, codigo, temporada, seq, modalidade, periodo_inicio, periodo_fim, status, criado_por)
                 VALUES (:tenant_id, :nome, :codigo, :temporada, :seq, :modalidade, :inicio, :fim, :status, :criado_por)",
                [
                    ':tenant_id'  => $clienteId,
                    ':nome'       => $nome,
                    ':codigo'     => $codigo,
                    ':temporada'  => $temporada,
                    ':seq'        => $seq,
                    ':modalidade' => $dados['modalidade'],
                    ':inicio'     => $dados['periodo_inicio'],
                    ':fim'        => $dados['periodo_fim'],
                    ':status'     => $dados['status'] ?? 'rascunho',
                    ':criado_por' => (int) $usuario['id'],
                ]
            );
            $campanhaId = (int) db_ultimo_id();
            gravar_vacinas_campanha($campanhaId, $clienteId, $vacinas);
            pdo()->commit();
            break;
        } catch (PDOException $e) {
            if (pdo()->inTransaction()) {
                pdo()->rollBack();
            }
            // 1062 = entrada duplicada (código colidiu numa corrida) => tenta o próximo SEQ.
            if ((int) ($e->errorInfo[1] ?? 0) === 1062 && $tentativa < 4) {
                continue;
            }
            throw $e;
        }
    }

    registrar_auditoria('campanha.criada', [
        'tenant_id'     => $clienteId,
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $campanhaId,
        'metadata'      => ['codigo' => $codigo],
    ]);

    responder_sucesso(
        ['campanha_id' => $campanhaId, 'codigo' => $codigo, 'temporada' => $temporada],
        'Campanha criada.',
        201
    );
}

/** POST /api/v1/interno/vacinas/{id}/sigla — define/atualiza a sigla da vacina. */
function rota_definir_sigla_vacina(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $dados = corpo_json();
    $sigla = normalizar_sigla($dados['sigla'] ?? '');
    if ($sigla === null) {
        responder_erro('Sigla inválida.', 422, [
            ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'],
        ]);
    }

    $vac = db_primeiro("SELECT id FROM vacina WHERE id = :id LIMIT 1", [':id' => $id]);
    if ($vac === null) {
        responder_erro('Vacina não encontrada.', 404, [
            ['field' => null, 'code' => 'VACINA_NAO_ENCONTRADA', 'message' => 'Vacina inexistente.'],
        ]);
    }
    $dup = db_primeiro("SELECT id FROM vacina WHERE sigla = :s AND id <> :id LIMIT 1", [':s' => $sigla, ':id' => $id]);
    if ($dup !== null) {
        responder_erro('Sigla já usada por outra vacina.', 409, [
            ['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.'],
        ]);
    }

    db_executar("UPDATE vacina SET sigla = :s WHERE id = :id", [':s' => $sigla, ':id' => $id]);
    responder_sucesso(['vacina_id' => $id, 'sigla' => $sigla], 'Sigla atualizada.');
}

/** GET /api/v1/interno/campanhas — lista campanhas do escopo. */
function rota_listar_campanhas(array $params): void
{
    $usuario = exigir_login();

    [$page, $porPagina, $offset] = paginacao();

    $where = 'c.excluido_em IS NULL';
    $bind  = [];

    // Escopo hierárquico: interna vê tudo; demais só clientes acessíveis (doc 04 §4.1).
    $acessiveis = clientes_acessiveis_pelo_usuario($usuario);
    if ($acessiveis !== ['*']) {
        if (!$acessiveis) {
            responder_sucesso(['itens' => []], 'OK.', 200, ['page' => $page, 'por_pagina' => $porPagina, 'total' => 0]);
        }
        $ph = [];
        foreach ($acessiveis as $i => $cid) { $ph[] = ":c_$i"; $bind[":c_$i"] = (int) $cid; }
        $where .= ' AND c.tenant_id IN (' . implode(',', $ph) . ')';
    } elseif (!empty($_GET['cliente_b2b_id']) && is_numeric($_GET['cliente_b2b_id'])) {
        // Interno pode filtrar por cliente opcionalmente.
        $where .= ' AND c.tenant_id = :tenant';
        $bind[':tenant'] = (int) $_GET['cliente_b2b_id'];
    }

    $total = (int) (db_primeiro("SELECT COUNT(*) AS t FROM campanha c WHERE $where", $bind)['t'] ?? 0);

    $itens = db_todos(
        "SELECT c.id, c.tenant_id AS cliente_b2b_id, cb.razao_social AS cliente,
                c.codigo, c.temporada, c.nome, c.modalidade, c.periodo_inicio, c.periodo_fim, c.status, c.criado_em
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
            'codigo'         => $campanha['codigo'] ?? null,
            'temporada'      => isset($campanha['temporada']) ? (int) $campanha['temporada'] : null,
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

/**
 * POST /api/v1/interno/campanhas/{id}/encerrar
 * Encerra a campanha e expira os elegíveis pendentes (RN-015).
 */
function rota_encerrar_campanha(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    try {
        pdo()->beginTransaction();
        db_executar("UPDATE campanha SET status = 'encerrada' WHERE id = :id", [':id' => $id]);
        $stmt = db_executar(
            "UPDATE elegivel SET status = 'expirado' WHERE campanha_id = :id AND status = 'pendente'",
            [':id' => $id]
        );
        $expirados = $stmt->rowCount();
        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('campanha.encerrada', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['elegiveis_expirados' => $expirados],
    ]);

    responder_sucesso(['campanha_id' => $id, 'elegiveis_expirados' => $expirados], 'Campanha encerrada.');
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

/** GET /api/v1/interno/vacinas — catálogo de vacinas ativas (para montar campanha). */
function rota_listar_vacinas(array $params): void
{
    exigir_login();
    // ?todas=1 => inclui inativas (tela de gestão); padrão só ativas (montar campanha).
    $where = !empty($_GET['todas']) ? '1 = 1' : "status = 'ativa'";
    $itens = db_todos(
        "SELECT id, nome, sigla, fabricante, doses_padrao, status
           FROM vacina WHERE $where ORDER BY nome"
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

const VACINA_STATUS = ['ativa', 'inativa'];

/** POST /api/v1/interno/vacinas — cadastra uma vacina (produto do catálogo). */
function rota_criar_vacina(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['nome']);

    $sigla = null;
    if (isset($dados['sigla']) && trim((string) $dados['sigla']) !== '') {
        $sigla = normalizar_sigla($dados['sigla']);
        if ($sigla === null) $erros[] = ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'];
    }
    $doses = isset($dados['doses_padrao']) && is_numeric($dados['doses_padrao']) ? max(1, (int) $dados['doses_padrao']) : 1;
    $status = $dados['status'] ?? 'ativa';
    if (!in_array($status, VACINA_STATUS, true)) $erros[] = ['field' => 'status', 'code' => 'STATUS_INVALIDO', 'message' => 'Use ativa ou inativa.'];
    if ($erros) erro_validacao($erros);

    if ($sigla !== null && db_primeiro("SELECT id FROM vacina WHERE sigla = :s LIMIT 1", [':s' => $sigla]) !== null) {
        responder_erro('Sigla já usada por outra vacina.', 409, [['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Escolha uma sigla única.']]);
    }

    db_executar(
        "INSERT INTO vacina (nome, sigla, fabricante, doses_padrao, status) VALUES (:nome, :sigla, :fab, :doses, :status)",
        [
            ':nome'   => trim($dados['nome']),
            ':sigla'  => $sigla,
            ':fab'    => isset($dados['fabricante']) && trim((string) $dados['fabricante']) !== '' ? trim($dados['fabricante']) : null,
            ':doses'  => $doses,
            ':status' => $status,
        ]
    );
    $id = (int) db_ultimo_id();

    registrar_auditoria('vacina.criada', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'vacina', 'entidade_id' => $id, 'metadata' => ['sigla' => $sigla],
    ]);
    responder_sucesso(['vacina_id' => $id], 'Vacina cadastrada.', 201);
}

/** PUT /api/v1/interno/vacinas/{id} — edita campos do catálogo da vacina. */
function rota_editar_vacina(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, PERFIS_INTERNOS);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    if (db_primeiro("SELECT id FROM vacina WHERE id = :id LIMIT 1", [':id' => $id]) === null) {
        responder_erro('Vacina não encontrada.', 404, [['field' => null, 'code' => 'VACINA_NAO_ENCONTRADA', 'message' => 'Vacina inexistente.']]);
    }

    $dados = corpo_json();
    $campos = [];
    $bind = [':id' => $id];
    $erros = [];

    if (isset($dados['nome']) && trim($dados['nome']) !== '') { $campos[] = 'nome = :nome'; $bind[':nome'] = trim($dados['nome']); }
    if (array_key_exists('fabricante', $dados)) {
        $campos[] = 'fabricante = :fab';
        $bind[':fab'] = trim((string) $dados['fabricante']) !== '' ? trim($dados['fabricante']) : null;
    }
    if (isset($dados['doses_padrao']) && is_numeric($dados['doses_padrao'])) { $campos[] = 'doses_padrao = :doses'; $bind[':doses'] = max(1, (int) $dados['doses_padrao']); }
    if (isset($dados['status'])) {
        if (!in_array($dados['status'], VACINA_STATUS, true)) $erros[] = ['field' => 'status', 'code' => 'STATUS_INVALIDO', 'message' => 'Use ativa ou inativa.'];
        else { $campos[] = 'status = :status'; $bind[':status'] = $dados['status']; }
    }
    if (isset($dados['sigla'])) {
        $sigla = trim((string) $dados['sigla']) === '' ? null : normalizar_sigla($dados['sigla']);
        if ($dados['sigla'] !== '' && $sigla === null) {
            $erros[] = ['field' => 'sigla', 'code' => 'SIGLA_INVALIDA', 'message' => 'Use 3 caracteres (A-Z, 0-9).'];
        } elseif ($sigla !== null && db_primeiro("SELECT id FROM vacina WHERE sigla = :s AND id <> :id LIMIT 1", [':s' => $sigla, ':id' => $id]) !== null) {
            $erros[] = ['field' => 'sigla', 'code' => 'SIGLA_DUPLICADA', 'message' => 'Sigla já usada por outra vacina.'];
        } else { $campos[] = 'sigla = :sigla'; $bind[':sigla'] = $sigla; }
    }

    if ($erros) erro_validacao($erros);
    if (!$campos) {
        responder_erro('Nada para atualizar.', 400, [['field' => null, 'code' => 'SEM_ALTERACAO', 'message' => 'Informe ao menos um campo.']]);
    }

    db_executar('UPDATE vacina SET ' . implode(', ', $campos) . ' WHERE id = :id', $bind);
    registrar_auditoria('vacina.editada', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'admin',
        'entidade_tipo' => 'vacina', 'entidade_id' => $id, 'metadata' => ['campos' => array_keys($dados)],
    ]);
    responder_sucesso(['vacina_id' => $id], 'Vacina atualizada.');
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
