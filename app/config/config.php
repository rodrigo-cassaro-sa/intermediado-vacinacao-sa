<?php
// ============================================================================
// app/config/config.php
// Função: carregar o .env, definir constantes globais e ambiente de execução.
// Stack: PHP procedural puro. Base: docs/05 (arquitetura) e docs/10 (segurança).
// ============================================================================

// Diretório raiz do projeto (um nível acima de /app).
define('BASE_PATH', dirname(__DIR__, 2));

/**
 * Carrega variáveis de um arquivo .env simples (CHAVE=valor) para getenv()/$_ENV.
 * Não versionar o .env (ver .gitignore).
 */
function carregar_env(string $caminho): void
{
    if (!is_file($caminho)) {
        return; // em produção as variáveis podem vir do ambiente/EasyPanel
    }
    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if ($linha === '' || $linha[0] === '#') {
            continue;
        }
        $pos = strpos($linha, '=');
        if ($pos === false) {
            continue;
        }
        $chave = trim(substr($linha, 0, $pos));
        $valor = trim(substr($linha, $pos + 1));
        // remove comentário inline e aspas
        $valor = preg_replace('/\s+#.*$/', '', $valor);
        $valor = trim($valor, "\"'");
        if ($chave !== '' && getenv($chave) === false) {
            putenv("$chave=$valor");
            $_ENV[$chave] = $valor;
        }
    }
}

/** Lê variável de ambiente com valor padrão. */
function env(string $chave, $padrao = null)
{
    $valor = getenv($chave);
    if ($valor === false) {
        return $padrao;
    }
    if ($valor === 'true')  return true;
    if ($valor === 'false') return false;
    return $valor;
}

carregar_env(BASE_PATH . '/.env');

// Constantes de ambiente
define('APP_ENV',   env('APP_ENV', 'desenvolvimento'));
define('APP_DEBUG', (bool) env('APP_DEBUG', false));
define('APP_URL',   env('APP_URL', 'http://localhost'));
define('APP_CHAVE', env('APP_CHAVE', ''));
define('APP_VERSION', env('APP_VERSION', 'dev'));  // versão/commit publicado (observabilidade)

date_default_timezone_set(env('APP_TIMEZONE', 'America/Sao_Paulo'));

// Em produção não exibir erro na tela; sempre registrar em log.
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php_erros.log');
