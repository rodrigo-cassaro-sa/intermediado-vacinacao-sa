# MVP, Versões e Roadmap

## Objetivo

Separar o que será feito agora do que ficará para versões futuras.

> **Decisões de 2026-07-06 (usuário):** MVP com **as duas modalidades** (in company + rede
> credenciada), ingestão por **upload + API** e **CPF** como identidade do paciente.
>
> ⚠️ **Alerta do orquestrador:** essas escolhas transformam o MVP no produto quase completo.
> O risco de prazo/complexidade sobe de "médio" para **alto** e a superfície de segurança
> (API pública para terceiros + dado sensível) precisa ser tratada já na V1. Recomendo, dentro
> desse MVP, uma **ordem de construção incremental** (ver seção 3) para não travar tudo de uma vez.

---

# 1. MVP

O MVP deve conter apenas o mínimo necessário para o sistema funcionar com valor real.

| Item | Descrição | Obrigatório? | Observação |
|---|---|---|---|
| Admin interno + multi-tenant | Login do operador, cadastro de cliente B2B, isolamento de dados por cliente | sim | Base de tudo (RN-006) |
| Cadastro de campanha | Criar campanha com cliente, modalidade, vacinas e período | sim | RN-001 |
| Ingestão de elegíveis (upload) | Importar lista de elegíveis por planilha/CSV pela interface | sim | — |
| Ingestão de elegíveis (API) | Endpoint para o cliente B2B enviar elegíveis via API | sim | **Decidido no MVP**; exige credencial/contrato e validação de payload |
| Identidade por CPF | CPF como chave única do paciente + deduplicação/consolidação de carteira | sim | RN-008 aprovada |
| Registro de aplicação | Registrar vacinado com dados rastreáveis (RN-004) | sim | Nas **duas** modalidades |
| App/PWA profissional in company | Tela de consulta de elegíveis + cadastro de vacinado | sim | Offline-first fica p/ versão futura |
| API rede credenciada | Endpoint para clínica consultar elegíveis e registrar vacinado (escopo por campanha) | sim | **Decidido no MVP**; RN-009 |
| Tabela verdade da campanha | Consolidação elegível x aplicado x pendente x recusado | sim | RN-005, coração do produto |
| Dashboard do cliente B2B | Painel com números da campanha + extração (CSV) | sim | Entrega o "retorno analítico" |
| Segurança/LGPD base | Autenticação, permissões no backend, log de auditoria de acesso a dado sensível | sim | RN-006/007/011 |
| Consentimento LGPD | Registro de aceite quando houver dado do paciente | sim | Base legal |

---

# 2. Fora do MVP

| Item | Motivo para ficar fora | Versão futura |
|---|---|---|
| Portal B2C completo (auto-elegibilidade + carteira consolidada) | Depende de definir fluxo de auto-elegibilidade e consentimento (dúvidas abertas) | V2 |
| PWA offline-first | Complexidade de sincronização; validar necessidade real | V2/V3 |
| Webhooks e notificações (WhatsApp/e-mail) | Não é essencial para o ciclo mínimo | V3 |
| Faturamento/cobrança por dose | Só se for escopo de negócio (dúvida aberta) | V3 |
| Gamificação/retenção do paciente | Engajamento, não essencial | V3 |

---

# 3. Versões

## Versão 1 (MVP) — decidido: escopo ampliado

```txt
Objetivo: Ciclo completo do intermediador nas DUAS modalidades, com ingestão por upload e API.
Escopo: Admin + multi-tenant, campanha, elegíveis por upload E por API, identidade por CPF,
        registro de aplicação em in company (app/PWA) E rede credenciada (API com escopo),
        tabela verdade, dashboard + extração p/ cliente B2B, segurança/LGPD base.

Ordem de construção incremental recomendada DENTRO do MVP (para reduzir risco):
  1. Núcleo: admin + multi-tenant + campanha + elegíveis por upload + CPF.
  2. Registro de aplicação in company (app/PWA) + tabela verdade + dashboard/extração.
  3. API de rede credenciada (consulta elegíveis + registrar vacinado, escopo por campanha).
  4. API de ingestão de elegíveis pelo cliente B2B.
  5. Endurecimento de segurança das APIs públicas (credenciais, escopo, rate limit, auditoria).

Data prevista: [a definir]
```

## Versão 2

```txt
Objetivo: Autoatendimento do paciente e refinamentos.
Escopo: Portal B2C (auto-elegibilidade + carteira consolidada por CPF), refinos de dashboard/BI.
Data prevista: [a definir]
```

## Versão 3

```txt
Objetivo: Escala, engajamento e automação.
Escopo: PWA offline-first, webhooks/notificações, faturamento por dose (se aplicável),
        gamificação/retenção, BI avançado.
Data prevista: [a definir]
```

---

# 4. Prioridades

| Prioridade | Item | Motivo |
|---|---|---|
| alta | Multi-tenant + segurança/LGPD | Sem isolamento não há produto viável (dado sensível) |
| alta | Campanha + elegíveis + registro + tabela verdade | É o núcleo de valor |
| alta | Dashboard/extração para o cliente B2B | É o "retorno analítico" prometido |
| alta | APIs de integração (rede + ingestão) com segurança | Agora no MVP por decisão do usuário; risco de segurança |
| baixa | B2C completo, PWA offline, webhooks, gamificação | Valor incremental |

---

# 5. Regra final

Nada deve entrar no MVP sem necessidade real.

> **Decisão registrada (2026-07-06):** MVP com as duas modalidades, upload + API e CPF.
> A ordem de construção incremental da seção 3 é a mitigação recomendada para o risco de
> entregar tudo de uma vez. Dúvidas ainda abertas que impactam o MVP: necessidade de
> offline-first no app in company e existência de faturamento por dose.
