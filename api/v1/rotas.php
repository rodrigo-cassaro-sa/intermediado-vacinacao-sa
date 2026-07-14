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

    // --- Dashboard admin: visão geral consolidada do escopo ---
    'GET  /api/v1/interno/dashboard' => ['arquivo' => 'interno/dashboard.php', 'funcao' => 'rota_dashboard_visao_geral'],

    // --- Portal D1: jornada (estado, consentimento, onboarding) ---
    'GET  /api/v1/interno/portal/estado'        => ['arquivo' => 'interno/portal.php', 'funcao' => 'rota_portal_estado'],
    'POST /api/v1/interno/consentimento'        => ['arquivo' => 'interno/portal.php', 'funcao' => 'rota_consentir'],
    'POST /api/v1/interno/onboarding/concluir'  => ['arquivo' => 'interno/portal.php', 'funcao' => 'rota_onboarding_concluir'],

    // --- Portal D0: acesso (grupos, unidades, usuários, atribuições) ---
    'GET  /api/v1/interno/acesso/eu'                => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_acesso_eu'],
    'POST /api/v1/interno/grupos'                   => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_criar_grupo'],
    'GET  /api/v1/interno/grupos'                   => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_listar_grupos'],
    'POST /api/v1/interno/grupos/{id}/sigla'        => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_definir_sigla_grupo'],
    'POST /api/v1/interno/clientes/{id}/grupo'      => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_vincular_cliente_grupo'],
    'POST /api/v1/interno/clientes/{id}/unidades'   => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_criar_unidade'],
    'GET  /api/v1/interno/clientes/{id}/unidades'   => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_listar_unidades'],
    'GET  /api/v1/interno/usuarios'                 => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_listar_usuarios'],
    'POST /api/v1/interno/usuarios'                 => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_criar_usuario_portal'],
    'POST /api/v1/interno/usuarios/{id}/atribuicoes' => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_adicionar_atribuicao'],
    'GET  /api/v1/interno/usuarios/{id}/atribuicoes' => ['arquivo' => 'interno/acesso.php', 'funcao' => 'rota_listar_atribuicoes'],

    // --- Clientes B2B (tenants) ---
    'POST /api/v1/interno/clientes' => ['arquivo' => 'interno/clientes.php', 'funcao' => 'rota_criar_cliente'],
    'GET  /api/v1/interno/clientes' => ['arquivo' => 'interno/clientes.php', 'funcao' => 'rota_listar_clientes'],
    'POST /api/v1/interno/clientes/{id}/sigla' => ['arquivo' => 'interno/clientes.php', 'funcao' => 'rota_definir_sigla_cliente'],

    // --- Clínicas da rede credenciada + atribuição (RN-012) ---
    'POST /api/v1/interno/clinicas' => ['arquivo' => 'interno/clinicas.php', 'funcao' => 'rota_criar_clinica'],
    'GET  /api/v1/interno/clinicas' => ['arquivo' => 'interno/clinicas.php', 'funcao' => 'rota_listar_clinicas'],
    'POST /api/v1/interno/campanhas/{id}/atribuir-clinica' => ['arquivo' => 'interno/clinicas.php', 'funcao' => 'rota_atribuir_clinica'],

    // --- Integrações: webhooks (Fase A) ---
    'POST /api/v1/interno/webhooks'                 => ['arquivo' => 'interno/webhooks.php', 'funcao' => 'rota_criar_webhook'],
    'GET  /api/v1/interno/webhooks'                 => ['arquivo' => 'interno/webhooks.php', 'funcao' => 'rota_listar_webhooks'],
    'POST /api/v1/interno/webhooks/{id}/desativar'  => ['arquivo' => 'interno/webhooks.php', 'funcao' => 'rota_desativar_webhook'],
    'GET  /api/v1/interno/webhooks/{id}/entregas'   => ['arquivo' => 'interno/webhooks.php', 'funcao' => 'rota_entregas_webhook'],
    'POST /api/v1/interno/webhooks/{id}/testar'     => ['arquivo' => 'interno/webhooks.php', 'funcao' => 'rota_testar_webhook'],

    // --- Preços e faturamento (item 4) ---
    'POST /api/v1/interno/clientes/{id}/precos'   => ['arquivo' => 'interno/precos.php', 'funcao' => 'rota_definir_preco_cliente'],
    'GET  /api/v1/interno/clientes/{id}/precos'   => ['arquivo' => 'interno/precos.php', 'funcao' => 'rota_listar_precos_cliente'],
    'POST /api/v1/interno/clinicas/{id}/precos'   => ['arquivo' => 'interno/precos.php', 'funcao' => 'rota_definir_preco_clinica'],
    'GET  /api/v1/interno/clinicas/{id}/precos'   => ['arquivo' => 'interno/precos.php', 'funcao' => 'rota_listar_precos_clinica'],
    'GET  /api/v1/interno/campanhas/{id}/faturamento-cliente'  => ['arquivo' => 'interno/faturamento.php', 'funcao' => 'rota_faturamento_cliente'],
    'GET  /api/v1/interno/campanhas/{id}/faturamento-clinicas' => ['arquivo' => 'interno/faturamento.php', 'funcao' => 'rota_faturamento_clinicas'],

    // --- Observabilidade (item 13) ---
    'GET  /api/v1/interno/metricas'  => ['arquivo' => 'interno/observabilidade.php', 'funcao' => 'rota_metricas'],
    'GET  /api/v1/interno/auditoria' => ['arquivo' => 'interno/observabilidade.php', 'funcao' => 'rota_auditoria_recente'],
    'GET  /api/v1/interno/portal/auditoria' => ['arquivo' => 'interno/observabilidade.php', 'funcao' => 'rota_auditoria_portal'],

    // --- Relatórios: carteira consolidada (9c) e resumo ano a ano ---
    'GET  /api/v1/interno/pacientes/{cpf}/carteira'         => ['arquivo' => 'interno/relatorios.php', 'funcao' => 'rota_carteira_paciente'],
    'GET  /api/v1/interno/clientes/{id}/campanhas-resumo'   => ['arquivo' => 'interno/relatorios.php', 'funcao' => 'rota_resumo_campanhas_cliente'],

    // --- Migração: importar vacinados de anos anteriores (RN-027, interno-only) ---
    'POST /api/v1/interno/clientes/{id}/vacinados-historico/importar' => ['arquivo' => 'interno/vacinados_historico.php', 'funcao' => 'rota_importar_vacinados_historico'],
    'GET  /api/v1/interno/importacoes-historico/{id}' => ['arquivo' => 'interno/vacinados_historico.php', 'funcao' => 'rota_status_importacao_historico'],

    // --- Catálogo de vacinas ---
    'GET  /api/v1/interno/vacinas' => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_listar_vacinas'],
    'POST /api/v1/interno/vacinas' => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_criar_vacina'],
    'PUT  /api/v1/interno/vacinas/{id}' => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_editar_vacina'],
    'POST /api/v1/interno/vacinas/{id}/sigla' => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_definir_sigla_vacina'],

    // --- Campanhas (RN-001) ---
    'POST /api/v1/interno/campanhas'                => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_criar_campanha'],
    'GET  /api/v1/interno/campanhas'                => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_listar_campanhas'],
    'GET  /api/v1/interno/campanhas/{id}'           => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_obter_campanha'],
    'PUT  /api/v1/interno/campanhas/{id}'           => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_editar_campanha'],
    'POST /api/v1/interno/campanhas/{id}/vacinas'   => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_definir_vacinas_campanha'],
    'POST /api/v1/interno/campanhas/{id}/encerrar'  => ['arquivo' => 'interno/campanhas.php', 'funcao' => 'rota_encerrar_campanha'],

    // --- Elegíveis (bloco 2) ---
    'POST /api/v1/interno/campanhas/{id}/elegiveis/importar'    => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_importar_elegiveis'],
    'POST /api/v1/interno/campanhas/{id}/elegiveis/sincronizar' => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_sincronizar_elegiveis'],
    'GET  /api/v1/interno/campanhas/{id}/elegiveis'          => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_listar_elegiveis'],
    'POST /api/v1/interno/campanhas/{id}/elegiveis/remover'  => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_remover_elegiveis'],

    // --- Importações (assíncronas) + relatório de erros (item 9a) ---
    'GET  /api/v1/interno/campanhas/{id}/importacoes'        => ['arquivo' => 'interno/importacoes.php', 'funcao' => 'rota_listar_importacoes'],
    'GET  /api/v1/interno/importacoes/{id}'                  => ['arquivo' => 'interno/importacoes.php', 'funcao' => 'rota_status_importacao'],
    'GET  /api/v1/interno/importacoes/{id}/erros/exportar'   => ['arquivo' => 'interno/importacoes.php', 'funcao' => 'rota_exportar_erros_importacao'],
    'POST /api/v1/interno/elegiveis/{id}/situacao'           => ['arquivo' => 'interno/elegiveis.php', 'funcao' => 'rota_definir_situacao_elegivel'],
    'PUT  /api/v1/interno/elegiveis/{id}'                    => ['arquivo' => 'interno/elegivel_edicao.php', 'funcao' => 'rota_editar_elegivel'],
    'GET  /api/v1/interno/elegiveis/{id}/historico'         => ['arquivo' => 'interno/elegivel_edicao.php', 'funcao' => 'rota_historico_elegivel'],

    // --- Credenciais de API (parceiro) ---
    'POST /api/v1/interno/credenciais'             => ['arquivo' => 'interno/credenciais.php', 'funcao' => 'rota_emitir_credencial'],
    'GET  /api/v1/interno/credenciais'             => ['arquivo' => 'interno/credenciais.php', 'funcao' => 'rota_listar_credenciais'],
    'POST /api/v1/interno/credenciais/{id}/revogar' => ['arquivo' => 'interno/credenciais.php', 'funcao' => 'rota_revogar_credencial'],

    // --- Aplicação (bloco 3) ---
    'POST /api/v1/interno/aplicacoes'              => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_registrar_aplicacao'],
    'POST /api/v1/interno/aplicacoes-lote'         => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_registrar_aplicacoes_lote'],
    'POST /api/v1/interno/aplicacoes/{id}/retificar' => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_retificar_aplicacao'],
    'POST /api/v1/interno/aplicacoes/{id}/estornar'  => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_estornar_aplicacao'],
    'GET  /api/v1/interno/aplicacoes/{id}/historico' => ['arquivo' => 'interno/aplicacoes.php', 'funcao' => 'rota_historico_aplicacao'],

    // --- Tabela verdade e dashboard (bloco 3) ---
    'GET  /api/v1/interno/campanhas/{id}/tabela-verdade' => ['arquivo' => 'interno/tabela_verdade.php', 'funcao' => 'rota_tabela_verdade'],
    'GET  /api/v1/interno/campanhas/{id}/dashboard'      => ['arquivo' => 'interno/tabela_verdade.php', 'funcao' => 'rota_dashboard'],
    'GET  /api/v1/interno/campanhas/{id}/exportar'       => ['arquivo' => 'interno/tabela_verdade.php', 'funcao' => 'rota_exportar_tabela_verdade'],

    // --- Grupo parceiro (Bearer + escopo) ---
    'POST /api/v1/parceiro/campanhas/{id}/elegiveis'             => ['arquivo' => 'parceiro/elegiveis.php', 'funcao' => 'rota_parceiro_ingerir_elegiveis'],
    'POST /api/v1/parceiro/campanhas/{id}/elegiveis/sincronizar' => ['arquivo' => 'parceiro/elegiveis.php', 'funcao' => 'rota_parceiro_sincronizar_elegiveis'],
    'POST /api/v1/parceiro/elegiveis/{id}/situacao'         => ['arquivo' => 'parceiro/elegiveis.php', 'funcao' => 'rota_parceiro_definir_situacao'],
    'GET  /api/v1/parceiro/campanhas/{id}/elegiveis/{cpf}'  => ['arquivo' => 'parceiro/aplicacoes.php', 'funcao' => 'rota_parceiro_consultar_elegivel'],
    'POST /api/v1/parceiro/aplicacoes'                      => ['arquivo' => 'parceiro/aplicacoes.php', 'funcao' => 'rota_parceiro_registrar_aplicacao'],
    'POST /api/v1/parceiro/aplicacoes-lote'                 => ['arquivo' => 'parceiro/aplicacoes.php', 'funcao' => 'rota_parceiro_registrar_aplicacoes_lote'],

    // --- App IN COMPANY por token (PWA/app/terceiro) — Fase C ---
    'GET  /api/v1/parceiro/incompany/campanhas/{id}/elegiveis/{cpf}' => ['arquivo' => 'parceiro/incompany.php', 'funcao' => 'rota_incompany_consultar_elegivel'],
    'POST /api/v1/parceiro/incompany/aplicacoes'                     => ['arquivo' => 'parceiro/incompany.php', 'funcao' => 'rota_incompany_registrar_aplicacao'],
    'POST /api/v1/parceiro/incompany/aplicacoes-lote'                => ['arquivo' => 'parceiro/incompany.php', 'funcao' => 'rota_incompany_registrar_aplicacoes_lote'],

    // --- API externa de CONSULTA (token tipo consulta, escopo tenant) — Fase A3 ---
    'GET  /api/v1/parceiro/carteira/{cpf}'                  => ['arquivo' => 'parceiro/consulta.php', 'funcao' => 'rota_consulta_carteira'],
    'GET  /api/v1/parceiro/campanhas/{id}/tabela-verdade'   => ['arquivo' => 'parceiro/consulta.php', 'funcao' => 'rota_consulta_tabela_verdade'],
];
