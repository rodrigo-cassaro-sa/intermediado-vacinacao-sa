-- ============================================================================
-- 013_historico_elegivel_aplicacao.sql
-- Função: RN-021/022 — trilha de histórico (lastro) por elegível e por aplicação,
--         com antes/depois, ator e momento. Base: docs/02, docs/08, docs/10.
-- ============================================================================

CREATE TABLE IF NOT EXISTS elegivel_historico (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  elegivel_id   BIGINT UNSIGNED NOT NULL,
  evento        VARCHAR(40)     NOT NULL,   -- criado|reingerido|editado|clinica_alterada|situacao_alterada|vacinado|desvacinado
  ator_tipo     VARCHAR(20)     NOT NULL,   -- usuario | credencial_api
  ator_id       BIGINT UNSIGNED NULL DEFAULT NULL,
  dados_antes   JSON            NULL DEFAULT NULL,
  dados_depois  JSON            NULL DEFAULT NULL,
  observacao    VARCHAR(255)    NULL DEFAULT NULL,
  criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_elehist_elegivel (elegivel_id),
  KEY ix_elehist_evento (evento),
  CONSTRAINT fk_elehist_elegivel FOREIGN KEY (elegivel_id)
      REFERENCES elegivel (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aplicacao_historico (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  aplicacao_id  BIGINT UNSIGNED NOT NULL,
  evento        VARCHAR(40)     NOT NULL,   -- registrada | retificada | estornada
  ator_tipo     VARCHAR(20)     NOT NULL,
  ator_id       BIGINT UNSIGNED NULL DEFAULT NULL,
  motivo        VARCHAR(255)    NULL DEFAULT NULL,
  snapshot      JSON            NULL DEFAULT NULL,
  criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_aplhist_aplicacao (aplicacao_id),
  CONSTRAINT fk_aplhist_aplicacao FOREIGN KEY (aplicacao_id)
      REFERENCES aplicacao (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('013_historico_elegivel_aplicacao.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
