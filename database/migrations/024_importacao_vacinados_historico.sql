-- ============================================================================
-- 024_importacao_vacinados_historico.sql
-- Função: habilitar importação retrocompatível de VACINADOS de anos anteriores
--         (carteira perpétua — RN-027). Sem quebrar dados atuais.
--
-- Não cria tabelas novas: reaproveita campanha/elegivel/aplicacao. As colunas de
-- lastro (cidade/uf/unidade/profissional) e códigos do cliente já são NULL desde
-- a 011, então o legado "sujo" cabe no modelo. O que este import usa de novo:
--   - campanha.modalidade = 'historico'  (campanha "Histórico — {vacina} {ano}",
--     status 'encerrada', período = ano cheio) — auto-criada por (cliente,vacina,ano);
--   - aplicacao.origem = 'historico' e executor_tipo = 'importacao_historica';
--   - elegivel.origem = 'historico', status 'aplicado'.
-- São apenas valores de VARCHAR já existentes (sem ENUM), nada a alterar no schema.
--
-- Único ajuste físico: índice para localizar/relatar o legado rapidamente
-- (o nome ix_aplicacao_origem já existe para aplicacao_origem_id — usamos outro).
-- ============================================================================

ALTER TABLE aplicacao ADD KEY ix_aplicacao_origem_canal (origem, tenant_id);

INSERT INTO schema_migracao (arquivo) VALUES ('024_importacao_vacinados_historico.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
