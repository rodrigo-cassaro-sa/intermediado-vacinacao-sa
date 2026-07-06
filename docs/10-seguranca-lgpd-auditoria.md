# Segurança, LGPD e Auditoria

## Objetivo

Registrar regras de segurança, autenticação, permissões, privacidade, logs e auditoria.

> Contexto crítico: a plataforma trata **dados de saúde** (categoria **sensível** na LGPD).
> Base: docs 04 (perfis), 05 (arquitetura), 08 (modelo), 09 (API). Risco nº 1 do projeto:
> vazamento entre tenants. Toda decisão aqui tem prioridade máxima na ordem de conflito.

---

# 1. Dados sensíveis

| Dado | Onde aparece | Base/justificativa | Proteção |
|---|---|---|---|
| CPF | paciente, elegivel, logs | Identificação; base legal do contrato B2B + consentimento B2C | Único por paciente; mascarado em logs/telas (`***.***.789-**`) |
| Nome / data nascimento | paciente | Identificação do vacinável | Minimização; acesso por escopo |
| Dado de vacinação (vacina, dose, lote, data) | aplicacao, tabela verdade | Execução do serviço de saúde | **Dado sensível de saúde**; criptografia em trânsito/repouso; acesso auditado |
| Consentimento | consentimento_lgpd | Base legal (RN-011) | Registro com versão do termo, data/hora, IP |
| Credencial de API (token) | credencial_api | Autenticação de parceiro | Armazenar **só hash**; nunca logar o token cru |
| Senha de usuário | usuario | Autenticação | `password_hash` (bcrypt/argon2); nunca em log |

**Princípios LGPD aplicados:** finalidade (só imunização contratada), minimização (só campos
necessários), base legal (contrato B2B + consentimento B2C), transparência, segurança e
auditoria de acesso. Direitos do titular (acesso/correção/eliminação) tratados via operador
e portal B2C (V2), com ação registrada em auditoria.

---

# 2. Autenticação e sessão

```txt
Humanos (admin, portal B2B, profissional, B2C):
  Método de login: e-mail + senha (password_hash), sobre HTTPS.
  Tempo de sessão: inatividade 30 min; expiração absoluta 8 h (revalidar login).
  Cookie seguro: HttpOnly + Secure + SameSite=Lax; ID de sessão rotacionado no login.
  CSRF: token por sessão exigido em toda mutação (POST/PUT/DELETE) do grupo interno.
  Anti-brute force: bloqueio progressivo por tentativas + log de falha de login.

Máquinas (rede credenciada, ingestão B2B):
  Método: Bearer token (credencial_api), somente HTTPS.
  Armazenamento: apenas token_hash no banco; token cru mostrado 1x na emissão.
  Escopo: escopo_campanha_id restringe à campanha (RN-009).
  Ciclo de vida: expira_em, revogado_em; rotação suportada (emitir nova + revogar antiga).
  Rate limit: por credencial (ex.: 60 req/min) → 429 quando excede.
```

---

# 3. Permissões críticas

| Ação | Perfil autorizado | Validação backend | Log |
|---|---|---|---|
| Criar/editar campanha | super_admin, operador_interno | sim | sim |
| Importar elegíveis (upload) | operador_interno, cliente_b2b (própria) | sim | sim |
| Ingerir elegíveis (API) | credencial ingestao_b2b (escopo) | sim | sim |
| Consultar elegíveis (rede) | credencial rede_credenciada (escopo) | sim | sim |
| Registrar aplicação | profissional_saude / clinica_credenciada (escopo) | sim | sim |
| Retificar aplicação | operador_interno, super_admin | sim | sim |
| Ver tabela verdade / dashboard | operador, cliente_b2b (própria) | sim | sim |
| Ver carteira do paciente | paciente (próprio); operador/profissional (auditado) | sim | sim |
| Emitir/revogar credencial de API | operador; cliente_b2b (própria) | sim | sim |
| Ver logs de auditoria | super_admin; operador (parcial) | sim | sim |

**Regra de ouro:** o backend deriva `tenant_id`/escopo da sessão ou do token — **nunca** do
corpo ou da URL para fins de autorização. Toda query filtra por `tenant_id` (isolamento).

---

# 4. Logs e auditoria

| Evento | Quando registrar | Dados mínimos | Retenção |
|---|---|---|---|
| login.sucesso / login.falha | toda autenticação | ator, origem, ip, request_id, data_hora | 12 meses |
| elegiveis.importados | upload/API de elegíveis | tenant, campanha, totais, ator, request_id | vida da campanha + 5 anos |
| aplicacao.registrada | cada aplicação | tenant, campanha, elegivel, vacina, executor, request_id | 5 anos (rastreabilidade sanitária) |
| aplicacao.retificada | cada retificação | aplicacao_origem, motivo, ator | 5 anos |
| credencial.emitida / revogada | gestão de token | titular, tipo, escopo, ator | 5 anos |
| dado_sensivel.acessado | leitura de carteira/PII fora do próprio titular | ator, paciente_id, motivo/origem | 12 meses |
| permissao.negada | 403/tentativa fora do escopo | ator, endpoint, escopo, ip | 12 meses |

- `log_auditoria.metadata` guarda payload **mascarado** (sem CPF cru, sem token, sem senha).
- Toda entrada tem `request_id` correlacionável ao header `X-Request-Id`.
- Retenções são premissas a validar com o jurídico/DPO.

---

# 5. Regras obrigatórias

- Não versionar `.env` (segredos, credenciais de banco, chave de criptografia fora do Git).
- Não expor senha, token, CPF cru ou stack trace em resposta/log.
- Não registrar senha/token em log; mascarar CPF.
- Não confiar no frontend para permissão — validação sempre no backend.
- HTTPS obrigatório; sem HTTP em produção. Cookies Secure/HttpOnly.
- Prepared statements em todo acesso a banco (anti-SQLi); saída HTML escapada (anti-XSS).
- Upload de elegíveis: validar tipo/tamanho, tratar como não confiável, salvar fora do docroot.
- Debug visual restrito a usuário autorizado, mascarado e removível (regra 9 do orquestrador).
- MySQL só em rede interna; phpMyAdmin protegido (regra 11 do orquestrador).
- Escopo por campanha (RN-009) e isolamento por tenant (RN-006/007) são invioláveis.

---

# 6. Consentimento e ciclo de vida do dado (LGPD)

```txt
Base legal:
- B2B: execução de contrato / obrigação do empregador na saúde ocupacional (a validar com DPO).
- B2C: consentimento do titular (consentimento_lgpd) antes de tratar dado de saúde.

Coleta do consentimento: dúvida aberta (doc 02) — via B2C no app ou via B2B em nome do
colaborador. Definir antes de liberar o portal B2C (V2).

Direitos do titular: acesso, correção, portabilidade e eliminação — atendidos por operador
(auditado) e, na V2, self-service no portal B2C.

Eliminação/anonimização: ao encerrar contrato/retenção, anonimizar PII mantendo dados
estatísticos agregados (sem identificar o titular). Não apagar sem confirmação (regra global).
```

---

# 7. Checklist

```md
- [ ] Login seguro (hash de senha, HTTPS, anti-brute force).
- [ ] Sessão segura (HttpOnly/Secure/SameSite, expiração, CSRF).
- [ ] Token de parceiro por hash, com escopo e rotação/revogação.
- [ ] Permissões validadas no backend; tenant/escopo nunca vindos do frontend.
- [ ] Logs sem dados sensíveis (CPF mascarado, sem token/senha).
- [ ] Auditoria de acesso a dado sensível e de aplicação.
- [ ] LGPD: base legal, consentimento B2C, minimização, retenção definida com DPO.
- [ ] Criptografia em trânsito (HTTPS) e em repouso para dado sensível.
- [ ] Rate limit e idempotência nas APIs de parceiro.
- [ ] Debug restrito e removível; .env fora do Git; phpMyAdmin protegido.
```

> Pendências (dúvidas abertas doc 02) que impactam segurança/LGPD: origem do consentimento
> (B2C x B2B), tratamento de estrangeiro sem CPF, e validação de bases legais e retenções com DPO/jurídico.
