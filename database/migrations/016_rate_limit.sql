-- ============================================================================
-- 016_rate_limit.sql
-- Item 9b/12: limitar requisições por credencial de parceiro (e por IP no login),
-- para 500+ clientes não travarem a API. Contador por janela (minuto).
-- ============================================================================

CREATE TABLE IF NOT EXISTS rate_limite (
  chave          VARCHAR(80)     NOT NULL,   -- ex.: cred:12 | login:1.2.3.4
  janela         BIGINT UNSIGNED NOT NULL,   -- bucket = floor(unixtime / 60)
  contador       INT UNSIGNED    NOT NULL DEFAULT 0,
  atualizado_em  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (chave, janela)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Limite por minuto opcional por credencial (sobrescreve o padrão do .env).
ALTER TABLE credencial_api ADD COLUMN limite_rpm INT UNSIGNED NULL DEFAULT NULL;

INSERT INTO schema_migracao (arquivo) VALUES ('016_rate_limit.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
