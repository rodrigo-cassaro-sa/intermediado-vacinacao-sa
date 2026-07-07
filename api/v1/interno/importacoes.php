<?php
// ============================================================================
// api/v1/interno/importacoes.php
// Função: acompanhar o status de uma importação e baixar o RELATÓRIO DE ERROS
// (rejeitados com motivo). Grupo interno (sessão). Item 9a.
// ============================================================================

/** GET /api/v1/interno/importacoes/{id} — status e progresso da importação. */
function rota_status_importacao(array $params): void
{
    $usuario = exigir_login();
    $imp = importacao_do_usuario($usuario, (int) ($params['id'] ?? 0));

    responder_sucesso([
        'importacao_id'     => (int) $imp['id'],
        'campanha_id'       => (int) $imp['campanha_id'],
        'origem'            => $imp['origem'],
        'status'            => $imp['status'],
        'total_linhas'      => (int) $imp['total_linhas'],
        'total_processados' => (int) $imp['total_processados'],
        'total_validos'     => (int) $imp['total_validos'],
        'total_invalidos'   => (int) $imp['total_invalidos'],
        'iniciado_em'       => $imp['iniciado_em'],
        'finalizado_em'     => $imp['finalizado_em'],
        'mensagem_erro'     => $imp['mensagem_erro'],
    ], 'OK.');
}

/** GET /api/v1/interno/campanhas/{id}/importacoes — histórico de importações. */
function rota_listar_importacoes(array $params): void
{
    $usuario = exigir_login();
    $id = id_campanha_rota($params['id'] ?? null);
    exigir_campanha_do_usuario($usuario, $id);

    $itens = db_todos(
        "SELECT id, origem, formato, status, total_linhas, total_validos, total_invalidos,
                total_processados, criado_em, finalizado_em
           FROM importacao_elegiveis WHERE campanha_id = :id ORDER BY id DESC LIMIT 50",
        [':id' => $id]
    );
    responder_sucesso(['itens' => $itens], 'OK.');
}

/**
 * GET /api/v1/interno/importacoes/{id}/erros/exportar — CSV dos rejeitados.
 * Relatório de erro para o cliente corrigir e reenviar.
 */
function rota_exportar_erros_importacao(array $params): void
{
    $usuario = exigir_login();
    $imp = importacao_do_usuario($usuario, (int) ($params['id'] ?? 0));

    $linhas = db_todos(
        "SELECT linha, cpf, nome, codigo FROM importacao_erro WHERE importacao_id = :id ORDER BY linha",
        [':id' => (int) $imp['id']]
    );

    registrar_auditoria('importacao.erros_exportados', [
        'tenant_id'     => (int) $imp['tenant_id'],
        'ator_tipo'     => 'usuario',
        'ator_id'       => (int) $usuario['id'],
        'origem'        => 'admin',
        'entidade_tipo' => 'importacao',
        'entidade_id'   => (int) $imp['id'],
    ]);

    if (!headers_sent()) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="erros-importacao-' . (int) $imp['id'] . '.csv"');
        header('X-Request-Id: ' . request_id());
    }
    echo "\xEF\xBB\xBF"; // BOM p/ Excel
    $out = fopen('php://output', 'w');
    fputcsv($out, ['linha', 'cpf', 'nome', 'codigo_erro', 'motivo'], ';');
    foreach ($linhas as $l) {
        fputcsv($out, [$l['linha'], $l['cpf'], $l['nome'], $l['codigo'], motivo_erro_importacao($l['codigo'])], ';');
    }
    fclose($out);
    exit;
}

/** Busca a importação e valida acesso do usuário (escopo da campanha). */
function importacao_do_usuario(array $usuario, int $importacaoId): array
{
    $imp = db_primeiro("SELECT * FROM importacao_elegiveis WHERE id = :id LIMIT 1", [':id' => $importacaoId]);
    if ($imp === null) {
        responder_erro('Importação inexistente.', 404, [
            ['field' => null, 'code' => 'IMPORTACAO_NAO_ENCONTRADA', 'message' => 'Importação não encontrada.'],
        ]);
    }
    exigir_campanha_do_usuario($usuario, (int) $imp['campanha_id']);
    return $imp;
}

/** Texto amigável para o código de erro (relatório ao cliente). */
function motivo_erro_importacao(string $codigo): string
{
    static $mapa = [
        'CPF_INVALIDO'               => 'CPF inválido',
        'NOME_OBRIGATORIO'           => 'Nome não informado',
        'DATA_NASCIMENTO_INVALIDA'   => 'Data de nascimento inválida',
        'TIPO_VINCULO_INVALIDO'      => 'Tipo de vínculo inválido (use colaborador, dependente ou terceiro)',
        'CPF_TITULAR_INVALIDO'       => 'CPF do titular inválido (dependente)',
        'CPF_TITULAR_NAO_ELEGIVEL'   => 'Titular do dependente não é colaborador elegível na campanha',
        'CODIGO_LOTACAO_OBRIGATORIO' => 'Código de lotação não informado',
        'CODIGO_RH_OBRIGATORIO'      => 'Código de RH não informado',
    ];
    return $mapa[$codigo] ?? $codigo;
}
