<?php
// ============================================================================
// api/v1/interno/portal.php
// Portal D1: estado do usuário (consentimento LGPD + onboarding) e ações da jornada.
// Grupo interno (sessão). Base: doc 07 §3.1.
// ============================================================================

// Versão vigente do termo de consentimento. Trocar força novo aceite.
const TERMO_VERSAO = '2026-07-v1';

/** GET /api/v1/interno/portal/estado — o que o portal precisa saber ao abrir. */
function rota_portal_estado(array $params): void
{
    $usuario = exigir_login();
    $u = db_primeiro(
        "SELECT nome, email, consentimento_em, versao_termo, onboarding_em FROM usuario WHERE id = :id LIMIT 1",
        [':id' => (int) $usuario['id']]
    );

    $consentido  = $u && $u['consentimento_em'] !== null && $u['versao_termo'] === TERMO_VERSAO;
    $onboarding  = $u && $u['onboarding_em'] !== null;

    responder_sucesso([
        'usuario'      => ['id' => (int) $usuario['id'], 'nome' => $u['nome'] ?? $usuario['nome'], 'email' => $u['email'] ?? null],
        'interno'      => usuario_eh_interno($usuario),
        'consentido'   => $consentido,
        'termo_versao' => TERMO_VERSAO,
        'onboarding_concluido' => $onboarding,
        'atribuicoes'  => atribuicoes_do_usuario((int) $usuario['id']),
    ], 'OK.');
}

/** POST /api/v1/interno/consentimento — registra o aceite do termo (LGPD). */
function rota_consentir(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();
    db_executar(
        "UPDATE usuario SET consentimento_em = NOW(), versao_termo = :v WHERE id = :id",
        [':v' => TERMO_VERSAO, ':id' => (int) $usuario['id']]
    );
    registrar_auditoria('portal.consentimento', [
        'ator_tipo' => 'usuario', 'ator_id' => (int) $usuario['id'], 'origem' => 'portal',
        'entidade_tipo' => 'usuario', 'entidade_id' => (int) $usuario['id'],
        'metadata' => ['versao_termo' => TERMO_VERSAO],
    ]);
    responder_sucesso(['consentido' => true], 'Consentimento registrado.', 201);
}

/** POST /api/v1/interno/onboarding/concluir — marca o onboarding como concluído. */
function rota_onboarding_concluir(array $params): void
{
    $usuario = exigir_login();
    exigir_csrf();
    db_executar("UPDATE usuario SET onboarding_em = NOW() WHERE id = :id", [':id' => (int) $usuario['id']]);
    responder_sucesso(['onboarding_concluido' => true], 'Onboarding concluído.', 201);
}
