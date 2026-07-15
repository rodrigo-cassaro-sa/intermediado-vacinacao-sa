<?php
// ============================================================================
// app/services/importacao.php
// Item 9a: orquestra a ingestão de elegíveis.
//  - listas pequenas: processa inline (feedback imediato)
//  - listas grandes: salva o conteúdo, cria importação 'pendente' e devolve na
//    hora; o worker (scripts/processar_importacoes.php via cron) processa em
//    chunks e gera o relatório de erros (importacao_erro).
// Requer app/services/elegiveis.php carregado.
// ============================================================================

const IMPORTACAO_LIMITE_SINCRONO = 2000;  // acima disso, vai para a fila
const IMPORTACAO_CHUNK           = 1000;  // tamanho do lote no worker

/** Converte conteúdo (csv|json) em lista normalizada de elegíveis. */
function importacao_parsear(string $conteudo, string $formato): array
{
    if ($formato === 'json') {
        $d = json_decode($conteudo, true);
        return normalizar_elegiveis_json($d['elegiveis'] ?? []);
    }
    return parsear_csv_elegiveis($conteudo);
}

/** Conta itens sem processar (para decidir inline x assíncrono). */
function importacao_contar(string $conteudo, string $formato): int
{
    if ($formato === 'json') {
        $d = json_decode($conteudo, true);
        return is_array($d['elegiveis'] ?? null) ? count($d['elegiveis']) : 0;
    }
    $linhas = preg_split('/\r\n|\r|\n/', trim($conteudo));
    $n = count(array_filter($linhas, fn($l) => trim($l) !== ''));
    // desconta cabeçalho, se houver
    return ($n > 0 && stripos($linhas[0], 'cpf') !== false) ? $n - 1 : $n;
}

/** Salva o conteúdo bruto em storage/uploads e devolve o nome do arquivo. */
function importacao_salvar_arquivo(string $conteudo, string $formato): string
{
    $dir = BASE_PATH . '/storage/uploads';
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
    $nome = 'imp_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . ($formato === 'json' ? 'json' : 'csv');
    file_put_contents($dir . '/' . $nome, $conteudo);
    return $nome;
}

/**
 * Inicia uma importação. Devolve:
 *  - ['status'=>'concluida', 'importacao_id'=>, 'totais'=>[...]]  (inline)
 *  - ['status'=>'pendente',  'importacao_id'=>]                    (assíncrono)
 */
function importacao_iniciar(int $tenantId, int $campanhaId, string $conteudo, string $formato, string $origem, ?int $criadoPor, array $ator, bool $sincronizar = false): array
{
    $qtd = importacao_contar($conteudo, $formato);
    $syncFlag = $sincronizar ? 1 : 0;

    if ($qtd <= IMPORTACAO_LIMITE_SINCRONO) {
        // Inline: cria importação, processa e finaliza.
        $inicio = date('Y-m-d H:i:s');
        db_executar(
            "INSERT INTO importacao_elegiveis (tenant_id, campanha_id, origem, formato, sincronizar, status, criado_por, criado_em, iniciado_em)
             VALUES (:t, :c, :o, :f, :sync, 'processando', :cp, NOW(), :ini)",
            [':t' => $tenantId, ':c' => $campanhaId, ':o' => $origem, ':f' => $formato, ':sync' => $syncFlag, ':cp' => $criadoPor, ':ini' => $inicio]
        );
        $impId = (int) db_ultimo_id();

        $lista = importacao_parsear($conteudo, $formato);
        $res = ingerir_elegiveis($campanhaId, $tenantId, $lista, $origem, $impId, $ator, null, 0, $sincronizar ? $inicio : null);

        $removidos = $sincronizar ? sincronizar_remover_ausentes($campanhaId, $inicio) : 0;

        db_executar(
            "UPDATE importacao_elegiveis
                SET total_linhas = :t, total_validos = :v, total_invalidos = :inv, total_removidos = :rem,
                    total_processados = :tp, status = 'concluida', finalizado_em = NOW()
              WHERE id = :id",
            [':t' => $res['recebidos'], ':tp' => $res['recebidos'], ':v' => $res['criados'] + $res['atualizados'],
             ':inv' => $res['rejeitados'], ':rem' => $removidos, ':id' => $impId]
        );

        registrar_auditoria('importacao.concluida', [
            'tenant_id' => $tenantId, 'ator_tipo' => $ator['tipo'] ?? 'usuario', 'ator_id' => $ator['id'] ?? null,
            'origem' => 'admin', 'entidade_tipo' => 'importacao', 'entidade_id' => $impId,
            'metadata' => ['campanha_id' => $campanhaId, 'validos' => $res['criados'] + $res['atualizados'], 'rejeitados' => $res['rejeitados'], 'removidos' => $removidos, 'sincronizar' => $syncFlag],
        ]);

        return [
            'status'        => 'concluida',
            'importacao_id' => $impId,
            'totais'        => [
                'total'      => $res['recebidos'],
                'validos'    => $res['criados'] + $res['atualizados'],
                'rejeitados' => $res['rejeitados'],
                'removidos'  => $removidos,
            ],
        ];
    }

    // Assíncrono: salva o arquivo e enfileira.
    $arquivo = importacao_salvar_arquivo($conteudo, $formato);
    db_executar(
        "INSERT INTO importacao_elegiveis (tenant_id, campanha_id, origem, formato, sincronizar, arquivo, total_linhas, status, criado_por, criado_em)
         VALUES (:t, :c, :o, :f, :sync, :arq, :tl, 'pendente', :cp, NOW())",
        [':t' => $tenantId, ':c' => $campanhaId, ':o' => $origem, ':f' => $formato, ':sync' => $syncFlag,
         ':arq' => $arquivo, ':tl' => $qtd, ':cp' => $criadoPor]
    );
    return ['status' => 'pendente', 'importacao_id' => (int) db_ultimo_id()];
}

/**
 * Sincronização (RN turnover): marca como 'removido' os elegíveis da campanha que
 * NÃO foram vistos neste sync (sincronizado_em < início) e não estão aplicados.
 * Devolve a quantidade removida.
 */
function sincronizar_remover_ausentes(int $campanhaId, string $inicio): int
{
    $stmt = db_executar(
        "UPDATE elegivel
            SET status = 'removido', motivo_situacao = 'ausente na sincronização'
          WHERE campanha_id = :id AND status NOT IN ('aplicado', 'removido')
            AND (sincronizado_em IS NULL OR sincronizado_em < :inicio)",
        [':id' => $campanhaId, ':inicio' => $inicio]
    );
    return $stmt->rowCount();
}

/**
 * Processa UMA importação pendente (usado pelo worker). Lê o arquivo, processa
 * em chunks, atualiza progresso e finaliza. Idempotente por status.
 */
function importacao_processar(int $importacaoId): void
{
    $imp = db_primeiro("SELECT * FROM importacao_elegiveis WHERE id = :id LIMIT 1", [':id' => $importacaoId]);
    if ($imp === null || $imp['status'] !== 'pendente') {
        return;
    }
    $inicioSync = date('Y-m-d H:i:s');
    $ehSync = (int) ($imp['sincronizar'] ?? 0) === 1;
    db_executar("UPDATE importacao_elegiveis SET status = 'processando', iniciado_em = :ini WHERE id = :id",
        [':ini' => $inicioSync, ':id' => $importacaoId]);

    $caminho = BASE_PATH . '/storage/uploads/' . $imp['arquivo'];
    $conteudo = is_file($caminho) ? file_get_contents($caminho) : '';
    if ($conteudo === '' ) {
        db_executar("UPDATE importacao_elegiveis SET status = 'falha', mensagem_erro = 'arquivo não encontrado', finalizado_em = NOW() WHERE id = :id", [':id' => $importacaoId]);
        return;
    }

    $formato = $imp['formato'] ?: 'csv';
    $lista = importacao_parsear($conteudo, $formato);

    // Colaboradores do arquivo inteiro (para validar titular dos dependentes).
    $colaboradores = [];
    foreach ($lista as $it) {
        if (strtolower(trim((string) ($it['tipo_vinculo'] ?? ''))) === 'colaborador') {
            $colaboradores[so_digitos($it['cpf'] ?? '')] = true;
        }
    }

    $ator = ['tipo' => 'usuario', 'id' => $imp['criado_por'] ? (int) $imp['criado_por'] : null];
    $totalValidos = 0; $totalInvalidos = 0; $processados = 0;
    $tenantId = (int) $imp['tenant_id']; $campanhaId = (int) $imp['campanha_id'];

    foreach (array_chunk($lista, IMPORTACAO_CHUNK, true) as $chunk) {
        $offset = (int) array_key_first($chunk);
        try {
            pdo()->beginTransaction();
            $res = ingerir_elegiveis($campanhaId, $tenantId, array_values($chunk), $imp['origem'], $importacaoId, $ator, $colaboradores, $offset, $ehSync ? $inicioSync : null);
            pdo()->commit();
        } catch (Throwable $e) {
            if (pdo()->inTransaction()) { pdo()->rollBack(); }
            db_executar("UPDATE importacao_elegiveis SET status='falha', mensagem_erro=:m, finalizado_em=NOW() WHERE id=:id",
                [':m' => substr($e->getMessage(), 0, 250), ':id' => $importacaoId]);
            return;
        }
        $totalValidos += $res['criados'] + $res['atualizados'];
        $totalInvalidos += $res['rejeitados'];
        $processados += $res['recebidos'];
        db_executar("UPDATE importacao_elegiveis SET total_processados = :p WHERE id = :id",
            [':p' => $processados, ':id' => $importacaoId]);
    }

    $removidos = $ehSync ? sincronizar_remover_ausentes($campanhaId, $inicioSync) : 0;

    db_executar(
        "UPDATE importacao_elegiveis
            SET total_linhas = :t, total_validos = :v, total_invalidos = :inv, total_removidos = :rem,
                total_processados = :tp, status = 'concluida', finalizado_em = NOW()
          WHERE id = :id",
        [':t' => $processados, ':tp' => $processados, ':v' => $totalValidos, ':inv' => $totalInvalidos, ':rem' => $removidos, ':id' => $importacaoId]
    );

    registrar_auditoria('importacao.concluida', [
        'tenant_id' => $tenantId, 'ator_tipo' => 'usuario', 'ator_id' => $ator['id'] ?? null,
        'origem' => 'api', 'entidade_tipo' => 'importacao', 'entidade_id' => $importacaoId,
        'metadata' => ['campanha_id' => $campanhaId, 'validos' => $totalValidos, 'rejeitados' => $totalInvalidos, 'removidos' => $removidos, 'sincronizar' => $ehSync ? 1 : 0],
    ]);
}
