# Mapa de Telas e Fluxos

## Objetivo

Registrar todas as telas, seus objetivos, dados, ações e permissões.

> Fase 2 do `protocolo-criacao-projeto-zero.md`. Perfis conforme `docs/04-perfis-permissoes.md`.
> Frentes de UI: **admin** (interno), **portal** (cliente B2B), **app/PWA** (profissional in company).
> Rede credenciada é majoritariamente **API** (telas mínimas). Portal B2C é **V2**.

---

# 1. Lista de telas

| Código | Tela | Objetivo | Perfil que acessa | Status |
|---|---|---|---|---|
| TL-001 | Login (interno) | Autenticar operador/admin | super_admin, operador_interno | rascunho |
| TL-002 | Dashboard geral | Visão consolidada de clientes/campanhas | super_admin, operador_interno | rascunho |
| TL-003 | Clientes B2B | CRUD de empresas contratantes | super_admin, operador_interno | rascunho |
| TL-004 | Campanhas | CRUD de campanha (cliente, modalidade, vacinas, período) | super_admin, operador_interno | rascunho |
| TL-005 | Rede credenciada | Cadastrar clínicas e gerar credenciais de API | super_admin, operador_interno | rascunho |
| TL-006 | Elegíveis da campanha | Importar (upload) e visualizar elegíveis | operador_interno, cliente_b2b (própria) | rascunho |
| TL-007 | Acompanhamento de aplicações | Ver/retificar registros de aplicação | operador_interno, super_admin | rascunho |
| TL-008 | Tabela verdade da campanha | Elegível x aplicado x pendente x recusado | operador_interno, cliente_b2b (própria) | rascunho |
| TL-009 | Logs/auditoria | Consultar acessos e ações críticas | super_admin, operador_interno (parcial) | rascunho |
| TL-101 | Login (portal B2B) | Autenticar gestor do cliente | cliente_b2b | rascunho |
| TL-102 | Dashboard da campanha (B2B) | Números e evolução da campanha | cliente_b2b | rascunho |
| TL-103 | Enviar elegíveis (B2B) | Upload de planilha/CSV | cliente_b2b | rascunho |
| TL-104 | Credenciais de API (B2B) | Gerir token de ingestão por API | cliente_b2b | rascunho |
| TL-105 | Extração/relatórios (B2B) | Exportar resultados (CSV) | cliente_b2b | rascunho |
| TL-201 | Login (app in company) | Autenticar profissional | profissional_saude | rascunho |
| TL-202 | Selecionar campanha (app) | Escolher campanha atribuída do dia | profissional_saude | rascunho |
| TL-203 | Buscar elegível (app) | Localizar paciente por CPF/nome | profissional_saude | rascunho |
| TL-204 | Registrar aplicação (app) | Cadastrar vacinado (vacina, dose, lote) | profissional_saude | rascunho |
| TL-205 | Minhas aplicações do dia (app) | Conferir/retomar registros | profissional_saude | rascunho |
| TL-301 | Portal B2C (cadastro/carteira) | Auto-elegibilidade e carteira | paciente_b2c | **V2** |

---

# 2. Detalhe de telas-chave

## TL-004 — Campanhas

```txt
Objetivo: Criar/editar campanha (base de tudo — RN-001).
Perfis: super_admin, operador_interno
URL/rota: /admin/campanhas
Status: rascunho
```

### Dados exibidos
| Dado | Origem | Observação |
|---|---|---|
| Cliente B2B, modalidade, vacinas, período | banco (campanhas) | modalidade define fluxo de execução |
| Status da campanha | banco | rascunho/ativa/encerrada |

### Ações
| Ação | Endpoint | Permissão | Log? |
|---|---|---|---|
| Criar campanha | POST /api/v1/interno/campanhas | operador_interno | sim |
| Editar campanha | PUT /api/v1/interno/campanhas/{id} | operador_interno | sim |

### Estados da tela
```md
- [ ] Carregando.  - [ ] Vazio.  - [ ] Erro.  - [ ] Sucesso.  - [ ] Sem permissão.
```

---

## TL-006 / TL-103 — Importar elegíveis

```txt
Objetivo: Carregar elegíveis por upload (RN-002); paralelo à ingestão por API (parceiro).
Perfis: operador_interno (TL-006) / cliente_b2b (TL-103)
URL/rota: /admin/campanhas/{id}/elegiveis  |  /portal/campanhas/{id}/elegiveis
Status: rascunho
```

### Dados exibidos
| Dado | Origem | Observação |
|---|---|---|
| Prévia do arquivo, erros de validação, total importado | upload + parser | valida CPF, deduplica (RN-008) |

### Ações
| Ação | Endpoint | Permissão | Log? |
|---|---|---|---|
| Enviar arquivo | POST /api/v1/interno/campanhas/{id}/elegiveis/importar | cliente_b2b (própria) / operador | sim |
| Ingerir via API (paralelo) | POST /api/v1/parceiro/campanhas/{id}/elegiveis | token B2B + escopo | sim |

### Estados da tela
```md
- [ ] Carregando.  - [ ] Vazio.  - [ ] Erro (linhas inválidas).  - [ ] Sucesso (resumo).  - [ ] Sem permissão.
```

---

## TL-204 — Registrar aplicação (app in company)

```txt
Objetivo: Registrar vacinado de forma rastreável (RN-004, RN-010).
Perfis: profissional_saude
URL/rota: /app/aplicacao
Status: rascunho
```

### Dados exibidos
| Dado | Origem | Observação |
|---|---|---|
| Paciente (elegível), vacina, dose, lote, data/hora, local | campanha + entrada do profissional | só elegível e dentro do período/vacinas (RN-003) |

### Ações
| Ação | Endpoint | Permissão | Log? |
|---|---|---|---|
| Registrar aplicação | POST /api/v1/interno/aplicacoes | profissional_saude (campanha atribuída) | sim (crítico) |

### Estados da tela
```md
- [ ] Carregando.  - [ ] Vazio.  - [ ] Erro.  - [ ] Sucesso.  - [ ] Sem permissão.  - [ ] Offline (a confirmar - dúvida aberta).
```

> A rede credenciada faz o equivalente a TL-204 via `POST /api/v1/parceiro/aplicacoes` (sem tela nossa).

---

# 3. Fluxos principais

```txt
Fluxo A — Criação e carga da campanha:
  Operador cria campanha (TL-004) → cliente B2B envia elegíveis por upload (TL-103)
  e/ou por API (ingestão parceiro) → elegíveis validados e deduplicados por CPF.

Fluxo B — Execução in company:
  Profissional seleciona campanha (TL-202) → busca elegível por CPF (TL-203)
  → registra aplicação (TL-204) → registro entra na tabela verdade.

Fluxo C — Execução rede credenciada (API):
  Clínica autentica por credencial → consulta elegíveis (GET parceiro, escopo campanha)
  → registra vacinado (POST parceiro/aplicacoes) → registro entra na tabela verdade.

Fluxo D — Retorno analítico ao cliente:
  Cliente B2B acompanha dashboard (TL-102) e tabela verdade (TL-008)
  → exporta resultados (TL-105).

Fluxo E (V2) — Paciente B2C:
  Paciente cria conta por CPF → se auto-elege (regra a definir) → consulta carteira.
```

---

# 3.1 PORTAL do cliente/gestor — jornada e telas (novo)

Foco: **autoatendimento** (o gestor de saúde faz sozinho, sem suporte) + um **modo avançado**
para o técnico (integrações/Power BI). Hierarquia e escopos: ver doc 04 §4.1.

## Jornada (primeiro acesso → uso recorrente)

```txt
cadastro/login
  → consentimento LGPD (aceite de termos, versão registrada)
  → onboarding de utilização (o que é o produto, como funciona)
  → assistente de primeiro uso (passo a passo: criar/entrar na campanha, importar elegíveis)
  → primeiro uso do dashboard (estado guiado, dicas)
Uso recorrente:
  dashboard → atualizar elegíveis (adicionar/remover/sincronizar) → extrair vacinados
```

## Telas do portal

| Código | Tela | Nível mínimo | Função |
|---|---|---|---|
| PT-001 | Login / cadastro | — | autenticação do gestor · **D1 ✅** (`public/portal/`) |
| PT-002 | Consentimento LGPD | qualquer | aceite de termos (bloqueia até aceitar) · **D1 ✅** (usuario.consentimento_em/versao_termo) |
| PT-003 | Onboarding + assistente 1º uso | qualquer | guia inicial (3 passos) · **D1 ✅** (usuario.onboarding_em) |
| PT-010 | Dashboard da(s) campanha(s) | local+ | cobertura, pendentes, aplicados; visão por escopo · **D2b ✅** |
| PT-020 | Elegíveis — importar/sincronizar | negocio/local | upload CSV/colar, sync (add/remove), relatório de erros · **D2b ✅** |
| PT-021 | Elegíveis — lista/remover | negocio/local | ver/remover (respeitando escopo) · **D2b ✅** |
| PT-030 | Vacinados — extrair | negocio+ | tabela verdade + export CSV · **D2b ✅** |
| PT-040 | Gestão de usuários | negocio+ (upper→lower) | criar/listar usuários no escopo · **D2b ✅** |
| PT-050 | **Painel de integrações (avançado)** | negocio+ (técnico) | doc de API (contrato v1), **gerar tokens** (consulta/ingestão/app), **registrar webhooks** e ver entregas, guia Power BI/automação |

## Dois modos

- **Simples (gestor de saúde):** fluxos guiados PT-010/020/030. Linguagem de negócio, sem jargão.
- **Avançado (técnico/integrações):** PT-050 — conectar **Power BI** (via token `consulta` + API/OData
  ou export), **automatizar** com webhooks/tokens, ler a **documentação** da API.

## Escopo aplicado nas telas

Toda tela filtra pelo escopo das atribuições do usuário (doc 04 §4.1): `grupo` vê seus clientes;
`negocio` vê a empresa; `local` vê só a unidade/lotação. Faturamento/preços: apenas `gestao_interna`.

---

# 4. Regras

- Tela não cria regra de negócio sozinha — validação em `app/services` (backend).
- Toda ação crítica (importar elegíveis, registrar/retificar aplicação) exige backend + log.
- Toda tela com API trata loading e erro (estados obrigatórios).
- Perfil `cliente_b2b` só vê a própria campanha; `profissional_saude` só a campanha atribuída (isolamento multi-tenant, RN-006/007/009).
- App in company: estado **offline** marcado como condicional até a decisão da dúvida aberta (PWA offline-first).
