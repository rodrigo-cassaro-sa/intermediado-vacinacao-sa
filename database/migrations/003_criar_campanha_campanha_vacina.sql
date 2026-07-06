-- ============================================================================
-- 003_criar_campanha_campanha_vacina.sql
-- Função: campanhas (RN-001) e vacinas oferecidas por campanha.
-- Base: docs/08. Depende de 001 (cliente_b2b, usuario) e 002 (vacina).
-- ============================================================================

CREATE TABLE IF NOT EXISTS campanha (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id       BIGINT UNSIGNED NOT NULL,             -- -> cliente_b2b
  nome            VARCHAR(160)    NOT NULL,
  modalidade      VARCHAR(20)     NOT NULL,             -- rede_credenciada | in_company
  periodo_inicio  DATE            NOT NULL,
  periodo_fim     DATE            NOT NULL,             -- janela de aplicação (RN-003)
  status          VARCHAR(20)     NOT NULL DEFAULT 'rascunho', -- rascunho | ativa | encerrada
  criado_por      BIGINT UNSIGNED NOT NULL,             -- -> usuario
  criado_em       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em   DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em     DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_campanha_tenant (tenant_id),
  KEY ix_campanha_modalidade (modalidade),
  KEY ix_campanha_status (status),
  CONSTRAINT fk_campanha_tenant FOREIGN KEY (tenant_id)
      REFERENCES cliente_b2b (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_campanha_criado_por FOREIGN KEY (criado_por)
      REFERENCES usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS campanha_vacina (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id         BIGINT UNSIGNED NOT NULL,
  campanha_id       BIGINT UNSIGNED NOT NULL,
  vacina_id         BIGINT UNSIGNED NOT NULL,
  doses_previstas   TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_campanha_vacina (campanha_id, vacina_id),
  KEY ix_campanha_vacina_tenant (tenant_id),
  KEY ix_campanha_vacina_vacina (vacina_id),
  CONSTRAINT fk_campanha_vacina_campanha FOREIGN KEY (campanha_id)
      REFERENCES campanha (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_campanha_vacina_vacina FOREIGN KEY (vacina_id)
      REFERENCES vacina (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('003_criar_campanha_campanha_vacina.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
