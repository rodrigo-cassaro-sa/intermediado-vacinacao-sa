-- ============================================================================
-- 010_adicionar_tipo_vinculo_elegivel.sql
-- Função: RN-016/017 — tipo de vínculo do elegível com a empresa contratante
--         (colaborador | dependente | terceiro) e, para dependente, o CPF do
--         titular (colaborador vinculado à empresa). Base: docs/02, docs/08.
-- Colunas NULL para não quebrar registros antigos; novas ingestões exigem os campos.
-- ============================================================================

ALTER TABLE elegivel
  ADD COLUMN tipo_vinculo VARCHAR(20) NULL DEFAULT NULL AFTER origem,
  ADD COLUMN cpf_titular  VARCHAR(11) NULL DEFAULT NULL AFTER tipo_vinculo,
  ADD KEY ix_elegivel_tipo_vinculo (tipo_vinculo);

INSERT INTO schema_migracao (arquivo) VALUES ('010_adicionar_tipo_vinculo_elegivel.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
