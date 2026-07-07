<?php
// ============================================================================
// app/services/aplicacoes.php
// Regra de negócio do registro de aplicação (vacinado). Reutilizado pelo app
// interno e pela API do parceiro. Aplica RN-003 (elegível/período/vacina),
// RN-010 (imutabilidade). O escopo (RN-009/tenant) é validado pelo chamador.
// ============================================================================

/**
 * Registra uma aplicação de dose de forma rastreável e imutável.
 * $ctx exige: elegivel_id, vacina_id, dose, lote, aplicado_em, executor_tipo,
 *             executor_id, origem; opcionais: via_administracao, local_aplicacao, criado_por.
 * Valida tudo no backend, insere a aplicação e marca o elegível como 'aplicado'.
 * Em erro de negócio, responde e encerra. Devolve [aplicacao_id, tenant_id, campanha_id].
 */
function registrar_aplicacao(array $ctx): array
{
    // Elegível + dados da campanha.
    $eleg = db_primeiro(
        "SELECT e.id, e.campanha_id, c.tenant_id, c.status AS campanha_status,
                c.periodo_inicio, c.periodo_fim
           FROM elegivel e
           JOIN campanha c ON c.id = e.campanha_id
          WHERE e.id = :id LIMIT 1",
        [':id' => (int) $ctx['elegivel_id']]
    );
    if ($eleg === null) {
        responder_erro('Paciente não elegível.', 422, [
            ['field' => 'elegivel_id', 'code' => 'NAO_ELEGIVEL', 'message' => 'Elegível inexistente.'],
        ]);
    }
    if ($eleg['campanha_status'] !== 'ativa') {
        responder_erro('Campanha não está ativa.', 422, [
            ['field' => null, 'code' => 'CAMPANHA_INATIVA', 'message' => 'Só é possível registrar em campanha ativa.'],
        ]);
    }

    // Data/hora da aplicação dentro da janela da campanha (RN-003).
    $ts = strtotime((string) $ctx['aplicado_em']);
    if ($ts === false) {
        responder_erro('Data de aplicação inválida.', 400, [
            ['field' => 'aplicado_em', 'code' => 'DATA_INVALIDA', 'message' => 'Use AAAA-MM-DD HH:MM:SS.'],
        ]);
    }
    $diaAplicacao = date('Y-m-d', $ts);
    if ($diaAplicacao < $eleg['periodo_inicio'] || $diaAplicacao > $eleg['periodo_fim']) {
        responder_erro('Fora da janela da campanha.', 422, [
            ['field' => 'aplicado_em', 'code' => 'FORA_DO_PERIODO', 'message' => 'Data fora do período da campanha.'],
        ]);
    }

    // Vacina precisa estar prevista na campanha.
    $prevista = db_primeiro(
        "SELECT id FROM campanha_vacina WHERE campanha_id = :c AND vacina_id = :v LIMIT 1",
        [':c' => (int) $eleg['campanha_id'], ':v' => (int) $ctx['vacina_id']]
    );
    if ($prevista === null) {
        responder_erro('Vacina não prevista na campanha.', 422, [
            ['field' => 'vacina_id', 'code' => 'VACINA_FORA_DA_CAMPANHA', 'message' => 'Vacina não faz parte da campanha.'],
        ]);
    }

    // RN-013: um elegível só pode ter UM vacinado confirmado (pagamento por vacinado
    // à clínica não pode repetir). Correção deve usar a retificação (RN-010).
    $jaVacinado = db_primeiro(
        "SELECT id FROM aplicacao WHERE elegivel_id = :e AND status = 'confirmada' LIMIT 1",
        [':e' => (int) $ctx['elegivel_id']]
    );
    if ($jaVacinado !== null) {
        responder_erro('Paciente já vacinado nesta campanha.', 409, [
            ['field' => null, 'code' => 'VACINADO_DUPLICADO', 'message' => 'Este paciente já consta como vacinado nesta campanha.'],
        ]);
    }

    try {
        pdo()->beginTransaction();

        db_executar(
            "INSERT INTO aplicacao
                (tenant_id, campanha_id, elegivel_id, paciente_id, vacina_id, dose, lote,
                 via_administracao, local_aplicacao, executor_tipo, executor_id, origem,
                 status, aplicado_em, criado_por)
             SELECT c.tenant_id, e.campanha_id, e.id, e.paciente_id, :vacina, :dose, :lote,
                    :via, :local, :etipo, :eid, :origem, 'confirmada', :aplicado_em, :criado_por
               FROM elegivel e JOIN campanha c ON c.id = e.campanha_id
              WHERE e.id = :elegivel",
            [
                ':vacina'      => (int) $ctx['vacina_id'],
                ':dose'        => (int) $ctx['dose'],
                ':lote'        => trim((string) $ctx['lote']),
                ':via'         => $ctx['via_administracao'] ?? null,
                ':local'       => $ctx['local_aplicacao'] ?? null,
                ':etipo'       => $ctx['executor_tipo'],
                ':eid'         => (int) $ctx['executor_id'],
                ':origem'      => $ctx['origem'],
                ':aplicado_em' => date('Y-m-d H:i:s', $ts),
                ':criado_por'  => $ctx['criado_por'] ?? null,
                ':elegivel'    => (int) $ctx['elegivel_id'],
            ]
        );
        $aplicacaoId = (int) db_ultimo_id();

        // Marca o elegível como aplicado (base da tabela verdade — RN-005).
        db_executar("UPDATE elegivel SET status = 'aplicado' WHERE id = :id", [':id' => (int) $ctx['elegivel_id']]);

        pdo()->commit();
    } catch (Throwable $e) {
        if (pdo()->inTransaction()) {
            pdo()->rollBack();
        }
        throw $e;
    }

    return [
        'aplicacao_id' => $aplicacaoId,
        'tenant_id'    => (int) $eleg['tenant_id'],
        'campanha_id'  => (int) $eleg['campanha_id'],
    ];
}

/** Lê campanha/clinica de um elegível (para validação de escopo no chamador). */
function campanha_do_elegivel(int $elegivelId): ?array
{
    return db_primeiro(
        "SELECT id, campanha_id, clinica_id, paciente_id FROM elegivel WHERE id = :id LIMIT 1",
        [':id' => $elegivelId]
    );
}
