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

    // --- Clientes B2B (tenants) ---
    'POST /api/v1/interno/clientes' => ['arquivo' => 'interno/clientes.php', 'funcao' => 'rota_criar_cliente'],
    'GET  /api/v1/interno/clientes' => ['arquivo' => 'interno/clientes.php', 'funcao' => 'rota_listar_clientes'],

    // --- Clínicas da rede credenciada + atribuição (RN-012) ---
    'POST /api/v1/interno/clinicas' => ['arquivo' => 'interno/clinicas.php', 'funcao' => 'rota_criar_clinica'],
    'GET  /api/v1/interno/clinicas' => ['arquivo' => 'interno/clinicas.php', 'funcao' => 'rota_listar_clinicas'],
    'POST /api/v1/interno/campanhas/{id}/atribuir-clinica' => ['arquivo' => 'interno/clinicas.php', 'funcao' => 'rota_atribuir_clinica'],

    // --- Catálogo de vacinas ---
    'GET  /api/v1/interno/vacinas' => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_listar_vacinas'],

    // --- Campanhas (RN-001) ---
    'POST /api/v1/interno/campanhas'                => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_criar_campanha'],
    'GET  /api/v1/interno/campanhas'                => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_listar_campanhas'],
    'GET  /api/v1/interno/campanhas/{id}'           => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_obter_campanha'],
    'PUT  /api/v1/interno/campanhas/{id}'           => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_editar_campanha'],
    'POST /api/v1/interno/campanhas/{id}/vacinas'   => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_definir_vacinas_campanha'],
    'POST /api/v1/interno/campanhas/{id}/encerrar'  => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_encerrar_campanha'],

    // --- Elegíveis (bloco 2) ---
    'POST /api/v1/interno/campanhas/{id}/elegiveis/importar' => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_importar_elegiveis'],
    'GET  /api/v1/interno/campanhas/{id}/elegiveis'          => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_listar_elegiveis'],

    // --- Credenciais de API (parceiro) ---
    'POST /api/v1/interno/credenciais'             => ['arquivo' => 'interno/credenciais.php', 'funcao' => 'rota_emitir_credencial'],
    'GET  /api/v1/interno/credenciais'             => ['arquivo' => 'interno/credenciais.php', 'funcao' => 'rota_listar_credenciais'],
    'POST /api/v1/interno/credenciais/{id}/revogar' => ['arquivo' => 'interno/credenciais.php', 'funcao' => 'rota_revogar_credencial'],

    // --- Aplicação (bloco 3) ---
    'POST /api/v1/interno/aplicacoes'              => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_registrar_aplicacao'],
    'POST /api/v1/interno/aplicacoes-lote'         => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_registrar_aplicacoes_lote'],
    'POST /api/v1/interno/aplicacoes/{id}/retificar' => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_retificar_aplicacao'],

    // --- Tabela verdade e dashboard (bloco 3) ---
    'GET  /api/v1/interno/campanhas/{id}/tabela-verdade' => ['arquivo' => 'interno/tabela_verdade.php', 'funcao' => 'rota_tabela_verdade'],
    'GET  /api/v1/interno/campanhas/{id}/dashboard'      => ['arquivo' => 'interno/tabela_verdade.php', 'funcao' => 'rota_dashboard'],
    'GET  /api/v1/interno/campanhas/{id}/exportar'       => ['arquivo' => 'interno/tabela_verdade.php', 'funcao' => 'rota_exportar_tabela_verdade'],

    // --- Grupo parceiro (Bearer + escopo) ---
    'POST /api/v1/parceiro/campanhas/{id}/elegiveis'        => ['arquivo' => 'parceiro/elegiveis.php', 'funcao' => 'rota_parceiro_ingerir_elegiveis'],
    'GET  /api/v1/parceiro/campanhas/{id}/elegiveis/{cpf}'  => ['arquivo' => 'parceiro/aplicacoes.php', 'funcao' => 'rota_parceiro_consultar_elegivel'],
    'POST /api/v1/parceiro/aplicacoes'                      => ['arquivo' => 'parceiro/aplicacoes.php', 'funcao' => 'rota_parceiro_registrar_aplicacao'],
    'POST /api/v1/parceiro/aplicacoes-lote'                 => ['arquivo' => 'parceiro/aplicacoes.php', 'funcao' => 'rota_parceiro_registrar_aplicacoes_lote'],
];
