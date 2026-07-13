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
require_once __DIR__ . '/../app/services/webhooks.php';
require_once __DIR__ . '/../app/services/elegiveis.php';
require_once __DIR__ . '/../app/services/importacao.php';
require_once __DIR__ . '/../app/services/historico_import.php';

$fez = false;

// 1) Importações de elegíveis (item 9a).
$pendentes = db_todos("SELECT id FROM importacao_elegiveis WHERE status = 'pendente' ORDER BY id LIMIT 5");
foreach ($pendentes as $p) {
    $fez = true;
    $id = (int) $p['id'];
    echo "Processando importação de elegíveis #$id ...\n";
    try {
        importacao_processar($id);
        echo "  concluída #$id\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "  FALHA #$id: " . $e->getMessage() . "\n");
    }
}

// 2) Importações de vacinados históricos (RN-027).
$pendHist = db_todos("SELECT id FROM importacao_historico WHERE status = 'pendente' ORDER BY id LIMIT 3");
foreach ($pendHist as $p) {
    $fez = true;
    $id = (int) $p['id'];
    echo "Processando histórico #$id ...\n";
    try {
        historico_import_processar($id);
        echo "  concluído histórico #$id\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "  FALHA histórico #$id: " . $e->getMessage() . "\n");
    }
}

if (!$fez) {
    echo "Nenhuma importação pendente.\n";
}
echo "Fim.\n";
