<?php
// ============================================================================
// app/services/acesso.php
// PORTAL D0: resolução de escopo/hierarquia (doc 04 §4.1).
//  níveis: gestao_interna > grupo > negocio > local.  "acima gere abaixo".
// ============================================================================

/** Peso do nível (maior = mais alcance). */
function nivel_peso(string $nivel): int
{
    return ['gestao_interna' => 4, 'grupo' => 3, 'negocio' => 2, 'local' => 1][$nivel] ?? 0;
}

/** Atribuições do usuário (cache por requisição). */
function atribuicoes_do_usuario(int $usuarioId): array
{
    static $cache = [];
    if (!isset($cache[$usuarioId])) {
        $cache[$usuarioId] = db_todos(
            "SELECT nivel, escopo_tipo, escopo_id FROM usuario_atribuicao WHERE usuario_id = :id",
            [':id' => $usuarioId]
        );
    }
    return $cache[$usuarioId];
}

/** Usuário é da gestão interna (nossa)? */
function usuario_eh_interno(array $usuario): bool
{
    if (in_array($usuario['perfil'] ?? '', ['super_admin', 'operador_interno'], true)) {
        return true; // compat com o modelo antigo
    }
    foreach (atribuicoes_do_usuario((int) $usuario['id']) as $a) {
        if ($a['nivel'] === 'gestao_interna') {
            return true;
        }
    }
    return false;
}

/**
 * Permissão de ver CPF completo (LGPD). Lê a flag usuario.pode_ver_cpf (migration
 * 028), com cache por requisição. Sem a flag => CPF mascarado.
 */
function usuario_pode_ver_cpf(array $usuario): bool
{
    static $cache = [];
    $id = (int) ($usuario['id'] ?? 0);
    if ($id <= 0) {
        return false;
    }
    if (!array_key_exists($id, $cache)) {
        try {
            $r = db_primeiro("SELECT pode_ver_cpf FROM usuario WHERE id = :id LIMIT 1", [':id' => $id]);
            $cache[$id] = $r !== null && (int) $r['pode_ver_cpf'] === 1;
        } catch (Throwable $e) {
            // Coluna ainda não migrada (028): padrão seguro = mascarado.
            $cache[$id] = false;
        }
    }
    return $cache[$id];
}

/** Devolve o CPF completo (só dígitos->formatado) ou mascarado, conforme a permissão do usuário. */
function cpf_para_usuario(?string $cpf, array $usuario): string
{
    if ($cpf === null || $cpf === '') {
        return '';
    }
    return usuario_pode_ver_cpf($usuario) ? formatar_cpf($cpf) : mascarar_cpf($cpf);
}

/** Grupo empresarial de um cliente (cache). */
function grupo_do_cliente(int $clienteId): ?int
{
    static $cache = [];
    if (!array_key_exists($clienteId, $cache)) {
        $r = db_primeiro("SELECT grupo_empresarial_id FROM cliente_b2b WHERE id = :id", [':id' => $clienteId]);
        $cache[$clienteId] = $r && $r['grupo_empresarial_id'] !== null ? (int) $r['grupo_empresarial_id'] : null;
    }
    return $cache[$clienteId];
}

/** Cliente de uma unidade (cache). */
function cliente_da_unidade(int $unidadeId): ?int
{
    static $cache = [];
    if (!array_key_exists($unidadeId, $cache)) {
        $r = db_primeiro("SELECT cliente_b2b_id FROM unidade WHERE id = :id", [':id' => $unidadeId]);
        $cache[$unidadeId] = $r ? (int) $r['cliente_b2b_id'] : null;
    }
    return $cache[$unidadeId];
}

/**
 * O usuário pode acessar dados do cliente?
 * $paraGestao=true ignora o nível 'local' (local opera, não gere).
 */
function usuario_pode_cliente(array $usuario, int $clienteId, bool $paraGestao = false): bool
{
    // compat: cliente_b2b antigo com tenant_id
    if (!$paraGestao && ($usuario['perfil'] ?? '') === 'cliente_b2b' && (int) ($usuario['tenant_id'] ?? 0) === $clienteId) {
        return true;
    }
    foreach (atribuicoes_do_usuario((int) $usuario['id']) as $a) {
        switch ($a['nivel']) {
            case 'gestao_interna':
                return true;
            case 'grupo':
                if (grupo_do_cliente($clienteId) !== null && (int) $a['escopo_id'] === grupo_do_cliente($clienteId)) {
                    return true;
                }
                break;
            case 'negocio':
                if ($a['escopo_tipo'] === 'cliente_b2b' && (int) $a['escopo_id'] === $clienteId) {
                    return true;
                }
                break;
            case 'local':
                if (!$paraGestao && $a['escopo_tipo'] === 'unidade' && cliente_da_unidade((int) $a['escopo_id']) === $clienteId) {
                    return true;
                }
                break;
        }
    }
    return false;
}

/** O usuário pode acessar a unidade (local vê só a sua; níveis acima veem todas do cliente)? */
function usuario_pode_unidade(array $usuario, int $unidadeId): bool
{
    $cliente = cliente_da_unidade($unidadeId);
    if ($cliente === null) {
        return false;
    }
    foreach (atribuicoes_do_usuario((int) $usuario['id']) as $a) {
        if ($a['nivel'] === 'gestao_interna') return true;
        if ($a['nivel'] === 'grupo' && grupo_do_cliente($cliente) !== null && (int) $a['escopo_id'] === grupo_do_cliente($cliente)) return true;
        if ($a['nivel'] === 'negocio' && (int) $a['escopo_id'] === $cliente) return true;
        if ($a['nivel'] === 'local' && (int) $a['escopo_id'] === $unidadeId) return true;
    }
    return usuario_eh_interno($usuario);
}

/**
 * O usuário (ator) pode GERIR (criar/atribuir) um usuário no nível/escopo alvo?
 * "acima gere abaixo": ator precisa cobrir o cliente do alvo em nível de gestão.
 */
function usuario_pode_gerir(array $ator, string $nivelAlvo, string $escopoTipo, int $escopoId): bool
{
    switch ($nivelAlvo) {
        case 'gestao_interna':
            return usuario_eh_interno($ator);
        case 'grupo':
            if (usuario_eh_interno($ator)) return true;
            foreach (atribuicoes_do_usuario((int) $ator['id']) as $a) {
                if ($a['nivel'] === 'grupo' && (int) $a['escopo_id'] === $escopoId) return true;
            }
            return false;
        case 'negocio':
            return usuario_pode_cliente($ator, $escopoId, true);
        case 'local':
            $cli = cliente_da_unidade($escopoId);
            return $cli !== null && usuario_pode_cliente($ator, $cli, true);
        default:
            return false;
    }
}

/** Lista de cliente_ids que o ator pode GERIR (para listagens do portal). */
function clientes_geridos_pelo_usuario(array $usuario): array
{
    if (usuario_eh_interno($usuario)) {
        return ['*']; // todos
    }
    $ids = [];
    foreach (atribuicoes_do_usuario((int) $usuario['id']) as $a) {
        if ($a['nivel'] === 'negocio' && $a['escopo_tipo'] === 'cliente_b2b') {
            $ids[(int) $a['escopo_id']] = true;
        } elseif ($a['nivel'] === 'grupo') {
            foreach (db_todos("SELECT id FROM cliente_b2b WHERE grupo_empresarial_id = :g AND excluido_em IS NULL", [':g' => (int) $a['escopo_id']]) as $c) {
                $ids[(int) $c['id']] = true;
            }
        }
    }
    return array_keys($ids);
}

/** Cliente_ids que o usuário pode ACESSAR (leitura), incluindo o nível local. */
function clientes_acessiveis_pelo_usuario(array $usuario): array
{
    if (usuario_eh_interno($usuario)) {
        return ['*'];
    }
    $ids = [];
    if (($usuario['perfil'] ?? '') === 'cliente_b2b' && !empty($usuario['tenant_id'])) {
        $ids[(int) $usuario['tenant_id']] = true;
    }
    foreach (atribuicoes_do_usuario((int) $usuario['id']) as $a) {
        if ($a['nivel'] === 'negocio') {
            $ids[(int) $a['escopo_id']] = true;
        } elseif ($a['nivel'] === 'grupo') {
            foreach (db_todos("SELECT id FROM cliente_b2b WHERE grupo_empresarial_id = :g AND excluido_em IS NULL", [':g' => (int) $a['escopo_id']]) as $c) {
                $ids[(int) $c['id']] = true;
            }
        } elseif ($a['nivel'] === 'local') {
            $cli = cliente_da_unidade((int) $a['escopo_id']);
            if ($cli !== null) $ids[$cli] = true;
        }
    }
    return array_keys($ids);
}

/**
 * Restrição de unidade para o usuário no cliente:
 *  - null  = sem restrição (interna/grupo/negocio veem o cliente inteiro)
 *  - array = lista de unidade_ids (usuário só-local vê apenas essas unidades)
 */
function unidades_restritas_do_usuario(array $usuario, int $clienteId): ?array
{
    if (usuario_eh_interno($usuario)) return null;
    if (($usuario['perfil'] ?? '') === 'cliente_b2b' && (int) ($usuario['tenant_id'] ?? 0) === $clienteId) return null;

    $unidades = [];
    foreach (atribuicoes_do_usuario((int) $usuario['id']) as $a) {
        if ($a['nivel'] === 'negocio' && (int) $a['escopo_id'] === $clienteId) return null;
        if ($a['nivel'] === 'grupo' && grupo_do_cliente($clienteId) !== null && (int) $a['escopo_id'] === grupo_do_cliente($clienteId)) return null;
        if ($a['nivel'] === 'local' && cliente_da_unidade((int) $a['escopo_id']) === $clienteId) {
            $unidades[(int) $a['escopo_id']] = true;
        }
    }
    return array_keys($unidades);
}

/**
 * Fragmento SQL + bind para restringir por unidade (usuário local).
 * Devolve ['', []] se não houver restrição; ' AND alias.unidade_id IN (...)' caso contrário.
 */
function filtro_unidade_sql(array $usuario, int $clienteId, string $alias = 'e'): array
{
    $restr = unidades_restritas_do_usuario($usuario, $clienteId);
    if ($restr === null) {
        return ['', []];
    }
    if (!$restr) {
        return [' AND 1 = 0', []]; // usuário local sem unidade => nada
    }
    $ph = []; $bind = [];
    foreach ($restr as $i => $uid) {
        $ph[] = ":u_$i";
        $bind[":u_$i"] = $uid;
    }
    return [' AND ' . $alias . '.unidade_id IN (' . implode(',', $ph) . ')', $bind];
}
