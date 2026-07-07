<?php
// ============================================================================
// app/helpers/resposta.php
// Função: padronizar respostas JSON conforme o padrão oficial do orquestrador.
// Formato: { success, message, data, meta, errors }. Base: docs/09.
// ============================================================================

/** Gera/recupera o request_id da requisição (correlaciona com log_auditoria). */
function request_id(): string
{
    static $id = null;
    if ($id === null) {
        $id = 'req_' . bin2hex(random_bytes(8));
    }
    return $id;
}

/** Envia resposta JSON e encerra a execução. */
function responder(int $http, array $corpo): void
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Request-Id: ' . request_id());
        http_response_code($http);
    }
    echo json_encode($corpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Resposta de sucesso padronizada. */
function responder_sucesso($data = [], string $mensagem = 'Operação realizada com sucesso.', int $http = 200, $meta = null): void
{
    responder($http, [
        'success' => true,
        'message' => $mensagem,
        'data'    => $data,
        'meta'    => $meta,
        'errors'  => [],
    ]);
}

/**
 * Resposta de erro padronizada.
 * $errors: lista de { field, code, message }.
 */
function responder_erro(string $mensagem, int $http = 400, array $errors = [], array $meta = []): void
{
    $meta = array_merge(['request_id' => request_id()], $meta);
    responder($http, [
        'success' => false,
        'message' => $mensagem,
        'data'    => null,
        'meta'    => $meta,
        'errors'  => $errors,
    ]);
}

/** Atalho para erro de validação (400) com lista de campos. */
function erro_validacao(array $errors, string $mensagem = 'Verifique os campos destacados.'): void
{
    responder_erro($mensagem, 400, $errors);
}

/** Lê page/por_pagina do querystring com limites seguros. Devolve [page, porPagina, offset]. */
function paginacao(): array
{
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $porPagina = isset($_GET['por_pagina']) && is_numeric($_GET['por_pagina'])
        ? min(100, max(1, (int) $_GET['por_pagina'])) : 50;
    $offset = ($page - 1) * $porPagina;
    return [$page, $porPagina, $offset];
}

/**
 * Paginação por cursor (keyset) — eficiente em milhões de linhas (evita OFFSET).
 * Lê ?apos (último id/chave da página anterior) e ?por_pagina. Devolve [apos, limite].
 */
function paginacao_keyset(): array
{
    $por = isset($_GET['por_pagina']) && is_numeric($_GET['por_pagina'])
        ? min(200, max(1, (int) $_GET['por_pagina'])) : 50;
    $apos = isset($_GET['apos']) && is_numeric($_GET['apos']) ? (int) $_GET['apos'] : 0;
    return [$apos, $por];
}

/** Lê e decodifica o corpo JSON da requisição (ou [] se vazio). */
function corpo_json(): array
{
    $bruto = file_get_contents('php://input');
    if ($bruto === '' || $bruto === false) {
        return [];
    }
    $dados = json_decode($bruto, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($dados)) {
        responder_erro('Estrutura inválida.', 400, [
            ['field' => null, 'code' => 'PAYLOAD_INVALIDO', 'message' => 'Corpo JSON inválido.'],
        ]);
    }
    return $dados;
}
