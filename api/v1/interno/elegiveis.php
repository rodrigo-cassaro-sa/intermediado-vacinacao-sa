<?php
// ============================================================================
// api/v1/interno/elegiveis.php
// Função: importar elegíveis (upload CSV OU JSON) e listar elegíveis da campanha.
// Grupo interno (sessão + CSRF). Base: docs/09, RN-002/003/008.
// ============================================================================

require_once BASE_PATH . '/app/services/elegiveis.php';
require_once BASE_PATH . '/app/services/importacao.php';

const PERFIS_IMPORTA_ELEGIVEIS = ['super_admin', 'operador_interno', 'cliente_b2b'];

/** POST /api/v1/interno/campanhas/{id}/elegiveis/importar */
function rota_importar_elegiveis(array $params): void
{
    executar_importacao_interno($params, false);
}

/** POST /api/v1/interno/campanhas/{id}/elegiveis/sincronizar — sync (remove ausentes). */
function rota_sincronizar_elegiveis(array $params): void
{
    executar_importacao_interno($params, true);
}

/** Corpo compartilhado: importa (ou sincroniza) elegíveis via upload CSV ou JSON. */
function executar_importacao_interno(array $params, bool $sincronizar): void
{
    $usuario = exigir_login();
    if (!in_array($usuario['perfil'], PERFIS_IMPORTA_ELEGIVEIS, true)) {
        responder_erro('Sem permissão para importar elegíveis.', 403, [
            ['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Seu perfil não permite esta ação.'],
        ]);
    }
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);
    if ($campanha['status'] === 'encerrada') {
        responder_erro('Campanha encerrada.', 422, [
            ['field' => null, 'code' => 'CAMPANHA_ENCERRADA', 'message' => 'Não é possível importar em campanha encerrada.'],
        ]);
    }

    // Fonte dos dados: arquivo (multipart CSV) OU JSON { elegiveis: [...] }.
    if (!empty($_FILES['arquivo']['tmp_name']) && is_uploaded_file($_FILES['arquivo']['tmp_name'])) {
        if (($_FILES['arquivo']['size'] ?? 0) > 20 * 1024 * 1024) {
            responder_erro('Arquivo muito grande (máx. 20MB).', 400, [
                ['field' => 'arquivo', 'code' => 'ARQUIVO_GRANDE', 'message' => 'Envie um arquivo de até 20MB.'],
            ]);
        }
        $ext = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'txt'], true)) {
            responder_erro('Formato inválido. Envie CSV.', 400, [
                ['field' => 'arquivo', 'code' => 'ARQUIVO_INVALIDO', 'message' => 'Apenas .csv ou .txt.'],
            ]);
        }
        $conteudo = (string) file_get_contents($_FILES['arquivo']['tmp_name']);
        $formato = 'csv';
    } else {
        $dados = corpo_json();
        if (empty($dados['elegiveis'])) {
            responder_erro('Envie um arquivo CSV ou a lista "elegiveis".', 400, [
                ['field' => 'elegiveis', 'code' => 'SEM_DADOS', 'message' => 'Nenhum elegível informado.'],
            ]);
        }
        $conteudo = json_encode(['elegiveis' => $dados['elegiveis']], JSON_UNESCAPED_UNICODE);
        $formato = 'json';
    }

    $r = importacao_iniciar((int) $campanha['tenant_id'], $id, $conteudo, $formato, 'upload',
        (int) $usuario['id'], ator_usuario($usuario), $sincronizar);

    registrar_auditoria('elegiveis.importados', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['importacao_id' => $r['importacao_id'], 'status' => $r['status'], 'sincronizar' => $sincronizar ? 1 : 0],
    ]);

    responder_sucesso($r, $r['status'] === 'concluida'
        ? ($sincronizar ? 'Sincronização processada.' : 'Importação processada.')
        : 'Recebido; processando em segundo plano.', 201);
}

/**
 * POST /api/v1/interno/elegiveis/{id}/situacao — marca recusado/ausente/inelegivel
 * (com motivo) ou volta para pendente. RN-020.
 */
function rota_definir_situacao_elegivel(array $params): void
{
    $usuario = exigir_login();
    if (!in_array($usuario['perfil'], ['super_admin', 'operador_interno', 'profissional_saude'], true)) {
        responder_erro('Sem permissão.', 403, [
            ['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Seu perfil não permite esta ação.'],
        ]);
    }
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $eleg = db_primeiro(
        "SELECT e.id, e.campanha_id, c.tenant_id
           FROM elegivel e JOIN campanha c ON c.id = e.campanha_id
          WHERE e.id = :id LIMIT 1",
        [':id' => $id]
    );
    if ($eleg === null) {
        responder_erro('Elegível inexistente.', 404, [
            ['field' => null, 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível não encontrado.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $eleg['campanha_id']);

    $dados = corpo_json();
    $res = alterar_situacao_elegivel($id, $dados['status'] ?? '', $dados['motivo'] ?? '');
    if (!$res['ok']) {
        responder_erro($res['message'], $res['http'], [
            ['field' => null, 'code' => $res['code'], 'message' => $res['message']],
        ]);
    }

    registrar_auditoria('elegivel.situacao_definida', [
        'tenant_id'     => (int) $eleg['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'elegivel',
        'entidade_id'   => $id,
        'metadata'      => ['status' => $dados['status'] ?? '', 'motivo' => $dados['motivo'] ?? ''],
    ]);
    historico_elegivel($id, 'situacao_alterada', ator_usuario($usuario), null,
        ['status' => $dados['status'] ?? ''], (string) ($dados['motivo'] ?? ''));

    responder_sucesso(['elegivel_id' => $id, 'status' => $dados['status']], 'Situação atualizada.');
}

/**
 * POST /api/v1/interno/elegiveis/situacao-lote
 * Aplica a MESMA situação (pendente|recusado|ausente|inelegivel) a vários
 * elegíveis. Body: { ids: [int], status, motivo }. Escopo verificado por item;
 * inexistentes, fora do escopo ou já vacinados são ignorados e reportados.
 */
function rota_definir_situacao_elegiveis_lote(array $params): void
{
    $usuario = exigir_login();
    if (!in_array($usuario['perfil'], ['super_admin', 'operador_interno', 'profissional_saude'], true)) {
        responder_erro('Sem permissão.', 403, [
            ['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Seu perfil não permite esta ação.'],
        ]);
    }
    exigir_csrf();

    $dados  = corpo_json();
    $ids    = array_values(array_unique(array_filter(
        array_map('intval', (array) ($dados['ids'] ?? [])),
        fn($v) => $v > 0
    )));
    $status = (string) ($dados['status'] ?? '');
    $motivo = (string) ($dados['motivo'] ?? '');

    if (!$ids) {
        responder_erro('Nenhum elegível informado.', 400, [
            ['field' => 'ids', 'code' => 'IDS_OBRIGATORIOS', 'message' => 'Envie ao menos um id.'],
        ]);
    }
    if (count($ids) > 5000) {
        responder_erro('Lote muito grande (máx. 5000).', 400, [
            ['field' => 'ids', 'code' => 'LOTE_GRANDE', 'message' => 'Divida em lotes de até 5000.'],
        ]);
    }
    // Mesma regra do individual (RN-020): status permitido e motivo quando != pendente.
    if (!in_array($status, ['pendente', 'recusado', 'ausente', 'inelegivel'], true)) {
        responder_erro('Situação inválida.', 400, [
            ['field' => 'status', 'code' => 'STATUS_INVALIDO', 'message' => 'Use pendente, recusado, ausente ou inelegivel.'],
        ]);
    }
    if ($status !== 'pendente' && trim($motivo) === '') {
        responder_erro('Informe o motivo.', 400, [
            ['field' => 'motivo', 'code' => 'MOTIVO_OBRIGATORIO', 'message' => 'Motivo obrigatório quando não é pendente.'],
        ]);
    }

    // Carrega todos os elegíveis de uma vez (id, campanha, tenant).
    $ph = []; $bind = [];
    foreach ($ids as $i => $id) { $ph[] = ":i$i"; $bind[":i$i"] = $id; }
    $rows = db_todos(
        "SELECT e.id, e.campanha_id, c.tenant_id
           FROM elegivel e JOIN campanha c ON c.id = e.campanha_id
          WHERE e.id IN (" . implode(',', $ph) . ")",
        $bind
    );
    $mapa = [];
    foreach ($rows as $r) { $mapa[(int) $r['id']] = $r; }

    $ator = ator_usuario($usuario);
    $atualizados = 0;
    $ignorados = [];
    foreach ($ids as $id) {
        $r = $mapa[$id] ?? null;
        if ($r === null) { $ignorados[] = ['id' => $id, 'code' => 'NAO_ENCONTRADO']; continue; }
        $tenantId = (int) $r['tenant_id'];
        if (!usuario_eh_interno($usuario) && !usuario_pode_cliente($usuario, $tenantId)) {
            $ignorados[] = ['id' => $id, 'code' => 'FORA_ESCOPO']; continue;
        }
        $res = alterar_situacao_elegivel($id, $status, $motivo);
        if (!$res['ok']) { $ignorados[] = ['id' => $id, 'code' => $res['code']]; continue; }

        registrar_auditoria('elegivel.situacao_definida', [
            'tenant_id'     => $tenantId,
            'ator_tipo'     => 'usuario',
            'ator_id'       => (int) $usuario['id'],
            'origem'        => 'admin',
            'entidade_tipo' => 'elegivel',
            'entidade_id'   => $id,
            'metadata'      => ['status' => $status, 'motivo' => $motivo, 'lote' => 1],
        ]);
        historico_elegivel($id, 'situacao_alterada', $ator, null, ['status' => $status], $motivo);
        $atualizados++;
    }

    responder_sucesso([
        'total'       => count($ids),
        'atualizados' => $atualizados,
        'ignorados'   => $ignorados,
    ], 'Lote processado.');
}

/**
 * POST /api/v1/interno/campanhas/{id}/elegiveis/remover
 * Remove (soft) elegíveis por CPF — turnover/limpeza. Não remove quem já foi
 * vacinado (integridade e faturamento). Body: { cpfs: [...] }. Item 6.
 */
function rota_remover_elegiveis(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    $dados = corpo_json();
    if (empty($dados['cpfs']) || !is_array($dados['cpfs'])) {
        responder_erro('Envie a lista de CPFs.', 400, [
            ['field' => 'cpfs', 'code' => 'CPFS_OBRIGATORIOS', 'message' => 'Nenhum CPF informado.'],
        ]);
    }

    $removidos = 0; $bloqueadosVacinados = []; $naoEncontrados = [];
    foreach ($dados['cpfs'] as $cpfBruto) {
        $cpf = so_digitos((string) $cpfBruto);
        $eleg = db_primeiro(
            "SELECT e.id, e.status FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
              WHERE e.campanha_id = :c AND p.cpf = :cpf LIMIT 1",
            [':c' => $id, ':cpf' => $cpf]
        );
        if ($eleg === null) { $naoEncontrados[] = mascarar_cpf($cpf); continue; }
        if ($eleg['status'] === 'aplicado') { $bloqueadosVacinados[] = mascarar_cpf($cpf); continue; }

        db_executar("UPDATE elegivel SET status = 'removido', motivo_situacao = 'removido da lista' WHERE id = :id",
            [':id' => (int) $eleg['id']]);
        historico_elegivel((int) $eleg['id'], 'situacao_alterada', ator_usuario($usuario), null,
            ['status' => 'removido'], 'removido da lista');
        $removidos++;
    }

    registrar_auditoria('elegiveis.removidos', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['removidos' => $removidos],
    ]);

    responder_sucesso([
        'removidos'             => $removidos,
        'bloqueados_vacinados'  => $bloqueadosVacinados,
        'nao_encontrados'       => $naoEncontrados,
    ], 'Remoção processada.');
}

/** GET /api/v1/interno/campanhas/{id}/elegiveis — lista + resumo. */
function rota_listar_elegiveis(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    $campanha = exigir_campanha_do_usuario($usuario, $id);

    // Restrição por unidade (usuário local vê só a sua unidade — doc 04 §4.1).
    [$fUni, $bUni] = filtro_unidade_sql($usuario, (int) $campanha['tenant_id'], 'e');

    // Paginação por cursor (keyset) — escala em milhões (item 10).
    [$apos, $porPagina] = paginacao_keyset();

    // Resumo por status (respeita a restrição de unidade).
    $resumoLinhas = db_todos(
        "SELECT status, COUNT(*) AS total FROM elegivel e WHERE e.campanha_id = :id$fUni GROUP BY status",
        array_merge([':id' => $id], $bUni)
    );
    $resumo = ['pendente' => 0, 'aplicado' => 0, 'recusado' => 0, 'inelegivel' => 0, 'ausente' => 0, 'expirado' => 0, 'removido' => 0];
    foreach ($resumoLinhas as $r) {
        $resumo[$r['status']] = (int) $r['total'];
    }
    $total = array_sum($resumo);

    $where = 'e.campanha_id = :id' . $fUni;
    $bind  = array_merge([':id' => $id], $bUni);
    if ($apos > 0) {
        $where .= ' AND e.id < :apos';   // ordem por id DESC
        $bind[':apos'] = $apos;
    }
    // Filtros opcionais (compõem com o keyset): tipo de vínculo, status e busca.
    if (!empty($_GET['tipo'])) {
        $where .= ' AND e.tipo_vinculo = :tipo';
        $bind[':tipo'] = (string) $_GET['tipo'];
    }
    if (!empty($_GET['status'])) {
        $where .= ' AND e.status = :st';
        $bind[':st'] = (string) $_GET['status'];
    }
    if (isset($_GET['q']) && trim((string) $_GET['q']) !== '') {
        $q = trim((string) $_GET['q']);
        $partes = ['COALESCE(e.nome, p.nome) LIKE :q', 'e.codigo_rh LIKE :q'];
        $bind[':q'] = '%' . $q . '%';
        $qd = so_digitos($q);
        if ($qd !== '') {              // só filtra por CPF quando a busca tem dígitos
            $partes[] = 'p.cpf LIKE :qd';
            $bind[':qd'] = '%' . $qd . '%';
        }
        $where .= ' AND (' . implode(' OR ', $partes) . ')';
    }

    $itens = db_todos(
        "SELECT e.id, p.cpf, COALESCE(e.nome, p.nome) AS nome, e.origem, e.tipo_vinculo, e.cpf_titular,
                e.codigo_lotacao, e.codigo_rh, COALESCE(e.data_nascimento, p.data_nascimento) AS data_nascimento,
                u.nome AS unidade, e.status, e.motivo_situacao, cc.nome AS clinica, e.criado_em
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
      LEFT JOIN clinica_credenciada cc ON cc.id = e.clinica_id
      LEFT JOIN unidade u ON u.id = e.unidade_id
          WHERE $where
          ORDER BY e.id DESC
          LIMIT $porPagina",
        $bind
    );
    foreach ($itens as &$it) {
        $it['cpf'] = mascarar_cpf($it['cpf']);
        if (!empty($it['cpf_titular'])) {
            $it['cpf_titular'] = mascarar_cpf($it['cpf_titular']);
        }
    }
    unset($it);

    $proximo = count($itens) === $porPagina ? (int) end($itens)['id'] : null;

    responder_sucesso(['resumo' => $resumo, 'itens' => $itens], 'OK.', 200, [
        'por_pagina' => $porPagina, 'total' => $total, 'proximo_cursor' => $proximo,
    ]);
}
