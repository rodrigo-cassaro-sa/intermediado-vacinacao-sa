# Perfis e Permissões

## Objetivo

Definir quem pode acessar cada área e executar cada ação.

---

# 1. Perfis

| Perfil | Descrição | Observação |
|---|---|---|
| super_admin (interno) | Equipe da prestadora; gere tudo na plataforma | Acesso global; ações críticas auditadas |
| operador_interno | Equipe operacional da prestadora | Gere campanhas, elegíveis, execução; sem config de sistema |
| cliente_b2b | RH/gestor da empresa contratante | Vê **apenas** suas campanhas; envia elegíveis; consulta dashboards/extração |
| profissional_saude | Profissional in company (app/PWA) | Consulta elegíveis e registra aplicação **na campanha atribuída** |
| clinica_credenciada | Sistema externo da rede (via API) | Consulta elegíveis e registra vacinado **por API**, escopo restrito à campanha |
| paciente_b2c | Colaborador/pessoa | Vê **apenas os próprios** dados; auto-elegibilidade e carteira (V2) |

> Multi-tenant: `cliente_b2b`, `profissional_saude` e `clinica_credenciada` são sempre
> vinculados a um tenant/campanha. `paciente_b2c` é vinculado ao próprio CPF (premissa RN-008).

---

# 2. Matriz de permissões

| Tela/Ação | super_admin | operador_interno | cliente_b2b | profissional_saude | clinica_credenciada | paciente_b2c |
|---|---|---|---|---|---|---|
| Configurar sistema/ambiente | sim | não | não | não | não | não |
| Cadastrar cliente B2B | sim | sim | não | não | não | não |
| Criar/editar campanha | sim | sim | não | não | não | não |
| Enviar/importar elegíveis | sim | sim | sim (da própria campanha) | não | não | não |
| Consultar elegíveis | sim | sim | sim (própria campanha) | sim (campanha atribuída) | sim (via API, escopo) | próprio (V2) |
| Registrar aplicação (vacinado) | sim | sim | não | sim (campanha atribuída) | sim (via API, escopo) | não |
| Retificar aplicação | sim | sim (auditado) | não | não | não | não |
| Ver tabela verdade | sim | sim | sim (própria campanha) | parcial (própria atuação) | não | não |
| Ver dashboard/extração | sim | sim | sim (própria campanha) | não | não | não |
| Ver carteira do paciente | sim (auditado) | parcial (auditado) | não | sim (paciente em atendimento) | via API (escopo) | próprio |
| Gerir credenciais de API (rede) | sim | sim | não | não | não | não |
| Ver logs de auditoria | sim | parcial | não | não | não | não |

> Célula "parcial"/"escopo" = acesso limitado por vínculo (campanha, tenant, paciente em atendimento) e sujeito a auditoria. Detalhamento fino na Fase de segurança (doc 10).

---

# 3. Regras obrigatórias

- Permissão visual no frontend **não é** segurança real.
- Toda permissão crítica deve ser validada **no backend** (RN-006, RN-007, RN-009).
- Ações administrativas e acesso a dado de saúde devem ser registrados em **log/auditoria** (RN-004, RN-011).
- Usuário/sistema não pode acessar dados de outro tenant/campanha sem autorização (isolamento multi-tenant).
- `clinica_credenciada` e `profissional_saude` operam sempre com **escopo restrito à campanha** autorizada.
- Paciente B2C só acessa os **próprios** dados.

---

# 4. Endpoints protegidos

> Contrato detalhado na Fase de API (doc 09). Lista inicial esperada:

| Endpoint | Permissão necessária | Log? |
|---|---|---|
| POST /elegiveis/importar | cliente_b2b (própria campanha) / operador_interno | sim |
| GET /campanhas/{id}/elegiveis | escopo da campanha (cliente_b2b, profissional_saude, clinica_credenciada, operador) | sim |
| POST /aplicacoes | profissional_saude / clinica_credenciada / operador (campanha autorizada) | sim (crítico) |
| POST /aplicacoes/{id}/retificar | operador_interno / super_admin | sim (crítico) |
| GET /campanhas/{id}/tabela-verdade | cliente_b2b (própria) / operador | sim |
| GET /campanhas/{id}/dashboard | cliente_b2b (própria) / operador | sim |
| GET /pacientes/me/carteira | paciente_b2c (próprio) | sim |

---

# 4.1 PORTAL — hierarquia de usuários e escopos (novo)

O portal introduz **níveis hierárquicos com escopo**, substituindo o `perfil` plano por
**atribuições** (um usuário pode ter várias). Regra central: **quem está acima gere quem está abaixo**,
dentro do seu escopo.

## Níveis (do maior para o menor alcance)

| Nível | Escopo | Alcance | Exemplo |
|---|---|---|---|
| `gestao_interna` | global (nós) | todos os clientes/grupos | equipe da prestadora |
| `grupo` | 1 grupo empresarial (carteira de clientes) | todos os clientes do grupo | holding, franqueadora, corretora |
| `negocio` | 1 cliente (empresa) | tudo da empresa (todas as unidades) | RH/gestor de saúde da empresa |
| `local` | 1 unidade/lotação/planta | só a unidade onde ocorre a vacinação | responsável da planta |

## Modelo de dados (planejado)

```txt
grupo_empresarial (id, nome)                         -- carteira de clientes
cliente_b2b.grupo_empresarial_id (nullable)          -- cliente pertence a um grupo
unidade (id, cliente_b2b_id, nome, codigo_lotacao, cidade, uf)  -- local de vacinação/lotação
elegivel.unidade_id (nullable)                       -- elegível vinculado a uma unidade (além do codigo_lotacao)

usuario_atribuicao (id, usuario_id, nivel, escopo_tipo, escopo_id, criado_por, criado_em)
  nivel: gestao_interna | grupo | negocio | local
  escopo_tipo: NULL(interna) | grupo_empresarial | cliente_b2b | unidade
  escopo_id: id do escopo
  -- UNIQUE(usuario_id, nivel, escopo_tipo, escopo_id) — multi-atribuição
```

## Resolução de permissão (por requisição)

Para um recurso (campanha do cliente T, unidade U), o acesso é concedido se **alguma atribuição**
do usuário cobrir o recurso:

```txt
gestao_interna         → cobre tudo
grupo (escopo G)       → cobre clientes onde cliente_b2b.grupo_empresarial_id = G (e suas unidades)
negocio (escopo T)     → cobre o cliente T e todas as unidades de T
local (escopo U)       → cobre apenas a unidade U (elegíveis/vacinados daquela lotação)
```

## Gestão de usuários (upper manages lower)

- Um usuário só pode **criar/atribuir** usuários em nível **igual ou inferior** e **dentro do seu escopo**.
- Ex.: nível `negocio` (cliente T) cria usuários `negocio` (T) e `local` (unidades de T); não cria `grupo`.
- Toda atribuição/revogação é auditada.

## Compatibilidade com o modelo atual

- `super_admin`/`operador_interno` → `gestao_interna`.
- `cliente_b2b` (com `tenant_id`) → `negocio` (escopo = cliente).
- `profissional_saude`/`clinica_credenciada`/token de app → operação (não gestão) — permanecem.
- Migração: criar `usuario_atribuicao` a partir do `perfil`/`tenant_id` atuais.

## Onboarding e LGPD do usuário do portal

- **Consentimento/aceite de termos** no 1º acesso (registrar `aceito_em`, `versao_termo`).
- **Onboarding/assistente de primeiro uso** (flag `onboarding_em`) — guia até o 1º dashboard.

---

# 5. Checklist

```md
- [ ] Perfis definidos.
- [ ] Permissões por tela definidas.
- [ ] Permissões por ação definidas.
- [ ] Backend valida permissão.
- [ ] Frontend apenas orienta visualmente.
- [ ] Logs de ações críticas definidos.
```

> Status: rascunho para aprovação de negócio/segurança. Depende das dúvidas abertas do doc 02
> (chave de identidade do paciente e fluxo de auto-elegibilidade).
