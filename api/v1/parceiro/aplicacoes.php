<?php
// ============================================================================
// api/v1/parceiro/aplicacoes.php
// Função: rede credenciada consulta elegível por CPF e registra vacinado via API.
// Grupo parceiro (Bearer rede_credenciada + escopo por campanha — RN-009).
// ============================================================================

require_once BASE_PATH . '/app/services/aplicacoes.php';

/** GET /api/v1/parceiro/campanhas/{id}/elegiveis/{cpf} — consulta elegível. */
function rota_parceiro_consultar_elegivel(array $params): void
{
    $cred = exigir_credencial('rede_credenciada');
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_escopo_campanha($cred, $id);

    $cpf = so_digitos($params['cpf'] ?? '');
    // RN-012: a clínica só enxerga elegíveis atribuídos a ela.
    $eleg = db_primeiro(
        "SELECT e.id AS elegivel_id, e.status, p.cpf, p.nome
           FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
          WHERE e.campanha_id = :c AND p.cpf = :cpf AND e.clinica_id = :clinica LIMIT 1",
        [':c' => $id, ':cpf' => $cpf, ':clinica' => (int) $cred['titular_id']]
    );
    if ($eleg === null) {
        responder_erro('CPF não elegível para esta clínica nesta campanha.', 404, [
            ['field' => null, 'code' => 'NAO_ELEGIVEL', 'message' => 'Sem elegibilidade atribuída a esta clínica.'],
        ]);
    }

    $vacinas = db_todos(
        "SELECT cv.vacina_id, v.nome, cv.doses_previstas
           FROM campanha_vacina cv JOIN vacina v ON v.id = cv.vacina_id
          WHERE cv.campanha_id = :c ORDER BY v.nome",
        [':c' => $id]
    );

    responder_sucesso([
        'elegivel_id'       => (int) $eleg['elegivel_id'],
        'paciente'          => ['cpf' => $eleg['cpf'], 'nome' => $eleg['nome']],
        'status'            => $eleg['status'],
        'vacinas_previstas' => $vacinas,
    ], 'Elegível encontrado.');
}

/** POST /api/v1/parceiro/aplicacoes — registra vacinado (rede credenciada). */
function rota_parceiro_registrar_aplicacao(array $params): void
{
    $cred = exigir_credencial('rede_credenciada');

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['elegivel_id', 'vacina_id', 'dose', 'lote', 'aplicado_em']);
    if ($erros) {
        erro_validacao($erros);
    }

    $eleg = campanha_do_elegivel((int) $dados['elegivel_id']);
    if ($eleg === null) {
        responder_erro('Paciente não elegível.', 422, [
            ['field' => 'elegivel_id', 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível inexistente.'],
        ]);
    }
    // Escopo: a campanha do elegível precisa ser a da credencial.
    exigir_escopo_campanha($cred, (int) $eleg['campanha_id']);
    // RN-012: a clínica só registra vacinado de elegível atribuído a ela.
    if ((int) ($eleg['clinica_id'] ?? 0) !== (int) $cred['titular_id']) {
        responder_erro('Elegível não pertence a esta clínica.', 403, [
            ['field' => 'elegivel_id', 'code' => 'FORA_DO_ESCOPO', 'message' => 'Este elegível não está atribuído à sua clínica.'],
        ]);
    }

    $res = registrar_aplicacao([
        'elegivel_id'       => (int) $dados['elegivel_id'],
        'vacina_id'         => (int) $dados['vacina_id'],
        'dose'              => (int) $dados['dose'],
        'lote'              => $dados['lote'],
        'via_administracao' => $dados['via_administracao'] ?? null,
        'local_aplicacao'   => $dados['local_aplicacao'] ?? null,
        'aplicado_em'       => $dados['aplicado_em'],
        'executor_tipo'     => 'clinica_credenciada',
        'executor_id'       => (int) $cred['titular_id'],
        'origem'            => 'api',
        'criado_por'        => null,
    ]);

    registrar_auditoria('aplicacao.registrada', [
        'tenant_id'     => $res['tenant_id'],
        'ator_tipo'     => 'credencial_api',
        'ator_id'       => (int) $cred['id'],
        'origem'        => 'api_parceiro',
        'entidade_tipo' => 'aplicacao',
        'entidade_id'   => $res['aplicacao_id'],
    ]);

    responder_sucesso([
        'aplicacao_id'    => $res['aplicacao_id'],
        'status'          => 'confirmada',
        'elegivel_status' => 'aplicado',
    ], 'Aplicação registrada.', 201);
}
