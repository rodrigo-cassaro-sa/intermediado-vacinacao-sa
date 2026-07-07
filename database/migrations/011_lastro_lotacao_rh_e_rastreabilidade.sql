-- ============================================================================
-- 011_lastro_lotacao_rh_e_rastreabilidade.sql
-- Função: RN-018 (códigos do cliente no elegível) e RN-019 (lastro/rastreabilidade
--         do vacinado: local e profissional). Base: docs/02, docs/08.
-- Colunas NULL para não quebrar registros antigos; novas gravações exigem via código.
-- ============================================================================

-- Elegível: identificadores do cliente contratante (RN-018).
ALTER TABLE elegivel
  ADD COLUMN codigo_lotacao VARCHAR(60) NULL DEFAULT NULL AFTER cpf_titular,
  ADD COLUMN codigo_rh      VARCHAR(60) NULL DEFAULT NULL AFTER codigo_lotacao,
  ADD KEY ix_elegivel_codigo_rh (codigo_rh);

-- Aplicação: lastro do local e do profissional (RN-019).
ALTER TABLE aplicacao
  ADD COLUMN cidade            VARCHAR(120) NULL DEFAULT NULL AFTER local_aplicacao,
  ADD COLUMN uf                CHAR(2)      NULL DEFAULT NULL AFTER cidade,
  ADD COLUMN unidade           VARCHAR(120) NULL DEFAULT NULL AFTER uf,
  ADD COLUMN profissional_nome VARCHAR(120) NULL DEFAULT NULL AFTER unidade,
  ADD COLUMN profissional_cpf  VARCHAR(11)  NULL DEFAULT NULL AFTER profissional_nome;

INSERT INTO schema_migracao (arquivo) VALUES ('011_lastro_lotacao_rh_e_rastreabilidade.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
