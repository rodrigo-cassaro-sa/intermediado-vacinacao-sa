-- ============================================================================
-- 022_view_tabela_verdade_unidade.sql
-- Portal D2: expõe unidade_id na VIEW da tabela verdade, para restrição de
-- escopo por unidade (usuário nível 'local'). Base: doc 04 §4.1.
-- ============================================================================

CREATE OR REPLACE VIEW vw_tabela_verdade AS
SELECT
  e.tenant_id,
  e.campanha_id,
  e.unidade_id,
  e.paciente_id,
  p.cpf,
  COALESCE(e.nome, p.nome) AS nome,
  e.status                 AS situacao_elegivel,
  COUNT(a.id)              AS total_aplicacoes,
  MAX(a.aplicado_em)       AS ultima_aplicacao_em
FROM elegivel e
JOIN paciente p ON p.id = e.paciente_id
LEFT JOIN aplicacao a ON a.elegivel_id = e.id AND a.status = 'confirmada'
GROUP BY e.tenant_id, e.campanha_id, e.unidade_id, e.paciente_id, p.cpf, COALESCE(e.nome, p.nome), e.status;

INSERT INTO schema_migracao (arquivo) VALUES ('022_view_tabela_verdade_unidade.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
