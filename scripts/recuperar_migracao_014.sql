-- ============================================================================
-- recuperar_migracao_014.sql
-- Recuperação IDEMPOTENTE da migration 014 (rodar no phpMyAdmin, banco correto).
-- Aplica só o que falta, deduplica aplicações confirmadas repetidas e marca a
-- 014 como aplicada em schema_migracao. Seguro rodar mais de uma vez.
-- ============================================================================

-- Evita erro 1215 (cópia de tabela com FK própria) durante os ALTER.
SET FOREIGN_KEY_CHECKS = 0;

-- 1) Tabela de idempotência (segura re-rodar).
CREATE TABLE IF NOT EXISTS idempotencia (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  escopo       VARCHAR(60)     NOT NULL,
  chave        VARCHAR(120)    NOT NULL,
  http_status  INT             NOT NULL,
  resposta     MEDIUMTEXT      NOT NULL,
  criado_em    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_idem (escopo, chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Colunas / índice / coluna gerada / dedup / UNIQUE — só se faltarem.
DROP PROCEDURE IF EXISTS _mig014_fix;
DELIMITER //
CREATE PROCEDURE _mig014_fix()
BEGIN
  DECLARE db VARCHAR(64);
  SET db = DATABASE();

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                 WHERE table_schema=db AND table_name='elegivel' AND column_name='nome') THEN
    ALTER TABLE elegivel ADD COLUMN nome VARCHAR(120) NULL DEFAULT NULL AFTER paciente_id;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                 WHERE table_schema=db AND table_name='elegivel' AND column_name='data_nascimento') THEN
    ALTER TABLE elegivel ADD COLUMN data_nascimento DATE NULL DEFAULT NULL AFTER nome;
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.statistics
                 WHERE table_schema=db AND table_name='elegivel' AND index_name='ix_elegivel_campanha_status') THEN
    ALTER TABLE elegivel ADD KEY ix_elegivel_campanha_status (campanha_id, status);
  END IF;

  IF NOT EXISTS (SELECT 1 FROM information_schema.columns
                 WHERE table_schema=db AND table_name='aplicacao' AND column_name='confirmacao_unica') THEN
    ALTER TABLE aplicacao ADD COLUMN confirmacao_unica VARCHAR(80)
      GENERATED ALWAYS AS (
        CASE WHEN status='confirmada' THEN CONCAT_WS('-', elegivel_id, vacina_id, dose) ELSE NULL END
      ) VIRTUAL;
  END IF;

  -- Deduplica: entre confirmadas iguais (elegivel,vacina,dose), mantém a de maior id,
  -- estorna as demais (evita falha da UNIQUE por dado repetido de teste).
  UPDATE aplicacao a
  JOIN (
    SELECT elegivel_id, vacina_id, dose, MAX(id) AS keep_id
      FROM aplicacao WHERE status='confirmada'
     GROUP BY elegivel_id, vacina_id, dose HAVING COUNT(*) > 1
  ) d ON a.elegivel_id=d.elegivel_id AND a.vacina_id=d.vacina_id AND a.dose=d.dose
     AND a.status='confirmada' AND a.id <> d.keep_id
  SET a.status='estornada';

  IF NOT EXISTS (SELECT 1 FROM information_schema.statistics
                 WHERE table_schema=db AND table_name='aplicacao' AND index_name='uq_aplicacao_confirmada') THEN
    ALTER TABLE aplicacao ADD UNIQUE KEY uq_aplicacao_confirmada (confirmacao_unica);
  END IF;
END //
DELIMITER ;
CALL _mig014_fix();
DROP PROCEDURE _mig014_fix;

-- 3) VIEW (idempotente).
CREATE OR REPLACE VIEW vw_tabela_verdade AS
SELECT e.tenant_id, e.campanha_id, e.paciente_id, p.cpf,
       COALESCE(e.nome, p.nome) AS nome, e.status AS situacao_elegivel,
       COUNT(a.id) AS total_aplicacoes, MAX(a.aplicado_em) AS ultima_aplicacao_em
FROM elegivel e
JOIN paciente p ON p.id = e.paciente_id
LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
GROUP BY e.tenant_id, e.campanha_id, e.paciente_id, p.cpf, COALESCE(e.nome, p.nome), e.status;

-- 4) Marca a migration 014 como aplicada.
INSERT INTO schema_migracao (arquivo) VALUES ('014_integridade_escala_batch1.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;

SET FOREIGN_KEY_CHECKS = 1;
