-- ============================================================================
-- 012_motivo_situacao_elegivel.sql
-- Função: RN-020 — registrar o MOTIVO quando o elegível não é vacinado
--         (recusado / ausente / inelegível). Ajuda no controle de adesão.
-- ============================================================================

ALTER TABLE elegivel
  ADD COLUMN motivo_situacao VARCHAR(160) NULL DEFAULT NULL AFTER status;

INSERT INTO schema_migracao (arquivo) VALUES ('012_motivo_situacao_elegivel.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
