-- ============================================================================
-- 001_criar_cliente_b2b_usuario.sql
-- Função: tenants (cliente_b2b) e usuários de acesso por sessão.
-- Base: docs/08 (tabelas cliente_b2b, usuario). cliente_b2b.id = tenant_id.
-- ============================================================================

CREATE TABLE IF NOT EXISTS cliente_b2b (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  razao_social   VARCHAR(180)    NOT NULL,
  cnpj           VARCHAR(14)     NOT NULL,              -- só dígitos
  status         VARCHAR(20)     NOT NULL DEFAULT 'ativo', -- ativo | inativo
  criado_em      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em  DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em    DATETIME        NULL DEFAULT NULL,     -- soft delete
  PRIMARY KEY (id),
  UNIQUE KEY uq_cliente_b2b_cnpj (cnpj),
  KEY ix_cliente_b2b_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS usuario (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id         BIGINT UNSIGNED NULL DEFAULT NULL,   -- NULL = usuário interno
  perfil            VARCHAR(30)     NOT NULL,            -- super_admin|operador_interno|cliente_b2b|profissional_saude
  nome              VARCHAR(120)    NOT NULL,
  email             VARCHAR(160)    NOT NULL,
  senha_hash        VARCHAR(255)    NOT NULL,            -- password_hash (bcrypt/argon2)
  status            VARCHAR(20)     NOT NULL DEFAULT 'ativo', -- ativo | bloqueado
  ultimo_acesso_em  DATETIME        NULL DEFAULT NULL,
  criado_em         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em     DATETIME        NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em       DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuario_email (email),
  KEY ix_usuario_tenant (tenant_id),
  KEY ix_usuario_perfil (perfil),
  KEY ix_usuario_status (status),
  CONSTRAINT fk_usuario_tenant FOREIGN KEY (tenant_id)
      REFERENCES cliente_b2b (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO schema_migracao (arquivo) VALUES ('001_criar_cliente_b2b_usuario.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
