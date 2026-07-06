# Protocolo — Notificações e Integrações

## Objetivo

Define como criar notificações, webhooks, integrações, filas, retry e comunicação com sistemas externos sem spam e sem duplicidade.

Este protocolo deve ser usado pelo `orquestrador.md` quando a tarefa exigir uma sequência de trabalho maior do que uma skill isolada.

Ele segue a arquitetura:

```txt
Prompt do usuário
  ↓
Orquestrador
  ↓
Protocolo
  ↓
Especialistas
  ↓
Skills
  ↓
Execução
  ↓
Evidência / Checklist / Documentação
```

---

## Quando usar

Use este protocolo quando houver:

- notificação
- e-mail
- WhatsApp
- SMS
- webhook
- API externa
- n8n
- fila
- retry
- idempotência
- evento de sistema

---

## Quando não usar

Não use este protocolo quando:

- mensagem manual fora do sistema
- tela sem comunicação externa
- gamificação sem notificação
- integração que ainda não tem evento real

---

## Especialista principal

```txt
especialista-engajamento-integracoes.md
```

## Especialistas de apoio

```txt
especialista-backend-api-php.md
especialista-seguranca-auditoria.md
especialista-banco-dados.md
especialista-qualidade-manutencao.md
especialista-negocio-saas.md
```

---

## Skills principais

```txt
skill-retencao.md
skill-gamificacao.md
skill-notificacoes.md
skill-integracoes-webhooks.md
skill-logs-auditoria.md
skill-lgpd-privacidade.md
skill-api-rest.md
skill-qa.md
```

## Skills de apoio

```txt
skill-backend.md
skill-erros-excecoes.md
skill-mysql.md
skill-performance.md
```

---

## Fluxo obrigatório

1. Definir evento real que dispara a ação.
2. Definir canal e motivo da comunicação.
3. Definir payload e contrato.
4. Definir preferências/consentimento quando aplicável.
5. Criar registro do evento.
6. Implementar fila/retry quando necessário.
7. Implementar idempotência.
8. Registrar logs.
9. Tratar erro de integração.
10. Testar sucesso, falha, duplicidade e reprocessamento.
11. Documentar integração.

---

## Regras obrigatórias

- não criar notificação sem evento real
- não criar spam
- não enviar dado sensível sem necessidade
- webhook precisa de log e idempotência
- falha de integração não pode sumir silenciosamente

---

## Entregáveis esperados

- mapa de eventos
- contrato de payload
- tabela/log de integração
- fila/retry
- idempotência
- templates de mensagem
- testes
- documentação

---

## Checklist de validação

```md
- [ ] Evento real definido
- [ ] Canal justificado
- [ ] Payload documentado
- [ ] Idempotência implementada
- [ ] Retry definido quando necessário
- [ ] Logs ativos
- [ ] LGPD considerada
- [ ] Testes de falha feitos
```

---

## Formato de resposta recomendado

```md
# Protocolo — Notificações e Integrações

## 1. Entendimento do pedido

## 2. Especialistas convocados

## 3. Skills aplicadas

## 4. Fluxo executado

## 5. Decisões tomadas

## 6. Riscos e cuidados

## 7. Entregáveis

## 8. Evidências

## 9. Checklist final
```



---

## Regra final

Este protocolo não substitui as skills.

Ele organiza a ordem correta de uso dos especialistas e das skills para reduzir erro, retrabalho, improviso e decisão técnica solta.
