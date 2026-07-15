-- ============================================================================
-- 028_permissao_ver_cpf.sql
-- Função: controle por usuário de VISIBILIDADE DE CPF (LGPD/minimização).
--   pode_ver_cpf = 1 -> vê o CPF completo (listas e export)
--   pode_ver_cpf = 0 -> vê o CPF mascarado (***.***.xxx-**)
-- Padrão 0 (mascarado). Os super_admin atuais já ficam liberados para não mudar
-- o comportamento de quem administra hoje. Idempotente (1060 tolerado).
-- ============================================================================

ALTER TABLE usuario ADD COLUMN pode_ver_cpf TINYINT(1) NOT NULL DEFAULT 0 AFTER perfil;

-- Mantém o comportamento atual de quem já administra (super_admin vê completo).
UPDATE usuario SET pode_ver_cpf = 1 WHERE perfil = 'super_admin';

INSERT INTO schema_migracao (arquivo) VALUES ('028_permissao_ver_cpf.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
