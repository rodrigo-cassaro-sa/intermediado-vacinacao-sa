<?php
// ============================================================================
// app/helpers/idempotencia.php
// Idempotência das escritas de máquina (API/lote): se o mesmo Idempotency-Key
// for reenviado (retry de rede), devolve a resposta guardada em vez de duplicar.
// ============================================================================

/** Lê o header Idempotency-Key (ou null). */
function idempotencia_chave(): ?string
{
    $c = $_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? '';
    $c = trim((string) $c);
    return $c !== '' ? substr($c, 0, 120) : null;
}

/**
 * Se houver chave e já existir resposta para (escopo, chave), reenvia-a e encerra.
 * Chamar no início do handler de escrita.
 */
function idempotencia_replay(string $escopo): void
{
    $chave = idempotencia_chave();
    if ($chave === null) {
        return;
    }
    $hit = db_primeiro(
        "SELECT http_status, resposta FROM idempotencia WHERE escopo = :e AND chave = :c LIMIT 1",
        [':e' => $escopo, ':c' => $chave]
    );
    if ($hit !== null) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('X-Request-Id: ' . request_id());
            header('Idempotent-Replay: true');
            http_response_code((int) $hit['http_status']);
        }
        echo $hit['resposta'];
        exit;
    }
}

/**
 * Finaliza uma operação idempotente: guarda a resposta (se houver chave) e responde.
 * Substitui responder_sucesso nos endpoints que suportam Idempotency-Key.
 */
function responder_idempotente(string $escopo, $data, string $mensagem = 'Operação realizada com sucesso.', int $http = 201): void
{
    $corpo = [
        'success' => true,
        'message' => $mensagem,
        'data'    => $data,
        'meta'    => null,
        'errors'  => [],
    ];
    $chave = idempotencia_chave();
    if ($chave !== null) {
        try {
            db_executar(
                "INSERT INTO idempotencia (escopo, chave, http_status, resposta)
                 VALUES (:e, :c, :h, :r) ON DUPLICATE KEY UPDATE chave = chave",
                [':e' => $escopo, ':c' => $chave, ':h' => $http, ':r' => json_encode($corpo, JSON_UNESCAPED_UNICODE)]
            );
        } catch (Throwable $e) {
            error_log('idempotencia_salvar falhou: ' . $e->getMessage());
        }
    }
    responder($http, $corpo);
}
