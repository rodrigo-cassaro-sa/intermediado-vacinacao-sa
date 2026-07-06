# Briefing e Regras de Negócio

## Objetivo

Registrar as regras oficiais do projeto para evitar decisões soltas.

---

# 1. Problema que o sistema resolve

```txt
A empresa presta imunização corporativa e hoje não tem uma forma única, transparente e
segura de receber as listas de colaboradores elegíveis das empresas contratantes,
executar a vacinação por duas modalidades diferentes e devolver ao cliente os dados
com qualidade analítica.

O sistema centraliza esse fluxo como um intermediador: recebe elegíveis, coordena a
execução (rede credenciada ou in company), registra cada aplicação de forma rastreável
e entrega dashboards + tabela verdade da campanha, tudo em conformidade com a LGPD.
```

---

# 2. Processo atual

```txt
- A empresa contratante (RH) informa quem são os colaboradores elegíveis (hoje, provavelmente
  por planilha/e-mail - a confirmar).
- A prestadora organiza a vacinação em duas modalidades:
    a) Rede credenciada: clínicas conveniadas vacinam os colaboradores.
    b) In company: a prestadora contrata profissionais de saúde e leva vacinas/insumos
       até o local de trabalho do cliente.
- O registro de quem foi vacinado e o retorno analítico ao cliente são fragmentados,
  sem uma fonte única da verdade.

Problemas: falta de centralização, retrabalho, risco de erro no controle de doses,
dificuldade de auditoria e de devolver dados confiáveis ao cliente.
```

---

# 3. Processo desejado

```txt
1. A prestadora cria uma CAMPANHA para um cliente B2B, definindo modalidade
   (rede credenciada / in company), vacinas oferecidas e período.
2. O cliente B2B envia a lista de ELEGÍVEIS por interface (upload) ou por API.
3. Opcionalmente, o PACIENTE B2C cria conta, confirma seus dados e se auto-elege
   (ou consulta a carteira do que já tomou).
4. A execução ocorre:
   - Rede credenciada: a clínica consulta elegíveis por API e registra o vacinado.
   - In company: o profissional de saúde consulta e cadastra o vacinado pelo app/PWA.
5. Cada APLICAÇÃO é registrada de forma rastreável (paciente, vacina, dose, lote,
   quem aplicou, onde, quando).
6. A plataforma consolida a TABELA VERDADE da campanha (elegível x aplicado x pendente
   x recusado) e disponibiliza DASHBOARDS e extração de dados ao cliente B2B.
```

---

# 4. Regras de negócio

| Código | Regra | Exemplo | Impacto | Status |
|---|---|---|---|---|
| RN-001 | Toda campanha pertence a **um** cliente B2B e tem **uma** modalidade (rede credenciada ou in company) | Campanha "Gripe 2026 - Empresa X - In company" | Isola dados por cliente; define fluxo de execução | rascunho |
| RN-002 | Elegibilidade vem do cliente B2B (lista) e/ou da auto-elegibilidade do paciente B2C, sempre **vinculada a uma campanha** | Colaborador só é elegível dentro da campanha da sua empresa | Evita vacinação fora de contrato | rascunho |
| RN-003 | Só pode registrar aplicação para paciente **elegível** e **dentro do período/vacinas** da campanha | Não registrar vacina não prevista na campanha | Integridade e faturamento correto | rascunho |
| RN-004 | Cada aplicação exige dados mínimos rastreáveis: paciente, vacina, dose, **lote**, profissional/clínica, local, data/hora | Registro por app profissional ou por API da clínica | Rastreabilidade sanitária e auditoria | rascunho |
| RN-005 | A **tabela verdade** da campanha classifica cada elegível em: pendente, aplicado, recusado, inelegível, ausente | Base de todo dashboard e extração | Fonte única da verdade | rascunho |
| RN-006 | Isolamento **multi-tenant**: cliente B2B só vê os dados da(s) sua(s) campanha(s) | Empresa X nunca vê elegíveis da Empresa Y | LGPD e confidencialidade | crítica |
| RN-007 | Paciente B2C só vê os **próprios** dados/carteira | Colaborador não vê colegas | LGPD | crítica |
| RN-008 | Identidade única do paciente por **CPF** (decidido em 2026-07-06) para consolidar carteira entre campanhas | Mesmo CPF em campanhas diferentes = mesma carteira | Consolidação e deduplicação | aprovada |
| RN-009 | Rede credenciada e app profissional só operam via **credencial/escopo** vinculada à campanha permitida | Clínica A não registra em campanha da Clínica B | Segurança de API | crítica |
| RN-010 | Registro de aplicação é **imutável após confirmação**; correção gera novo registro/estorno auditado, nunca edição silenciosa | Erro de lote vira retificação auditada | Auditoria sanitária | rascunho |
| RN-011 | Consentimento LGPD do paciente é pré-requisito para tratar dado de saúde do B2C | Aceite registrado com data/hora e versão do termo | Base legal | crítica |

---

# 5. Regras críticas

Regras que não podem ser quebradas:

- **RN-006 / RN-007** — isolamento de dados entre clientes B2B e entre pacientes (LGPD).
- **RN-009** — API externa (rede e app) sempre com escopo restrito à campanha autorizada.
- **RN-011** — sem base legal/consentimento não se trata dado de saúde do paciente B2C.
- Toda validação de elegibilidade, período, vacina e permissão é feita **no backend**.
- Dado sensível de saúde nunca trafega/armazena sem criptografia e sem log de auditoria de acesso.

---

# 6. Exceções

| Regra | Exceção | Quem pode autorizar |
|---|---|---|
| RN-003 (aplicação só para elegível) | Vacinação de "elegível tardio" (colaborador que apareceu no dia, in company) | Operador interno / regra da campanha (a confirmar) |
| RN-010 (registro imutável) | Retificação por erro de digitação de lote/dose | Operador interno com registro auditado |
| RN-008 (CPF como chave) | Paciente estrangeiro sem CPF | Identificador alternativo (passaporte) — a definir |

---

# 7. Dúvidas abertas

| Dúvida | Impacto | Precisa decisão de quem? | Status |
|---|---|---|---|
| ~~Chave de identidade do paciente é CPF?~~ **DECIDIDO: CPF.** Falta definir tratamento de estrangeiro sem CPF | Modelagem de banco e deduplicação de carteira | Negócio | resolvida (fallback estrangeiro em aberto) |
| A auto-elegibilidade do B2C precisa de aprovação do cliente B2B ou é automática se o CPF constar na lista? | Fluxo de elegibilidade e permissões | Negócio | aberta |
| O app in company precisa funcionar **offline** (PWA offline-first)? | Arquitetura frontend e sincronização | Negócio/Técnico | aberta |
| Há **faturamento/cobrança** por dose aplicada na plataforma, ou só gestão/analytics? | Escopo do MVP e módulo de pagamentos | Negócio | aberta |
| ~~A ingestão de elegíveis por API é obrigatória no MVP?~~ **DECIDIDO: upload + API já no MVP.** | Escopo do MVP | Negócio | resolvida |
| Quais vacinas/esquemas (doses múltiplas, reforços) o MVP precisa suportar? | Modelagem de vacina/dose | Negócio | aberta |
| Consentimento LGPD: coletado pelo B2C no app ou pelo cliente B2B em nome do colaborador? | Base legal e fluxo | Jurídico/Negócio | aberta |

---

# 8. Critérios de aceite

```md
- [ ] Regra principal documentada.
- [ ] Exceções documentadas.
- [ ] Dúvidas abertas registradas.
- [ ] Regras críticas validadas.
```

> Observação: as regras estão em status **rascunho/crítica** e precisam de aprovação do responsável de negócio para passarem a "aprovada" antes da modelagem de banco (Fase seguinte).
