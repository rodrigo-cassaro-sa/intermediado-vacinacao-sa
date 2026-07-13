-- ============================================================================
-- 025_importacao_historico_fila.sql
-- Função: fila da importação ASSÍNCRONA de vacinados históricos (RN-027).
-- Lotes grandes (>2000 linhas) são salvos e processados pelo worker
-- (scripts/processar_importacoes.php via cron), com acompanhamento de status.
-- ============================================================================

CREATE TABLE IF NOT EXISTS importacao_historico (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id         BIGINT UNSIGNED NOT NULL,             -- cliente destino
  arquivo           VARCHAR(255)    NOT NULL,             -- CSV em storage/uploads
  status            VARCHAR(20)     NOT NULL DEFAULT 'pendente', -- pendente|processando|concluida|falha
  total_linhas      INT UNSIGNED    NULL DEFAULT NULL,
  total_processados INT UNSIGNED    NULL DEFAULT 0,
  total_aplicacoes  INT UNSIGNED    NULL DEFAULT 0,       -- aplicações (vacinados) criadas
  total_duplicados  INT UNSIGNED    NULL DEFAULT 0,
  total_rejeitados  INT UNSIGNED    NULL DEFAULT 0,
  total_campanhas   INT UNSIGNED    NULL DEFAULT 0,       -- campanhas históricas criadas
  total_vacinas     INT UNSIGNED    NULL DEFAULT 0,       -- vacinas criadas no catálogo
  erros_amostra     LONGTEXT        NULL DEFAULT NULL,    -- JSON [{linha,code}] (amostra)
  mensagem_erro     VARCHAR(255)    NULL DEFAULT NULL,
  criado_por        BIGINT UNSIGNED NULL DEFAULT NULL,    -- usuario interno
  criado_em         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  iniciado_em       DATETIME        NULL DEFAULT NULL,
  finalizado_em     DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_imp_hist_status (status),
  KEY ix_imp_hist_tenant (tenant_id),
  CONSTRAINT fk_imp_hist_tenant FOREIGN KEY (tenant_id)
      REFERENCES cliente_b2b (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('025_importacao_historico_fila.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
