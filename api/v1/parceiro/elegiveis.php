<?php
// ============================================================================
// api/v1/parceiro/elegiveis.php
// Função: ingestão de elegíveis pelo cliente B2B via API (token + escopo).
// Grupo parceiro (Bearer). Base: docs/09, RN-002/009. Mesmo serviço do upload.
// ============================================================================

require_once BASE_PATH . '/app/services/elegiveis.php';

/** POST /api/v1/parceiro/campanhas/{id}/elegiveis */
function rota_parceiro_ingerir_elegiveis(array $params): void
{
    $credencial = exigir_credencial('ingestao_b2b');
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_escopo_campanha($credencial, $id);

    $campanha = db_primeiro(
        "SELECT id, tenant_id, status FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1",
        [':id' => $id]
    );
    if ($campanha === null) {
        responder_erro('Campanha inexistente.', 404, [
            ['field' => null, 'code' => 'CAMPANHA_NAO_ENCONTRADA', 'message' => 'Campanha não encontrada.'],
        ]);
    }
    if ($campanha['status'] === 'encerrada') {
        responder_erro('Campanha encerrada.', 422, [
            ['field' => null, 'code' => 'CAMPANHA_ENCERRADA', 'message' => 'Campanha encerrada.'],
        ]);
    }

    $dados = corpo_json();
    if (empty($dados['elegiveis'])) {
        responder_erro('Estrutura inválida.', 400, [
            ['field' => 'elegiveis', 'code' => 'PAYLOAD_INVALIDO', 'message' => 'Envie a lista "elegiveis".'],
        ]);
    }
    $lista = normalizar_elegiveis_json($dados['elegiveis']);

    try {
        pdo()->beginTransaction();

        db_executar(
            "INSERT INTO importacao_elegiveis (tenant_id, campanha_id, origem, status, criado_em)
             VALUES (:tenant, :campanha, 'api', 'processando', NOW())",
            [':tenant' => (int) $campanha['tenant_id'], ':campanha' => $id]
        );
        $importacaoId = (int) db_ultimo_id();

        $res = ingerir_elegiveis($id, (int) $campanha['tenant_id'], $lista, 'api', $importacaoId);

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
        'ator_tipo'     => 'credencial_api',
        'ator_id'       => (int) $credencial['id'],
        'origem'        => 'api_parceiro',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['importacao_id' => $importacaoId, 'recebidos' => $res['recebidos']],
    ]);

    responder_sucesso([
        'recebidos'  => $res['recebidos'],
        'criados'    => $res['criados'],
        'atualizados' => $res['atualizados'],
        'rejeitados' => $res['rejeitados'],
        'erros'      => $res['erros'],
    ], 'Elegíveis recebidos.', 201);
}
