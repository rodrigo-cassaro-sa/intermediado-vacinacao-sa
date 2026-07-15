-- ============================================================================
-- 029_permissao_cpf_por_cliente.sql
-- Função: visibilidade de CPF GRANULAR por (usuário, cliente_b2b). Complementa a
--   flag global usuario.pode_ver_cpf (028, blanket p/ interno). Um usuário vê o
--   CPF completo de um cliente se tiver a flag global OU uma linha aqui p/ aquele
--   cliente. Sem linha => mascarado. Sem FK rígida (evita 1215); refs por índice.
-- Idempotente: runner tolera 1050 (tabela dup.).
-- ============================================================================

CREATE TABLE IF NOT EXISTS permissao_ver_cpf (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id     BIGINT UNSIGNED NOT NULL,
  cliente_b2b_id BIGINT UNSIGNED NOT NULL,
  criado_por     BIGINT UNSIGNED NULL DEFAULT NULL,
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_perm_cpf (usuario_id, cliente_b2b_id),
  KEY ix_perm_cpf_usuario (usuario_id),
  KEY ix_perm_cpf_cliente (cliente_b2b_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('029_permissao_cpf_por_cliente.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
