-- ============================================================================
-- 021_modelo_acesso_portal.sql
-- PORTAL D0: hierarquia de acesso com escopos (doc 04 §4.1).
--  - grupo_empresarial (carteira de clientes) + cliente_b2b.grupo_empresarial_id
--  - unidade (local de vacinação/lotação) + elegivel.unidade_id
--  - usuario_atribuicao (multi-atribuição; níveis gestao_interna|grupo|negocio|local)
-- Backfill: cria atribuições a partir do perfil/tenant atuais.
-- ============================================================================

CREATE TABLE IF NOT EXISTS grupo_empresarial (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome          VARCHAR(180)    NOT NULL,
  status        VARCHAR(20)     NOT NULL DEFAULT 'ativo',
  criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em   DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE cliente_b2b ADD COLUMN grupo_empresarial_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER razao_social;
ALTER TABLE cliente_b2b ADD KEY ix_cliente_grupo (grupo_empresarial_id);
ALTER TABLE cliente_b2b ADD CONSTRAINT fk_cliente_grupo FOREIGN KEY (grupo_empresarial_id)
    REFERENCES grupo_empresarial (id) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS unidade (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_b2b_id BIGINT UNSIGNED NOT NULL,
  nome           VARCHAR(160)    NOT NULL,
  codigo_lotacao VARCHAR(60)     NULL DEFAULT NULL,
  cidade         VARCHAR(120)    NULL DEFAULT NULL,
  uf             CHAR(2)         NULL DEFAULT NULL,
  status         VARCHAR(20)     NOT NULL DEFAULT 'ativa',
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em    DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_unidade_cliente (cliente_b2b_id),
  CONSTRAINT fk_unidade_cliente FOREIGN KEY (cliente_b2b_id)
      REFERENCES cliente_b2b (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE elegivel ADD COLUMN unidade_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER campanha_id;
ALTER TABLE elegivel ADD KEY ix_elegivel_unidade (unidade_id);
ALTER TABLE elegivel ADD CONSTRAINT fk_elegivel_unidade FOREIGN KEY (unidade_id)
    REFERENCES unidade (id) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS usuario_atribuicao (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id  BIGINT UNSIGNED NOT NULL,
  nivel       VARCHAR(20)     NOT NULL,  -- gestao_interna | grupo | negocio | local
  escopo_tipo VARCHAR(20)     NOT NULL,  -- global | grupo_empresarial | cliente_b2b | unidade
  escopo_id   BIGINT UNSIGNED NOT NULL DEFAULT 0,  -- 0 para global
  criado_por  BIGINT UNSIGNED NULL DEFAULT NULL,
  criado_em   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_atribuicao (usuario_id, nivel, escopo_tipo, escopo_id),
  KEY ix_atribuicao_usuario (usuario_id),
  KEY ix_atribuicao_escopo (escopo_tipo, escopo_id),
  CONSTRAINT fk_atribuicao_usuario FOREIGN KEY (usuario_id)
      REFERENCES usuario (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill: internos -> gestao_interna; cliente_b2b -> negocio(escopo cliente).
INSERT IGNORE INTO usuario_atribuicao (usuario_id, nivel, escopo_tipo, escopo_id)
  SELECT id, 'gestao_interna', 'global', 0 FROM usuario
   WHERE perfil IN ('super_admin', 'operador_interno') AND excluido_em IS NULL;

INSERT IGNORE INTO usuario_atribuicao (usuario_id, nivel, escopo_tipo, escopo_id)
  SELECT id, 'negocio', 'cliente_b2b', tenant_id FROM usuario
   WHERE perfil = 'cliente_b2b' AND tenant_id IS NOT NULL AND excluido_em IS NULL;

INSERT INTO schema_migracao (arquivo) VALUES ('021_modelo_acesso_portal.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
