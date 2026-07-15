<?php
// ============================================================================
// app/helpers/validacao.php
// Função: validações reutilizáveis (obrigatórios, CPF, e-mail, datas) e máscaras.
// Base: docs/08 (CPF chave), docs/10 (mascarar dado sensível em log/tela).
// ============================================================================

/** Mantém apenas dígitos. */
function so_digitos(?string $valor): string
{
    return preg_replace('/\D+/', '', (string) $valor);
}

/** Valida CPF (11 dígitos + dígitos verificadores). */
function validar_cpf(?string $cpf): bool
{
    $cpf = so_digitos($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += (int) $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ((10 * $soma) % 11) % 10;
        if ((int) $cpf[$t] !== $digito) {
            return false;
        }
    }
    return true;
}

/** Mascara CPF para exibição/log: 123.456.789-00 -> ***.***.789-** */
function mascarar_cpf(?string $cpf): string
{
    $cpf = so_digitos($cpf);
    if (strlen($cpf) !== 11) {
        return '***';
    }
    return '***.***.' . substr($cpf, 6, 3) . '-**';
}

/** Formata CPF completo (000.000.000-00). Não-11 dígitos volta como veio. */
function formatar_cpf(?string $cpf): string
{
    $d = so_digitos($cpf);
    if (strlen($d) !== 11) {
        return (string) $cpf;
    }
    return substr($d, 0, 3) . '.' . substr($d, 3, 3) . '.' . substr($d, 6, 3) . '-' . substr($d, 9, 2);
}

/** Valida e-mail simples. */
function validar_email(?string $email): bool
{
    return (bool) filter_var((string) $email, FILTER_VALIDATE_EMAIL);
}

/** Valida data no formato Y-m-d. */
function validar_data(?string $data): bool
{
    if (!is_string($data)) return false;
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d && $d->format('Y-m-d') === $data;
}

/**
 * Verifica campos obrigatórios em um array associativo.
 * Devolve lista de erros no formato { field, code, message } (vazia se ok).
 */
function exigir_campos(array $dados, array $obrigatorios): array
{
    $erros = [];
    foreach ($obrigatorios as $campo) {
        if (!isset($dados[$campo]) || $dados[$campo] === '' || $dados[$campo] === null) {
            $erros[] = ['field' => $campo, 'code' => 'CAMPO_OBRIGATORIO', 'message' => "O campo '$campo' é obrigatório."];
        }
    }
    return $erros;
}
