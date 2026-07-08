<?php
// ============================================================================
// api/v1/parceiro/consulta.php
// API externa de CONSULTA (Fase A3): token tipo 'consulta' (escopo = cliente/tenant).
// Somente leitura. Para sistema de carteira, RH ou BI do cliente.
// Escopo: titular_id da credencial = cliente_b2b (tenant). Só vê dados do próprio cliente.
// ============================================================================

/** GET /api/v1/parceiro/carteira/{cpf} — carteira do paciente no escopo do cliente. */
function rota_consulta_carteira(array $params): void
{
    $cred = exigir_credencial('consulta');
    $tenant = (int) $cred['titular_id'];

    $raw = trim((string) ($params['cpf'] ?? ''));
    $cpf = so_digitos($raw);
    if (validar_cpf($cpf)) {
        $paciente = db_primeiro("SELECT id, cpf, identificador, nome FROM paciente WHERE cpf = :v LIMIT 1", [':v' => $cpf]);
    } else {
        $paciente = db_primeiro("SELECT id, cpf, identificador, nome FROM paciente WHERE identificador = :v LIMIT 1", [':v' => $raw]);
    }
    if ($paciente === null) {
        responder_erro('Paciente não encontrado.', 404, [
            ['field' => null, 'code' => 'PACIENTE_NAO_ENCONTRADO', 'message' => 'Sem cadastro.'],
        ]);
    }

    $doses = db_todos(
        "SELECT a.aplicado_em, v.nome AS vacina, a.dose, a.lote, c.nome AS campanha, a.cidade, a.uf
           FROM aplicacao a
           JOIN vacina v ON v.id = a.vacina_id
           JOIN campanha c ON c.id = a.campanha_id
          WHERE a.paciente_id = :pid AND a.status = 'confirmada' AND a.tenant_id = :t
          ORDER BY a.aplicado_em DESC",
        [':pid' => (int) $paciente['id'], ':t' => $tenant]
    );

    registrar_auditoria('carteira.consultada', [
        'tenant_id' => $tenant, 'ator_tipo' => 'credencial_api', 'ator_id' => (int) $cred['id'],
        'origem' => 'api_parceiro', 'entidade_tipo' => 'paciente', 'entidade_id' => (int) $paciente['id'],
        'metadata' => ['doses' => count($doses)],
    ]);

    $identidade = $paciente['cpf'] ? mascarar_cpf($paciente['cpf']) : ('voucher:' . $paciente['identificador']);
    responder_sucesso([
        'paciente'    => ['identidade' => $identidade, 'nome' => $paciente['nome']],
        'total_doses' => count($doses),
        'doses'       => $doses,
    ], 'OK.');
}

/** GET /api/v1/parceiro/campanhas/{id}/tabela-verdade — consolidação da campanha (escopo tenant). */
function rota_consulta_tabela_verdade(array $params): void
{
    $cred = exigir_credencial('consulta');
    $tenant = (int) $cred['titular_id'];
    $id = id_campanha_rota($params['id'] ?? null);

    $campanha = db_primeiro("SELECT id, tenant_id FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1", [':id' => $id]);
    if ($campanha === null || (int) $campanha['tenant_id'] !== $tenant) {
        responder_erro('Sem acesso a esta campanha.', 403, [
            ['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Campanha fora do escopo do token.'],
        ]);
    }

    [$apos, $por] = paginacao_keyset();
    $where = 'campanha_id = :id';
    $bind = [':id' => $id];
    if ($apos > 0) { $where .= ' AND paciente_id > :apos'; $bind[':apos'] = $apos; }

    $itens = db_todos(
        "SELECT paciente_id, cpf, nome, situacao_elegivel, total_aplicacoes, ultima_aplicacao_em
           FROM vw_tabela_verdade WHERE $where ORDER BY paciente_id ASC LIMIT $por",
        $bind
    );
    foreach ($itens as &$it) { $it['cpf'] = mascarar_cpf($it['cpf']); }
    unset($it);
    $proximo = count($itens) === $por ? (int) end($itens)['paciente_id'] : null;

    responder_sucesso(['itens' => $itens], 'OK.', 200, ['por_pagina' => $por, 'proximo_cursor' => $proximo]);
}
