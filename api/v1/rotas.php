<?php
// ============================================================================
// api/v1/rotas.php
// Função: tabela de rotas da API v1. Cada rota aponta para um handler (arquivo
//         que define uma função). Base: docs/09 (contrato de endpoints).
// Formato: [ 'METODO CAMINHO' => ['arquivo' => ..., 'funcao' => ...] ]
// Segmentos dinâmicos usam {param}. Ex.: /campanhas/{id}/elegiveis
// ============================================================================

return [
    // --- Saúde / diagnóstico ---
    'GET /api/v1/health' => ['arquivo' => 'health.php', 'funcao' => 'rota_health'],

    // --- Autenticação (interno) ---
    'POST /api/v1/interno/auth/login'  => ['arquivo' => 'interno/auth.php', 'funcao' => 'rota_login'],
    'POST /api/v1/interno/auth/logout' => ['arquivo' => 'interno/auth.php', 'funcao' => 'rota_logout'],
    'GET  /api/v1/interno/auth/eu'     => ['arquivo' => 'interno/auth.php', 'funcao' => 'rota_eu'],

    // --- Próximos endpoints do doc 09 (a implementar) ---
    // 'POST /api/v1/interno/campanhas' => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_criar_campanha'],
    // 'POST /api/v1/interno/campanhas/{id}/elegiveis/importar' => [...],
    // 'POST /api/v1/interno/aplicacoes' => [...],
    // 'POST /api/v1/parceiro/campanhas/{id}/elegiveis' => [...],
    // 'GET  /api/v1/parceiro/campanhas/{id}/elegiveis/{cpf}' => [...],
    // 'POST /api/v1/parceiro/aplicacoes' => [...],
];
