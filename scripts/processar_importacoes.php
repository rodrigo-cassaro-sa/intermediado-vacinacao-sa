<?php
// ============================================================================
// scripts/processar_importacoes.php
// Worker da ingestão assíncrona (item 9a). Rodar por CRON (ex.: a cada minuto).
// Pega importações 'pendente' e processa em chunks, gerando o relatório de erros.
// Uso (container):  php scripts/processar_importacoes.php
// ============================================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';
require_once __DIR__ . '/../app/helpers/resposta.php';   // request_id (usado por historico/auditoria)
require_once __DIR__ . '/../app/helpers/validacao.php';
require_once __DIR__ . '/../app/helpers/auditoria.php';
require_once __DIR__ . '/../app/services/historico.php';
require_once __DIR__ . '/../app/services/elegiveis.php';
require_once __DIR__ . '/../app/services/importacao.php';

$pendentes = db_todos("SELECT id FROM importacao_elegiveis WHERE status = 'pendente' ORDER BY id LIMIT 5");
if (!$pendentes) {
    echo "Nenhuma importação pendente.\n";
    exit(0);
}

foreach ($pendentes as $p) {
    $id = (int) $p['id'];
    echo "Processando importação #$id ...\n";
    try {
        importacao_processar($id);
        echo "  concluída #$id\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "  FALHA #$id: " . $e->getMessage() . "\n");
    }
}
echo "Fim.\n";
