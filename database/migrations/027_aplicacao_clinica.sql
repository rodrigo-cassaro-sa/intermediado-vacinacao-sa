-- ============================================================================
-- 027_aplicacao_clinica.sql
-- Função: vincular a aplicação à clínica credenciada que a realizou (quando foi
--         em clínica). Complementa o lastro do profissional (nome/CPF, migration
--         011) e o executor polimórfico. Coluna nullable + índice; sem FK rígida
--         (mesmo padrão do executor_id polimórfico — evita cópia de tabela/1215).
-- Idempotente: o runner tolera 1060 (coluna dup.) e 1061 (índice dup.).
-- ============================================================================

ALTER TABLE aplicacao ADD COLUMN clinica_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER executor_id;
ALTER TABLE aplicacao ADD KEY ix_aplicacao_clinica (clinica_id);

INSERT INTO schema_migracao (arquivo) VALUES ('027_aplicacao_clinica.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
