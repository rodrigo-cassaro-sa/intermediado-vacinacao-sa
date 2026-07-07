<?php
// ============================================================================
// app/services/historico.php
// Trilha de histórico (lastro) por elegível e por aplicação — RN-021/022.
// Nunca deve derrubar a operação: falha ao gravar histórico é só logada.
// ============================================================================

/** Ator (usuário logado) para o histórico. */
function ator_usuario(array $usuario): array
{
    return ['tipo' => 'usuario', 'id' => (int) $usuario['id']];
}

/** Ator (credencial de máquina) para o histórico. */
function ator_credencial(array $cred): array
{
    return ['tipo' => 'credencial_api', 'id' => (int) $cred['id']];
}

/** Registra um evento no histórico do elegível (antes/depois em JSON). */
function historico_elegivel(int $elegivelId, string $evento, array $ator, ?array $antes = null, ?array $depois = null, string $obs = ''): void
{
    try {
        db_executar(
            "INSERT INTO elegivel_historico (elegivel_id, evento, ator_tipo, ator_id, dados_antes, dados_depois, observacao)
             VALUES (:e, :ev, :at, :ai, :a, :d, :o)",
            [
                ':e'  => $elegivelId,
                ':ev' => $evento,
                ':at' => $ator['tipo'],
                ':ai' => $ator['id'] ?? null,
                ':a'  => $antes !== null ? json_encode($antes, JSON_UNESCAPED_UNICODE) : null,
                ':d'  => $depois !== null ? json_encode($depois, JSON_UNESCAPED_UNICODE) : null,
                ':o'  => $obs !== '' ? $obs : null,
            ]
        );
    } catch (Throwable $e) {
        error_log('historico_elegivel falhou: ' . $e->getMessage());
    }
}

/** Registra um evento no histórico da aplicação. */
function historico_aplicacao(int $aplicacaoId, string $evento, array $ator, string $motivo = '', ?array $snapshot = null): void
{
    try {
        db_executar(
            "INSERT INTO aplicacao_historico (aplicacao_id, evento, ator_tipo, ator_id, motivo, snapshot)
             VALUES (:a, :ev, :at, :ai, :m, :s)",
            [
                ':a'  => $aplicacaoId,
                ':ev' => $evento,
                ':at' => $ator['tipo'],
                ':ai' => $ator['id'] ?? null,
                ':m'  => $motivo !== '' ? $motivo : null,
                ':s'  => $snapshot !== null ? json_encode($snapshot, JSON_UNESCAPED_UNICODE) : null,
            ]
        );
    } catch (Throwable $e) {
        error_log('historico_aplicacao falhou: ' . $e->getMessage());
    }
}
