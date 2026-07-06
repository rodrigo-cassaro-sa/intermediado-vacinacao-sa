-- ============================================================================
-- 008_criar_view_tabela_verdade.sql
-- Função: VIEW consolidada elegível x aplicado (RN-005). Não é tabela física.
-- Base: docs/08 §6. Sempre consultada com filtro por tenant_id + campanha_id.
-- ============================================================================

CREATE OR REPLACE VIEW vw_tabela_verdade AS
SELECT
  e.tenant_id,
  e.campanha_id,
  e.paciente_id,
  p.cpf,
  p.nome,
  e.status                       AS situacao_elegivel,
  COUNT(a.id)                    AS total_aplicacoes,
  MAX(a.aplicado_em)             AS ultima_aplicacao_em
FROM elegivel e
JOIN paciente p ON p.id = e.paciente_id
LEFT JOIN aplicacao a
       ON a.elegivel_id = e.id AND a.status = 'confirmada'
GROUP BY e.tenant_id, e.campanha_id, e.paciente_id, p.cpf, p.nome, e.status;

INSERT INTO schema_migracao (arquivo) VALUES ('008_criar_view_tabela_verdade.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
