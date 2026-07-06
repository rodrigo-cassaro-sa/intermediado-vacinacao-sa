<?php
// ============================================================================
// public/index.php
// Front controller único (docs/05). Somente public/ é o document root.
// Carrega o bootstrap, resolve a rota da API e despacha para o handler.
// ============================================================================

require_once dirname(__DIR__) . '/app/bootstrap.php';

$metodo  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$caminho = rtrim($caminho, '/') ?: '/';

// Só tratamos /api aqui; páginas HTML (admin/portal/app) são servidas estáticas.
if (strpos($caminho, '/api/') !== 0) {
    responder_erro('Rota não encontrada.', 404, [
        ['field' => null, 'code' => 'ROTA_NAO_ENCONTRADA', 'message' => 'Recurso inexistente.'],
    ]);
}

$rotas = require dirname(__DIR__) . '/api/v1/rotas.php';

/**
 * Casa o caminho requisitado contra os padrões de rota (com {param}).
 * Devolve [handler, params] ou [null, []].
 */
function casar_rota(array $rotas, string $metodo, string $caminho): array
{
    foreach ($rotas as $chave => $handler) {
        [$m, $padrao] = preg_split('/\s+/', trim($chave));
        if (strcasecmp($m, $metodo) !== 0) {
            continue;
        }
        $regex = '#^' . preg_replace('/\{[^}]+\}/', '([^/]+)', $padrao) . '$#';
        if (preg_match($regex, $caminho, $m2)) {
            array_shift($m2);
            preg_match_all('/\{([^}]+)\}/', $padrao, $nomes);
            $params = array_combine($nomes[1], $m2) ?: [];
            return [$handler, $params];
        }
    }
    return [null, []];
}

[$handler, $params] = casar_rota($rotas, $metodo, $caminho);

if ($handler === null) {
    responder_erro('Rota não encontrada.', 404, [
        ['field' => null, 'code' => 'ROTA_NAO_ENCONTRADA', 'message' => 'Recurso inexistente.'],
    ]);
}

require dirname(__DIR__) . '/api/v1/' . $handler['arquivo'];
$funcao = $handler['funcao'];
$funcao($params);
