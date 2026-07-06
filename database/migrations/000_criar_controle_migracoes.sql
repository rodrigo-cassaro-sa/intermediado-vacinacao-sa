-- ============================================================================
-- 000_criar_controle_migracoes.sql
-- Função: tabela de controle das migrations aplicadas (idempotência do deploy).
-- Base: docs/08-modelagem-banco-dados.md (skill-migracoes-banco).
-- Charset/engine: InnoDB, utf8mb4. Nomes de campos em português.
-- ============================================================================

CREATE TABLE IF NOT EXISTS schema_migracao (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  arquivo       VARCHAR(160)    NOT NULL,
  aplicado_em   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_schema_migracao_arquivo (arquivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Registro desta migration (repetir o padrão ao final de cada arquivo aplicado):
INSERT INTO schema_migracao (arquivo) VALUES ('000_criar_controle_migracoes.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
