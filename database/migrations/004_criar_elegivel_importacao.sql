-- ============================================================================
-- 004_criar_elegivel_importacao.sql
-- Função: lotes de importação e o vínculo elegível (paciente <-> campanha).
-- Base: docs/08. Depende de 001,002,003. importacao criada antes de elegivel (FK).
-- ============================================================================

CREATE TABLE IF NOT EXISTS importacao_elegiveis (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id         BIGINT UNSIGNED NOT NULL,
  campanha_id       BIGINT UNSIGNED NOT NULL,
  origem            VARCHAR(20)     NOT NULL,           -- upload | api
  arquivo           VARCHAR(255)    NULL DEFAULT NULL,  -- caminho em storage/uploads (só upload)
  total_linhas      INT UNSIGNED    NULL DEFAULT NULL,
  total_validos     INT UNSIGNED    NULL DEFAULT NULL,
  total_invalidos   INT UNSIGNED    NULL DEFAULT NULL,
  status            VARCHAR(20)     NOT NULL DEFAULT 'processando', -- processando|concluida|falha
  criado_por        BIGINT UNSIGNED NULL DEFAULT NULL,  -- usuario (upload) ou NULL/credencial (api)
  criado_em         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_importacao_tenant (tenant_id),
  KEY ix_importacao_campanha (campanha_id),
  CONSTRAINT fk_importacao_campanha FOREIGN KEY (campanha_id)
      REFERENCES campanha (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS elegivel (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id      BIGINT UNSIGNED NOT NULL,
  campanha_id    BIGINT UNSIGNED NOT NULL,
  paciente_id    BIGINT UNSIGNED NOT NULL,
  origem         VARCHAR(20)     NOT NULL,              -- upload | api | autoelegivel
  status         VARCHAR(20)     NOT NULL DEFAULT 'pendente', -- pendente|aplicado|recusado|inelegivel|ausente
  importacao_id  BIGINT UNSIGNED NULL DEFAULT NULL,
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_elegivel_campanha_paciente (campanha_id, paciente_id), -- 1 por paciente/campanha
  KEY ix_elegivel_tenant (tenant_id),
  KEY ix_elegivel_paciente (paciente_id),
  KEY ix_elegivel_status (status),
  KEY ix_elegivel_importacao (importacao_id),
  CONSTRAINT fk_elegivel_campanha FOREIGN KEY (campanha_id)
      REFERENCES campanha (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_elegivel_paciente FOREIGN KEY (paciente_id)
      REFERENCES paciente (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_elegivel_importacao FOREIGN KEY (importacao_id)
      REFERENCES importacao_elegiveis (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('004_criar_elegivel_importacao.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
