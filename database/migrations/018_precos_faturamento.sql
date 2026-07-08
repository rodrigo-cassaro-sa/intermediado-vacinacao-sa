-- ============================================================================
-- 018_precos_faturamento.sql
-- Item 4 (faturamento) / RN-029: tabelas de preço.
--  - preco_cliente: quanto COBRAMOS do cliente, por (cliente, modalidade, vacina)
--    (rede credenciada = por elegível indicado; in company = por vacinado)
--  - preco_clinica: quanto PAGAMOS à clínica, por (clínica, vacina) — por vacinado
-- ============================================================================

CREATE TABLE IF NOT EXISTS preco_cliente (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_b2b_id BIGINT UNSIGNED NOT NULL,
  modalidade     VARCHAR(20)     NOT NULL,   -- in_company | rede_credenciada
  vacina_id      BIGINT UNSIGNED NOT NULL,
  valor          DECIMAL(10,2)   NOT NULL,
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_preco_cliente (cliente_b2b_id, modalidade, vacina_id),
  KEY ix_preco_cliente_vacina (vacina_id),
  CONSTRAINT fk_preco_cliente_cliente FOREIGN KEY (cliente_b2b_id)
      REFERENCES cliente_b2b (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_preco_cliente_vacina FOREIGN KEY (vacina_id)
      REFERENCES vacina (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS preco_clinica (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  clinica_id     BIGINT UNSIGNED NOT NULL,
  vacina_id      BIGINT UNSIGNED NOT NULL,
  valor          DECIMAL(10,2)   NOT NULL,
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_preco_clinica (clinica_id, vacina_id),
  KEY ix_preco_clinica_vacina (vacina_id),
  CONSTRAINT fk_preco_clinica_clinica FOREIGN KEY (clinica_id)
      REFERENCES clinica_credenciada (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_preco_clinica_vacina FOREIGN KEY (vacina_id)
      REFERENCES vacina (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('018_precos_faturamento.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
