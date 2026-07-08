-- ============================================================================
-- 020_sync_turnover.sql
-- Fase B: sincronização de turnover por diferença. RH envia a lista completa;
-- quem não estiver nela (e não vacinado) é marcado como 'removido'.
--  - elegivel.sincronizado_em: carimbo do último sync que "viu" o elegível
--  - importacao_elegiveis.sincronizar: 1 = modo sync (remove ausentes)
--  - importacao_elegiveis.total_removidos: quantos foram removidos no sync
-- ============================================================================

ALTER TABLE elegivel ADD COLUMN sincronizado_em DATETIME NULL DEFAULT NULL;

ALTER TABLE importacao_elegiveis ADD COLUMN sincronizar TINYINT(1) NOT NULL DEFAULT 0 AFTER formato;
ALTER TABLE importacao_elegiveis ADD COLUMN total_removidos INT UNSIGNED NULL DEFAULT 0 AFTER total_invalidos;

INSERT INTO schema_migracao (arquivo) VALUES ('020_sync_turnover.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
