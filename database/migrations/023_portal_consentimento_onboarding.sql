-- ============================================================================
-- 023_portal_consentimento_onboarding.sql
-- Portal D1: rastrear consentimento LGPD e onboarding do usuário do portal.
-- ============================================================================

ALTER TABLE usuario ADD COLUMN consentimento_em DATETIME NULL DEFAULT NULL;
ALTER TABLE usuario ADD COLUMN versao_termo VARCHAR(20) NULL DEFAULT NULL;
ALTER TABLE usuario ADD COLUMN onboarding_em DATETIME NULL DEFAULT NULL;

INSERT INTO schema_migracao (arquivo) VALUES ('023_portal_consentimento_onboarding.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
