<?php
// ============================================================================
// api/v1/interno/relatorios.php
// Item 9c: carteira de vacinação consolidada (perpétua, por CPF) e relatório
// ano a ano de campanhas por cliente. Grupo interno.
//  - Internos veem tudo; cliente_b2b vê só o próprio tenant (LGPD).
// ============================================================================

/**
 * GET /api/v1/interno/pacientes/{cpf}/carteira
 * Carteira consolidada: todas as aplicações confirmadas da pessoa (todos os anos).
 */
function rota_carteira_paciente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno', 'cliente_b2b']);

    // Identidade por CPF (validado) OU por identificador/voucher (RN-028).
    $raw = trim((string) ($params['cpf'] ?? ''));
    $cpf = so_digitos($raw);
    if (validar_cpf($cpf)) {
        $paciente = db_primeiro("SELECT id, cpf, identificador, nome FROM paciente WHERE cpf = :v LIMIT 1", [':v' => $cpf]);
    } else {
        $paciente = db_primeiro("SELECT id, cpf, identificador, nome FROM paciente WHERE identificador = :v LIMIT 1", [':v' => $raw]);
    }
    if ($paciente === null) {
        responder_erro('Paciente não encontrado.', 404, [
            ['field' => null, 'code' => 'PACIENTE_NAO_ENCONTRADO', 'message' => 'Sem cadastro para este CPF/identificador.'],
        ]);
    }

    // Escopo: cliente_b2b só vê as aplicações das próprias campanhas (LGPD).
    $where = "a.paciente_id = :pid AND a.status = 'confirmada'";
    $bind  = [':pid' => (int) $paciente['id']];
    if (!in_array($usuario['perfil'], ['super_admin', 'operador_interno'], true)) {
        if ($usuario['tenant_id'] === null) {
            responder_erro('Sem permissão.', 403, [['field' => null, 'code' => 'SEM_PERMISSAO', 'message' => 'Acesso negado.']]);
        }
        $where .= ' AND a.tenant_id = :tenant';
        $bind[':tenant'] = (int) $usuario['tenant_id'];
    }

    $doses = db_todos(
        "SELECT a.id, a.aplicado_em, a.dose, a.lote, v.nome AS vacina,
                c.nome AS campanha, cb.razao_social AS cliente,
                a.cidade, a.uf, a.unidade, a.profissional_nome
           FROM aplicacao a
           JOIN vacina v      ON v.id = a.vacina_id
           JOIN campanha c    ON c.id = a.campanha_id
           JOIN cliente_b2b cb ON cb.id = a.tenant_id
          WHERE $where
          ORDER BY a.aplicado_em DESC",
        $bind
    );

    // Auditoria de acesso a dado sensível de saúde (docs/10).
    registrar_auditoria('carteira.consultada', [
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'paciente',
        'entidade_id'   => (int) $paciente['id'],
        'metadata'      => ['doses' => count($doses)],
    ]);

    $identidade = $paciente['cpf'] ? mascarar_cpf($paciente['cpf']) : ('voucher:' . $paciente['identificador']);
    responder_sucesso([
        'paciente' => ['identidade' => $identidade, 'nome' => $paciente['nome']],
        'total_doses' => count($doses),
        'doses'   => $doses,
    ], 'OK.');
}

/**
 * GET /api/v1/interno/clientes/{id}/campanhas-resumo
 * Relatório ano a ano: campanhas do cliente com cobertura, ordenadas por período.
 */
function rota_resumo_campanhas_cliente(array $params): void
{
    $usuario = exigir_login();
    exigir_perfil($usuario, ['super_admin', 'operador_interno', 'cliente_b2b']);

    $clienteId = (int) ($params['id'] ?? 0);
    // Escopo: cliente_b2b só o próprio.
    if (!in_array($usuario['perfil'], ['super_admin', 'operador_interno'], true)
        && (int) ($usuario['tenant_id'] ?? 0) !== $clienteId) {
        responder_erro('Sem acesso a este cliente.', 403, [
            ['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Acesso negado.'],
        ]);
    }

    $campanhas = db_todos(
        "SELECT c.id, c.nome, c.modalidade, c.periodo_inicio, c.periodo_fim, c.status,
                YEAR(c.periodo_inicio) AS ano,
                (SELECT COUNT(*) FROM elegivel e WHERE e.campanha_id = c.id) AS elegiveis,
                (SELECT COUNT(*) FROM elegivel e WHERE e.campanha_id = c.id AND e.status = 'aplicado') AS aplicados
           FROM campanha c
          WHERE c.tenant_id = :cid AND c.excluido_em IS NULL
          ORDER BY c.periodo_inicio DESC",
        [':cid' => $clienteId]
    );

    foreach ($campanhas as &$c) {
        $eleg = (int) $c['elegiveis'];
        $c['cobertura_percentual'] = $eleg > 0 ? round((int) $c['aplicados'] * 100 / $eleg, 1) : 0;
    }
    unset($c);

    responder_sucesso(['itens' => $campanhas], 'OK.');
}
