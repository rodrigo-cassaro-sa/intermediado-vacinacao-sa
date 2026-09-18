-- ============================================================================
-- 033_redefinicao_senha.sql
-- Fluxo "esqueci minha senha": token de uso único, com prazo, enviado por e-mail.
--
-- Por que uma tabela e não uma coluna em `usuario`: o token precisa de histórico
-- (quem pediu, de qual IP, quando usou) para a auditoria conseguir responder
-- "quem trocou a senha daquele usuário e quando" — exigência de dado sensível
-- (docs/10). Coluna única apagaria o rastro a cada pedido.
--
-- O token NUNCA é gravado: guardamos o SHA-256 dele. Quem ler o banco (ou um
-- dump, ou um backup) não consegue redefinir a senha de ninguém. Mesma lógica
-- do senha_hash do usuário.
-- ============================================================================

CREATE TABLE IF NOT EXISTS senha_redefinicao (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id     BIGINT UNSIGNED NOT NULL,
  token_hash     CHAR(64)        NOT NULL,            -- sha256(token) em hex
  expira_em      DATETIME        NOT NULL,
  usado_em       DATETIME        NULL DEFAULT NULL,   -- NULL = ainda válido
  invalidado_em  DATETIME        NULL DEFAULT NULL,   -- revogado por pedido novo/uso de outro
  ip_solicitante VARCHAR(45)     NULL DEFAULT NULL,   -- cabe IPv6
  origem         VARCHAR(20)     NOT NULL DEFAULT 'portal',  -- portal | admin
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_senha_redef_token (token_hash),
  KEY ix_senha_redef_usuario (usuario_id),
  KEY ix_senha_redef_expira (expira_em),
  CONSTRAINT fk_senha_redef_usuario FOREIGN KEY (usuario_id)
      REFERENCES usuario (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('033_redefinicao_senha.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
