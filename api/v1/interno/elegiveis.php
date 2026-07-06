<?php
// ============================================================================
// api/v1/interno/elegiveis.php
// Função: importar elegíveis (upload CSV OU JSON) e listar elegíveis da campanha.
// Grupo interno (sessão + CSRF). Base: docs/09, RN-002/003/008.
// ============================================================================

require_once BASE_PATH . '/app/services/elegiveis.php';

const PERFIS_IMPORTA_ELEGIVEIS = ['super_admin', 'operador_interno', 'cliente_b2b'];

/** POST /api/v1/interno/campanhas/{id}/elegiveis/importar */
function rota_importar_elegiveis(array $params): void
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

    // Fonte dos dados: arquivo (multipart) OU JSON { elegiveis: [...] }.
    $arquivoNome = null;
    if (!empty($_FILES['arquivo']['tmp_name']) && is_uploaded_file($_FILES['arquivo']['tmp_name'])) {
        if (($_FILES['arquivo']['size'] ?? 0) > 5 * 1024 * 1024) {
            responder_erro('Arquivo muito grande (máx. 5MB).', 400, [
                ['field' => 'arquivo', 'code' => 'ARQUIVO_GRANDE', 'message' => 'Envie um arquivo de até 5MB.'],
            ]);
        }
        $ext = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'txt'], true)) {
            responder_erro('Formato inválido. Envie CSV.', 400, [
                ['field' => 'arquivo', 'code' => 'ARQUIVO_INVALIDO', 'message' => 'Apenas .csv ou .txt.'],
            ]);
        }
        $conteudo = file_get_contents($_FILES['arquivo']['tmp_name']);
        $lista = parsear_csv_elegiveis((string) $conteudo);
        $arquivoNome = basename($_FILES['arquivo']['name']);
    } else {
        $dados = corpo_json();
        if (empty($dados['elegiveis'])) {
            responder_erro('Envie um arquivo CSV ou a lista "elegiveis".', 400, [
                ['field' => 'elegiveis', 'code' => 'SEM_DADOS', 'message' => 'Nenhum elegível informado.'],
            ]);
        }
        $lista = normalizar_elegiveis_json($dados['elegiveis']);
    }

    if (!$lista) {
        responder_erro('Nenhuma linha válida encontrada.', 400, [
            ['field' => null, 'code' => 'ARQUIVO_VAZIO', 'message' => 'O conteúdo não tinha linhas processáveis.'],
        ]);
    }

    // Registra o lote e processa dentro de transação.
    try {
        pdo()->beginTransaction();

        db_executar(
            "INSERT INTO importacao_elegiveis (tenant_id, campanha_id, origem, arquivo, status, criado_por, criado_em)
             VALUES (:tenant, :campanha, 'upload', :arquivo, 'processando', :criado_por, NOW())",
            [
                ':tenant'     => (int) $campanha['tenant_id'],
                ':campanha'   => $id,
                ':arquivo'    => $arquivoNome,
                ':criado_por' => (int) $usuario['id'],
            ]
        );
        $importacaoId = (int) db_ultimo_id();

        $res = ingerir_elegiveis($id, (int) $campanha['tenant_id'], $lista, 'upload', $importacaoId);

        db_executar(
            "UPDATE importacao_elegiveis
                SET total_linhas = :t, total_validos = :v, total_invalidos = :inv, status = 'concluida'
              WHERE id = :id",
            [
                ':t'   => $res['recebidos'],
                ':v'   => $res['criados'] + $res['atualizados'],
                ':inv' => $res['rejeitados'],
                ':id'  => $importacaoId,
            ]
        );

        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('elegiveis.importados', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['importacao_id' => $importacaoId, 'recebidos' => $res['recebidos'], 'criados' => $res['criados']],
    ]);

    responder_sucesso([
        'importacao_id'   => $importacaoId,
        'total_linhas'    => $res['recebidos'],
        'total_validos'   => $res['criados'] + $res['atualizados'],
        'novos_elegiveis' => $res['criados'],
        'ja_existentes'   => $res['atualizados'],
        'total_invalidos' => $res['rejeitados'],
        'invalidos'       => $res['erros'],
    ], 'Importação processada.', 201);
}

/** GET /api/v1/interno/campanhas/{id}/elegiveis — lista + resumo. */
function rota_listar_elegiveis(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_campanha_do_usuario($usuario, $id);

    [$page, $porPagina, $offset] = paginacao();

    $total = (int) (db_primeiro(
        "SELECT COUNT(*) AS t FROM elegivel WHERE campanha_id = :id", [':id' => $id]
    )['t'] ?? 0);

    // Resumo por status (base da tabela verdade — RN-005).
    $resumoLinhas = db_todos(
        "SELECT status, COUNT(*) AS total FROM elegivel WHERE campanha_id = :id GROUP BY status",
        [':id' => $id]
    );
    $resumo = ['pendente' => 0, 'aplicado' => 0, 'recusado' => 0, 'inelegivel' => 0, 'ausente' => 0];
    foreach ($resumoLinhas as $r) {
        $resumo[$r['status']] = (int) $r['total'];
    }

    $itens = db_todos(
        "SELECT e.id, p.cpf, p.nome, e.origem, e.status, e.criado_em
           FROM elegivel e
           JOIN paciente p ON p.id = e.paciente_id
          WHERE e.campanha_id = :id
          ORDER BY p.nome
          LIMIT $porPagina OFFSET $offset",
        [':id' => $id]
    );
    // Mascara CPF na listagem (docs/10).
    foreach ($itens as &$it) {
        $it['cpf'] = mascarar_cpf($it['cpf']);
    }
    unset($it);

    responder_sucesso(['resumo' => $resumo, 'itens' => $itens], 'OK.', 200, [
        'page' => $page, 'por_pagina' => $porPagina, 'total' => $total,
    ]);
}
