-- ============================================================================
-- 002_criar_paciente_vacina.sql
-- Função: identidade global do paciente (chave CPF) e catálogo global de vacinas.
-- Base: docs/08. Sem tenant_id (globais) — RN-008 (consolidação de carteira por CPF).
-- ============================================================================

CREATE TABLE IF NOT EXISTS paciente (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cpf               VARCHAR(11)     NOT NULL,           -- dígitos; chave de deduplicação
  nome              VARCHAR(120)    NOT NULL,           -- dado pessoal
  data_nascimento   DATE            NULL DEFAULT NULL,
  sexo              CHAR(1)         NULL DEFAULT NULL,
  criado_em         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em     DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_paciente_cpf (cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vacina (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome           VARCHAR(120)    NOT NULL,
  fabricante     VARCHAR(120)    NULL DEFAULT NULL,
  doses_padrao   TINYINT UNSIGNED NOT NULL DEFAULT 1,   -- nº de doses do esquema
  status         VARCHAR(20)     NOT NULL DEFAULT 'ativa', -- ativa | inativa
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_vacina_nome (nome),
  KEY ix_vacina_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('002_criar_paciente_vacina.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
