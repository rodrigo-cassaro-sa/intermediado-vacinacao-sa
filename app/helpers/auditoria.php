<?php
// ============================================================================
// app/helpers/auditoria.php
// Função: gravar trilha de auditoria (log_auditoria) com metadata mascarada.
// Base: docs/10 §4. Nunca gravar CPF cru, token ou senha.
// ============================================================================

/**
 * Registra um evento de auditoria.
 * $contexto: [
 *   'tenant_id'    => ?int,
 *   'ator_tipo'    => 'usuario'|'credencial_api',
 *   'ator_id'      => ?int,
 *   'origem'       => 'admin'|'portal'|'app'|'api_parceiro',
 *   'entidade_tipo'=> ?string,
 *   'entidade_id'  => ?int,
 *   'metadata'     => ?array  (será mascarada e serializada em JSON)
 * ]
 */
function registrar_auditoria(string $evento, array $contexto = []): void
{
    try {
        $metadata = $contexto['metadata'] ?? null;
        $metadataMasc = is_array($metadata) ? mascarar_metadata($metadata) : null;
        if ($metadataMasc !== null) {
            $metadata = json_encode($metadataMasc, JSON_UNESCAPED_UNICODE);
        }

        db_executar(
            "INSERT INTO log_auditoria
                (tenant_id, ator_tipo, ator_id, evento, origem, entidade_tipo, entidade_id, request_id, ip, metadata, data_hora)
             VALUES
                (:tenant_id, :ator_tipo, :ator_id, :evento, :origem, :entidade_tipo, :entidade_id, :request_id, :ip, :metadata, NOW())",
            [
                ':tenant_id'     => $contexto['tenant_id']     ?? null,
                ':ator_tipo'     => $contexto['ator_tipo']     ?? 'usuario',
                ':ator_id'       => $contexto['ator_id']       ?? null,
                ':evento'        => $evento,
                ':origem'        => $contexto['origem']        ?? 'admin',
                ':entidade_tipo' => $contexto['entidade_tipo'] ?? null,
                ':entidade_id'   => $contexto['entidade_id']   ?? null,
                ':request_id'    => request_id(),
                ':ip'            => $_SERVER['REMOTE_ADDR']     ?? null,
                ':metadata'      => $metadata,
            ]
        );
    } catch (Throwable $e) {
        // Auditoria não pode derrubar a operação; só registra em log técnico.
        error_log('Falha ao registrar auditoria: ' . $e->getMessage());
    }

    // Dispara webhook de saída para eventos da whitelist (fail-safe).
    if (function_exists('disparar_evento')) {
        disparar_evento($evento, [
            'entidade_tipo' => $contexto['entidade_tipo'] ?? null,
            'entidade_id'   => $contexto['entidade_id']   ?? null,
            'tenant_id'     => $contexto['tenant_id']     ?? null,
            'metadata'      => $metadataMasc,
        ], isset($contexto['tenant_id']) ? (int) $contexto['tenant_id'] : null);
    }
}

/** Mascara chaves sensíveis dentro do metadata antes de persistir. */
function mascarar_metadata(array $dados): array
{
    $sensiveis = ['cpf', 'senha', 'senha_hash', 'token', 'token_hash', 'authorization'];
    foreach ($dados as $chave => $valor) {
        $chaveLower = strtolower((string) $chave);
        if (in_array($chaveLower, $sensiveis, true)) {
            $dados[$chave] = ($chaveLower === 'cpf') ? mascarar_cpf((string) $valor) : '***';
        } elseif (is_array($valor)) {
            $dados[$chave] = mascarar_metadata($valor);
        }
    }
    return $dados;
}
