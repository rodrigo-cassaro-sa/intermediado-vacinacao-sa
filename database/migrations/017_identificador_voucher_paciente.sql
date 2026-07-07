-- ============================================================================
-- 017_identificador_voucher_paciente.sql
-- Item 8 / RN-028: identidade do paciente por CPF (validado) OU por um
-- identificador/voucher (estrangeiro/terceiro sem CPF). CPF passa a ser opcional.
-- ============================================================================

-- CPF opcional (permite paciente identificado só por voucher). A UNIQUE existente
-- (uq_paciente_cpf) continua válida: MySQL permite múltiplos NULL em índice único.
ALTER TABLE paciente MODIFY COLUMN cpf VARCHAR(11) NULL DEFAULT NULL;

-- Identificador alternativo (voucher/passaporte/etc.), único quando informado.
ALTER TABLE paciente ADD COLUMN identificador VARCHAR(40) NULL DEFAULT NULL AFTER cpf;
ALTER TABLE paciente ADD UNIQUE KEY uq_paciente_identificador (identificador);

INSERT INTO schema_migracao (arquivo) VALUES ('017_identificador_voucher_paciente.sql')
  ON DUPLICATE KEY UPDATE arquivo = arquivo;
