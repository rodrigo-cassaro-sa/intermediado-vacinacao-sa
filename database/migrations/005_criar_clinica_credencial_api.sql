-- ============================================================================
-- 005_criar_clinica_credencial_api.sql
-- Função: clínicas da rede (global) e credenciais de API de máquina (escopo/RN-009).
-- Base: docs/08 e docs/10. titular_id/executor são polimórficos (sem FK rígida).
-- ============================================================================

CREATE TABLE IF NOT EXISTS clinica_credenciada (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome           VARCHAR(180)    NOT NULL,
  cnpj           VARCHAR(14)     NOT NULL,
  status         VARCHAR(20)     NOT NULL DEFAULT 'ativa', -- ativa | inativa
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em    DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_clinica_cnpj (cnpj),
  KEY ix_clinica_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS credencial_api (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo                VARCHAR(20)     NOT NULL,          -- ingestao_b2b | rede_credenciada
  titular_tipo        VARCHAR(20)     NOT NULL,          -- cliente_b2b | clinica_credenciada
  titular_id          BIGINT UNSIGNED NOT NULL,          -- polimórfico (sem FK)
  token_hash          VARCHAR(255)    NOT NULL,          -- só o hash do token (docs/10)
  escopo_campanha_id  BIGINT UNSIGNED NULL DEFAULT NULL, -- restringe à campanha (RN-009)
  ativo               TINYINT(1)      NOT NULL DEFAULT 1,
  expira_em           DATETIME        NULL DEFAULT NULL,
  criado_em           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  revogado_em         DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_credencial_token_hash (token_hash),
  KEY ix_credencial_tipo (tipo),
  KEY ix_credencial_titular (titular_tipo, titular_id),
  KEY ix_credencial_escopo (escopo_campanha_id),
  KEY ix_credencial_ativo (ativo),
  CONSTRAINT fk_credencial_escopo_campanha FOREIGN KEY (escopo_campanha_id)
      REFERENCES campanha (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('005_criar_clinica_credencial_api.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
