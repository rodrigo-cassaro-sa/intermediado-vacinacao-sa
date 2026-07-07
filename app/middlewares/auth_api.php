<?php
// ============================================================================
// app/middlewares/auth_api.php
// Função: autenticação de MÁQUINAS por Bearer token (grupo /api/v1/parceiro)
//         e aplicação do escopo por campanha (RN-009). Base: docs/09, docs/10.
// ============================================================================

/** Extrai o token Bearer do header Authorization. */
function token_bearer(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('apache_request_headers')) {
        $h = apache_request_headers();
        $header = $h['Authorization'] ?? '';
    }
    if (preg_match('/^Bearer\s+(.+)$/i', trim($header), $m)) {
        return trim($m[1]);
    }
    return null;
}

/**
 * Autentica a credencial de parceiro pelo token.
 * Devolve a linha de credencial_api (ativa e não expirada) ou responde 401.
 * Guardamos apenas token_hash no banco (docs/10).
 */
function exigir_credencial(string $tipoEsperado): array
{
    $token = token_bearer();
    if ($token === null) {
        responder_erro('Autenticação necessária.', 401, [
            ['field' => null, 'code' => 'TOKEN_AUSENTE', 'message' => 'Envie o token Bearer.'],
        ]);
    }

    $hash = hash('sha256', $token);
    $cred = db_primeiro(
        "SELECT * FROM credencial_api
          WHERE token_hash = :hash AND ativo = 1 AND revogado_em IS NULL
            AND (expira_em IS NULL OR expira_em > NOW())
          LIMIT 1",
        [':hash' => $hash]
    );

    if ($cred === null || $cred['tipo'] !== $tipoEsperado) {
        responder_erro('Credencial inválida.', 401, [
            ['field' => null, 'code' => 'CREDENCIAL_INVALIDA', 'message' => 'Token inválido ou sem permissão.'],
        ]);
    }

    // Rate limit por credencial (item 9b/12): limite_rpm da própria credencial
    // ou o padrão do ambiente. Protege a API de excesso de acessos/consultas.
    $limite = isset($cred['limite_rpm']) && $cred['limite_rpm'] !== null
        ? (int) $cred['limite_rpm']
        : (int) env('RATE_LIMIT_RPM', 120);
    rate_limit_ou_429('cred:' . $cred['id'], $limite);

    return $cred;
}

/** Verifica (sem encerrar) se a credencial tem escopo na campanha. */
function credencial_tem_escopo(array $credencial, int $campanhaId): bool
{
    $escopo = $credencial['escopo_campanha_id'] ?? null;
    return $escopo !== null && (int) $escopo === $campanhaId;
}

/**
 * Garante que a campanha da URL está dentro do escopo da credencial (RN-009).
 * Responde 403 FORA_DO_ESCOPO caso contrário.
 */
function exigir_escopo_campanha(array $credencial, int $campanhaId): void
{
    if (!credencial_tem_escopo($credencial, $campanhaId)) {
        registrar_auditoria('permissao.negada', [
            'ator_tipo'     => 'credencial_api',
            'ator_id'       => (int) $credencial['id'],
            'origem'        => 'api_parceiro',
            'entidade_tipo' => 'campanha',
            'entidade_id'   => $campanhaId,
            'metadata'      => ['escopo' => $escopo],
        ]);
        responder_erro('Sem acesso a esta campanha.', 403, [
            ['field' => null, 'code' => 'FORA_DO_ESCOPO', 'message' => 'A credencial não tem acesso a esta campanha.'],
        ]);
    }
}

/** Gera um token de parceiro e devolve [token_cru, token_hash]. */
function gerar_token_credencial(): array
{
    $tokenCru = bin2hex(random_bytes(32));
    return [$tokenCru, hash('sha256', $tokenCru)];
}
