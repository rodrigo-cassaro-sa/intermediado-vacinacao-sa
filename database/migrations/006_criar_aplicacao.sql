-- ============================================================================
-- 006_criar_aplicacao.sql
-- Função: registro IMUTÁVEL de dose aplicada (RN-004, RN-010).
-- Base: docs/08. Sem atualizado_em. Retificação = novo registro (aplicacao_origem_id).
-- executor_id é polimórfico (usuario | clinica_credenciada) — sem FK rígida.
-- ============================================================================

CREATE TABLE IF NOT EXISTS aplicacao (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id             BIGINT UNSIGNED NOT NULL,
  campanha_id           BIGINT UNSIGNED NOT NULL,
  elegivel_id           BIGINT UNSIGNED NOT NULL,
  paciente_id           BIGINT UNSIGNED NOT NULL,
  vacina_id             BIGINT UNSIGNED NOT NULL,
  dose                  TINYINT UNSIGNED NOT NULL,      -- 1ª, 2ª, reforço
  lote                  VARCHAR(60)     NOT NULL,       -- rastreabilidade sanitária
  via_administracao     VARCHAR(30)     NULL DEFAULT NULL,
  local_aplicacao       VARCHAR(160)    NULL DEFAULT NULL,
  executor_tipo         VARCHAR(20)     NOT NULL,       -- profissional_saude | clinica_credenciada
  executor_id           BIGINT UNSIGNED NOT NULL,       -- polimórfico (sem FK)
  origem                VARCHAR(20)     NOT NULL,       -- app | api
  status                VARCHAR(20)     NOT NULL DEFAULT 'confirmada', -- confirmada|retificada|estornada
  aplicacao_origem_id   BIGINT UNSIGNED NULL DEFAULT NULL, -- preenchido quando retifica outra
  motivo_retificacao    VARCHAR(255)    NULL DEFAULT NULL,
  aplicado_em           DATETIME        NOT NULL,       -- data/hora da dose
  criado_por            BIGINT UNSIGNED NULL DEFAULT NULL,
  criado_em             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  -- Sem atualizado_em: registro imutável (RN-010).
  PRIMARY KEY (id),
  KEY ix_aplicacao_tenant (tenant_id),
  KEY ix_aplicacao_campanha (campanha_id),
  KEY ix_aplicacao_elegivel (elegivel_id),
  KEY ix_aplicacao_paciente (paciente_id),
  KEY ix_aplicacao_vacina (vacina_id),
  KEY ix_aplicacao_executor (executor_tipo, executor_id),
  KEY ix_aplicacao_status (status),
  KEY ix_aplicacao_aplicado_em (aplicado_em),
  KEY ix_aplicacao_origem (aplicacao_origem_id),
  CONSTRAINT fk_aplicacao_campanha FOREIGN KEY (campanha_id)
      REFERENCES campanha (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_aplicacao_elegivel FOREIGN KEY (elegivel_id)
      REFERENCES elegivel (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_aplicacao_paciente FOREIGN KEY (paciente_id)
      REFERENCES paciente (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_aplicacao_vacina FOREIGN KEY (vacina_id)
      REFERENCES vacina (id) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_aplicacao_origem FOREIGN KEY (aplicacao_origem_id)
      REFERENCES aplicacao (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('006_criar_aplicacao.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
