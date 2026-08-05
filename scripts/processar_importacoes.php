<?php
// ============================================================================
// scripts/processar_importacoes.php
// Worker da ingestão assíncrona (item 9a). Roda em loop pelo entrypoint do
// container e/ou por Cron Job. Pega importações 'pendente' e processa em chunks,
// gerando o relatório de erros.
// Uso (container):  php scripts/processar_importacoes.php
//
// Listas de 20k a 30k linhas são o alvo (BUG-003). Medido: 30k linhas = 36,6 MB
// de pico no parse; 100k = 120,7 MB. Por isso o limite de memória aqui é maior
// que o do php.ini do site (256M), que serve às requisições web.
// ============================================================================

// Só faz sentido em linha de comando (nunca exposto pelo Apache).
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

set_time_limit(0);                                            // lote grande não tem prazo
ini_set('memory_limit', getenv('WORKER_MEMORIA') ?: '512M');  // folga sobre os 120 MB de 100k linhas

// Trava para não processar a mesma fila duas vezes (worker do container + Cron
// Job do painel configurados juntos, ou uma passada demorando mais que o intervalo).
$lockArquivo = sys_get_temp_dir() . '/imz_worker_importacoes.lock';
$lock = fopen($lockArquivo, 'c');
if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
    echo "Outro worker já está processando a fila; saindo.\n";
    exit(0);
}

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';
require_once __DIR__ . '/../app/helpers/resposta.php';   // request_id (usado por historico/auditoria)
require_once __DIR__ . '/../app/helpers/validacao.php';
require_once __DIR__ . '/../app/helpers/csv.php';
require_once __DIR__ . '/../app/helpers/auditoria.php';
require_once __DIR__ . '/../app/services/historico.php';
require_once __DIR__ . '/../app/services/webhooks.php';
require_once __DIR__ . '/../app/services/elegiveis.php';
require_once __DIR__ . '/../app/services/importacao.php';
require_once __DIR__ . '/../app/services/historico_import.php';

$fez = false;

// 0) Importação travada em 'processando' (container reiniciou no meio, por ex.)
// volta para a fila. Sem isso ela fica parada para sempre — mesmo sintoma do
// BUG-003. Reprocessar é seguro: elegível é único por (campanha, paciente) e a
// linha repetida vira atualização, não duplicata.
$travadas = db_executar(
    "UPDATE importacao_elegiveis
        SET status = 'pendente'
      WHERE status = 'processando'
        AND iniciado_em IS NOT NULL
        AND iniciado_em < DATE_SUB(NOW(), INTERVAL 60 MINUTE)"
);
if ($travadas->rowCount() > 0) {
    echo "Reenfileiradas {$travadas->rowCount()} importacao(oes) travada(s).\n";
}

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

flock($lock, LOCK_UN);
fclose($lock);
