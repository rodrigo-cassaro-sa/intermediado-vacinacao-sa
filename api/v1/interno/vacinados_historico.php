<?php
// ============================================================================
// api/v1/interno/vacinados_historico.php
// Importação retrocompatível de vacinados de anos anteriores (RN-027).
// INTERNO-ONLY (super_admin/operador_interno): burla campanha ativa/período,
// então não fica exposto ao portal. Base: app/services/historico_import.php.
// ============================================================================

require_once BASE_PATH . '/app/services/historico_import.php';

/** POST /api/v1/interno/clientes/{id}/vacinados-historico/importar */
function rota_importar_vacinados_historico(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $tenantId = (int) ($params['id'] ?? 0);
    $cliente = db_primeiro("SELECT id FROM cliente_b2b WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $tenantId]);
    if ($cliente === null) {
        responder_erro('Cliente inexistente.', 404, [
            ['field' => 'id', 'code' => 'CLIENTE_NAO_ENCONTRADO', 'message' => 'Cliente não encontrado.'],
        ]);
    }

    $dados = corpo_json();
    if (isset($dados['csv']) && is_string($dados['csv']) && trim($dados['csv']) !== '') {
        $linhas = parsear_csv_vacinados_historico($dados['csv']);
    } elseif (isset($dados['vacinados']) && is_array($dados['vacinados'])) {
        $linhas = $dados['vacinados'];
    } else {
        $linhas = [];
    }
    if (!$linhas) {
        responder_erro('Nada para importar.', 422, [
            ['field' => 'vacinados', 'code' => 'LISTA_VAZIA', 'message' => 'Envie "csv" (texto) ou "vacinados" (lista).'],
        ]);
    }
    // Guarda de tamanho: import interno é síncrono; peça para fatiar arquivos enormes.
    if (count($linhas) > 20000) {
        responder_erro('Lote muito grande.', 422, [
            ['field' => 'vacinados', 'code' => 'LOTE_MUITO_GRANDE', 'message' => 'Máximo 20.000 linhas por envio; fatie o arquivo.'],
        ]);
    }

    $res = importar_vacinados_historico($tenantId, $linhas, ator_usuario($usuario));

    // Uma auditoria por lote (evita tempestade de webhooks por linha).
    registrar_auditoria('vacinados.importados_historico', [
        'tenant_id'     => $tenantId,
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'cliente_b2b',
        'entidade_id'   => $tenantId,
        'metadata'      => [
            'recebidos'          => $res['recebidos'],
            'aplicacoes_criadas' => $res['aplicacoes_criadas'],
            'duplicados'         => $res['duplicados'],
            'rejeitados'         => $res['rejeitados'],
            'campanhas_criadas'  => $res['campanhas_criadas'],
        ],
    ]);

    responder_sucesso($res, 'Importação de histórico concluída.');
}
