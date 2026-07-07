<?php
// ============================================================================
// scripts/expirar_elegiveis.php
// Função: RN-015 — expirar elegíveis pendentes de campanhas cujo período já
// terminou, e encerrar campanhas ativas vencidas. Rodar diariamente (cron).
// Uso (no container):  php scripts/expirar_elegiveis.php
// ============================================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';

// Expira elegíveis pendentes de campanhas já vencidas.
$stmt = db_executar(
    "UPDATE elegivel e
        JOIN campanha c ON c.id = e.campanha_id
        SET e.status = 'expirado'
      WHERE e.status = 'pendente' AND c.periodo_fim < CURDATE()"
);
$expirados = $stmt->rowCount();

// Encerra campanhas ativas cujo período já terminou.
$stmt2 = db_executar(
    "UPDATE campanha SET status = 'encerrada'
      WHERE status = 'ativa' AND periodo_fim < CURDATE()"
);
$encerradas = $stmt2->rowCount();

// Limpeza dos contadores de rate limit de janelas antigas (> 1h atrás).
try {
    $bucketCorte = intdiv(time(), 60) - 60;
    db_executar("DELETE FROM rate_limite WHERE janela < :b", [':b' => $bucketCorte]);
} catch (Throwable $e) {
    // tabela pode não existir em ambientes antigos; ignora
}

echo "Elegíveis expirados: $expirados | Campanhas encerradas: $encerradas\n";
