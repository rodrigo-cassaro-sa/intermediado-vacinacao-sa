<?php
// ============================================================================
// api/v1/parceiro/incompany.php
// Fase C: API do app IN COMPANY por token (compatível com PWA, app próprio ou
// sistema de terceiro). Token tipo 'app_in_company', escopo por campanha.
// Sem isolamento por clínica (in company atende todos os elegíveis da campanha).
// ============================================================================

require_once BASE_PATH . '/app/services/aplicacoes.php';

/** GET /api/v1/parceiro/incompany/campanhas/{id}/elegiveis/{cpf} — consulta elegível. */
function rota_incompany_consultar_elegivel(array $params): void
{
    $cred = exigir_credencial('app_in_company');
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_escopo_campanha($cred, $id);

    $raw = trim((string) ($params['cpf'] ?? ''));
    $cpf = so_digitos($raw);
    // Aceita CPF ou identificador/voucher.
    if (validar_cpf($cpf)) {
        $eleg = db_primeiro(
            "SELECT e.id AS elegivel_id, e.status, p.cpf, COALESCE(e.nome, p.nome) AS nome
               FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
              WHERE e.campanha_id = :c AND p.cpf = :v LIMIT 1",
            [':c' => $id, ':v' => $cpf]
        );
    } else {
        $eleg = db_primeiro(
            "SELECT e.id AS elegivel_id, e.status, p.cpf, COALESCE(e.nome, p.nome) AS nome
               FROM elegivel e JOIN paciente p ON p.id = e.paciente_id
              WHERE e.campanha_id = :c AND p.identificador = :v LIMIT 1",
            [':c' => $id, ':v' => $raw]
        );
    }
    if ($eleg === null) {
        responder_erro('CPF não elegível nesta campanha.', 404, [
            ['field' => null, 'code' => 'NAO_ELEGIVEL', 'message' => 'Sem elegibilidade nesta campanha.'],
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
        'paciente'          => ['cpf' => mascarar_cpf($eleg['cpf']), 'nome' => $eleg['nome']],
        'status'            => $eleg['status'],
        'vacinas_previstas' => $vacinas,
    ], 'Elegível encontrado.');
}

/** POST /api/v1/parceiro/incompany/aplicacoes — registra vacinado (app in company). */
function rota_incompany_registrar_aplicacao(array $params): void
{
    $cred = exigir_credencial('app_in_company');
    idempotencia_replay('credencial:' . $cred['id']);

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
    exigir_escopo_campanha($cred, (int) $eleg['campanha_id']);

    $res = registrar_aplicacao(_incompany_ctx($dados, $cred));

    registrar_auditoria('aplicacao.registrada', [
        'tenant_id' => $res['tenant_id'], 'ator_tipo' => 'credencial_api', 'ator_id' => (int) $cred['id'],
        'origem' => 'api_parceiro', 'entidade_tipo' => 'aplicacao', 'entidade_id' => $res['aplicacao_id'],
    ]);
    historico_aplicacao($res['aplicacao_id'], 'registrada', ator_credencial($cred));
    historico_elegivel((int) $dados['elegivel_id'], 'vacinado', ator_credencial($cred), null, ['aplicacao_id' => $res['aplicacao_id']]);

    responder_idempotente('credencial:' . $cred['id'], [
        'aplicacao_id' => $res['aplicacao_id'], 'status' => 'confirmada', 'elegivel_status' => 'aplicado',
    ], 'Aplicação registrada.', 201);
}

/** POST /api/v1/parceiro/incompany/aplicacoes-lote — registra vários (app in company). */
function rota_incompany_registrar_aplicacoes_lote(array $params): void
{
    $cred = exigir_credencial('app_in_company');
    idempotencia_replay('credencial:' . $cred['id']);

    $dados = corpo_json();
    if (empty($dados['aplicacoes']) || !is_array($dados['aplicacoes'])) {
        responder_erro('Envie a lista "aplicacoes".', 400, [
            ['field' => 'aplicacoes', 'code' => 'PAYLOAD_INVALIDO', 'message' => 'Nenhuma aplicação informada.'],
        ]);
    }

    $confirmados = 0; $rejeitados = 0; $itens = [];
    foreach ($dados['aplicacoes'] as $i => $ap) {
        $indice = $i + 1;
        if (!is_array($ap) || empty($ap['elegivel_id'])) {
            $rejeitados++; $itens[] = ['indice' => $indice, 'ok' => false, 'code' => 'CAMPO_OBRIGATORIO']; continue;
        }
        $eleg = campanha_do_elegivel((int) $ap['elegivel_id']);
        if ($eleg === null || !credencial_tem_escopo($cred, (int) $eleg['campanha_id'])) {
            $rejeitados++; $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => false, 'code' => 'FORA_DO_ESCOPO']; continue;
        }
        $res = processar_aplicacao(_incompany_ctx($ap, $cred));
        if ($res['ok']) {
            $confirmados++;
            historico_aplicacao($res['aplicacao_id'], 'registrada', ator_credencial($cred), 'lote');
            historico_elegivel((int) $ap['elegivel_id'], 'vacinado', ator_credencial($cred), null, ['aplicacao_id' => $res['aplicacao_id']]);
            $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => true, 'aplicacao_id' => $res['aplicacao_id']];
        } else {
            $rejeitados++; $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => false, 'code' => $res['code']];
        }
    }

    registrar_auditoria('aplicacoes.lote_registradas', [
        'ator_tipo' => 'credencial_api', 'ator_id' => (int) $cred['id'], 'origem' => 'api_parceiro',
        'entidade_tipo' => 'aplicacao', 'metadata' => ['recebidos' => count($dados['aplicacoes']), 'confirmados' => $confirmados],
    ]);

    responder_idempotente('credencial:' . $cred['id'], [
        'recebidos' => count($dados['aplicacoes']), 'confirmados' => $confirmados, 'rejeitados' => $rejeitados, 'itens' => $itens,
    ], 'Lote processado.', 201);
}

/** Monta o contexto de aplicação para o app in company (executor = credencial). */
function _incompany_ctx(array $ap, array $cred): array
{
    return [
        'elegivel_id'       => (int) $ap['elegivel_id'],
        'vacina_id'         => (int) ($ap['vacina_id'] ?? 0),
        'dose'              => (int) ($ap['dose'] ?? 0),
        'lote'              => $ap['lote'] ?? '',
        'via_administracao' => $ap['via_administracao'] ?? null,
        'local_aplicacao'   => $ap['local_aplicacao'] ?? null,
        'cidade'            => $ap['cidade'] ?? null,
        'uf'                => $ap['uf'] ?? null,
        'unidade'           => $ap['unidade'] ?? null,
        'profissional_nome' => $ap['profissional_nome'] ?? null,
        'profissional_cpf'  => $ap['profissional_cpf'] ?? null,
        'aplicado_em'       => $ap['aplicado_em'] ?? '',
        'executor_tipo'     => 'app_in_company',
        'executor_id'       => (int) $cred['id'],
        'origem'            => 'app',
        'criado_por'        => null,
    ];
}
