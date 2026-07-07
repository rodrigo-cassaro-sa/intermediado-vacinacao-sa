<?php
// ============================================================================
// api/v1/interno/aplicacoes.php
// Função: registrar aplicação (app in company) e retificar. Grupo interno.
// Base: docs/09, RN-003/010. Registro imutável — correção gera novo registro.
// ============================================================================

require_once BASE_PATH . '/app/services/aplicacoes.php';

const PERFIS_APLICA = ['super_admin', 'operador_interno', 'profissional_saude'];

/** POST /api/v1/interno/aplicacoes */
function rota_registrar_aplicacao(array $params): void
{
    $usuario = exigir_login();
    if (!in_array($usuario['perfil'], PERFIS_APLICA, true)) {
        responder_erro('Sem permissão para registrar aplicação.', 403, [
            ['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Seu perfil não permite esta ação.'],
        ]);
    }
    exigir_csrf();

    $dados = corpo_json();
    $erros = exigir_campos($dados, ['elegivel_id', 'vacina_id', 'dose', 'lote', 'aplicado_em']);
    if ($erros) {
        erro_validacao($erros);
    }

    // Escopo: a campanha do elegível precisa ser acessível ao usuário.
    $eleg = campanha_do_elegivel((int) $dados['elegivel_id']);
    if ($eleg === null) {
        responder_erro('Paciente não elegível.', 422, [
            ['field' => 'elegivel_id', 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível inexistente.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $eleg['campanha_id']);

    $res = registrar_aplicacao([
        'elegivel_id'       => (int) $dados['elegivel_id'],
        'vacina_id'         => (int) $dados['vacina_id'],
        'dose'              => (int) $dados['dose'],
        'lote'              => $dados['lote'],
        'via_administracao' => $dados['via_administracao'] ?? null,
        'local_aplicacao'   => $dados['local_aplicacao'] ?? null,
        'cidade'            => $dados['cidade'] ?? null,
        'uf'                => $dados['uf'] ?? null,
        'unidade'           => $dados['unidade'] ?? null,
        'profissional_nome' => $dados['profissional_nome'] ?? null,
        'profissional_cpf'  => $dados['profissional_cpf'] ?? null,
        'aplicado_em'       => $dados['aplicado_em'],
        'executor_tipo'     => 'profissional_saude',
        'executor_id'       => (int) $usuario['id'],
        'origem'            => 'app',
        'criado_por'        => (int) $usuario['id'],
    ]);

    registrar_auditoria('aplicacao.registrada', [
        'tenant_id'     => $res['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'app',
        'entidade_tipo' => 'aplicacao',
        'entidade_id'   => $res['aplicacao_id'],
    ]);
    historico_aplicacao($res['aplicacao_id'], 'registrada', ator_usuario($usuario));
    historico_elegivel((int) $dados['elegivel_id'], 'vacinado', ator_usuario($usuario), null,
        ['aplicacao_id' => $res['aplicacao_id']]);

    responder_sucesso([
        'aplicacao_id'    => $res['aplicacao_id'],
        'status'          => 'confirmada',
        'elegivel_status' => 'aplicado',
    ], 'Aplicação registrada.', 201);
}

/**
 * POST /api/v1/interno/aplicacoes/{id}/estornar — "desvacinar" (RN-022).
 * Marca a aplicação como estornada, volta o elegível para pendente (permite
 * relançar depois) e registra motivo + histórico. Imutabilidade preservada.
 */
function rota_estornar_aplicacao(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $dados = corpo_json();
    if (empty($dados['motivo'])) {
        responder_erro('Informe o motivo do estorno.', 400, [
            ['field' => 'motivo', 'code' => 'MOTIVO_OBRIGATORIO', 'message' => 'Motivo é obrigatório.'],
        ]);
    }

    $ap = db_primeiro("SELECT id, campanha_id, elegivel_id, tenant_id FROM aplicacao WHERE id = :id AND status = 'confirmada' LIMIT 1", [':id' => $id]);
    if ($ap === null) {
        responder_erro('Aplicação não encontrada.', 404, [
            ['field' => null, 'code' => 'APLICACAO_NAO_ENCONTRADA', 'message' => 'Registro inexistente ou não confirmado.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $ap['campanha_id']);

    try {
        pdo()->beginTransaction();
        db_executar("UPDATE aplicacao SET status = 'estornada' WHERE id = :id", [':id' => $id]);
        db_executar("UPDATE elegivel SET status = 'pendente', motivo_situacao = NULL WHERE id = :id", [':id' => (int) $ap['elegivel_id']]);
        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('aplicacao.estornada', [
        'tenant_id'     => (int) $ap['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'aplicacao',
        'entidade_id'   => $id,
        'metadata'      => ['motivo' => $dados['motivo']],
    ]);
    historico_aplicacao($id, 'estornada', ator_usuario($usuario), trim((string) $dados['motivo']));
    historico_elegivel((int) $ap['elegivel_id'], 'desvacinado', ator_usuario($usuario), null,
        ['aplicacao_id' => $id], trim((string) $dados['motivo']));

    responder_sucesso(['aplicacao_id' => $id, 'elegivel_status' => 'pendente'], 'Aplicação estornada.');
}

/** GET /api/v1/interno/aplicacoes/{id}/historico — trilha da aplicação. */
function rota_historico_aplicacao(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    $id = (int) ($params['id'] ?? 0);
    $ap = db_primeiro("SELECT campanha_id FROM aplicacao WHERE id = :id LIMIT 1", [':id' => $id]);
    if ($ap === null) {
        responder_erro('Aplicação não encontrada.', 404, [
            ['field' => null, 'code' => 'APLICACAO_NAO_ENCONTRADA', 'message' => 'Registro inexistente.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $ap['campanha_id']);
    $itens = db_todos(
        "SELECT id, evento, ator_tipo, ator_id, motivo, criado_em
           FROM aplicacao_historico WHERE aplicacao_id = :id ORDER BY id DESC",
        [':id' => $id]
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/**
 * POST /api/v1/interno/aplicacoes-lote — registra várias aplicações de uma vez.
 * Body: { aplicacoes: [ {elegivel_id, vacina_id, dose, lote, aplicado_em, ...}, ... ] }
 * Processa item a item (não para no 1º erro) e devolve relatório por índice.
 */
function rota_registrar_aplicacoes_lote(array $params): void
{
    $usuario = exigir_login();
    if (!in_array($usuario['perfil'], PERFIS_APLICA, true)) {
        responder_erro('Sem permissão para registrar aplicação.', 403, [
            ['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Seu perfil não permite esta ação.'],
        ]);
    }
    exigir_csrf();

    $dados = corpo_json();
    if (empty($dados['aplicacoes']) || !is_array($dados['aplicacoes'])) {
        responder_erro('Envie a lista "aplicacoes".', 400, [
            ['field' => 'aplicacoes', 'code' => 'SEM_DADOS', 'message' => 'Nenhuma aplicação informada.'],
        ]);
    }

    $confirmados = 0; $rejeitados = 0; $itens = [];
    foreach ($dados['aplicacoes'] as $i => $ap) {
        $indice = $i + 1;
        if (!is_array($ap) || empty($ap['elegivel_id'])) {
            $rejeitados++;
            $itens[] = ['indice' => $indice, 'ok' => false, 'code' => 'CAMPO_OBRIGATORIO'];
            continue;
        }
        // Escopo por item: a campanha do elegível precisa ser acessível ao usuário.
        $eleg = campanha_do_elegivel((int) $ap['elegivel_id']);
        if ($eleg === null) {
            $rejeitados++;
            $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => false, 'code' => 'NAO_ELEGIVEL'];
            continue;
        }
        if (!usuario_pode_campanha($usuario, (int) $eleg['campanha_id'])) {
            $rejeitados++;
            $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => false, 'code' => 'FORA_DO_ESCOPO'];
            continue;
        }

        $res = processar_aplicacao([
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
            'executor_tipo'     => 'profissional_saude',
            'executor_id'       => (int) $usuario['id'],
            'origem'            => 'app',
            'criado_por'        => (int) $usuario['id'],
        ]);

        if ($res['ok']) {
            $confirmados++;
            historico_aplicacao($res['aplicacao_id'], 'registrada', ator_usuario($usuario), 'lote');
            historico_elegivel((int) $ap['elegivel_id'], 'vacinado', ator_usuario($usuario), null, ['aplicacao_id' => $res['aplicacao_id']]);
            $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => true, 'aplicacao_id' => $res['aplicacao_id']];
        } else {
            $rejeitados++;
            $itens[] = ['indice' => $indice, 'elegivel_id' => (int) $ap['elegivel_id'], 'ok' => false, 'code' => $res['code']];
        }
    }

    registrar_auditoria('aplicacoes.lote_registradas', [
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'app',
        'entidade_tipo' => 'aplicacao',
        'metadata'      => ['recebidos' => count($dados['aplicacoes']), 'confirmados' => $confirmados],
    ]);

    responder_sucesso([
        'recebidos'   => count($dados['aplicacoes']),
        'confirmados' => $confirmados,
        'rejeitados'  => $rejeitados,
        'itens'       => $itens,
    ], 'Lote processado.', 201);
}

/** Verifica (sem encerrar) se o usuário pode operar na campanha. */
function usuario_pode_campanha(array $usuario, int $campanhaId): bool
{
    $internos = ['super_admin', 'operador_interno'];
    if (in_array($usuario['perfil'], $internos, true)) {
        return true;
    }
    $c = db_primeiro("SELECT tenant_id FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $campanhaId]);
    return $c !== null && $usuario['tenant_id'] !== null && (int) $c['tenant_id'] === (int) $usuario['tenant_id'];
}

/** POST /api/v1/interno/aplicacoes/{id}/retificar — cria novo registro (RN-010). */
function rota_retificar_aplicacao(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno']);
    exigir_csrf();

    $id = (int) ($params['id'] ?? 0);
    $dados = corpo_json();
    if (empty($dados['motivo'])) {
        responder_erro('Informe o motivo da retificação.', 400, [
            ['field' => 'motivo', 'code' => 'MOTIVO_OBRIGATORIO', 'message' => 'Motivo é obrigatório.'],
        ]);
    }

    $orig = db_primeiro(
        "SELECT * FROM aplicacao WHERE id = :id AND status = 'confirmada' LIMIT 1",
        [':id' => $id]
    );
    if ($orig === null) {
        responder_erro('Aplicação não encontrada.', 404, [
            ['field' => null, 'code' => 'APLICACAO_NAO_ENCONTRADA', 'message' => 'Registro inexistente ou já retificado.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $orig['campanha_id']);

    // Campos corrigidos (ou mantém os originais).
    $vacinaId = isset($dados['vacina_id']) ? (int) $dados['vacina_id'] : (int) $orig['vacina_id'];
    $dose     = isset($dados['dose']) ? (int) $dados['dose'] : (int) $orig['dose'];
    $lote     = isset($dados['lote']) ? trim((string) $dados['lote']) : $orig['lote'];
    $aplicadoEm = $dados['aplicado_em'] ?? $orig['aplicado_em'];

    try {
        pdo()->beginTransaction();

        // Marca a original como retificada.
        db_executar("UPDATE aplicacao SET status = 'retificada' WHERE id = :id", [':id' => $id]);

        // Cria o novo registro referenciando a origem.
        db_executar(
            "INSERT INTO aplicacao
                (tenant_id, campanha_id, elegivel_id, paciente_id, vacina_id, dose, lote,
                 via_administracao, local_aplicacao, cidade, uf, unidade,
                 profissional_nome, profissional_cpf, executor_tipo, executor_id, origem,
                 status, aplicacao_origem_id, motivo_retificacao, aplicado_em, criado_por)
             VALUES
                (:tenant, :campanha, :elegivel, :paciente, :vacina, :dose, :lote,
                 :via, :local, :cidade, :uf, :unidade,
                 :pnome, :pcpf, :etipo, :eid, :origem,
                 'confirmada', :origem_id, :motivo, :aplicado_em, :criado_por)",
            [
                ':tenant'      => (int) $orig['tenant_id'],
                ':campanha'    => (int) $orig['campanha_id'],
                ':elegivel'    => (int) $orig['elegivel_id'],
                ':paciente'    => (int) $orig['paciente_id'],
                ':vacina'      => $vacinaId,
                ':dose'        => $dose,
                ':lote'        => $lote,
                ':via'         => $orig['via_administracao'],
                ':local'       => $orig['local_aplicacao'],
                ':cidade'      => $orig['cidade'],
                ':uf'          => $orig['uf'],
                ':unidade'     => $orig['unidade'],
                ':pnome'       => $orig['profissional_nome'],
                ':pcpf'        => $orig['profissional_cpf'],
                ':etipo'       => $orig['executor_tipo'],
                ':eid'         => (int) $orig['executor_id'],
                ':origem'      => $orig['origem'],
                ':origem_id'   => $id,
                ':motivo'      => trim((string) $dados['motivo']),
                ':aplicado_em' => $aplicadoEm,
                ':criado_por'  => (int) $usuario['id'],
            ]
        );
        $novaId = (int) db_ultimo_id();

        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    registrar_auditoria('aplicacao.retificada', [
        'tenant_id'     => (int) $orig['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'aplicacao',
        'entidade_id'   => $novaId,
        'metadata'      => ['aplicacao_origem_id' => $id, 'motivo' => $dados['motivo']],
    ]);
    historico_aplicacao($id, 'retificada', ator_usuario($usuario), trim((string) $dados['motivo']));
    historico_aplicacao($novaId, 'registrada', ator_usuario($usuario), 'retificação de #' . $id);

    responder_sucesso(['aplicacao_id' => $novaId, 'aplicacao_origem_id' => $id], 'Aplicação retificada.', 201);
}
