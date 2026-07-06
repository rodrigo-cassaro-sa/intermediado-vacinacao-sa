# Modelagem de Banco de Dados

## Objetivo

Documentar entidades, tabelas, campos, relacionamentos, índices, migrations e cuidados de dados.

> Escopo: núcleo do MVP (docs 02, 03, 05). Multi-tenant por `tenant_id`, identidade do paciente
> por **CPF** (RN-008), aplicação **imutável** após confirmação (RN-010), tabela verdade como VIEW.
> Convenção: nomes de campos em português; termos técnicos (`tenant_id`, `request_id`) em inglês.
> MySQL/MariaDB, InnoDB, `utf8mb4`. Toda tabela de negócio tem `tenant_id`.

---

# 1. Entidades

| Entidade | Descrição | Tabela |
|---|---|---|
| Cliente B2B (tenant) | Empresa contratante; **é o tenant** do sistema | `cliente_b2b` |
| Usuário | Pessoa que acessa por sessão (admin, operador, gestor B2B, profissional) | `usuario` |
| Paciente | Identidade única da pessoa vacinável (chave CPF) | `paciente` |
| Vacina | Catálogo global de vacinas | `vacina` |
| Campanha | Ação de imunização de um cliente numa modalidade | `campanha` |
| Vacinas da campanha | Quais vacinas/esquema uma campanha oferece | `campanha_vacina` |
| Elegível | Vínculo paciente↔campanha com status de execução | `elegivel` |
| Importação de elegíveis | Lote de carga (upload ou API) | `importacao_elegiveis` |
| Clínica credenciada | Parceiro da rede (executa por API) | `clinica_credenciada` |
| Aplicação | Registro imutável de dose aplicada (rastreável) | `aplicacao` |
| Credencial de API | Token de máquina (ingestão B2B / rede), com escopo | `credencial_api` |
| Consentimento LGPD | Aceite do paciente (base legal) | `consentimento_lgpd` |
| Log de auditoria | Trilha de acesso/ações críticas | `log_auditoria` |

---

# 2. Tabelas

## cliente_b2b  (tenant)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK | é o `tenant_id` das demais tabelas |
| razao_social | VARCHAR(180) | sim |  |  |
| cnpj | VARCHAR(14) | sim | UNIQUE | só dígitos |
| status | VARCHAR(20) | sim | IDX | ativo / inativo |
| criado_em | DATETIME | sim |  |  |
| atualizado_em | DATETIME | não |  |  |
| excluido_em | DATETIME | não |  | soft delete |

## usuario
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | não | FK/IDX | NULL = usuário interno (super_admin/operador) |
| perfil | VARCHAR(30) | sim | IDX | super_admin, operador_interno, cliente_b2b, profissional_saude |
| nome | VARCHAR(120) | sim |  |  |
| email | VARCHAR(160) | sim | UNIQUE |  |
| senha_hash | VARCHAR(255) | sim |  | password_hash (bcrypt/argon2) |
| status | VARCHAR(20) | sim | IDX | ativo / bloqueado |
| ultimo_acesso_em | DATETIME | não |  |  |
| criado_em / atualizado_em / excluido_em | DATETIME | — |  | padrão |

## paciente  (identidade global por CPF)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| cpf | VARCHAR(11) | sim | UNIQUE | chave de deduplicação (RN-008) |
| nome | VARCHAR(120) | sim |  | dado pessoal |
| data_nascimento | DATE | não |  |  |
| sexo | CHAR(1) | não |  |  |
| criado_em / atualizado_em | DATETIME | — |  | padrão |

> **Sem `tenant_id`**: o paciente é global para consolidar a carteira entre campanhas/empresas.
> O vínculo com o tenant vive em `elegivel`/`aplicacao`. Estrangeiro sem CPF = dúvida aberta (doc 02).

## vacina  (catálogo global)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| nome | VARCHAR(120) | sim | IDX | ex.: Influenza (Gripe) |
| fabricante | VARCHAR(120) | não |  |  |
| doses_padrao | TINYINT | sim |  | nº de doses do esquema (default 1) |
| status | VARCHAR(20) | sim |  | ativa / inativa |
| criado_em / atualizado_em | DATETIME | — |  |  |

## campanha
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | sim | FK/IDX | → cliente_b2b (RN-001) |
| nome | VARCHAR(160) | sim |  |  |
| modalidade | VARCHAR(20) | sim | IDX | `rede_credenciada` \| `in_company` |
| periodo_inicio | DATE | sim |  |  |
| periodo_fim | DATE | sim |  | RN-003 (janela de aplicação) |
| status | VARCHAR(20) | sim | IDX | rascunho / ativa / encerrada |
| criado_por | BIGINT | sim |  | → usuario |
| criado_em / atualizado_em / excluido_em | DATETIME | — |  | padrão |

## campanha_vacina
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | sim | IDX |  |
| campanha_id | BIGINT | sim | FK/IDX |  |
| vacina_id | BIGINT | sim | FK/IDX |  |
| doses_previstas | TINYINT | sim |  | pode sobrescrever doses_padrao |
| — | — | — | UNIQUE(campanha_id, vacina_id) | evita duplicidade |

## elegivel  (paciente ↔ campanha)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | sim | IDX |  |
| campanha_id | BIGINT | sim | FK/IDX |  |
| clinica_id | BIGINT | não | FK/IDX | RN-012: clínica atribuída (rede credenciada); NULL em in_company. Migration 009 |
| paciente_id | BIGINT | sim | FK/IDX |  |
| origem | VARCHAR(20) | sim |  | upload / api / autoelegivel |
| status | VARCHAR(20) | sim | IDX | pendente / aplicado / recusado / inelegivel / ausente (RN-005) |
| importacao_id | BIGINT | não | FK | lote de origem |
| criado_em / atualizado_em | DATETIME | — |  |  |
| — | — | — | UNIQUE(campanha_id, paciente_id) | 1 elegibilidade por paciente/campanha |

## importacao_elegiveis
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | sim | IDX |  |
| campanha_id | BIGINT | sim | FK/IDX |  |
| origem | VARCHAR(20) | sim |  | upload / api |
| arquivo | VARCHAR(255) | não |  | caminho em storage/uploads (só upload) |
| total_linhas | INT | não |  |  |
| total_validos | INT | não |  |  |
| total_invalidos | INT | não |  |  |
| status | VARCHAR(20) | sim |  | processando / concluida / falha |
| criado_por | BIGINT | não |  | usuario (upload) ou credencial (api) |
| criado_em | DATETIME | sim |  |  |

## clinica_credenciada  (rede)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| nome | VARCHAR(180) | sim |  |  |
| cnpj | VARCHAR(14) | sim | UNIQUE |  |
| status | VARCHAR(20) | sim | IDX | ativa / inativa |
| criado_em / atualizado_em / excluido_em | DATETIME | — |  |  |

> Clínica é **global** (parceira da plataforma), não pertence a um tenant. O acesso dela é
> limitado por `credencial_api.escopo_campanha_id`.

## aplicacao  (registro imutável — RN-004, RN-010)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | sim | IDX |  |
| campanha_id | BIGINT | sim | FK/IDX |  |
| elegivel_id | BIGINT | sim | FK/IDX |  |
| paciente_id | BIGINT | sim | FK/IDX |  |
| vacina_id | BIGINT | sim | FK/IDX |  |
| dose | TINYINT | sim |  | 1ª, 2ª, reforço |
| lote | VARCHAR(60) | sim |  | rastreabilidade sanitária |
| via_administracao | VARCHAR(30) | não |  | ex.: intramuscular |
| local_aplicacao | VARCHAR(160) | não |  | local físico / endereço in company |
| executor_tipo | VARCHAR(20) | sim | IDX | profissional_saude / clinica_credenciada |
| executor_id | BIGINT | sim |  | → usuario ou clinica_credenciada |
| origem | VARCHAR(20) | sim |  | app / api |
| status | VARCHAR(20) | sim | IDX | confirmada / retificada / estornada |
| aplicacao_origem_id | BIGINT | não | FK | preenchido quando este registro retifica outro |
| motivo_retificacao | VARCHAR(255) | não |  | obrigatório quando há retificação |
| aplicado_em | DATETIME | sim | IDX | data/hora da dose |
| criado_por | BIGINT | não |  | quem registrou no sistema |
| criado_em | DATETIME | sim |  | **sem `atualizado_em`**: registro imutável |

> **Imutabilidade (RN-010):** aplicação `confirmada` não é editada. Correção cria **nova** aplicação
> com `aplicacao_origem_id` e `motivo_retificacao`, e marca a original como `retificada`/`estornada`.

## credencial_api  (máquinas: ingestão B2B e rede)
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tipo | VARCHAR(20) | sim | IDX | ingestao_b2b / rede_credenciada |
| titular_tipo | VARCHAR(20) | sim |  | cliente_b2b / clinica_credenciada |
| titular_id | BIGINT | sim | IDX |  |
| token_hash | VARCHAR(255) | sim | UNIQUE | armazenar só o hash do token |
| escopo_campanha_id | BIGINT | não | FK/IDX | restringe à campanha (RN-009); NULL = definir por regra |
| ativo | TINYINT(1) | sim | IDX |  |
| expira_em | DATETIME | não |  |  |
| criado_em / revogado_em | DATETIME | — |  |  |

## consentimento_lgpd
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| paciente_id | BIGINT | sim | FK/IDX |  |
| versao_termo | VARCHAR(20) | sim |  |  |
| aceito_em | DATETIME | sim |  | base legal (RN-011) |
| origem | VARCHAR(30) | sim |  | b2c / b2b_em_nome (a definir) |
| ip | VARCHAR(45) | não |  |  |

## log_auditoria
| Campo | Tipo | Obrigatório | Índice | Observação |
|---|---|---|---|---|
| id | BIGINT | sim | PK |  |
| tenant_id | BIGINT | não | IDX | NULL para ações internas globais |
| ator_tipo | VARCHAR(20) | sim |  | usuario / credencial_api |
| ator_id | BIGINT | não |  |  |
| evento | VARCHAR(60) | sim | IDX | ex.: aplicacao.registrada |
| origem | VARCHAR(30) | sim |  | admin / portal / app / api_parceiro |
| entidade_tipo | VARCHAR(40) | não |  |  |
| entidade_id | BIGINT | não |  |  |
| request_id | VARCHAR(40) | não | IDX |  |
| ip | VARCHAR(45) | não |  |  |
| metadata | JSON | não |  | payload mascarado (sem dado sensível cru) |
| data_hora | DATETIME | sim | IDX |  |

---

# 3. Padrões de campos

```txt
criado_em / atualizado_em / excluido_em
criado_por / atualizado_por / excluido_por
status
Datas de evento: aplicado_em, aceito_em, revogado_em, expira_em
```

- Toda tabela de negócio: `tenant_id` (exceto identidade/catálogo global: `paciente`, `vacina`, `clinica_credenciada`).
- Soft delete via `excluido_em` onde faz sentido (cliente, usuário, campanha).
- `aplicacao` **não** tem `atualizado_em` (imutável).

---

# 4. Relacionamentos

| Tabela origem | Campo | Tabela destino | Tipo |
|---|---|---|---|
| usuario | tenant_id | cliente_b2b | N:1 (opcional) |
| campanha | tenant_id | cliente_b2b | N:1 |
| campanha_vacina | campanha_id / vacina_id | campanha / vacina | N:1 / N:1 |
| elegivel | campanha_id / paciente_id | campanha / paciente | N:1 / N:1 |
| elegivel | importacao_id | importacao_elegiveis | N:1 |
| aplicacao | elegivel_id / vacina_id | elegivel / vacina | N:1 |
| aplicacao | aplicacao_origem_id | aplicacao | auto-referência (retificação) |
| credencial_api | titular_id / escopo_campanha_id | cliente_b2b\|clinica / campanha | N:1 |
| consentimento_lgpd | paciente_id | paciente | N:1 |

---

# 5. Status

| Campo | Valores possíveis | Quem altera | Observação |
|---|---|---|---|
| campanha.status | rascunho, ativa, encerrada | operador_interno | só `ativa` aceita aplicação (RN-003) |
| elegivel.status | pendente, aplicado, recusado, inelegivel, ausente | sistema/operador | base da tabela verdade (RN-005) |
| aplicacao.status | confirmada, retificada, estornada | sistema/operador | nunca editar; gerar novo registro (RN-010) |
| cliente_b2b.status | ativo, inativo | super_admin | inativo bloqueia acesso do tenant |
| usuario.status | ativo, bloqueado | admin | bloqueado impede login |
| credencial_api.ativo | 0/1 | operador_interno | revogação por `revogado_em` |

---

# 6. Tabela verdade (VIEW consolidada — RN-005)

A "tabela verdade" da campanha **não é uma tabela física**; é a consolidação de `elegivel` +
`aplicacao`. Recomenda-se uma VIEW:

```sql
CREATE VIEW vw_tabela_verdade AS
SELECT
  e.tenant_id,
  e.campanha_id,
  e.paciente_id,
  p.cpf,
  p.nome,
  e.status                       AS situacao_elegivel,
  COUNT(a.id)                    AS total_aplicacoes,
  MAX(a.aplicado_em)             AS ultima_aplicacao_em
FROM elegivel e
JOIN paciente p ON p.id = e.paciente_id
LEFT JOIN aplicacao a
       ON a.elegivel_id = e.id AND a.status = 'confirmada'
GROUP BY e.tenant_id, e.campanha_id, e.paciente_id, p.cpf, p.nome, e.status;
```

Dashboards e extração (docs 09/11) leem sempre com filtro por `tenant_id` + `campanha_id`.

---

# 7. Migrations (plano)

> Arquivos SQL criados em `database/migrations/` e `database/seeds/`. Ver `database/README.md`.
> **Ainda não executados** contra um MySQL (nenhum ambiente provisionado) — validar em dev.

| Arquivo | Objetivo | Ambiente testado | Backup? | Status |
|---|---|---|---|---|
| 000_criar_controle_migracoes.sql | controle de migrations | — | — | escrito |
| 001_criar_cliente_b2b_usuario.sql | tenants e usuários | — | — | escrito |
| 002_criar_paciente_vacina.sql | identidade e catálogo | — | — | escrito |
| 003_criar_campanha_campanha_vacina.sql | campanhas | — | — | escrito |
| 004_criar_elegivel_importacao.sql | elegibilidade e cargas | — | — | escrito |
| 005_criar_clinica_credencial_api.sql | rede e credenciais | — | — | escrito |
| 006_criar_aplicacao.sql | registro de aplicação | — | — | escrito |
| 007_criar_consentimento_log_auditoria.sql | LGPD e auditoria | — | — | escrito |
| 008_criar_view_tabela_verdade.sql | VIEW consolidada | — | — | escrito |
| seeds/seeds_vacinas_perfis.sql | catálogo + admin inicial | — | — | escrito |

---

# 8. Regras

- Não alterar produção sem backup; toda mudança estrutural via migration versionada.
- Não criar tabela sem chave primária nem campo sem regra.
- **Toda query de negócio filtra por `tenant_id`** no backend (isolamento — RN-006/007).
- Prepared statements sempre (anti-SQLi).
- `aplicacao` é imutável após confirmada (RN-010).
- Dado pessoal/sensível: minimizar colunas, mascarar em log (`metadata`), tratar criptografia/coluna
  sensível na Fase de segurança (doc 10).
- Não misturar padrão português/inglês para o mesmo tipo de campo.

> Pendências que podem alterar a modelagem (dúvidas abertas do doc 02): estrangeiro sem CPF,
> faturamento por dose (novo domínio), auto-elegibilidade B2C (fluxo/origem do consentimento).
