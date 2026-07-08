# Integrações, Webhooks e Notificações

## Objetivo

Documentar eventos, webhooks, APIs externas, notificações, filas, retry e idempotência.

> Estado (2026-07): APIs de **entrada** implementadas (parceiro/rede, ingestão B2B, app interno).
> **Webhooks de saída IMPLEMENTADOS (Fase A):** disparo automático via auditoria (whitelist
> WEBHOOK_EVENTOS), fila `webhook_entrega`, worker `scripts/processar_webhooks.php` (cron),
> assinatura **HMAC-SHA256** (header `X-Assinatura`), retry com backoff e status `dead`.
> Endpoints do painel: `POST/GET /interno/webhooks`, `/webhooks/{id}/desativar|entregas|testar`.
>
> **A3 IMPLEMENTADO:** token tipo `consulta` (escopo = cliente/tenant) + API externa de leitura
> (`GET /parceiro/carteira/{cpf}`, `GET /parceiro/campanhas/{id}/tabela-verdade`), com contrato
> público v1 no doc 09 §3.9. Fundação de integração (A) concluída.

---

# 1. Eventos do sistema (fonte dos webhooks)

Já registrados em `log_auditoria` e nos históricos — reaproveitar como gatilho de webhook.

| Evento | Origem | Quando ocorre | Próxima ação (webhook) |
|---|---|---|---|
| `aplicacao.registrada` | app/api | vacinado criado | notificar carteira externa, RH, cliente |
| `aplicacao.estornada` | admin | "desvacinar" | notificar sistemas que receberam o vacinado |
| `elegivel.criado` | ingestão | novo elegível | notificar sistemas de consulta |
| `elegivel.situacao_alterada` | admin/clínica | recusa/ausência/removido | atualizar RH |
| `importacao.concluida` | worker | lote processado | avisar cliente (com nº de válidos/erros) |
| `campanha.encerrada` | admin | fim da campanha | fechar faturamento/consolidar |

---

# 2. Integrações externas — mapa e status

| Sistema | Tipo | Objetivo | Status |
|---|---|---|---|
| App/sistema da **clínica** (rede) | API entrada (Bearer + escopo) | consultar elegível, registrar vacinado | ✅ feito |
| App/sistema **in company** | API entrada (sessão hoje; token a fazer) | registrar vacinado | ⚠️ parcial (auth por sessão; falta token de app e PWA offline) |
| **RH / API REST** | API entrada + sync | enviar elegíveis, controlar turnover | ⚠️ parcial (ingestão ok; falta sync por diferença) |
| **Sistema de carteira** | API saída + webhook | receber doses e consultar carteira | ⚠️ parcial (carteira interna; falta API externa/webhook) |
| **Autoadesão (B2C)** | API entrada | paciente se auto-elege | ❌ V2 (consentimento LGPD) |
| **Venda de voucher** | API entrada + pagamento | resgatar voucher → elegibilidade | ❌ não feito (pagamento) |

---

# 3. Webhooks (saída) — desenho alvo

```txt
Direção: saída
Assinatura de webhook (por endpoint): segredo → HMAC-SHA256 no header X-Assinatura
Idempotência: cada entrega tem um id único; o receptor deve deduplicar por ele
Retry: backoff exponencial (ex.: 1m, 5m, 30m, 2h, 6h), com dead-letter após N falhas
Log: toda tentativa registrada (status_code, tentativa, próxima_tentativa_em)
```

### Modelo de dados (planejado)
```txt
webhook_assinatura (id, tenant_id?, evento, url, segredo, ativo, criado_em)
webhook_entrega    (id, assinatura_id, evento, payload JSON, status, tentativas,
                    proxima_tentativa_em, ultimo_status_code, criado_em, entregue_em)
```

### Payload (exemplo `aplicacao.registrada`)
```json
{
  "id_entrega": "whd_abc123",
  "evento": "aplicacao.registrada",
  "ocorrido_em": "2026-07-16T09:00:00-03:00",
  "dados": {
    "aplicacao_id": 5001,
    "campanha_id": 12,
    "cpf": "***.***.247-25",
    "vacina": "Influenza",
    "dose": 1,
    "executor_tipo": "clinica_credenciada"
  }
}
```

---

# 4. Notificações

| Evento | Canal | Mensagem | Frequência | Pode desativar? |
|---|---|---|---|---|
| importacao.concluida | e-mail (futuro) | resumo válidos/erros | por importação | sim |
| campanha.encerrada | e-mail (futuro) | fechamento | por campanha | sim |

> Notificações a usuários (e-mail/WhatsApp) ficam para depois dos webhooks (V2 de engajamento).

---

# 5. Regras

- Não criar notificação/webhook sem **evento real**.
- Webhook precisa de **log** e **retry**; financeiro precisa de **idempotência**.
- Falha de integração **não pode sumir** sem registro (dead-letter).
- Expor dado sensível externamente só com **escopo** e **mínimo necessário** (LGPD); CPF mascarado por padrão.

---

# 6. PLANO — fundação de integração ANTES do portal

Ordem recomendada (o portal vai *surfacar* estes controles, então precisam existir antes):

## Fase A — Webhooks + API externa (habilita funções 3, 5, 7, 8)
- **A1. Webhooks de saída:** tabelas `webhook_assinatura` e `webhook_entrega`; ao ocorrer um evento, enfileirar entrega; **worker (cron)** com retry/backoff + HMAC + idempotência + logs.
- **A2. Admin de integrações:** ligar/desligar webhook por evento, gerar/rotacionar **tokens de consulta**, ver **logs de entrega**. (surge no portal, mas os endpoints já ficam prontos)
- **A3. API externa formalizada:** publicar o **contrato** (doc 09) + versão; expor **carteira** e **consultas** por token com escopo (para sistema de carteira/RH).

## Fase B — Turnover/RH (função 5) ✅ IMPLEMENTADO
- **B1. Sync por diferença:** `POST /interno/campanhas/{id}/elegiveis/sincronizar` (CSV/JSON) e
  `POST /parceiro/campanhas/{id}/elegiveis/sincronizar` (API). Recebe a lista completa, atualiza
  os presentes e **marca como `removido`** os ausentes não vacinados (via carimbo `sincronizado_em`).
  Escala em 100k (assíncrono no worker). RN-030.

## Fase C — App in company (função 4)
- **C1. Credencial/token de app** (tipo `app_in_company`) para o app do profissional; opcional **PWA offline-first** com fila de sincronização.

## Fase D — Portal (cliente + gestor de campanha)
- Construir **sobre** a fundação A/B/C: gestão de campanhas, importação, tabela verdade, faturamento, **painel de integrações/webhooks**, relatórios.

## Fase E — V2 (depois do portal)
- **Autoadesão B2C (9):** portal do paciente + consentimento LGPD + verificação de identidade.
- **Venda de voucher (10):** entitlement de voucher, resgate → elegibilidade, **pagamento** (domínio fiscal/PCI).

---

# 7. Recomendação

Fazer **Fase A (webhooks + API externa)** antes do portal. Assim o portal já nasce com o
"painel de integrações" (função 7) funcionando, e as funções 3/5/8 ficam garantidas por contrato.
Autoadesão (9) e venda de voucher (10) ficam para a V2 por trazerem consentimento e pagamento.
