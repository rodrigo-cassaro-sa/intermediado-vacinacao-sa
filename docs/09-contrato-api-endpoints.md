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

## Identidade da campanha em qualquer resposta

```txt
codigo = identidade oficial da campanha (VAC.TEMP.MOD.GRP.CLI.SEQ), migration 026.
nome   = rótulo humano OPCIONAL. Pode ser null. Nunca use sozinho para identificar.
Na tela:  codigo || nome || ('#' + id)   — o nome só aparece como texto secundário.
```

Endpoints que devolvem campanha e já trazem `codigo`: `/interno/campanhas`,
`/interno/campanhas/{id}`, `/interno/dashboard`, `/interno/relatorios/campanhas`,
`/interno/clientes/{id}/campanhas-resumo`, `/interno/campanhas/{id}/faturamento-cliente`
e `/interno/campanhas/{id}/faturamento-clinicas`. Nas carteiras (interna e de parceiro)
o campo `campanha` já vem resolvido para o código.

## POST /api/v1/interno/campanhas/{id}/elegiveis/importar

```txt
Objetivo: Importar lista de elegíveis por upload (CSV/planilha).
Permissão: cliente_b2b (própria campanha) ou operador_interno.
Autenticação: sessão + CSRF. Content-Type: multipart/form-data (arquivo).
```

### Request
`multipart/form-data`: `arquivo=@elegiveis.csv` (colunas mínimas: `cpf,nome,data_nascimento`).

#### Leitura do CSV (vale para TODAS as importações do sistema)

```txt
1ª linha  = CABEÇALHO sempre que trouxer nomes de coluna reconhecidos.
            Nesse caso o mapeamento é POR NOME e a ORDEM das colunas não importa;
            colunas desconhecidas são ignoradas e coluna ausente vira null.
Sem cabeçalho reconhecível, vale a ordem posicional padrão:
            cpf, nome, data_nascimento, tipo_vinculo, cpf_titular,
            codigo_lotacao, codigo_rh, identificador
Delimitador: ; , TAB ou | (detectado pela 1ª linha).
BOM UTF-8 (Excel), acentos, maiúsculas/minúsculas e aspas são normalizados.
```

Nomes aceitos no cabeçalho (além do nome canônico):

| Campo | Também aceita |
|---|---|
| cpf | cpf_colaborador, cpf_funcionario, cpf_paciente, cpf_beneficiario, num_cpf, documento |
| nome | nome_completo, nome_colaborador, nome_funcionario, nome_paciente, nome_beneficiario |
| data_nascimento | nascimento, data de nascimento, dt_nascimento, data_nasc, dt_nasc, nasc |
| tipo_vinculo | vinculo, tipo, parentesco, categoria |
| cpf_titular | titular, cpf_responsavel |
| codigo_lotacao | lotacao, cod_lotacao, centro de custo, codigo_unidade, unidade, filial, setor, departamento |
| codigo_rh | matricula, cod_rh, matricula_rh, registro, chapa, codigo_funcionario |
| identificador | voucher, passaporte, codigo_voucher, id_externo, documento_estrangeiro, rne |

Para o histórico de vacinados (`POST /interno/clientes/{id}/vacinados-historico/importar`)
valem os mesmos campos de pessoa mais: `vacina` (imunizante, imunobiologico, produto),
`dose`, `lote`, `aplicado_em` (data_aplicacao, data da aplicação, data_vacinacao, data),
`cidade` (municipio) e `uf` (estado).

Implementação: `app/helpers/csv.php` (backend) e `public/assets/csv.js` (telas).
Teste: `php scripts/testar_csv.php`.

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

## POST /api/v1/interno/campanhas/{id}/vacinados/importar  (RN-031)

```txt
Objetivo: importar vacinados em MASSA numa campanha ATIVA.
Permissão: quem tem acesso aos dados do paciente na campanha (doc 04 §4.1).
Autenticação: sessão + CSRF. multipart (arquivo=@vacinados.csv) OU JSON {csv, padroes, criar_elegivel}.
IMPORTANTE: esta chamada NÃO grava — ela SIMULA e devolve o relatório.
```

### Colunas do CSV

Cabeçalho por nome (ordem livre, sinônimos aceitos — ver §3.1):

```txt
cpf | identificador, vacina (nome OU sigla), dose, lote, aplicado_em,
profissional_nome, profissional_cpf, cidade, uf, unidade
```

O que faltar na linha é preenchido pelos **dados comuns do lote** (`padroes`), com os
mesmos campos mais `vacina_id` e `clinica_id`. Com `criar_elegivel = true`, quem não está
na lista é criado — e aí a linha também precisa de `tipo_vinculo`, `codigo_lotacao` e
`codigo_rh` (RN-016/RN-018 continuam valendo).

### Fluxo

```txt
POST /campanhas/{id}/vacinados/importar        -> status 'simulada' (ou 'simulando' se grande)
GET  /importacoes-vacinados/{id}               -> acompanha e traz o resumo + amostra de erros
POST /importacoes-vacinados/{id}/confirmar     -> grava (status 'concluida' ou 'pendente' na fila)
POST /importacoes-vacinados/{id}/estornar      -> desfaz o LOTE (interno + justificativa)
GET  /importacoes-vacinados/{id}/erros/exportar -> CSV dos rejeitados com o motivo
GET  /campanhas/{id}/importacoes-vacinados     -> histórico dos lotes da campanha
```

### GET /campanhas/{id}/importacoes-vacinados

Últimos 50 lotes, com quem importou, totais e — quando estornado — quem desfez e por quê.
**O cliente enxerga** (confere o que enviou); o campo `pode_estornar` só vem `true` para
`super_admin`/`operador_interno`, e é apenas dica de tela: quem decide é o endpoint de estorno.
Sem essa listagem o controle "só o interno estorna" seria inoperante — o operador não teria
como descobrir o número do lote que o cliente importou.

Acima de 2.000 linhas a simulação e a gravação vão para a fila do worker (doc 13).

### Response (resumo do lote)
```json
{
  "success": true,
  "data": {
    "importacao_id": 12, "status": "simulada",
    "total_linhas": 5000, "total_processados": 5000,
    "total_aplicacoes": 4800, "total_elegiveis": 0, "total_rejeitados": 200,
    "erros_amostra": [{ "linha": 17, "cpf": "529***.**-25", "codigo": "NAO_ELEGIVEL",
                        "motivo": "Pessoa não está na lista de elegíveis da campanha" }]
  }
}
```

### Códigos de erro por linha
`CPF_INVALIDO` · `SEM_IDENTIDADE` · `NAO_ELEGIVEL` · `NOME_OBRIGATORIO` ·
`TIPO_VINCULO_INVALIDO` · `CODIGO_LOTACAO_OBRIGATORIO` · `CODIGO_RH_OBRIGATORIO` ·
`VACINA_OBRIGATORIA` · `VACINA_FORA_DA_CAMPANHA` · `VACINADO_DUPLICADO` ·
`FORA_DO_PERIODO` · `DATA_INVALIDA` · `CAMPO_OBRIGATORIO` · `CPF_PROFISSIONAL_INVALIDO` · `UF_INVALIDA`

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
| `app_in_company` | 1 campanha | app/PWA/terceiro registra vacinado in company | GET `/parceiro/incompany/campanhas/{id}/elegiveis/{cpf}`, POST `/parceiro/incompany/aplicacoes[-lote]` |
| `consulta` | 1 cliente (tenant) | sistema de carteira / RH / BI lê dados do cliente | GET `/parceiro/carteira/{cpf}`, GET `/parceiro/campanhas/{id}/tabela-verdade` |

**Consulta — exemplos:**

```txt
GET /api/v1/parceiro/carteira/{cpf|voucher}     (token consulta)
  → { paciente:{identidade,nome}, total_doses,
      doses:[{aplicado_em,vacina,dose,lote,campanha,campanha_codigo,cidade,uf}] }
  Só doses do PRÓPRIO cliente (escopo do token). CPF mascarado.
  campanha        = CÓDIGO da campanha (migration 026). Campanhas anteriores ao
                    código caem no nome antigo. Antes da correção do BUG-002 este
                    campo vinha null para toda campanha criada após a 026.
  campanha_codigo = o código puro (null nas campanhas antigas). Use este para
                    casar registros de forma estável.

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
