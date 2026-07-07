-- ============================================================================
-- 015_importacao_assincrona_relatorio_erro.sql
-- Item 9a: ingestão assíncrona (lotes grandes ~100k) + relatório de erros.
--  - importacao_elegiveis ganha formato, progresso e datas
--  - importacao_erro: cada linha rejeitada com motivo (relatório ao cliente)
-- ============================================================================

ALTER TABLE importacao_elegiveis ADD COLUMN formato VARCHAR(10) NULL DEFAULT NULL AFTER origem;
ALTER TABLE importacao_elegiveis ADD COLUMN total_processados INT UNSIGNED NULL DEFAULT 0 AFTER total_invalidos;
ALTER TABLE importacao_elegiveis ADD COLUMN iniciado_em DATETIME NULL DEFAULT NULL;
ALTER TABLE importacao_elegiveis ADD COLUMN finalizado_em DATETIME NULL DEFAULT NULL;
ALTER TABLE importacao_elegiveis ADD COLUMN mensagem_erro VARCHAR(255) NULL DEFAULT NULL;

CREATE TABLE IF NOT EXISTS importacao_erro (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  importacao_id  BIGINT UNSIGNED NOT NULL,
  linha          INT UNSIGNED    NULL DEFAULT NULL,
  cpf            VARCHAR(20)     NULL DEFAULT NULL,   -- dado do próprio cliente (relatório)
  nome           VARCHAR(120)    NULL DEFAULT NULL,
  codigo         VARCHAR(40)     NOT NULL,            -- CPF_INVALIDO, TIPO_VINCULO_INVALIDO, ...
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_imperro_importacao (importacao_id),
  CONSTRAINT fk_imperro_importacao FOREIGN KEY (importacao_id)
      REFERENCES importacao_elegiveis (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('015_importacao_assincrona_relatorio_erro.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
