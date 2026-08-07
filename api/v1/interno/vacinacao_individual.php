<?php
// ============================================================================
// api/v1/interno/vacinacao_individual.php
// Busca de elegível para VACINAÇÃO INDIVIDUAL (atendimento no posto).
//
// O fluxo das telas de vacinados é o inverso do atendimento real: escolher a
// campanha, achar a pessoa na lista, clicar na linha. Aqui a pessoa chega, o
// operador digita o CPF e o sistema responde onde ela é elegível e o que ela
// pode tomar agora.
//
// IMPORTANTE: isto ORIENTA a tela. Quem decide se a dose entra continua sendo
// POST /interno/aplicacoes (validar_aplicacao) — as regras não são duplicadas
// aqui. O que esta busca faz é responder "aparece na lista?" e "por que não?".
//
// Dado de saúde: escopo pelos clientes acessíveis, CPF mascarado conforme LGPD
// e consulta auditada (docs/10).
// ============================================================================

/**
 * GET /api/v1/interno/elegiveis/vacinacao?termo=...
 * termo: CPF (com ou sem máscara), identificador/voucher ou parte do nome.
 */
function rota_buscar_para_vacinacao(array $params): void
{
    $usuario = exigir_login();

    $termo = trim((string) ($_GET['termo'] ?? ''));
    if (mb_strlen($termo) < 3) {
        responder_erro('Informe ao menos 3 caracteres.', 400, [
            ['field' => 'termo', 'code' => 'TERMO_CURTO', 'message' => 'Digite CPF, voucher ou parte do nome (mín. 3 caracteres).'],
        ]);
    }

    // Escopo: só os clientes que o usuário alcança (doc 04 §4.1).
    $where = ['e.status <> :removido'];
    $bind  = [':removido' => 'removido'];
    $acessiveis = clientes_acessiveis_pelo_usuario($usuario);
    if ($acessiveis !== ['*']) {
        if (!$acessiveis) {
            responder_sucesso(['itens' => []], 'OK.');
        }
        $ph = [];
        foreach ($acessiveis as $i => $cid) { $ph[] = ":c_$i"; $bind[":c_$i"] = (int) $cid; }
        $where[] = 'c.tenant_id IN (' . implode(',', $ph) . ')';
    }

    // CPF só entra na busca quando o termo tem dígitos suficientes; nome e
    // voucher entram sempre. Assim "Ana" não varre a base inteira por CPF.
    $ors = ['COALESCE(e.nome, p.nome) LIKE :nome', 'p.identificador = :ident', 'e.codigo_rh = :rh'];
    $bind[':nome']  = '%' . $termo . '%';
    $bind[':ident'] = $termo;
    $bind[':rh']    = $termo;
    $digitos = so_digitos($termo);
    if (strlen($digitos) >= 3) {
        $ors[] = 'p.cpf LIKE :cpf';
        $bind[':cpf'] = '%' . $digitos . '%';
    }
    $where[] = '(' . implode(' OR ', $ors) . ')';

    $linhas = db_todos(
        "SELECT e.id AS elegivel_id, e.status AS status_elegivel, e.codigo_rh,
                p.cpf, p.identificador, COALESCE(e.nome, p.nome) AS nome,
                COALESCE(e.data_nascimento, p.data_nascimento) AS data_nascimento,
                u.nome AS unidade, cc.nome AS clinica,
                c.id AS campanha_id, c.codigo AS campanha_codigo, c.nome AS campanha_nome,
                c.status AS campanha_status, c.modalidade,
                c.periodo_inicio, c.periodo_fim, c.tenant_id,
                cb.razao_social AS cliente
           FROM elegivel e
           JOIN paciente p    ON p.id = e.paciente_id
           JOIN campanha c    ON c.id = e.campanha_id AND c.excluido_em IS NULL
           JOIN cliente_b2b cb ON cb.id = c.tenant_id
      LEFT JOIN unidade u     ON u.id = e.unidade_id
      LEFT JOIN clinica_credenciada cc ON cc.id = e.clinica_id
          WHERE " . implode(' AND ', $where) . "
       ORDER BY (c.status = 'ativa') DESC, c.periodo_fim DESC, e.id DESC
          LIMIT 30",
        $bind
    );

    $itens = [];
    $hoje = date('Y-m-d');
    foreach ($linhas as $l) {
        $campanhaId = (int) $l['campanha_id'];
        $elegivelId = (int) $l['elegivel_id'];

        // Impedimento no nível da campanha/elegível (códigos do catálogo, doc 19).
        $impedimento = null;
        if ($l['campanha_status'] !== 'ativa') {
            $impedimento = 'CAMPANHA_INATIVA';
        } elseif ($hoje < $l['periodo_inicio'] || $hoje > $l['periodo_fim']) {
            $impedimento = 'FORA_DO_PERIODO';
        }

        // Vacinas previstas x doses já confirmadas (RN-013: 1 dose por vacina).
        $vacinas = [];
        foreach (db_todos(
            "SELECT cv.vacina_id, v.nome, v.sigla, cv.doses_previstas,
                    (SELECT COUNT(*) FROM aplicacao a
                      WHERE a.elegivel_id = :e AND a.vacina_id = cv.vacina_id
                        AND a.status = 'confirmada') AS aplicadas
               FROM campanha_vacina cv
               JOIN vacina v ON v.id = cv.vacina_id
              WHERE cv.campanha_id = :c
              ORDER BY v.nome",
            [':e' => $elegivelId, ':c' => $campanhaId]
        ) as $v) {
            $previstas = max(1, (int) $v['doses_previstas']);
            $aplicadas = (int) $v['aplicadas'];
            $proxima   = $aplicadas + 1;

            $motivo = $impedimento;
            if ($motivo === null && $proxima > $previstas) {
                $motivo = 'VACINADO_DUPLICADO';   // já tomou todas as doses previstas
            }
            $vacinas[] = [
                'vacina_id'       => (int) $v['vacina_id'],
                'nome'            => $v['nome'],
                'sigla'           => $v['sigla'],
                'doses_previstas' => $previstas,
                'doses_aplicadas' => $aplicadas,
                'proxima_dose'    => $proxima,
                'pode'            => $motivo === null,
                'motivo'          => $motivo,
                'motivo_texto'    => $motivo === null ? null : msg_importacao($motivo),
            ];
        }

        $itens[] = [
            'elegivel_id'     => $elegivelId,
            'nome'            => $l['nome'],
            'cpf'             => cpf_para_usuario($l['cpf'], $usuario, (int) $l['tenant_id']),
            'identificador'   => $l['identificador'],
            'data_nascimento' => $l['data_nascimento'],
            'codigo_rh'       => $l['codigo_rh'],
            'unidade'         => $l['unidade'],
            'clinica'         => $l['clinica'],
            'status_elegivel' => $l['status_elegivel'],
            'campanha'        => [
                'id'             => $campanhaId,
                'codigo'         => $l['campanha_codigo'],
                'nome'           => $l['campanha_nome'],
                'cliente'        => $l['cliente'],
                'status'         => $l['campanha_status'],
                'modalidade'     => $l['modalidade'],
                'periodo_inicio' => $l['periodo_inicio'],
                'periodo_fim'    => $l['periodo_fim'],
            ],
            'pode_vacinar'  => $impedimento === null && $vacinas
                && count(array_filter($vacinas, fn($v) => $v['pode'])) > 0,
            'impedimento'   => $impedimento,
            'impedimento_texto' => $impedimento === null ? null : msg_importacao($impedimento),
            'vacinas'       => $vacinas,
        ];
    }

    // Consulta a dado de saúde é auditada (docs/10). Registra o termo mascarado.
    registrar_auditoria('vacinacao.busca_individual', [
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'elegivel',
        'metadata'      => [
            'termo'      => strlen($digitos) >= 3 ? mascarar_cpf($digitos) : $termo,
            'resultados' => count($itens),
        ],
    ]);

    responder_sucesso(['itens' => $itens], 'OK.');
}
