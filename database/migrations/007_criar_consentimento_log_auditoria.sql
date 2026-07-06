-- ============================================================================
-- 007_criar_consentimento_log_auditoria.sql
-- Função: base legal do consentimento (RN-011) e trilha de auditoria (docs/10).
-- ============================================================================

CREATE TABLE IF NOT EXISTS consentimento_lgpd (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  paciente_id    BIGINT UNSIGNED NOT NULL,
  versao_termo   VARCHAR(20)     NOT NULL,
  aceito_em      DATETIME        NOT NULL,              -- base legal (RN-011)
  origem         VARCHAR(30)     NOT NULL,              -- b2c | b2b_em_nome (a definir)
  ip             VARCHAR(45)     NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_consentimento_paciente (paciente_id),
  CONSTRAINT fk_consentimento_paciente FOREIGN KEY (paciente_id)
      REFERENCES paciente (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS log_auditoria (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id      BIGINT UNSIGNED NULL DEFAULT NULL,     -- NULL = ação interna global
  ator_tipo      VARCHAR(20)     NOT NULL,              -- usuario | credencial_api
  ator_id        BIGINT UNSIGNED NULL DEFAULT NULL,
  evento         VARCHAR(60)     NOT NULL,              -- ex.: aplicacao.registrada
  origem         VARCHAR(30)     NOT NULL,              -- admin | portal | app | api_parceiro
  entidade_tipo  VARCHAR(40)     NULL DEFAULT NULL,
  entidade_id    BIGINT UNSIGNED NULL DEFAULT NULL,
  request_id     VARCHAR(40)     NULL DEFAULT NULL,
  ip             VARCHAR(45)     NULL DEFAULT NULL,
  metadata       JSON            NULL DEFAULT NULL,     -- payload mascarado (sem dado sensível cru)
  data_hora      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_log_tenant (tenant_id),
  KEY ix_log_evento (evento),
  KEY ix_log_request (request_id),
  KEY ix_log_data_hora (data_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('007_criar_consentimento_log_auditoria.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
