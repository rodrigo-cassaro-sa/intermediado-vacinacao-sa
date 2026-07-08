-- ============================================================================
-- 019_webhooks.sql
-- Fase A (integração): webhooks de saída — assinaturas por evento e fila de
-- entregas com retry. Base: docs/11.
-- ============================================================================

CREATE TABLE IF NOT EXISTS webhook_assinatura (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id     BIGINT UNSIGNED NULL DEFAULT NULL,  -- NULL = global (todos os clientes)
  evento        VARCHAR(60)     NOT NULL,
  url           VARCHAR(500)    NOT NULL,
  segredo       VARCHAR(80)     NOT NULL,           -- para HMAC-SHA256
  ativo         TINYINT(1)      NOT NULL DEFAULT 1,
  criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_wh_ass_evento (evento, ativo),
  KEY ix_wh_ass_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS webhook_entrega (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  assinatura_id       BIGINT UNSIGNED NOT NULL,
  evento              VARCHAR(60)     NOT NULL,
  payload             MEDIUMTEXT      NOT NULL,
  status              VARCHAR(20)     NOT NULL DEFAULT 'pendente', -- pendente|entregue|dead
  tentativas          INT UNSIGNED    NOT NULL DEFAULT 0,
  ultimo_status_code  INT             NULL DEFAULT NULL,
  proxima_tentativa_em DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  criado_em           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  entregue_em         DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_wh_ent_fila (status, proxima_tentativa_em),
  KEY ix_wh_ent_assinatura (assinatura_id),
  CONSTRAINT fk_wh_ent_assinatura FOREIGN KEY (assinatura_id)
      REFERENCES webhook_assinatura (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('019_webhooks.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
