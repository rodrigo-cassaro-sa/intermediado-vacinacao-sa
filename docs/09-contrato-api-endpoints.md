# Contrato de API e Endpoints

## Objetivo

Documentar endpoints, payloads, respostas, erros e permissões.

> Base: docs 05 (arquitetura API interno/parceiro) e 08 (modelo). Dois grupos:
> **`/api/v1/interno`** (humanos logados — sessão + CSRF) e **`/api/v1/parceiro`** (máquinas —
> token + escopo por campanha). Todo endpoint valida permissão e `tenant_id` no backend.

---

# 1. Padrão JSON oficial

Sucesso:

```json
{
  "success": true,
  "message": "Operação realizada com sucesso.",
  "data": {},
  "meta": null,
  "errors": []
}
```

Erro:

```json
{
  "success": false,
  "message": "Verifique os campos destacados.",
  "data": null,
  "meta": { "request_id": "req_123" },
  "errors": [
    { "field": "email", "code": "EMAIL_INVALIDO", "message": "Informe um e-mail válido." }
  ]
}
```

---

# 1.1 Convenções gerais

```txt
Base URL: https://{dominio}/api/v1
Formato: JSON (Content-Type: application/json), UTF-8.
Datas: ISO 8601 (2026-07-06T14:30:00-03:00).

Autenticação:
- interno  → cookie de sessão + header X-CSRF-Token (mutações).
- parceiro → header Authorization: Bearer {token}. O token define titular e escopo (campanha).

Headers de resposta:
- X-Request-Id: correlaciona com log_auditoria (campo request_id).

Multi-tenant:
- interno  → tenant_id derivado da sessão (NUNCA aceito do corpo/URL para escopo).
- parceiro → tenant_id/escopo derivado do token (credencial_api).

Paginação (listagens grandes): keyset/cursor — ?apos={ultimo_cursor}&por_pagina=50
  → meta { por_pagina, total, proximo_cursor }. Enviar proximo_cursor em `apos` para a
  próxima página; proximo_cursor=null significa fim. Evita OFFSET (escala em milhões).
  Listas pequenas podem ignorar (retorna a 1ª página).
Idempotência (parceiro, POST de escrita): header Idempotency-Key para evitar duplicidade.

Códigos HTTP:
200 OK · 201 Criado · 400 validação · 401 não autenticado · 403 sem permissão/escopo ·
404 não encontrado · 409 conflito/duplicado · 422 regra de negócio · 429 rate limit · 500 erro interno.
```

---

# 2. Endpoints

## Grupo interno (`/api/v1/interno`) — sessão

| Método | Endpoint | Objetivo | Permissão | Log? |
|---|---|---|---|---|
| POST | /auth/login | Autenticar usuário | público | sim |
| POST | /auth/logout | Encerrar sessão | autenticado | sim |
| GET | /clientes | Listar clientes B2B | operador_interno+ | não |
| POST | /clientes | Criar cliente B2B | operador_interno+ | sim |
| GET | /campanhas | Listar campanhas (do tenant/escopo) | operador / cliente_b2b | não |
| POST | /campanhas | Criar campanha | operador_interno+ | sim |
| PUT | /campanhas/{id} | Editar campanha | operador_interno+ | sim |
| POST | /campanhas/{id}/elegiveis/importar | Importar elegíveis por upload | operador / cliente_b2b (própria) | sim |
| GET | /campanhas/{id}/elegiveis | Listar elegíveis da campanha | escopo da campanha | não |
| POST | /aplicacoes | Registrar aplicação (app in company) | profissional_saude (campanha atribuída) | sim |
| POST | /aplicacoes-lote | Registrar várias aplicações (relatório por item) | profissional_saude / operador | sim |
| POST | /aplicacoes/{id}/retificar | Retificar aplicação (novo registro) | operador_interno+ | sim |
| GET | /campanhas/{id}/tabela-verdade | Consolidação elegível×aplicado | operador / cliente_b2b (própria) | sim |
| GET | /campanhas/{id}/dashboard | Métricas da campanha | operador / cliente_b2b (própria) | sim |
| GET | /campanhas/{id}/exportar | Exportar resultados (CSV) | operador / cliente_b2b (própria) | sim |
| GET | /credenciais | Listar credenciais de API | operador / cliente_b2b (própria) | sim |
| POST | /credenciais | Emitir credencial de API | operador / cliente_b2b (própria) | sim |
| POST | /credenciais/{id}/revogar | Revogar credencial | operador / cliente_b2b (própria) | sim |

## Grupo parceiro (`/api/v1/parceiro`) — token + escopo

| Método | Endpoint | Objetivo | Titular | Log? |
|---|---|---|---|---|
| POST | /campanhas/{id}/elegiveis | Ingestão de elegíveis via API | cliente_b2b (token ingestão) | sim |
| GET | /campanhas/{id}/elegiveis | Consultar elegíveis (para vacinar) | clinica_credenciada | sim |
| GET | /campanhas/{id}/elegiveis/{cpf} | Consultar um elegível por CPF | clinica_credenciada | sim |
| POST | /aplicacoes | Registrar vacinado | clinica_credenciada | sim |
| POST | /aplicacoes-lote | Registrar vários vacinados (relatório por item) | clinica_credenciada | sim |

> **Endpoints em lote** (`/aplicacoes-lote`): body `{ "aplicacoes": [ {elegivel_id, vacina_id, dose, lote, aplicado_em}, ... ] }`.
> Processam item a item (não param no 1º erro) e devolvem `{recebidos, confirmados, rejeitados, itens:[{indice, elegivel_id, ok, code|aplicacao_id}]}`.
> A ingestão de **elegíveis** já é em lote (ver seções de importar/ingestão).

> Todo endpoint parceiro só opera dentro de `credencial_api.escopo_campanha_id` (RN-009).
> Acesso a campanha fora do escopo → **403 FORA_DO_ESCOPO**.
>
> **RN-012 (rede credenciada):** a clínica só consulta/registra elegíveis com
> `elegivel.clinica_id = credencial.titular_id`. CPF não atribuído à clínica →
> **404 NAO_ELEGIVEL**; registrar vacinado de elegível de outra clínica → **403 FORA_DO_ESCOPO**.
> A atribuição é feita pelo operador: `POST /api/v1/interno/campanhas/{id}/atribuir-clinica`.

---

# 3. Detalhe dos endpoints críticos

## POST /api/v1/interno/campanhas/{id}/elegiveis/importar

```txt
Objetivo: Importar lista de elegíveis por upload (CSV/planilha).
Permissão: cliente_b2b (própria campanha) ou operador_interno.
Autenticação: sessão + CSRF. Content-Type: multipart/form-data (arquivo).
```

### Request
`multipart/form-data`: `arquivo=@elegiveis.csv` (colunas mínimas: `cpf,nome,data_nascimento`).

### Response sucesso (201)
```json
{
  "success": true,
  "message": "Importação processada.",
  "data": {
    "importacao_id": 45,
    "total_linhas": 120,
    "total_validos": 118,
    "total_invalidos": 2,
    "invalidos": [
      { "linha": 17, "cpf": "000...", "code": "CPF_INVALIDO" },
      { "linha": 88, "cpf": "111...", "code": "CPF_DUPLICADO_NO_ARQUIVO" }
    ]
  },
  "meta": { "request_id": "req_abc" },
  "errors": []
}
```

### Validações
- Campanha pertence ao tenant da sessão e está `rascunho`/`ativa`.
- CPF válido (dígito verificador) e deduplicado no arquivo e por campanha (RN-008, UNIQUE campanha+paciente).
- Cria/reutiliza `paciente` por CPF; cria `elegivel` com `origem=upload` e `status=pendente`.

### Códigos de erro
| Código | Mensagem | Quando ocorre |
|---|---|---|
| ARQUIVO_INVALIDO | Envie um CSV válido. | formato/colunas incorretas |
| CAMPANHA_NAO_ENCONTRADA | Campanha inexistente. | id não pertence ao tenant |
| CAMPANHA_ENCERRADA | Campanha encerrada. | status = encerrada |

---

## POST /api/v1/parceiro/campanhas/{id}/elegiveis  (ingestão B2B via API)

```txt
Objetivo: Cliente B2B envia elegíveis por API (paralelo ao upload).
Titular: cliente_b2b (token tipo ingestao_b2b), escopo = campanha.
Autenticação: Bearer token. Idempotency-Key recomendado.
```

### Request
```json
{
  "elegiveis": [
    { "cpf": "12345678901", "nome": "Maria Silva", "data_nascimento": "1990-05-10" },
    { "cpf": "98765432100", "nome": "João Souza",  "data_nascimento": "1985-11-02" }
  ]
}
```

### Response sucesso (201)
```json
{
  "success": true,
  "message": "Elegíveis recebidos.",
  "data": { "recebidos": 2, "criados": 2, "atualizados": 0, "rejeitados": 0 },
  "meta": { "request_id": "req_def" },
  "errors": []
}
```

### Códigos de erro
| Código | Mensagem | Quando ocorre |
|---|---|---|
| FORA_DO_ESCOPO | Sem acesso a esta campanha. | campanha ≠ escopo do token |
| CPF_INVALIDO | CPF inválido em item da lista. | validação (retorna em errors[] por índice) |
| PAYLOAD_INVALIDO | Estrutura inválida. | JSON fora do contrato |

---

## GET /api/v1/parceiro/campanhas/{id}/elegiveis/{cpf}  (rede consulta)

```txt
Objetivo: Clínica consulta se o CPF é elegível na campanha e o que aplicar.
Titular: clinica_credenciada, escopo = campanha.
```

### Response sucesso (200)
```json
{
  "success": true,
  "message": "Elegível encontrado.",
  "data": {
    "elegivel_id": 900,
    "paciente": { "cpf": "12345678901", "nome": "Maria Silva" },
    "status": "pendente",
    "vacinas_previstas": [ { "vacina_id": 3, "nome": "Influenza", "doses_previstas": 1 } ]
  },
  "meta": { "request_id": "req_ghi" },
  "errors": []
}
```

### Códigos de erro
| Código | Mensagem | Quando ocorre |
|---|---|---|
| NAO_ELEGIVEL | CPF não elegível nesta campanha. | sem registro de elegível |
| FORA_DO_ESCOPO | Sem acesso a esta campanha. | escopo do token |

---

## POST /api/v1/interno/aplicacoes  e  POST /api/v1/parceiro/aplicacoes  (registrar vacinado)

```txt
Objetivo: Registrar aplicação de dose de forma rastreável (RN-004).
Interno: profissional_saude (campanha atribuída). Parceiro: clinica_credenciada (escopo).
Registro IMUTÁVEL (RN-010). Idempotency-Key recomendado no parceiro.
```

### Request
```json
{
  "elegivel_id": 900,
  "vacina_id": 3,
  "dose": 1,
  "lote": "ABC-2026-77",
  "via_administracao": "intramuscular",
  "local_aplicacao": "Sede Empresa X - 3º andar",
  "aplicado_em": "2026-07-06T10:15:00-03:00"
}
```

### Response sucesso (201)
```json
{
  "success": true,
  "message": "Aplicação registrada.",
  "data": { "aplicacao_id": 5001, "status": "confirmada", "elegivel_status": "aplicado" },
  "meta": { "request_id": "req_jkl" },
  "errors": []
}
```

### Validações (backend)
- Elegível existe, pertence ao tenant/escopo e à campanha; campanha `ativa`.
- `aplicado_em` dentro de `periodo_inicio`..`periodo_fim` (RN-003).
- `vacina_id` consta em `campanha_vacina`.
- Executor (profissional/clínica) autorizado na campanha.
- Ao confirmar, atualiza `elegivel.status = aplicado` e grava `log_auditoria` (evento `aplicacao.registrada`).

### Códigos de erro
| Código | Mensagem | Quando ocorre |
|---|---|---|
| NAO_ELEGIVEL | Paciente não elegível. | elegível inexistente/fora da campanha |
| VACINA_FORA_DA_CAMPANHA | Vacina não prevista. | vacina_id ∉ campanha_vacina |
| FORA_DO_PERIODO | Fora da janela da campanha. | aplicado_em fora do período |
| CAMPANHA_INATIVA | Campanha não está ativa. | status ≠ ativa |
| VACINADO_DUPLICADO | Paciente já vacinado. | elegível já tem aplicação confirmada (RN-013) |
| FORA_DO_ESCOPO | Sem acesso a esta campanha/clínica. | escopo do token / clínica (RN-009/012) |

---

## POST /api/v1/interno/aplicacoes/{id}/retificar

```txt
Objetivo: Corrigir aplicação sem editar o registro original (RN-010).
Permissão: operador_interno / super_admin (auditado).
Efeito: cria NOVA aplicacao com aplicacao_origem_id={id} e motivo_retificacao;
        marca a original como 'retificada' (ou 'estornada').
```
Erros: `APLICACAO_NAO_ENCONTRADA`, `MOTIVO_OBRIGATORIO`.

---

## GET /api/v1/interno/campanhas/{id}/tabela-verdade

```txt
Objetivo: Consolidação elegível × aplicado (RN-005), lida de vw_tabela_verdade.
Permissão: operador / cliente_b2b (própria campanha). Filtra por tenant_id + campanha_id.
```

### Response sucesso (200)
```json
{
  "success": true,
  "message": "OK.",
  "data": {
    "resumo": { "elegiveis": 118, "aplicados": 90, "pendentes": 25, "recusados": 3 },
    "itens": [
      { "cpf": "123...", "nome": "Maria Silva", "situacao": "aplicado", "ultima_aplicacao_em": "2026-07-06T10:15:00-03:00" }
    ]
  },
  "meta": { "request_id": "req_mno", "page": 1, "por_pagina": 50, "total": 118 },
  "errors": []
}
```

---

# 3.9 API EXTERNA (parceiros/integrações) — contrato público v1

Base: `https://{dominio}/api/v1`. Autenticação: `Authorization: Bearer {token}`.
Rate limit por credencial (429 + Retry-After). Versão atual: **v1** (mudança incompatível → v2).

**Tipos de credencial (token de máquina):**

| Tipo | Escopo | Uso | Endpoints |
|---|---|---|---|
| `ingestao_b2b` | 1 campanha | cliente/RH envia elegíveis | POST `/parceiro/campanhas/{id}/elegiveis` |
| `rede_credenciada` | 1 campanha + 1 clínica | clínica consulta/registra vacinado | GET `/parceiro/campanhas/{id}/elegiveis/{cpf}`, POST `/parceiro/aplicacoes[-lote]`, POST `/parceiro/elegiveis/{id}/situacao` |
| `consulta` | 1 cliente (tenant) | sistema de carteira / RH / BI lê dados do cliente | GET `/parceiro/carteira/{cpf}`, GET `/parceiro/campanhas/{id}/tabela-verdade` |

**Consulta — exemplos:**

```txt
GET /api/v1/parceiro/carteira/{cpf|voucher}     (token consulta)
  → { paciente:{identidade,nome}, total_doses, doses:[{aplicado_em,vacina,dose,lote,campanha,cidade,uf}] }
  Só doses do PRÓPRIO cliente (escopo do token). CPF mascarado.

GET /api/v1/parceiro/campanhas/{id}/tabela-verdade?apos=&por_pagina=   (token consulta)
  → { itens:[{cpf,nome,situacao_elegivel,total_aplicacoes,ultima_aplicacao_em}] }, meta.proximo_cursor
  Campanha precisa pertencer ao cliente do token (senão 403 FORA_DO_ESCOPO).
```

**Webhooks de saída (o cliente recebe eventos):** ver docs/11. Assinatura `X-Assinatura`
(HMAC-SHA256 do corpo), `X-Entrega-Id` para idempotência, retry com backoff.

**Idempotência de escrita:** header `Idempotency-Key` nos POSTs de máquina (evita duplicar em retry).

---

# 4. Regras

- API não expõe erro técnico bruto (stack/SQL); erro interno → `500` + `code=ERRO_INTERNO` + `request_id`.
- API valida **permissão e tenant/escopo no backend** — nunca confia no frontend nem no corpo para escopo.
- API valida payload recebido (tipos, obrigatórios, CPF, datas).
- Endpoints de escrita do grupo parceiro aceitam `Idempotency-Key` (evita registro duplicado).
- Todo endpoint com `Log? sim` grava `log_auditoria` com `request_id`, `evento`, `origem`, `metadata` mascarada.
- Aplicação nunca é atualizada (RN-010); correção só via `/retificar`.

> Segurança detalhada (emissão/rotação de token, rate limit, criptografia, LGPD) no doc 10.
