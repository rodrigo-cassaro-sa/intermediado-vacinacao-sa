# Controle e Memória do Projeto

> Este arquivo deve ficar ao lado do `orquestrador.md`.
>
> Função: registrar onde o projeto está, o que já foi feito, quais decisões foram tomadas e quais são os próximos passos.
>
> Atualize este arquivo sempre que houver mudança relevante.

---

# 1. Identificação do projeto

```txt
Nome do projeto: Plataforma de Gestão de Imunização Corporativa (nome provisório)
Cliente/empresa: empresa prestadora de imunização corporativa
Responsável: [a preencher]
Data de início: 2026-07-06
Última atualização: 2026-07-06
Status geral: planejamento
```

---

# 2. Visão geral

## Objetivo do projeto

```txt
Plataforma intermediadora (hub central) que orquestra campanhas de imunização corporativa
entre a prestadora, os clientes B2B, os pacientes B2C, a rede credenciada e os profissionais
in company. Recebe elegíveis, coordena a execução (rede credenciada / in company), registra
cada aplicação de forma rastreável e devolve dados com qualidade analítica (dashboards +
tabela verdade), em conformidade com a LGPD.
```

## Stack definida

```txt
PHP procedural puro / MySQL/MariaDB / phpMyAdmin / HTML / CSS / JavaScript puro /
Fetch API / JSON / Git/GitHub / Docker/Docker Compose / EasyPanel / Apache ou Nginx /
Domínio com SSL. Sem framework e sem OO por padrão.
```

## Observações importantes

```txt
- Dado de saúde é dado SENSÍVEL (LGPD): base legal, consentimento, minimização, criptografia
  e auditoria de acesso são obrigatórios.
- Multi-tenant obrigatório: dados de um cliente B2B nunca vazam para outro.
- A plataforma é a fonte da verdade; sistemas externos (rede, app) consomem/alimentam via API.
- Fase atual: BRIEFING (Fase 1 do protocolo-criacao-projeto-zero.md). Nenhum código escrito ainda.
```

---

# 3. Etapa atual

```txt
Etapa atual: MVP FUNCIONAL COMPLETO validado em homologação. Ambas modalidades (in_company + rede credenciada com isolamento por clínica RN-012), elegíveis (upload/JSON/API), aplicação, tabela verdade/dashboard e extração CSV — tudo via /admin em https://imunizacao-imz-app.imx7lc.easypanel.host. Próximo: telas reais (sair do console) e/ou preparar produção.
Protocolo em uso: protocolo-criacao-projeto-zero.md
Especialista principal: especialista-produto-planejamento.md
Especialistas de apoio: especialista-negocio-saas.md, especialista-seguranca-auditoria.md, especialista-banco-dados.md, especialista-engajamento-integracoes.md, especialista-documentacao-memoria.md
Skills principais: skill-briefing.md, skill-perfis-permissoes.md, skill-arquitetura.md, skill-multitenant-workspaces.md, skill-lgpd-privacidade.md
```

---

# 4. Progresso por fase

| Fase | Status | Observação |
|---|---|---|
| Briefing | feito | docs 01, 02, 03, 04 preenchidos; decisões estruturais tomadas |
| Perfis e permissões | feito | doc 04 preenchido (6 perfis + matriz) |
| Arquitetura | feito | doc 05: multi-tenant por tenant_id, API interno/parceiro, public/ docroot |
| Mapa de telas | feito | doc 07: telas admin/portal/app + fluxos A–E |
| Design/UX/UI | pendente |  |
| Banco de dados | validado (homolog) | migrations 000..013 (013 = elegivel_historico + aplicacao_historico, RN-021/022). Auto-migração no deploy. |
| Backend/API/PHP | validado (homolog) | blocos 1, 2 e 3 VALIDADOS em homolog via /admin (cliente→campanha→elegíveis→aplicação→tabela verdade/dashboard). Falta: extração CSV, rede credenciada testada com clínica real, refino. |
| Frontend | em andamento | public/admin/index.html: console de testes (login/health/clientes/campanhas) via Fetch; validar no deploy |
| Segurança/auditoria | em andamento | doc 10 preenchido (auth, escopo, auditoria, LGPD, criptografia); implementação nos middlewares pendente |
| QA/testes | pendente |  |
| Documentação | em andamento | fase 1 documentada |
| Git/GitHub | feito | repo público no GitHub (intermediado-vacinacao-sa), branch main, push do commit inicial 7071c6c |
| Docker/EasyPanel | validado (homolog) | 3 serviços no ar; entrypoint aplica migrations no boot (AUTO_MIGRAR, incremental); cron de expiração (RN-015) documentado |
| Homologação | pendente |  |
| Produção | pendente |  |
| Monitoramento | pendente |  |

---

# 5. Histórico de decisões

| Data | Decisão | Motivo | Impacto | Quem decidiu |
|---|---|---|---|---|
| 2026-07-06 | Usar protocolo-criacao-projeto-zero e começar pela Fase 1 (briefing), sem código | Projeto do zero, grande/crítico; regra do protocolo | Define ordem de trabalho | Orquestrador (a ratificar pelo usuário) |
| 2026-07-06 | MVP com **as duas modalidades** (in company + rede credenciada) | Decisão do usuário | Escopo do MVP ampliado; risco de prazo alto | Usuário |
| 2026-07-06 | Ingestão de elegíveis por **upload + API** já no MVP | Decisão do usuário | Superfície de segurança de API na V1 | Usuário |
| 2026-07-06 | Identidade do paciente = **CPF** (RN-008 aprovada) | Decisão do usuário | Deduplicação e consolidação de carteira | Usuário |
| 2026-07-06 | Ordem de construção incremental dentro do MVP (mitigação) | Reduzir risco de entregar tudo de uma vez | Sequência de execução | Orquestrador |
| 2026-07-06 | Multi-tenant por coluna `tenant_id` (schema compartilhado) | Simples para PHP procedural; isolamento no middleware | Toda tabela de negócio tem tenant_id | Orquestrador (doc 05) |
| 2026-07-06 | API separada: `/api/v1/interno` (sessão) e `/api/v1/parceiro` (credencial+escopo) | Reduzir superfície e aplicar escopo por campanha (RN-009) | Estrutura de endpoints | Orquestrador (doc 05) |
| 2026-07-06 | `public/` como único document root | Esconder código/config/uploads | Deploy aponta docroot p/ public/ | Orquestrador (doc 05) |

---

# 6. O que já foi feito

| Data | Item concluído | Arquivos afetados | Evidência | Observação |
|---|---|---|---|---|
| 2026-07-06 | Leitura da memória e docs base | orquestrador.md, controle-projeto.md, docs/00, docs/01-04, protocolo | — | Estado confirmado: projeto do zero |
| 2026-07-06 | Preenchimento da Fase 1 (briefing) | docs/01, docs/02, docs/03, docs/04 | arquivos gerados | Decisões estruturais aplicadas |
| 2026-07-06 | Preenchimento da Fase 2 (arquitetura + telas) | docs/05, docs/07 | arquivos gerados | Multi-tenant, API interno/parceiro, mapa de telas |
| 2026-07-06 | Modelagem de banco (doc 08) | docs/08 | arquivo gerado | 13 tabelas + VIEW tabela verdade + plano de migrations |
| 2026-07-06 | Contrato de API (doc 09) | docs/09 | arquivo gerado | Grupos interno/parceiro, endpoints críticos, erros, idempotência |
| 2026-07-06 | Segurança/LGPD (doc 10) | docs/10 | arquivo gerado | Auth, escopo por campanha, auditoria, criptografia, consentimento |
| 2026-07-06 | SQL/migrations reais (000..008 + seeds) | database/migrations/*, database/seeds/*, database/README.md | arquivos gerados | 13 tabelas + VIEW; não executados ainda |
| 2026-07-06 | Scaffold backend PHP | app/*, api/v1/*, public/index.php, .env.example, .gitignore | arquivos gerados | Fundação procedural; health + login; não executado (sem PHP/MySQL) |
| 2026-07-06 | Docker + deploy EasyPanel + Git | Dockerfile, docker/*, public/.htaccess, .dockerignore, scripts/migrar.php, docs/13 | commit ac5a892 | docroot public/; health em /api/v1/health; repo local (main) |
| 2026-07-06 | Atualização do checkpoint | controle-projeto.md | este arquivo | — |

---

# 7. Próximos passos

## Próxima ação imediata

```txt
Repo local pronto (commit ac5a892). Falta: (1) criar o repositório remoto no GitHub e dar push;
(2) no EasyPanel, criar projeto com 3 serviços (imz-app via Dockerfile, imz-mysql, imz-phpmyadmin),
configurar variáveis (doc 13 §3), volumes (§6), domínio+SSL (§7); (3) deploy; (4) aplicar migrations
(php scripts/migrar.php --seeds); (5) rodar checklist pós-deploy validando /api/v1/health e login.
```

## Lista de próximos passos

| Ordem | Próximo passo | Responsável | Prioridade | Status |
|---:|---|---|---|---|
| 1 | ~~Criar repo GitHub + push~~ | usuário/deploy | alta | feito (commit 7071c6c) |
| 2 | ~~EasyPanel: criar serviços + deploy~~ | usuário/deploy | alta | feito (health app:ok/banco:ok) |
| 3 | ~~Aplicar migrations + criar admin~~ | deploy | alta | feito (13 tabelas + VIEW + seeds; admin id=1) |
| 4 | ~~Validar login~~ | QA/deploy | alta | feito (success:true, super_admin) |
| 5 | ~~Bloco 1 (cliente/campanha)~~ | usuário/deploy | alta | validado no /admin |
| 6 | ~~Bloco 2 (elegíveis)~~ | usuário/deploy | alta | validado no /admin |
| 7 | ~~Bloco 3 (aplicação + tabela verdade)~~ | usuário/deploy | alta | validado no /admin |
| 8 | Extração/CSV p/ cliente B2B (commit df335e6) — validado | especialista-backend | média | feito |
| 8b | ~~RN-012 isolamento por clínica~~ (migration 009 aplicada) | usuário/deploy | alta | validado no /admin |
| 8c | RN-013/014/015 regras de faturamento (commit 791f9cb) — deploy e testar | usuário/deploy | alta | em andamento |
| 8d | Registro de aplicações em LOTE interno/parceiro (commit 4f09e6b); elegíveis já em lote | especialista-backend | média | em andamento |
| 8e | RN-016/017 tipo_vinculo + cpf_titular (commit 3cc41c5) | usuário/deploy | alta | em andamento |
| 8f | RN-018/019 códigos do cliente + lastro do vacinado (commit 8e1b241) | usuário/deploy | alta | em andamento |
| 8g | RN-020 motivo de não-vacinação (commit 30613a4) — migration 012 auto | usuário/deploy | média | em andamento |
| 8h | RN-021/022 histórico (elegível/aplicação) + editar elegível + estornar/desvacinar (commit a490beb) — migration 013 auto | usuário/deploy | alta | em andamento |
| backlog | Rastreabilidade extra: fabricante/validade lote, conselho profissional, comprovante, idempotência (recomendado) | especialista-backend | baixa/média | pendente |
| 9 | Telas reais (portal B2B / painel operador) saindo do console de testes | especialista-design/frontend | média | pendente |
| 10 | Preencher docs pendentes (11 integrações, 12 QA, 14 backup, 15 changelog, 16 handoff) | especialista-documentacao | média | pendente |
| 6 | (paralelo) Guia visual/UX (doc 06) ao iniciar frontend | especialista-design | média | pendente |

---

# 8. Pendências e bloqueios

| Tipo | Descrição | Impacto | Precisa decisão de quem? | Status |
|---|---|---|---|---|
| ~~decisão~~ | Modalidade do MVP → **resolvida: as duas** | Define fluxo e telas | Negócio | resolvida |
| ~~decisão~~ | Identidade do paciente → **resolvida: CPF** | Modelagem de banco | Negócio | resolvida |
| ~~dúvida~~ | Ingestão por API → **resolvida: upload + API** | Escopo MVP | Negócio | resolvida |
| dúvida | Necessidade de PWA offline-first no app in company | Arquitetura frontend | Negócio/Técnico | aberta |
| dúvida | Faturamento por dose faz parte do escopo | Módulo pagamentos | Negócio | aberta |
| dúvida | Auto-elegibilidade B2C: automática ou aprovada pelo B2B | Fluxo/permissões | Negócio | aberta |
| dúvida | Tratamento de paciente estrangeiro sem CPF | Modelagem | Negócio | aberta |

---

# 9. Riscos conhecidos

| Risco | Área | Gravidade | Mitigação | Status |
|---|---|---|---|---|
| Vazamento de dado sensível de saúde entre tenants | segurança/LGPD | crítica | Multi-tenant no backend, criptografia, auditoria | aberto |
| Escopo grande demais entregue de uma vez | produto | alta | Usuário optou por MVP amplo (2 modalidades + upload/API); mitigação = ordem de construção incremental no doc 03 | mitigando |
| Superfície de segurança de APIs públicas (rede + ingestão) na V1 | segurança/integração | alta | Credencial por parceiro, escopo por campanha, rate limit e auditoria já no MVP | aberto |
| Concorrência duplicar vacinado (pagamento em dobro) | integridade/financeiro | crítica | RESOLVIDO: UNIQUE (elegivel,vacina,dose) confirmada + idempotência (mig 014) | mitigado |
| Vazamento de dados entre clientes no paciente global | privacidade | alta | RESOLVIDO: nome/nascimento por elegível (RN-023, mig 014) | mitigado |
| Ingestão síncrona não escala p/ ~100k por lista (500 clientes) | performance | alta | PENDENTE: processar importação assíncrona em lotes (item 3) | aberto |
| Retenção/particionamento de histórico e auditoria em milhões/ano | operação/escala | alta | PENDENTE: política de arquivamento + partição; vacinado perpétuo p/ carteira (item 9) | aberto |
| Sem rate limit real nas APIs (500 clientes podem travar) | segurança/escala | alta | PENDENTE: limitar acessos/consultas por credencial (item 12) | aberto |
| Sem módulo de faturamento (pagar clínica / cobrar cliente) | negócio | alta | ADIADO pelo usuário (item 4) | aberto |
| Estrangeiro sem CPF | cobertura | média | Decidido: usar voucher/identificador (item 8) — a implementar | aberto |
| Sem relatório longitudinal ano a ano / carteira consolidada | produto | média | PENDENTE (itens 9 e 14) | aberto |
| Observabilidade só /health | operação | média | PENDENTE: métricas, alertas, visão de fila (item 13) | aberto |
| Paginação por OFFSET lenta em milhões | performance | média | PENDENTE: keyset/cursor (item 10) | aberto |
| API externa (rede) com escopo mal definido | integração/segurança | alta | Credencial por parceiro, escopo por campanha (RN-009) | aberto |
| Registro de aplicação sem rastreabilidade (lote/dose) | banco/negócio | alta | RN-004 e RN-010 (imutabilidade + retificação auditada) | aberto |

---

# 10. Arquivos e pastas importantes

## Estrutura principal

```txt
/
  README.md
  orquestrador.md
  controle-projeto.md
  /protocols
  /specialty
  /skills
  /docs
```

## Arquivos do projeto real

| Caminho | Função | Observação |
|---|---|---|
| docs/01-visao-geral-projeto.md | Visão geral oficial | preenchido |
| docs/02-briefing-regras-negocio.md | Regras de negócio (RN-001..011) | preenchido, rascunho |
| docs/03-mvp-versoes-roadmap.md | MVP e versões | preenchido, aguarda aprovação |
| docs/04-perfis-permissoes.md | Perfis e matriz de permissões | preenchido, rascunho |
| docs/05-arquitetura-pastas.md | Arquitetura e estrutura de pastas | preenchido |
| docs/07-mapa-telas-fluxos.md | Mapa de telas e fluxos | preenchido |
| docs/08-modelagem-banco-dados.md | Modelagem de banco (13 tabelas + VIEW) | preenchido |
| docs/09-contrato-api-endpoints.md | Contrato de API (interno + parceiro) | preenchido |
| docs/10-seguranca-lgpd-auditoria.md | Segurança, LGPD e auditoria | preenchido |
| database/migrations/000..008 + seeds | SQL real do modelo (doc 08) | escrito, não executado |
| database/README.md | Ordem/uso das migrations | preenchido |
| app/config, app/helpers, app/middlewares, app/bootstrap.php | Fundação do backend PHP | escrito, não executado |
| api/v1/rotas.php, health.php, interno/auth.php | Roteador + endpoints de prova | escrito, não executado |
| public/index.php | Front controller (docroot) | escrito, não executado |
| .env.example, .gitignore | Config de ambiente e exclusões | preenchido |
| Dockerfile, docker/apache/vhost.conf, docker/php/php.ini | Imagem PHP+Apache (docroot public/) | escrito, não executado |
| scripts/migrar.php, scripts/criar_admin.php | Aplicar migrations + criar admin | escrito, não executado |

---

# 11. Banco de dados

```txt
Banco usado: MySQL/MariaDB (a provisionar)
Ambiente atual: nenhum (planejamento)
Host: [a definir]
Administração visual: phpMyAdmin (planejado)
```

## Ambientes

| Ambiente | Banco | Usuário | Observação |
|---|---|---|---|
| desenvolvimento |  |  | a definir |
| homologação |  |  | a definir |
| produção |  |  | a definir |

## Migrations / alterações estruturais

| Data | Migration/SQL | Ambiente | Backup feito? | Status |
|---|---|---|---|---|
| — | nenhuma ainda | — | — | pendente |

---

# 12. Deploy e produção

```txt
Painel: EasyPanel
Servidor: [a definir]
Repositório: https://github.com/rodrigo-cassaro-sa/intermediado-vacinacao-sa (branch main, push feito, commit 7071c6c)
Branch desenvolvimento: [a definir]
Branch homologação: main (proposto para o 1º ambiente)
Branch produção: producao (criar após validar homologação)
Domínio: [a definir]
SSL: [a definir - Let's Encrypt via EasyPanel]
Status do deploy: preparado (Docker + docs/13), aguardando push e configuração no painel
```

## Checklist rápido de produção

```md
- [ ] Código versionado.
- [ ] Branch/tag correta.
- [ ] .env configurado fora do Git.
- [ ] Banco criado.
- [ ] MySQL não exposto publicamente.
- [ ] phpMyAdmin protegido.
- [ ] Volumes persistentes validados.
- [ ] Domínio configurado.
- [ ] SSL funcionando.
- [ ] Backup feito.
- [ ] Rollback definido.
- [ ] Health check funcionando.
- [ ] Logs ativos.
```

---

# 13. Último checkpoint

```txt
Última coisa feita: Docker + deploy EasyPanel (Dockerfile, vhost docroot public/, php.ini,
scripts/migrar.php, docs/13) e Git iniciado (branch main, commit inicial ac5a892).

Estado atual: docs 01-10 + 13, SQL do modelo, backend scaffold e artefatos de deploy prontos.
NADA executado/validado — o EasyPanel será o primeiro ambiente real.
Decisões: 2 modalidades, upload+API, CPF, multi-tenant por tenant_id, API interno/parceiro,
aplicação imutável, JSON oficial + idempotência, auth sessão/CSRF e Bearer com escopo,
Docker php:8.3-apache com docroot public/, health em /api/v1/health.

Próximo passo recomendado: push para GitHub; criar serviços no EasyPanel; deploy; aplicar
migrations (scripts/migrar.php --seeds); validar checklist pós-deploy (doc 13 §10).

Arquivos que devem ser lidos primeiro: controle-projeto.md, docs/13, database/README.md,
Dockerfile, docs/05, docs/09, app/bootstrap.php.

Cuidados antes de continuar: isolamento multi-tenant e escopo por campanha (RN-009);
aplicação nunca é editada (RN-010); LGPD/dado sensível como risco crítico; validar bases legais
e retenções com DPO; resolver dúvidas abertas antes das telas B2C e detalhes finos.

O que não deve ser alterado agora: stack (PHP procedural), multi-tenant por tenant_id,
separação API interno/parceiro, public/ como único docroot.
```

---

# 14. Resumo para próxima IA ou programador

```txt
Contexto rápido: SaaS intermediador de imunização corporativa (B2B2C) com rede credenciada
e in company. Fonte da verdade central, multi-tenant, LGPD reforçada.

O projeto está na etapa: Fase 1 - Briefing (documentado, aguardando aprovação).

Já foi decidido: usar protocolo-criacao-projeto-zero; MVP por 1 modalidade; stack padrão.

Já foi implementado: nada de código. Apenas documentação de planejamento (docs 01-04).

Está pendente: aprovação do briefing e respostas às dúvidas abertas.

Principal risco: vazamento de dado sensível entre tenants (LGPD) e escopo grande demais.

Próxima ação: aprovar briefing e responder decisões pendentes; depois arquitetura (05),
mapa de telas (07) e modelagem de banco (08).
```

---

# 15. Regra de atualização obrigatória

Atualizar este arquivo sempre que houver: nova decisão técnica; mudança de fase; criação de
módulo; alteração de regra de negócio; criação/alteração de tabela; criação/alteração de
endpoint; mudança em permissão; correção de bug relevante; deploy; homologação; incidente;
mudança de domínio, banco, porta ou ambiente; entrega final.

---

# 16. Regra final

```txt
orquestrador.md decide o caminho.
protocolos conduzem o processo.
especialistas analisam.
skills orientam.
controle-projeto.md guarda a memória.
```

Se este arquivo estiver desatualizado, a próxima IA ou programador pode tomar decisão errada.
