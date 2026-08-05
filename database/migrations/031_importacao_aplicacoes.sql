-- ============================================================================
-- 031_importacao_aplicacoes.sql
-- Função: importação em MASSA de vacinados em campanha ATIVA (RN-031).
--
-- Diferente da 024/025 (vacinados históricos, que criam campanha 'historico'),
-- aqui a aplicação entra na campanha corrente e passa por todas as regras
-- normais (RN-003 período/vacina prevista, RN-013 dose duplicada, RN-019 lastro).
--
-- Fluxo: simulação obrigatória -> confirmação -> fila -> worker.
-- O vínculo aplicacao.importacao_aplicacoes_id permite estornar o LOTE inteiro
-- quando o arquivo entrou errado (dose aplicada é dose faturada).
-- ============================================================================

CREATE TABLE IF NOT EXISTS importacao_aplicacoes (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tenant_id         BIGINT UNSIGNED NOT NULL,
  campanha_id       BIGINT UNSIGNED NOT NULL,
  arquivo           VARCHAR(255)    NOT NULL,             -- CSV em storage/uploads
  -- simulada: processada só para conferência, nada gravado (RN-031)
  -- simulando|simulada|pendente|processando|concluida|falha|estornada
  status            VARCHAR(20)     NOT NULL DEFAULT 'simulando',
  criar_elegivel    TINYINT(1)      NOT NULL DEFAULT 0,   -- CPF fora da lista: criar elegível ou rejeitar
  padroes           LONGTEXT        NULL DEFAULT NULL,    -- JSON com os dados comuns do lote
  total_linhas      INT UNSIGNED    NULL DEFAULT NULL,
  total_processados INT UNSIGNED    NULL DEFAULT 0,
  total_aplicacoes  INT UNSIGNED    NULL DEFAULT 0,       -- vacinados registrados
  total_elegiveis   INT UNSIGNED    NULL DEFAULT 0,       -- elegíveis criados na hora
  total_rejeitados  INT UNSIGNED    NULL DEFAULT 0,
  total_estornados  INT UNSIGNED    NULL DEFAULT 0,
  mensagem_erro     VARCHAR(255)    NULL DEFAULT NULL,
  motivo_estorno    VARCHAR(255)    NULL DEFAULT NULL,
  estornado_por     BIGINT UNSIGNED NULL DEFAULT NULL,
  estornado_em      DATETIME        NULL DEFAULT NULL,
  criado_por        BIGINT UNSIGNED NULL DEFAULT NULL,
  criado_em         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  iniciado_em       DATETIME        NULL DEFAULT NULL,
  finalizado_em     DATETIME        NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ix_imp_aplic_status (status),
  KEY ix_imp_aplic_campanha (campanha_id),
  KEY ix_imp_aplic_tenant (tenant_id),
  CONSTRAINT fk_imp_aplic_campanha FOREIGN KEY (campanha_id)
      REFERENCES campanha (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_imp_aplic_tenant FOREIGN KEY (tenant_id)
      REFERENCES cliente_b2b (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Relatório de erros por linha (o cliente corrige e reenvia). Serve tanto para a
-- simulação quanto para a importação confirmada.
CREATE TABLE IF NOT EXISTS importacao_aplicacao_erro (
  id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  importacao_id BIGINT UNSIGNED NOT NULL,
  linha         INT UNSIGNED    NOT NULL,
  cpf           VARCHAR(14)     NULL DEFAULT NULL,
  nome          VARCHAR(160)    NULL DEFAULT NULL,
  codigo        VARCHAR(40)     NOT NULL,
  criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY ix_imp_aplic_erro (importacao_id, linha),
  CONSTRAINT fk_imp_aplic_erro FOREIGN KEY (importacao_id)
      REFERENCES importacao_aplicacoes (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lastro do lote na própria aplicação: permite o estorno em massa e responde
-- "de onde veio este vacinado?" na auditoria.
ALTER TABLE aplicacao
  ADD COLUMN importacao_aplicacoes_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER origem;

ALTER TABLE aplicacao
  ADD KEY ix_aplicacao_importacao (importacao_aplicacoes_id);

INSERT INTO schema_migracao (arquivo) VALUES ('031_importacao_aplicacoes.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
