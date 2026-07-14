-- ============================================================================
-- seeds_vacinas_perfis.sql
-- Função: dados iniciais seguros — catálogo de vacinas.
-- Base: docs/08 (plano de seeds).
-- O usuário admin NÃO é criado aqui (evita hash placeholder inválido).
-- Crie o admin com senha real: php scripts/criar_admin.php <email> <senha> "<Nome>"
-- ============================================================================

-- Catálogo inicial de vacinas (exemplos comuns em imunização corporativa).
-- A sigla (3 caracteres) alimenta o código automático de campanha (migration 026).
INSERT INTO vacina (nome, sigla, fabricante, doses_padrao, status) VALUES
  ('Influenza (Gripe)',        'IF3', NULL, 1, 'ativa'),
  ('Hepatite B',               'HEP', NULL, 3, 'ativa'),
  ('Tétano/Difteria (dT)',     'DTA', NULL, 1, 'ativa'),
  ('Febre Amarela',            'FAM', NULL, 1, 'ativa'),
  ('COVID-19',                 'COV', NULL, 1, 'ativa')
ON DUPLICATE KEY UPDATE nome = VALUES(nome), sigla = COALESCE(vacina.sigla, VALUES(sigla));
