-- ============================================================================
-- 014_integridade_escala_batch1.sql
-- Batch 1 de robustez (itens 1,2,5,7 da análise de gaps):
--  - trava de concorrência: 1 vacinado CONFIRMADO por (elegivel, vacina, dose) — RN-013 revisada
--  - tabela de idempotência (evita duplicar em reenvio de API/lote)
--  - nome/data_nascimento POR ELEGÍVEL (isola o dado que cada cliente enviou; RN-023)
--  - VIEW da tabela verdade passa a usar o nome do elegível (fallback paciente)
-- ATENÇÃO: a UNIQUE abaixo falha se já existirem 2 aplicações 'confirmada' com
--          (elegivel, vacina, dose) iguais. Em base de teste, deduplicar antes.
-- ============================================================================

-- Item 5 / RN-023: dado do cliente por elegível (não sobrescreve outros clientes).
ALTER TABLE elegivel
  ADD COLUMN nome VARCHAR(120) NULL DEFAULT NULL AFTER paciente_id,
  ADD COLUMN data_nascimento DATE NULL DEFAULT NULL AFTER nome;

-- Item 1 + 7: unicidade do vacinado confirmado por (elegivel, vacina, dose).
-- Coluna gerada = chave só quando 'confirmada'; NULL nos demais (MySQL permite N NULLs).
ALTER TABLE aplicacao
  ADD COLUMN confirmacao_unica VARCHAR(80)
    GENERATED ALWAYS AS (
      CASE WHEN status = 'confirmada'
           THEN CONCAT_WS('-', elegivel_id, vacina_id, dose)
           ELSE NULL END
    ) STORED,
  ADD UNIQUE KEY uq_aplicacao_confirmada (confirmacao_unica);

-- Item 2: idempotência das operações de escrita (API/lote).
CREATE TABLE IF NOT EXISTS idempotencia (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  escopo       VARCHAR(60)     NOT NULL,   -- ex.: credencial:12
  chave        VARCHAR(120)    NOT NULL,   -- Idempotency-Key enviado
  http_status  INT             NOT NULL,
  resposta     MEDIUMTEXT      NOT NULL,   -- envelope JSON devolvido
  criado_em    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_idem (escopo, chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice composto para dashboards/tabela verdade em escala.
ALTER TABLE elegivel ADD KEY ix_elegivel_campanha_status (campanha_id, status);

-- VIEW da tabela verdade usa o nome do elegível (fallback para o do paciente).
CREATE OR REPLACE VIEW vw_tabela_verdade AS
SELECT
  e.tenant_id,
  e.campanha_id,
  e.paciente_id,
  p.cpf,
  COALESCE(e.nome, p.nome) AS nome,
  e.status                 AS situacao_elegivel,
  COUNT(a.id)              AS total_aplicacoes,
  MAX(a.aplicado_em)       AS ultima_aplicacao_em
FROM elegivel e
JOIN paciente p ON p.id = e.paciente_id
LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
GROUP BY e.tenant_id, e.campanha_id, e.paciente_id, p.cpf, COALESCE(e.nome, p.nome), e.status;

INSERT INTO schema_migracao (arquivo) VALUES ('014_integridade_escala_batch1.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
