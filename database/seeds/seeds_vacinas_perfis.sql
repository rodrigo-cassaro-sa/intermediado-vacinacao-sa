-- ============================================================================
-- seeds_vacinas_perfis.sql
-- Função: dados iniciais mínimos — catálogo de vacinas e usuário admin inicial.
-- Base: docs/08 (plano de seeds), docs/10 (senha via password_hash).
-- ATENÇÃO: troque o senha_hash abaixo por um hash real gerado com password_hash().
--          NÃO versionar senha real; este é apenas um placeholder de bootstrap.
-- ============================================================================

-- Catálogo inicial de vacinas (exemplos comuns em imunização corporativa)
INSERT INTO vacina (nome, fabricante, doses_padrao, status) VALUES
  ('Influenza (Gripe)',        NULL, 1, 'ativa'),
  ('Hepatite B',               NULL, 3, 'ativa'),
  ('Tétano/Difteria (dT)',     NULL, 1, 'ativa'),
  ('Febre Amarela',            NULL, 1, 'ativa'),
  ('COVID-19',                 NULL, 1, 'ativa')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Usuário interno inicial (super_admin). tenant_id NULL = usuário da prestadora.
-- Gere o hash real, por exemplo (CLI): php -r "echo password_hash('SUA_SENHA', PASSWORD_DEFAULT);"
INSERT INTO usuario (tenant_id, perfil, nome, email, senha_hash, status)
VALUES (NULL, 'super_admin', 'Administrador', 'admin@exemplo.com',
        '$2y$10$SUBSTITUA_POR_HASH_REAL_GERADO_COM_PASSWORD_HASH', 'ativo')
ON DUPLICATE KEY UPDATE email = email;
