<?php
// ============================================================================
// app/config/conexao.php
// Função: conexão única com MySQL via PDO (prepared statements — anti-SQLi).
// Uso procedural: pdo() devolve a mesma instância durante a requisição.
// ============================================================================

/**
 * Retorna a conexão PDO (singleton por requisição).
 * Lança PDOException em caso de falha (tratada pelo handler global).
 */
function pdo(): PDO
{
    static $conexao = null;
    if ($conexao instanceof PDO) {
        return $conexao;
    }

    $host = env('DB_HOST', '127.0.0.1');
    $porta = env('DB_PORT', '3306');
    $nome = env('DB_NOME', '');
    $dsn = "mysql:host=$host;port=$porta;dbname=$nome;charset=utf8mb4";

    $opcoes = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // prepares reais no servidor
    ];

    $conexao = new PDO($dsn, env('DB_USUARIO', ''), env('DB_SENHA', ''), $opcoes);
    return $conexao;
}

/** Executa uma consulta preparada e devolve o statement. */
function db_executar(string $sql, array $parametros = []): PDOStatement
{
    $stmt = pdo()->prepare($sql);
    $stmt->execute($parametros);
    return $stmt;
}

/** Retorna a primeira linha (ou null). */
function db_primeiro(string $sql, array $parametros = []): ?array
{
    $linha = db_executar($sql, $parametros)->fetch();
    return $linha === false ? null : $linha;
}

/** Retorna todas as linhas. */
function db_todos(string $sql, array $parametros = []): array
{
    return db_executar($sql, $parametros)->fetchAll();
}

/** Retorna o último id inserido. */
function db_ultimo_id(): string
{
    return pdo()->lastInsertId();
}
