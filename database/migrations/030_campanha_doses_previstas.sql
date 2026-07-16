-- ============================================================================
-- 030_campanha_doses_previstas.sql
-- Função: meta de DOSES PREVISTAS por campanha (quantidade contratada/planejada).
--   Usada nos relatórios gerenciais: se os elegíveis ficam abaixo da meta há
--   ALERTA; abaixo de 70% da meta, estágio CRÍTICO. Nullable (sem meta = sem alerta).
-- Idempotente: runner tolera 1060 (coluna dup.).
-- ============================================================================

ALTER TABLE campanha ADD COLUMN doses_previstas INT UNSIGNED NULL DEFAULT NULL AFTER temporada;

INSERT INTO schema_migracao (arquivo) VALUES ('030_campanha_doses_previstas.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
