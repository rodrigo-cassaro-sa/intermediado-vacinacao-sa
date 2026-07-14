-- ============================================================================
-- 026_codigo_campanha.sql
-- Função: codificação automática de campanha no formato
--         <VAC>.<TEMP>.<MOD>.<GRP>.<CLI>.<SEQ>  (ex.: IF3.2026.IC.TES.TES.1)
--   VAC  = vacina.sigla        TEMP = campanha.temporada (ano)
--   MOD  = IC | RC             GRP  = grupo.sigla (ou sigla do cliente, se sem grupo)
--   CLI  = cliente.sigla       SEQ  = contador por prefixo VAC.TEMP.MOD.GRP.CLI
--
-- Adiciona colunas de sigla (vacina/cliente/grupo) e de código/temporada/seq na
-- campanha. Colunas entram como NULL (não quebra dados existentes); a
-- obrigatoriedade na criação é validada na aplicação. nome vira opcional.
-- Idempotente: o runner tolera 1060 (coluna dup.) e 1061 (índice dup.).
-- ============================================================================

-- Siglas (3 caracteres A-Z/0-9, maiúsculas) — únicas por cadastro.
ALTER TABLE vacina ADD COLUMN sigla CHAR(3) NULL DEFAULT NULL AFTER nome;
ALTER TABLE vacina ADD UNIQUE KEY uq_vacina_sigla (sigla);

ALTER TABLE cliente_b2b ADD COLUMN sigla CHAR(3) NULL DEFAULT NULL AFTER razao_social;
ALTER TABLE cliente_b2b ADD UNIQUE KEY uq_cliente_sigla (sigla);

ALTER TABLE grupo_empresarial ADD COLUMN sigla CHAR(3) NULL DEFAULT NULL AFTER nome;
ALTER TABLE grupo_empresarial ADD UNIQUE KEY uq_grupo_sigla (sigla);

-- Campanha: código gerado, temporada explícita, contador e nome opcional.
ALTER TABLE campanha ADD COLUMN codigo VARCHAR(40) NULL DEFAULT NULL AFTER nome;
ALTER TABLE campanha ADD COLUMN temporada SMALLINT UNSIGNED NULL DEFAULT NULL AFTER codigo;
ALTER TABLE campanha ADD COLUMN seq INT UNSIGNED NULL DEFAULT NULL AFTER temporada;
ALTER TABLE campanha ADD UNIQUE KEY uq_campanha_codigo (codigo);
ALTER TABLE campanha MODIFY COLUMN nome VARCHAR(160) NULL DEFAULT NULL;

-- Siglas iniciais do catálogo de vacinas semeado (só preenche se ainda vazio).
UPDATE vacina SET sigla = 'IF3' WHERE nome = 'Influenza (Gripe)'    AND sigla IS NULL;
UPDATE vacina SET sigla = 'HEP' WHERE nome = 'Hepatite B'           AND sigla IS NULL;
UPDATE vacina SET sigla = 'DTA' WHERE nome = 'Tétano/Difteria (dT)' AND sigla IS NULL;
UPDATE vacina SET sigla = 'FAM' WHERE nome = 'Febre Amarela'        AND sigla IS NULL;
UPDATE vacina SET sigla = 'COV' WHERE nome = 'COVID-19'             AND sigla IS NULL;

INSERT INTO schema_migracao (arquivo) VALUES ('026_codigo_campanha.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
