# Integrações, Webhooks e Notificações

## Objetivo

Documentar eventos, webhooks, APIs externas, notificações, filas, retry e idempotência.

---

# 1. Eventos do sistema

| Evento | Origem | Quando ocorre | Próxima ação |
|---|---|---|---|
|  |  |  |  |

---

# 2. Integrações externas

| Sistema | Tipo | Objetivo | Status |
|---|---|---|---|
|  | API/Webhook |  |  |

---

# 3. Webhooks

## Webhook: nome

```txt
Direção: entrada/saída
URL:
Evento:
Autenticação:
Idempotência:
Retry:
```

### Payload

```json
{}
```

---

# 4. Notificações

| Evento | Canal | Mensagem | Frequência | Pode desativar? |
|---|---|---|---|---|
|  | WhatsApp/e-mail/SMS/sistema |  |  | sim/não |

---

# 5. Regras

- Não criar notificação sem evento real.
- Não enviar spam.
- Webhook precisa de log.
- Webhook financeiro precisa de idempotência.
- Falha de integração não pode sumir sem registro.
