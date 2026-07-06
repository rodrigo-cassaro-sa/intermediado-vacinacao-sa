-- ============================================================================
-- 009_adicionar_clinica_ao_elegivel.sql
-- Função: RN-012 — vincular cada elegível à clínica da rede a que foi atribuído.
--         Em campanha in_company o valor fica NULL. Base: docs/02, docs/08.
-- Idempotência controlada pelo scripts/migrar.php (pula se já aplicada).
-- ============================================================================

ALTER TABLE elegivel
  ADD COLUMN clinica_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER campanha_id,
  ADD KEY ix_elegivel_clinica (clinica_id),
  ADD CONSTRAINT fk_elegivel_clinica FOREIGN KEY (clinica_id)
      REFERENCES clinica_credenciada (id) ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO schema_migracao (arquivo) VALUES ('009_adicionar_clinica_ao_elegivel.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
