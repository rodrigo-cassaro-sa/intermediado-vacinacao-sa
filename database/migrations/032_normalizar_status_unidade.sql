-- ============================================================================
-- 032_normalizar_status_unidade.sql
-- Função: alinhar unidade.status ao vocabulário da coluna (BUG-005).
--
-- A coluna nasceu com DEFAULT 'ativa' (unidade é substantivo feminino, como
-- vacina/clinica/campanha), mas a API validava 'ativo'/'inativo' e a tela
-- comparava com 'ativo'. Resultado: unidade recém-criada vinha 'ativa', não
-- casava com o filtro padrão "Ativas", era pintada como "Inativa" e abria com o
-- select de status em branco. Quem editou pela tela gravou a forma masculina.
--
-- Aqui só o VALOR é normalizado — nenhuma unidade é apagada e a intenção de quem
-- desativou é preservada ('inativo' continua inativa). Vazio vira 'ativa', que é
-- a regra: toda unidade nasce ativa.
-- Idempotente: rodar de novo não altera mais nada.
-- ============================================================================

UPDATE unidade SET status = 'ativa'   WHERE status = 'ativo';
UPDATE unidade SET status = 'inativa' WHERE status = 'inativo';
UPDATE unidade SET status = 'ativa'   WHERE status IS NULL OR TRIM(status) = '';

INSERT INTO schema_migracao (arquivo) VALUES ('032_normalizar_status_unidade.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
