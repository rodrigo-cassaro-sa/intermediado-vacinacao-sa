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
function importacao_iniciar(int $tenantId, int $campanhaId, string $conteudo, string $formato, string $origem, ?int $criadoPor, array $ator): array
{
    $qtd = importacao_contar($conteudo, $formato);

    if ($qtd <= IMPORTACAO_LIMITE_SINCRONO) {
        // Inline: cria importação, processa e finaliza.
        db_executar(
            "INSERT INTO importacao_elegiveis (tenant_id, campanha_id, origem, formato, status, criado_por, criado_em, iniciado_em)
             VALUES (:t, :c, :o, :f, 'processando', :cp, NOW(), NOW())",
            [':t' => $tenantId, ':c' => $campanhaId, ':o' => $origem, ':f' => $formato, ':cp' => $criadoPor]
        );
        $impId = (int) db_ultimo_id();

        $lista = importacao_parsear($conteudo, $formato);
        $res = ingerir_elegiveis($campanhaId, $tenantId, $lista, $origem, $impId, $ator);

        db_executar(
            "UPDATE importacao_elegiveis
                SET total_linhas = :t, total_validos = :v, total_invalidos = :inv,
                    total_processados = :t, status = 'concluida', finalizado_em = NOW()
              WHERE id = :id",
            [':t' => $res['recebidos'], ':v' => $res['criados'] + $res['atualizados'],
             ':inv' => $res['rejeitados'], ':id' => $impId]
        );

        registrar_auditoria('importacao.concluida', [
            'tenant_id' => $tenantId, 'ator_tipo' => $ator['tipo'] ?? 'usuario', 'ator_id' => $ator['id'] ?? null,
            'origem' => 'admin', 'entidade_tipo' => 'importacao', 'entidade_id' => $impId,
            'metadata' => ['campanha_id' => $campanhaId, 'validos' => $res['criados'] + $res['atualizados'], 'rejeitados' => $res['rejeitados']],
        ]);

        return [
            'status'        => 'concluida',
            'importacao_id' => $impId,
            'totais'        => [
                'total'      => $res['recebidos'],
                'validos'    => $res['criados'] + $res['atualizados'],
                'rejeitados' => $res['rejeitados'],
            ],
        ];
    }

    // Assíncrono: salva o arquivo e enfileira.
    $arquivo = importacao_salvar_arquivo($conteudo, $formato);
    db_executar(
        "INSERT INTO importacao_elegiveis (tenant_id, campanha_id, origem, formato, arquivo, total_linhas, status, criado_por, criado_em)
         VALUES (:t, :c, :o, :f, :arq, :tl, 'pendente', :cp, NOW())",
        [':t' => $tenantId, ':c' => $campanhaId, ':o' => $origem, ':f' => $formato,
         ':arq' => $arquivo, ':tl' => $qtd, ':cp' => $criadoPor]
    );
    return ['status' => 'pendente', 'importacao_id' => (int) db_ultimo_id()];
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
    db_executar("UPDATE importacao_elegiveis SET status = 'processando', iniciado_em = NOW() WHERE id = :id", [':id' => $importacaoId]);

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
            $res = ingerir_elegiveis($campanhaId, $tenantId, array_values($chunk), $imp['origem'], $importacaoId, $ator, $colaboradores, $offset);
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

    db_executar(
        "UPDATE importacao_elegiveis
            SET total_linhas = :t, total_validos = :v, total_invalidos = :inv,
                total_processados = :t, status = 'concluida', finalizado_em = NOW()
          WHERE id = :id",
        [':t' => $processados, ':v' => $totalValidos, ':inv' => $totalInvalidos, ':id' => $importacaoId]
    );

    registrar_auditoria('importacao.concluida', [
        'tenant_id' => $tenantId, 'ator_tipo' => 'usuario', 'ator_id' => $ator['id'] ?? null,
        'origem' => 'api', 'entidade_tipo' => 'importacao', 'entidade_id' => $importacaoId,
        'metadata' => ['campanha_id' => $campanhaId, 'validos' => $totalValidos, 'rejeitados' => $totalInvalidos],
    ]);
}
