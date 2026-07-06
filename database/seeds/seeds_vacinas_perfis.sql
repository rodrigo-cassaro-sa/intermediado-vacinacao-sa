-- ============================================================================
-- seeds_vacinas_perfis.sql
-- Função: dados iniciais seguros — catálogo de vacinas.
-- Base: docs/08 (plano de seeds).
-- O usuário admin NÃO é criado aqui (evita hash placeholder inválido).
-- Crie o admin com senha real: php scripts/criar_admin.php <email> <senha> "<Nome>"
-- ============================================================================

-- Catálogo inicial de vacinas (exemplos comuns em imunização corporativa)
INSERT INTO vacina (nome, fabricante, doses_padrao, status) VALUES
  ('Influenza (Gripe)',        NULL, 1, 'ativa'),
  ('Hepatite B',               NULL, 3, 'ativa'),
  ('Tétano/Difteria (dT)',     NULL, 1, 'ativa'),
  ('Febre Amarela',            NULL, 1, 'ativa'),
  ('COVID-19',                 NULL, 1, 'ativa')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);
