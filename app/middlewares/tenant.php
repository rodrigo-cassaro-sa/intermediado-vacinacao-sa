<?php
// ============================================================================
// app/middlewares/tenant.php
// Função: derivar e impor o tenant_id/escopo no backend — NUNCA vindo do corpo.
// Base: docs/10 (regra de ouro) e RN-006/007. Todo acesso de negócio filtra tenant.
// ============================================================================

/**
 * Verifica se um usuário logado pode acessar uma campanha.
 * - Internos (super_admin, operador_interno): acessam qualquer tenant.
 * - cliente_b2b / profissional_saude: só a campanha do próprio tenant.
 * Responde 403/404 quando não autorizado. Devolve a linha da campanha.
 */
function exigir_campanha_do_usuario(array $usuario, int $campanhaId): array
{
    $campanha = db_primeiro(
        "SELECT * FROM campanha WHERE id = :id AND excluido_em IS NULL LIMIT 1",
        [':id' => $campanhaId]
    );
    if ($campanha === null) {
        responder_erro('Campanha inexistente.', 404, [
            ['field' => null, 'code' => 'CAMPANHA_NAO_ENCONTRADA', 'message' => 'Campanha não encontrada.'],
        ]);
    }

    $internos = ['super_admin', 'operador_interno'];
    if (in_array($usuario['perfil'], $internos, true)) {
        return $campanha; // acesso global
    }

    // Demais perfis: campanha precisa ser do mesmo tenant do usuário.
    if ($usuario['tenant_id'] === null || (int) $campanha['tenant_id'] !== (int) $usuario['tenant_id']) {
        registrar_auditoria('permissao.negada', [
            'tenant_id'     => $usuario['tenant_id'],
            'ator_tipo'     => 'usuario',
            'ator_id'       => $usuario['id'],
            'origem'        => 'portal',
            'entidade_tipo' => 'campanha',
            'entidade_id'   => $campanhaId,
        ]);
        responder_erro('Sem acesso a esta campanha.', 403, [
            ['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'Você não tem acesso a esta campanha.'],
        ]);
    }
    return $campanha;
}

/** Lê o id de campanha da rota como inteiro válido (ou 404). */
function id_campanha_rota($valor): int
{
    if (!is_numeric($valor) || (int) $valor <= 0) {
        responder_erro('Campanha inexistente.', 404, [
            ['field' => null, 'code' => 'CAMPANHA_NAO_ENCONTRADA', 'message' => 'Identificador inválido.'],
        ]);
    }
    return (int) $valor;
}
