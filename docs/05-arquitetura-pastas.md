# Arquitetura e Estrutura de Pastas

## Objetivo

Definir como o projeto será organizado tecnicamente.

> Projeto: Plataforma intermediadora de imunização corporativa (B2B2C, multi-tenant, API-first).
> Base: `protocolo-criacao-projeto-zero.md` — Fase 2. Decisões estruturais aprovadas em 2026-07-06.

---

# 1. Stack oficial

```txt
PHP procedural puro
MySQL/MariaDB
HTML
CSS
JavaScript puro
Fetch API
JSON
Git/GitHub
Docker/EasyPanel quando aplicável
```

---

# 2. Estrutura sugerida

```txt
/
  README.md
  controle-projeto.md
  orquestrador.md
  /docs
  /public                      # document root (único ponto exposto ao navegador)
    index.php                  # front controller / roteador simples
    /assets
      /css
      /js
      /img
    /admin                     # painel interno (operador/super_admin) - HTML/JS
    /portal                    # portal do cliente B2B - HTML/JS
    /app                       # PWA do profissional in company
      manifest.json
      service-worker.js
  /app                         # lógica de backend (PHP procedural)
    /config                    # config.php, conexão, .env loader
    /helpers                   # funções utilitárias (resposta JSON, datas, validação, CPF)
    /services                  # regras de negócio por domínio
      campanhas.php
      elegiveis.php
      pacientes.php
      aplicacoes.php
      tabela_verdade.php
      dashboard.php
    /middlewares               # auth_sessao, auth_api, tenant_scope, permissao, rate_limit, auditoria
    /db                        # acesso a dados (queries preparadas por entidade)
  /api                         # endpoints JSON (Fetch API e integrações)
    /v1
      /interno                 # consumido por admin/portal/app via sessão
      /parceiro                # consumido por rede credenciada e ingestão B2B via credencial/token
  /database
    /migrations                # SQL versionado
    /seeds                     # dados iniciais (vacinas, perfis)
  /storage
    /uploads                   # arquivos de importação de elegíveis (volume)
    /logs                      # logs de aplicação e auditoria (volume)
  /scripts                     # automações internas (import batch, manutenção)
```

---

# 3. Responsabilidade das pastas

| Pasta | Função | Observação |
|---|---|---|
| public | única entrada pública (document root) | Só `public/` é servido; `app/`, `api/`, `database/`, `storage/` ficam fora do docroot |
| public/admin, public/portal, public/app | frontends HTML/CSS/JS por perfil | Consomem `/api/v1/interno` via Fetch |
| app/config | configuração e conexão | `.env` fora do Git; segredos e credenciais de API aqui |
| app/services | regra de negócio por domínio | Ponto único de verdade das regras (RN-001..011) |
| app/middlewares | autenticação, tenant, permissão, rate limit, auditoria | **Toda** requisição passa por middleware antes do service |
| app/db | queries preparadas | Prepared statements sempre (anti-SQLi) |
| api/v1/interno | endpoints para humanos logados | Sessão + CSRF |
| api/v1/parceiro | endpoints para máquinas (rede + ingestão) | Credencial/token + escopo por campanha (RN-009) |
| database | SQL/migrations versionadas | Nunca alterar banco fora de migration |
| storage | uploads/logs | Usar volume persistente no EasyPanel |
| scripts | automações internas | Não expor via web |

---

# 4. Regras

- Não misturar regra crítica diretamente com HTML solto — regra vive em `app/services`, validada no backend.
- Somente `public/` no document root; todo o resto fica inacessível pela web.
- Cada arquivo deve ter comentário inicial explicando sua função.
- Manter padrão procedural; sem framework/OO salvo decisão explícita registrada.
- Prepared statements obrigatórios em todo acesso a banco.
- Respostas de API seguem o padrão JSON oficial do `orquestrador.md` (success/message/data/meta/errors).
- Nomes internos de banco/log/auditoria em português (`criado_em`, `aplicado_em` etc.); termos técnicos (`tenant_id`, `request_id`) em inglês.

---

# 5. Decisões técnicas

| Data | Decisão | Motivo | Impacto |
|---|---|---|---|
| 2026-07-06 | **Multi-tenant por coluna `tenant_id`** (schema compartilhado, isolamento por linha) | Simples e adequado a PHP procedural; isolamento garantido no middleware `tenant_scope` | Toda tabela de negócio tem `tenant_id`; toda query filtra por ele no backend |
| 2026-07-06 | **Dois canais de autenticação:** sessão+CSRF para humanos (admin, portal B2B, B2C); **token/credencial** para máquinas (rede credenciada, ingestão B2B, e app PWA) | Naturezas diferentes de cliente | Middlewares `auth_sessao` e `auth_api` separados |
| 2026-07-06 | **API separada por audiência:** `/api/v1/interno` (sessão) e `/api/v1/parceiro` (credencial+escopo) | Reduzir superfície e aplicar escopo por campanha (RN-009) | Endpoints públicos de terceiros isolados e auditados |
| 2026-07-06 | **Front controller único** em `public/index.php` roteando para `/api` e páginas | Organização sem framework | Um ponto de entrada, roteamento simples por caminho |
| 2026-07-06 | **`public/` como único document root** | Segurança: esconder código/config/uploads | Deploy no EasyPanel aponta docroot para `public/` |
| 2026-07-06 | **Scaffold implementado** (bootstrap, config/env, PDO, helpers, middlewares, front controller, rotas) | Fundação reutilizável dos endpoints do doc 09 | Código em `app/`, `api/v1/`, `public/index.php`; roteador por tabela de rotas |
| 2026-07-06 | **Roteamento por tabela** em `api/v1/rotas.php` (handlers em arquivos por domínio) | Sem framework, organizado e extensível | Novos endpoints = nova linha na tabela + função handler |
| 2026-07-06 | **PDO com prepared statements** encapsulado em funções procedurais | Anti-SQLi mantendo estilo procedural | `db_executar/db_primeiro/db_todos` |

> Pendências que podem alterar esta arquitetura (dúvidas abertas do doc 02):
> - **Offline-first** no PWA in company → se confirmado, exige camada de sincronização (IndexedDB + fila de aplicações) em `public/app`.
> - **Faturamento por dose** → se confirmado, adiciona domínio `faturamento` em `app/services`.
