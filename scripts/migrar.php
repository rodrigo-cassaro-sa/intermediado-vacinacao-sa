<?php
// ============================================================================
// scripts/migrar.php
// Função: aplicar as migrations (e opcionalmente seeds) em ordem, via PDO.
// Uso (dentro do container da app):  php scripts/migrar.php [--seeds]
// Cada arquivo já registra a si mesmo em schema_migracao (idempotente por UNIQUE).
// ATENÇÃO: nunca rodar em produção sem backup (regra global).
// ============================================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';

$comSeeds = in_array('--seeds', $argv, true);

$dirMigrations = BASE_PATH . '/database/migrations';
$arquivos = glob($dirMigrations . '/*.sql');
sort($arquivos); // ordem 000, 001, 002, ...

if (!$arquivos) {
    fwrite(STDERR, "Nenhuma migration encontrada em $dirMigrations\n");
    exit(1);
}

// Garante a tabela de controle e lê o que já foi aplicado (execução incremental).
pdo()->exec(
    "CREATE TABLE IF NOT EXISTS schema_migracao (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        arquivo VARCHAR(160) NOT NULL,
        aplicado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id), UNIQUE KEY uq_schema_migracao_arquivo (arquivo)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);
$aplicadas = array_column(db_todos("SELECT arquivo FROM schema_migracao"), 'arquivo');

foreach ($arquivos as $arquivo) {
    $nome = basename($arquivo);
    if (in_array($nome, $aplicadas, true)) {
        echo "JÁ APLICADA: $nome\n";
        continue;
    }
    $sql = file_get_contents($arquivo);
    if ($sql === false || trim($sql) === '') {
        echo "Ignorado (vazio): $nome\n";
        continue;
    }
    try {
        pdo()->exec($sql); // executa o conteúdo do arquivo (DDL + registro em schema_migracao)
        echo "OK: $nome\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "FALHA em $nome: " . $e->getMessage() . "\n");
        exit(1);
    }
}

if ($comSeeds) {
    foreach (glob(BASE_PATH . '/database/seeds/*.sql') as $seed) {
        $nome = basename($seed);
        try {
            pdo()->exec(file_get_contents($seed));
            echo "SEED OK: $nome\n";
        } catch (Throwable $e) {
            fwrite(STDERR, "FALHA no seed $nome: " . $e->getMessage() . "\n");
            exit(1);
        }
    }
}

echo "Migrations concluídas.\n";
