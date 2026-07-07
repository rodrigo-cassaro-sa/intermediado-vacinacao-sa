<?php
// ============================================================================
// app/services/aplicacoes.php
// Regra de negócio do registro de aplicação (vacinado). Reutilizado pelo app
// interno e pela API do parceiro. Aplica RN-003 (elegível/período/vacina),
// RN-010 (imutabilidade). O escopo (RN-009/tenant) é validado pelo chamador.
// ============================================================================

/**
 * Wrapper para registro UNITÁRIO: valida, e em erro responde e encerra.
 * Usado pelos endpoints de aplicação única. Devolve [aplicacao_id, tenant_id, campanha_id].
 */
function registrar_aplicacao(array $ctx): array
{
    $res = processar_aplicacao($ctx);
    if (!$res['ok']) {
        responder_erro($res['message'], $res['http'], [
            ['field' => $res['field'] ?? null, 'code' => $res['code'], 'message' => $res['message']],
        ]);
    }
    return $res;
}

/**
 * Registra uma aplicação de dose (rastreável e imutável) SEM encerrar em erro.
 * Devolve, em sucesso: ['ok'=>true, 'aplicacao_id', 'tenant_id', 'campanha_id'];
 *          em erro:    ['ok'=>false, 'http', 'code', 'message', 'field'].
 * Não valida escopo/permissão (responsabilidade do chamador). Base: RN-003/010/013.
 */
function processar_aplicacao(array $ctx): array
{
    $erro = fn(int $http, string $code, string $msg, ?string $field = null) =>
        ['ok' => false, 'http' => $http, 'code' => $code, 'message' => $msg, 'field' => $field];

    // Campos mínimos (inclui lastro/rastreabilidade — RN-019).
    foreach (['elegivel_id', 'vacina_id', 'dose', 'lote', 'aplicado_em',
              'profissional_nome', 'profissional_cpf', 'cidade', 'uf'] as $campo) {
        if (!isset($ctx[$campo]) || $ctx[$campo] === '' || $ctx[$campo] === null) {
            return $erro(400, 'CAMPO_OBRIGATORIO', "O campo '$campo' é obrigatório.", $campo);
        }
    }
    // RN-019: CPF do profissional válido e UF com 2 letras.
    $profCpf = so_digitos($ctx['profissional_cpf']);
    if (!validar_cpf($profCpf)) {
        return $erro(400, 'CPF_PROFISSIONAL_INVALIDO', 'CPF do profissional inválido.', 'profissional_cpf');
    }
    $uf = strtoupper(trim((string) $ctx['uf']));
    if (!preg_match('/^[A-Z]{2}$/', $uf)) {
        return $erro(400, 'UF_INVALIDA', 'UF deve ter 2 letras.', 'uf');
    }

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
        return $erro(422, 'NAO_ELEGIVEL', 'Elegível inexistente.', 'elegivel_id');
    }
    if ($eleg['campanha_status'] !== 'ativa') {
        return $erro(422, 'CAMPANHA_INATIVA', 'Só é possível registrar em campanha ativa.');
    }

    // Data/hora da aplicação dentro da janela da campanha (RN-003).
    $ts = strtotime((string) $ctx['aplicado_em']);
    if ($ts === false) {
        return $erro(400, 'DATA_INVALIDA', 'Use AAAA-MM-DD HH:MM:SS.', 'aplicado_em');
    }
    $diaAplicacao = date('Y-m-d', $ts);
    if ($diaAplicacao < $eleg['periodo_inicio'] || $diaAplicacao > $eleg['periodo_fim']) {
        return $erro(422, 'FORA_DO_PERIODO', 'Data fora do período da campanha.', 'aplicado_em');
    }

    // Vacina precisa estar prevista na campanha.
    $prevista = db_primeiro(
        "SELECT id FROM campanha_vacina WHERE campanha_id = :c AND vacina_id = :v LIMIT 1",
        [':c' => (int) $eleg['campanha_id'], ':v' => (int) $ctx['vacina_id']]
    );
    if ($prevista === null) {
        return $erro(422, 'VACINA_FORA_DA_CAMPANHA', 'Vacina não faz parte da campanha.', 'vacina_id');
    }

    // RN-013: um elegível só pode ter UM vacinado confirmado.
    $jaVacinado = db_primeiro(
        "SELECT id FROM aplicacao WHERE elegivel_id = :e AND status = 'confirmada' LIMIT 1",
        [':e' => (int) $ctx['elegivel_id']]
    );
    if ($jaVacinado !== null) {
        return $erro(409, 'VACINADO_DUPLICADO', 'Este paciente já consta como vacinado nesta campanha.');
    }

    try {
        pdo()->beginTransaction();

        db_executar(
            "INSERT INTO aplicacao
                (tenant_id, campanha_id, elegivel_id, paciente_id, vacina_id, dose, lote,
                 via_administracao, local_aplicacao, cidade, uf, unidade,
                 profissional_nome, profissional_cpf, executor_tipo, executor_id, origem,
                 status, aplicado_em, criado_por)
             SELECT c.tenant_id, e.campanha_id, e.id, e.paciente_id, :vacina, :dose, :lote,
                    :via, :local, :cidade, :uf, :unidade,
                    :pnome, :pcpf, :etipo, :eid, :origem, 'confirmada', :aplicado_em, :criado_por
               FROM elegivel e JOIN campanha c ON c.id = e.campanha_id
              WHERE e.id = :elegivel",
            [
                ':vacina'      => (int) $ctx['vacina_id'],
                ':dose'        => (int) $ctx['dose'],
                ':lote'        => trim((string) $ctx['lote']),
                ':via'         => $ctx['via_administracao'] ?? null,
                ':local'       => $ctx['local_aplicacao'] ?? null,
                ':cidade'      => trim((string) $ctx['cidade']),
                ':uf'          => $uf,
                ':unidade'     => $ctx['unidade'] ?? null,
                ':pnome'       => trim((string) $ctx['profissional_nome']),
                ':pcpf'        => $profCpf,
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
        'ok'           => true,
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
