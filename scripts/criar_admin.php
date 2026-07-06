<?php
// ============================================================================
// scripts/criar_admin.php
// Função: criar/atualizar o usuário interno super_admin com senha real (hash).
// Uso (dentro do container da app):
//   php scripts/criar_admin.php <email> <senha> "<Nome>"
// Ex.: php scripts/criar_admin.php admin@empresa.com MinhaSenhaForte "Administrador"
// A senha é gravada com password_hash (bcrypt) — nunca em texto puro. Base: docs/10.
// ============================================================================

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexao.php';

$email = $argv[1] ?? null;
$senha = $argv[2] ?? null;
$nome  = $argv[3] ?? 'Administrador';

if (!$email || !$senha) {
    fwrite(STDERR, "Uso: php scripts/criar_admin.php <email> <senha> \"<Nome>\"\n");
    exit(1);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "E-mail inválido.\n");
    exit(1);
}
if (strlen($senha) < 8) {
    fwrite(STDERR, "Senha muito curta (mínimo 8 caracteres).\n");
    exit(1);
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

// Insere se não existir; se já existir o e-mail, atualiza nome/senha/status.
db_executar(
    "INSERT INTO usuario (tenant_id, perfil, nome, email, senha_hash, status)
     VALUES (NULL, 'super_admin', :nome, :email, :hash, 'ativo')
     ON DUPLICATE KEY UPDATE nome = VALUES(nome), senha_hash = VALUES(senha_hash), status = 'ativo'",
    [':nome' => $nome, ':email' => $email, ':hash' => $hash]
);

echo "Admin criado/atualizado: $email\n";
