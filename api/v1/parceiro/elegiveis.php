<?php
// ============================================================================
// api/v1/parceiro/elegiveis.php
// Função: ingestão de elegíveis pelo cliente B2B via API (token + escopo).
// Grupo parceiro (Bearer). Base: docs/09, RN-002/009. Mesmo serviço do upload.
// ============================================================================

require_once BASE_PATH . '/app/services/elegiveis.php';
require_once BASE_PATH . '/app/services/importacao.php';

/**
 * POST /api/v1/parceiro/elegiveis/{id}/situacao — clínica marca recusa/ausência do
 * seu elegível (RN-020), respeitando escopo (RN-009) e isolamento por clínica (RN-012).
 */
function rota_parceiro_definir_situacao(array $params): void
{
    $cred = exigir_credencial('rede_credenciada');
    $id = (int) ($params['id'] ?? 0);
    $eleg = db_primeiro("SELECT id, campanha_id, clinica_id FROM elegivel WHERE id = :id LIMIT 1", [':id' => $id]);
    if ($eleg === null) {
        responder_erro('Elegível inexistente.', 404, [
            ['field' => null, 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível não encontrado.'],
        ]);
    }
    exigir_escopo_campanha($cred, (int) $eleg['campanha_id']);
    if ((int) ($eleg['clinica_id'] ?? 0) !== (int) $cred['titular_id']) {
        responder_erro('Elegível não pertence a esta clínica.', 403, [
            ['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Sem acesso a este elegível.'],
        ]);
    }

    $dados = corpo_json();
    $res = alterar_situacao_elegivel($id, $dados['status'] ?? '', $dados['motivo'] ?? '');
    if (!$res['ok']) {
        responder_erro($res['message'], $res['http'], [
            ['field' => null, 'code' => $res['code'], 'message' => $res['message']],
        ]);
    }

    registrar_auditoria('elegivel.situacao_definida', [
        'ator_tipo'     => 'credencial_api',
        'ator_id'       => (int) $cred['id'],
        'origem'        => 'api_parceiro',
        'entidade_tipo' => 'elegivel',
        'entidade_id'   => $id,
        'metadata'      => ['status' => $dados['status'] ?? ''],
    ]);
    historico_elegivel($id, 'situacao_alterada', ator_credencial($cred), null,
        ['status' => $dados['status'] ?? ''], (string) ($dados['motivo'] ?? ''));

    responder_sucesso(['elegivel_id' => $id, 'status' => $dados['status']], 'Situação atualizada.');
}

/** POST /api/v1/parceiro/campanhas/{id}/elegiveis */
function rota_parceiro_ingerir_elegiveis(array $params): void
{
    $credencial = exigir_credencial('ingestao_b2b');
    idempotencia_replay('credencial:' . $credencial['id']);
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

    $conteudo = json_encode(['elegiveis' => $dados['elegiveis']], JSON_UNESCAPED_UNICODE);
    $r = importacao_iniciar((int) $campanha['tenant_id'], $id, $conteudo, 'json', 'api',
        null, ator_credencial($credencial));

    registrar_auditoria('elegiveis.importados', [
        'tenant_id'     => (int) $campanha['tenant_id'],
        'ator_tipo'     => 'credencial_api',
        'ator_id'       => (int) $credencial['id'],
        'origem'        => 'api_parceiro',
        'entidade_tipo' => 'campanha',
        'entidade_id'   => $id,
        'metadata'      => ['importacao_id' => $r['importacao_id'], 'status' => $r['status']],
    ]);

    responder_idempotente('credencial:' . $credencial['id'], $r, 'Elegíveis recebidos.', 201);
}
