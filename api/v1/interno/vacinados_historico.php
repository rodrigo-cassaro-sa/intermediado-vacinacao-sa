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

    // Aceita CSV (texto) ou lista JSON "vacinados". A lista JSON é convertida em CSV
    // para reaproveitar a mesma fila/worker do caminho por arquivo.
    $dados = corpo_json();
    if (isset($dados['csv']) && is_string($dados['csv']) && trim($dados['csv']) !== '') {
        $csv = $dados['csv'];
    } elseif (isset($dados['vacinados']) && is_array($dados['vacinados']) && $dados['vacinados']) {
        $csv = vacinados_json_para_csv($dados['vacinados']);
    } else {
        responder_erro('Nada para importar.', 422, [
            ['field' => 'vacinados', 'code' => 'LISTA_VAZIA', 'message' => 'Envie "csv" (texto) ou "vacinados" (lista).'],
        ]);
    }

    $res = historico_import_iniciar($tenantId, $csv, ator_usuario($usuario));

    if ($res['status'] === 'concluida') {
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
                'vacinas_criadas'    => $res['vacinas_criadas'],
            ],
        ]);
        responder_sucesso($res, 'Importação de histórico concluída.');
    }

    // Assíncrono: enfileirado; o worker processa e o status fica em /importacoes-historico/{id}.
    responder_sucesso($res, 'Importação enfileirada; acompanhe o status.', 202);
}

/** GET /api/v1/interno/importacoes-historico/{id} — status/progresso do lote. */
function rota_status_importacao_historico(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);

    $job = db_primeiro("SELECT * FROM importacao_historico WHERE id = :id LIMIT 1", [':id' => (int) ($params['id'] ?? 0)]);
    if ($job === null) {
        responder_erro('Importação inexistente.', 404, [
            ['field' => null, 'code' => 'IMPORTACAO_NAO_ENCONTRADA', 'message' => 'Não encontrada.'],
        ]);
    }
    responder_sucesso([
        'importacao_id'      => (int) $job['id'],
        'status'             => $job['status'],
        'total_linhas'       => (int) $job['total_linhas'],
        'total_processados'  => (int) $job['total_processados'],
        'aplicacoes_criadas' => (int) $job['total_aplicacoes'],
        'duplicados'         => (int) $job['total_duplicados'],
        'rejeitados'         => (int) $job['total_rejeitados'],
        'campanhas_criadas'  => (int) $job['total_campanhas'],
        'vacinas_criadas'    => (int) $job['total_vacinas'],
        'erros'              => $job['erros_amostra'] ? json_decode($job['erros_amostra'], true) : [],
        'mensagem_erro'      => $job['mensagem_erro'],
        'iniciado_em'        => $job['iniciado_em'],
        'finalizado_em'      => $job['finalizado_em'],
    ], 'OK.');
}

/** Converte lista JSON de vacinados no CSV canônico do histórico. */
function vacinados_json_para_csv(array $itens): string
{
    $cols = ['cpf', 'nome', 'data_nascimento', 'vacina', 'dose', 'lote', 'aplicado_em', 'codigo_lotacao', 'cidade', 'uf', 'identificador'];
    $linhas = [implode(',', $cols)];
    foreach ($itens as $it) {
        if (!is_array($it)) { continue; }
        $campos = [];
        foreach ($cols as $c) {
            $v = (string) ($it[$c] ?? '');
            // Escapa vírgula/aspas/quebra de linha para CSV.
            if (preg_match('/[",\r\n]/', $v)) { $v = '"' . str_replace('"', '""', $v) . '"'; }
            $campos[] = $v;
        }
        $linhas[] = implode(',', $campos);
    }
    return implode("\n", $linhas);
}
