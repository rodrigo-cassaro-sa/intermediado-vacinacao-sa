<?php
// ============================================================================
// app/helpers/rate_limit.php
// Limite de requisições por janela (fixed window de 60s) baseado em MySQL.
// Item 9b/12. Fail-open: se o controle falhar, não bloqueia tráfego legítimo.
// ============================================================================

/**
 * Encaixa a chave no VARCHAR(80) da coluna.
 *
 * Por que isto existe: `rate_limite.chave` é VARCHAR(80) e o INSERT de uma chave
 * maior falha com "Data too long". Como esta função é fail-open, a falha era
 * SILENCIOSA — o limite simplesmente não valia, e nada na tela denunciava.
 * Aconteceu com `senha_esqueci_email:<sha256>` (85 caracteres).
 *
 * Truncar seria pior: duas chaves longas com o mesmo começo virariam a mesma e
 * dividiriam o contador. Por isso o excesso é comprimido num hash, mantendo o
 * prefixo legível para quem for ler a tabela.
 */
function rate_limit_chave(string $chave): string
{
    if (strlen($chave) <= 80) {
        return $chave;
    }
    $resumo = substr(sha1($chave), 0, 40);
    return substr($chave, 0, 39) . '~' . $resumo;   // 39 + 1 + 40 = 80
}

/**
 * Incrementa o contador da chave na janela atual e responde 429 se exceder o limite.
 * $limite = requisições permitidas por janela (por minuto por padrão).
 */
function rate_limit_ou_429(string $chave, int $limite, int $janelaSeg = 60): void
{
    if ($limite <= 0) {
        return; // 0/negativo = sem limite
    }
    $chave = rate_limit_chave($chave);
    $bucket = intdiv(time(), $janelaSeg);
    try {
        db_executar(
            "INSERT INTO rate_limite (chave, janela, contador) VALUES (:c, :j, 1)
             ON DUPLICATE KEY UPDATE contador = contador + 1",
            [':c' => $chave, ':j' => $bucket]
        );
        $row = db_primeiro("SELECT contador FROM rate_limite WHERE chave = :c AND janela = :j",
            [':c' => $chave, ':j' => $bucket]);
        $contador = (int) ($row['contador'] ?? 0);
    } catch (Throwable $e) {
        error_log('rate_limit falhou (fail-open): ' . $e->getMessage());
        return;
    }

    if ($contador > $limite) {
        $reset = ($bucket + 1) * $janelaSeg - time();
        if (!headers_sent()) {
            header('Retry-After: ' . max(1, $reset));
        }
        responder_erro('Muitas requisições. Tente novamente em instantes.', 429, [
            ['field' => null, 'code' => 'RATE_LIMIT', 'message' => "Limite de $limite requisições por minuto excedido."],
        ]);
    }
}
