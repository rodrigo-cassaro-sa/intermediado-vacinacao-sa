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
ESTADO DO DEPLOY (verificado em 2026-08-07 pelo /health e pelos assets servidos):
homologação está com TUDO até o commit 3a89b3b (BUG-007). Migrations 031 e 032 APLICADAS.
Confirmado no ar: /assets/mensagens.js responde 200, /interno/importacoes-vacinados dá 401
(rota existe) e o csv.js servido já bloqueia cabeçalho não reconhecido.
NÃO está no ar: TL-205 (commit 56445a8) — /interno/elegiveis/vacinacao ainda dá 404.
Pendência de rastreabilidade: APP_VERSION não está configurada no painel, então o /health
responde "versao: dev" e não dá para saber qual commit está rodando sem sondar rotas.

Etapa atual (2026-08-07): TL-205 — vacinação individual (atendimento no posto). O fluxo das
telas exigia escolher a campanha e achar a pessoa na lista; no posto é o contrário: a pessoa
chega e o CPF resolve. Novo GET /interno/elegiveis/vacinacao busca por CPF/voucher/matrícula/
nome em todas as campanhas do escopo e devolve o que a pessoa pode tomar AGORA, mostrando
também os bloqueados com o motivo. O popup reaproveita o formulário de registro que já
existia. Evidência: 19/19 contra banco real; suíte 178/178.

Etapa anterior (2026-08-07): BUG-007 — a 1ª linha é SEMPRE o cabeçalho. Com nome de coluna
errado, a importação caía na leitura posicional e a própria linha de cabeçalho entrava como
registro. REVERTE a decisão que tomei no BUG-004 (avisar em vez de bloquear, para preservar
o import sem cabeçalho): fui verificar a premissa e ela era falsa — a API do parceiro só
aceita JSON, todo CSV vem de upload humano e todo modelo gerado já traz cabeçalho. A
compatibilidade que eu protegia não tinha usuário. Agora bloqueia, com a 1ª linha lida no
detalhe. Evidência: 20/20, suíte 159/159.

Etapa anterior (2026-08-06): BUG-006 — padronização das mensagens de erro das importações.
As frases estavam longas, cada arquivo tinha a sua versão do mesmo problema e nenhuma dizia
o que fazer. Agora há UM catálogo (app/helpers/mensagens_importacao.php + public/assets/
mensagens.js) no padrão "<Problema>. <O que fazer>.", publicado em docs/19 com a tabela
código -> quando acontece -> mensagem -> ação do usuário. A instrução de cabeçalho das telas
de unidades/clientes/grupos foi reescrita ("1ª linha = cabeçalho", colunas e obrigatórias).
Evidência: 12/12 do catálogo, suíte 139/139.

Etapa anterior (2026-08-06): BUG-005 — status da unidade. A coluna nasce 'ativa' (feminino,
como vacina/clinica/campanha) mas a API validava 'ativo' e a tela comparava com 'ativo':
unidade nova sumia do filtro padrão, era pintada como "Inativa" e abria com o campo em
branco. API e tela passaram a falar 'ativa'/'inativa', o "nasce ativa" ficou explícito nos
INSERTs e a migration 032 normaliza o que já estava gravado. Evidência: 17/17 na tela +
ciclo em MySQL real. A tabela de status oficiais do doc 17 estava VAZIA — foi preenchida,
porque era a lacuna que permitiu o descasamento.

Etapa anterior (2026-08-06): BUG-004 — mapeamento de erro e resposta visual das importações.
Cabeçalho errado fazia as linhas sumirem no filtro do frontend e a tela dizia "Cole ao menos
uma linha" para quem tinha colado dezenas; o verde "Concluído" aparecia mesmo com 0 inseridos;
cabeçalho irreconhecível virava registro em silêncio; e o relatório da tentativa anterior
ficava na tela. Regra única em CSV.conferir()/csv_conferir(), semáforo verde/amarelo/vermelho
e relatório mostrando descartadas + como a 1ª linha foi lida. Evidência: 25/25 novos, suíte
completa 108/108.

Etapa anterior (2026-08-05): RN-031 — vacinar pelo admin/portal e importar vacinados em massa.
Descoberta: as duas telas JÁ registravam vacinação, mas o backend travava por 'perfil' e dava
403 no portal (botão existia, ação não funcionava). O gate passou a ser o acesso ao paciente.
Novo: importação de vacinados por CSV em campanha ativa, com SIMULAÇÃO obrigatória antes de
gravar e estorno do lote inteiro (interno + justificativa). Migration 031.
Evidência: 27/27 casos contra banco real.

Etapa anterior (2026-08-05): correção BUG-003 — importação de listas grandes. Acima de
~2000 linhas o lote ia para a fila e dependia de um Cron Job do EasyPanel que nunca foi
criado: ficava em 'pendente' para sempre (parecia um teto de ~1500). O worker passou a
rodar dentro do container (docker/entrypoint.sh), com lock, recuperação de importação
travada e progresso acompanhado na tela. Evidência: container normal, sem cron nenhum,
drenou 30.000 linhas de ponta a ponta.

Etapa anterior (2026-08-05): correção BUG-002 — identidade da campanha. O console.html
ainda exibia campanha.nome cru (null desde a migration 026) na tabela, nos 5 dropdowns,
no resumo do cliente e na carteira. Passou a usar o CÓDIGO, com o nome como rótulo
secundário. 4 endpoints que entregavam a campanha só pelo nome agora mandam o código.
Evidência: SQL contra o schema real (31 migrations em MySQL 8) + 16/16 no teste do rótulo.

Etapa anterior (2026-08-05): correção BUG-001 — leitura de CSV. Toda importação passou a
tratar a 1ª linha como CABEÇALHO e a mapear as colunas POR NOME (a ordem deixou de
importar). Leitor único: app/helpers/csv.php (backend) + public/assets/csv.js (telas).
Evidência: php scripts/testar_csv.php (37/37) e espelho JS (35/35). Aguarda deploy e
validação nas telas em homologação.

Etapa anterior: MVP + robustez + faturamento. Migrations até 018. Todos os gaps da análise resolvidos (concorrência, idempotência, isolamento, voucher, ingestão assíncrona+relatório de erros, rate limit, carteira/relatório anual, observabilidade, keyset, faturamento). Aguardando deploy das migrations 015-018. Próximo natural: telas reais / preparar produção.

Etapa anterior: MVP FUNCIONAL COMPLETO validado em homologação. Ambas modalidades (in_company + rede credenciada com isolamento por clínica RN-012), elegíveis (upload/JSON/API), aplicação, tabela verdade/dashboard e extração CSV — tudo via /admin em https://imunizacao-imz-app.imx7lc.easypanel.host. Próximo: telas reais (sair do console) e/ou preparar produção.
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
| Banco de dados | validado (homolog) | migrations 000..014 aplicadas (014 = UNIQUE vacinado confirmado [coluna VIRTUAL], idempotencia, elegivel.nome/nascimento). migrar.php tolerante a "já existe". Lição: coluna gerada STORED força cópia da tabela e quebra com FK própria (erro 1215) → usar VIRTUAL + FOREIGN_KEY_CHECKS off. |
| Backend/API/PHP | validado (homolog) | blocos 1, 2 e 3 VALIDADOS em homolog via /admin (cliente→campanha→elegíveis→aplicação→tabela verdade/dashboard). Falta: extração CSV, rede credenciada testada com clínica real, refino. |
| Frontend | em andamento | public/admin/index.html: console de testes (login/health/clientes/campanhas) via Fetch; validar no deploy |
| Segurança/auditoria | em andamento | doc 10 preenchido (auth, escopo, auditoria, LGPD, criptografia); implementação nos middlewares pendente |
| QA/testes | em andamento | doc 12 preenchido para a importação de CSV; `scripts/testar_csv.php` roda sem banco e sai 1 se falhar (serve de check pós-deploy) |
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
| 2026-08-05 | CSV: cabeçalho na 1ª linha manda; colunas mapeadas por NOME, não por posição | BUG-001: cabeçalho entrava como registro e o BOM do Excel quebrava a detecção | Contrato de importação (doc 09 §3.1); ordem das colunas deixou de importar | Orquestrador |
| 2026-08-05 | Um leitor de CSV só: `app/helpers/csv.php` + espelho `public/assets/csv.js` | Havia 8 parsers duplicados (2 PHP + 6 JS), cada um com uma regra diferente | Qualquer ajuste futuro de coluna é feito em 2 lugares, não em 8 | Orquestrador |
| 2026-08-07 | A busca da vacinação individual ORIENTA; quem valida a dose continua sendo POST /aplicacoes | Duplicar RN-003/013/019 no caminho da busca criaria duas fontes da mesma regra, que divergem com o tempo | `pode_vacinar` é dica de tela; `validar_aplicacao` decide | Orquestrador |
| 2026-08-07 | Resultado mostra também quem NÃO pode ser vacinado, com o motivo | Sumir com a pessoa faz o operador achar que ela não está cadastrada e abrir chamado | Usa os códigos do catálogo do doc 19 | Orquestrador |
| 2026-08-07 | A 1ª linha é SEMPRE o cabeçalho; nome de coluna não reconhecido BLOQUEIA a importação | BUG-007, decisão do usuário. Reverte o BUG-004, onde eu escolhi avisar para não quebrar o import posicional — premissa que se mostrou falsa (parceiro só manda JSON; CSV é sempre upload humano com modelo cabeçalhado) | Contrato do doc 09 alterado; `csv_parsear` mantém o fallback, mas nenhum ponto de entrada o alcança | Usuário |
| 2026-08-07 | A regra é garantida por teste que enumera os pontos de entrada de CSV | Regra centralizada só vale se não houver porta lateral; havia duas (histórico e console) | 9 pontos conferidos automaticamente | Orquestrador |
| 2026-08-06 | Mensagem de erro tem catálogo único; o CÓDIGO é contrato, a MENSAGEM é reescrevível | BUG-006. Frase montada em cada arquivo produzia três textos para o mesmo problema. Integrações leem o código (vai no CSV de erros), então ele não pode mudar | docs/19 é a fonte oficial; teste falha se PHP, JS e doc divergirem | Orquestrador |
| 2026-08-06 | Detalhe técnico ("Reconheci… Esperado…") sai da mensagem e vai para o campo `detalhe` | Mantém a frase principal curta sem perder a informação de diagnóstico | `csv_conferir`/`CSV.conferir` devolvem `codigo`, `erro` e `detalhe` | Orquestrador |
| 2026-08-06 | Status concorda em GÊNERO com a entidade: unidade/vacina/clinica/campanha = `ativa`; cliente/usuario/grupo = `ativo` | BUG-005. O banco já estava certo; API e tela é que divergiam. Mudar o banco quebraria dados existentes e as outras entidades femininas | Convenção registrada no doc 17 §2 (a tabela estava vazia) | Orquestrador |
| 2026-08-06 | API de unidade aceita as duas grafias e normaliza para a feminina | Durante o deploy, aba aberta com o JS antigo em cache ainda envia 'ativo' | Tolerância na entrada, vocabulário único no banco | Orquestrador |
| 2026-08-06 | Cabeçalho reconhecido sem coluna obrigatória = erro que BLOQUEIA; cabeçalho irreconhecível = só aviso | BUG-004. Bloquear o segundo caso quebraria a leitura posicional que o BUG-001 preservou de propósito e que já está em uso | `CSV.conferir()`/`csv_conferir()` com `obrigatorias` por tipo | Orquestrador |
| 2026-08-06 | Semáforo da importação: verde só quando algo entrou sem ressalva; amarelo com ressalva; vermelho quando nada entrou | O verde incondicional era o que fazia lixo passar despercebido | Nova classe `.msg.warn` nas 5 telas de importação | Orquestrador |
| 2026-08-05 | Quem tem acesso aos dados do paciente pode registrar a vacinação dele (com log) | Decisão do usuário. O gate por 'perfil' dava 403 no portal, que exibia o botão mas não executava | Portal passa a vacinar de fato; desfazer segue interno | Usuário |
| 2026-08-05 | Importar vacinados em massa SEMPRE simula antes de gravar (RN-031) | Decisão do usuário. Dose aplicada é dose faturada: arquivo errado com 5.000 linhas = fatura errada | Duas etapas na tela e na API (importar -> confirmar) | Usuário |
| 2026-08-05 | CPF fora da lista: perguntar se cria o elegível; criando, RN-016/RN-018 continuam valendo | Decisão do usuário. Criar sem tipo de vínculo/lotação/matrícula geraria base suja e furaria a regra da lista | Arquivo precisa das colunas quando a opção é marcada; resolve a pendência do "elegível tardio" | Usuário + Orquestrador |
| 2026-08-05 | Estorno em lote: super_admin e operador_interno, sempre com justificativa | Decisão do usuário | `POST /importacoes-vacinados/{id}/estornar`; vínculo `aplicacao.importacao_aplicacoes_id` | Usuário |
| 2026-08-05 | Vacina no arquivo aceita nome OU sigla | Decisão do usuário | Resolvida dentro da campanha; não cria vacina no catálogo | Usuário |
| 2026-08-05 | Cliente enxerga o histórico de importações da campanha (confere o que enviou), mas não estorna | Decisão do usuário, confirmando o risco financeiro. Sem a listagem o "só o interno estorna" era inoperante: o operador não tinha como achar o lote importado pelo cliente | `GET /campanhas/{id}/importacoes-vacinados`; link de estornar só no admin | Usuário |
| 2026-08-05 | Validação extraída para `validar_aplicacao()`, compartilhada por gravação e simulação | Simulação com regra duplicada prometeria o que a gravação recusa — foi o que o teste pegou | Fonte única de RN-003/013/019 | Orquestrador |
| 2026-08-05 | Worker da fila passa a rodar DENTRO do container, não como Cron Job manual do painel | BUG-003: o cron nunca foi criado e toda importação grande morria em 'pendente'. Deploy que depende de alguém lembrar de 3 crons é frágil | `docker/entrypoint.sh` sobe o loop; `WORKERS_EMBUTIDOS=false` volta ao modelo antigo | Orquestrador |
| 2026-08-05 | Manter `IMPORTACAO_LIMITE_SINCRONO` em 2000 | Aumentar jogaria lote grande para dentro da requisição web (60s de max_execution_time). O caminho assíncrono é o certo — só precisava funcionar | Lotes grandes seguem assíncronos, agora com progresso na tela | Orquestrador |
| 2026-08-05 | Campanha se identifica pelo `codigo`; `nome` é rótulo humano opcional e nunca aparece sozinho | BUG-002: nome virou NULL na migration 026 e o console exibia "null" | Regra de exibição `codigo \|\| nome \|\| ('#' + id)` vale para toda a UI (doc 09 §3) | Orquestrador |
| 2026-08-05 | `/parceiro/carteira` passa a devolver o código no campo `campanha` (+ novo `campanha_codigo`) | O campo já vinha null desde a 026; consumidores externos não tinham como identificar a campanha | Contrato público v1 ajustado no doc 09 §3.9 — campo mantido, conteúdo corrigido | Orquestrador |
| 2026-08-05 | Arquivo SEM cabeçalho continua lido pela ordem padrão | Não quebrar quem já importa assim; cabeçalho só é consumido com 2+ colunas reconhecidas | Compatibilidade preservada | Orquestrador |

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
| 2026-08-07 | TL-205: vacinação individual por busca de CPF/voucher/nome, com popup e formulário contextualizado | api/v1/interno/vacinacao_individual.php (novo), api/v1/rotas.php, public/{admin,portal}/vacinados.html, docs/07, docs/09, docs/12 | 19/19 contra banco real; suíte 178/178 | Sem migration. Rota literal posicionada antes das que usam {id} |
| 2026-08-07 | BUG-007 corrigido: cabeçalho com nomes errados entrava como registro | public/assets/csv.js, app/helpers/csv.php, os 2 catálogos, api/v1/interno/vacinados_historico.php, public/admin/console.html, 5 telas (comentários), docs/09, docs/12, docs/19 | 20/20 + suíte 159/159; teste prova que os 9 pontos de entrada conferem | Sem migration. Mudança de contrato documentada no doc 09 |
| 2026-08-06 | BUG-006 corrigido: mensagens de erro das importações padronizadas em catálogo único, com documento oficial | app/helpers/mensagens_importacao.php (novo), public/assets/mensagens.js (novo), docs/19 (novo), app/helpers/csv.php, public/assets/csv.js, api/v1/interno/{importacoes,importacao_vacinados}.php, 6 telas, docs/README, docs/12 | 12/12 do catálogo; suíte 139/139 | Instrução de cabeçalho reescrita em unidades/clientes/grupos |
| 2026-08-06 | BUG-005 corrigido: unidade só aparecia no filtro "Todas", exibida como "Inativa" e sem status na edição (banco fala `ativa`, API e tela falavam `ativo`) | api/v1/interno/acesso.php, public/admin/unidades.html, database/migrations/032 (nova), docs/12, docs/17 | 17/17 na tela + ciclo em MySQL real (base suja → 032 → idempotente); suíte 125/125 | Migration 032 normaliza dados já gravados; grupo/cliente (masculinos) não foram tocados |
| 2026-08-06 | BUG-004 corrigido: mensagem de erro mentia ("cole ao menos uma linha" com dezenas coladas), verde incondicional escondia importação vazia/errada, linhas descartadas em silêncio e relatório fantasma | public/assets/csv.js, app/helpers/csv.php, public/admin/{unidades,clientes,grupos,elegiveis}.html, public/portal/elegiveis.html, api/v1/interno/{elegiveis,importacao_vacinados}.php, docs/09, docs/12 | 25/25 novos casos + suíte completa 108/108; `php -l` limpo; JS das 5 telas validado | Leitura posicional (BUG-001) preservada: recebe aviso, não bloqueio |
| 2026-08-05 | RN-031: vacinar pelo admin/portal (gate corrigido) + importar vacinados em massa com simulação e estorno de lote | database/migrations/031, app/services/importacao_aplicacoes.php (novo), app/services/aplicacoes.php, api/v1/interno/importacao_vacinados.php (novo), api/v1/interno/aplicacoes.php, api/v1/rotas.php, app/helpers/csv.php, scripts/processar_importacoes.php, public/{admin,portal}/vacinados.html, docs/02/04/09/12 | 27/27 casos contra banco real; `php -l` limpo; JS das 2 telas validado | Migration 031 pendente de deploy |
| 2026-08-05 | BUG-003 corrigido: importação acima de ~2000 linhas nunca era processada (fila sem worker rodando) | docker/entrypoint.sh, scripts/processar_importacoes.php, public/admin/elegiveis.html, public/portal/elegiveis.html, docs/12, docs/13 | Container normal, sem cron, drenou 30.000 linhas em 285s (0 → 29.999 elegíveis); trava e recuperação validadas | Medições: 30k = 830 ms de parse e 36,6 MB de pico; 100k = 120,7 MB. `max_execution_time` do php.ini não afeta o CLI (é 0) |
| 2026-08-05 | BUG-002 corrigido: campanha aparecia `null` (console.html usava `nome`, opcional desde a mig 026, em vez de `codigo`) | public/admin/console.html, api/v1/interno/relatorios.php, api/v1/interno/faturamento.php, api/v1/parceiro/consulta.php, docs/09, docs/12 | SQL no schema real: `campanha_antes = NULL` → `campanha_depois = IFT.2026.IC.GTE.CTE.1`; 16/16 no teste do rótulo; `php -l` limpo | Novo helper `rotuloCampanha()` no console; campanhas antigas sem código continuam pelo nome |
| 2026-08-05 | BUG-001 corrigido: cabeçalho do CSV virava registro e colunas eram lidas por posição | app/helpers/csv.php (novo), public/assets/csv.js (novo), scripts/testar_csv.php (novo), app/services/{elegiveis,historico_import,importacao}.php, app/bootstrap.php, scripts/processar_importacoes.php, public/{admin,portal}/*.html (6 telas), docs/09, docs/12 | `php scripts/testar_csv.php` = 37/37; espelho JS = 35/35; `php -l` limpo | Afetava as 6 telas de importação; nenhuma regra de negócio/banco/permissão foi tocada |

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
| 8i | Batch 1 robustez (concorrência/idempotência/isolamento/titular/remoção — RN-013 rev, RN-023..026) — migration 014 APLICADA em homolog | usuário/deploy | alta | feito |
| 9a | Ingestão assíncrona em lotes + relatório de erros ao cliente (commit c98204b) — migration 015 auto; requer cron do worker | usuário/deploy | alta | em andamento |
| 9b | Rate limit por credencial + login (commit 9048c1b) — migration 016 auto | usuário/deploy | alta | feito |
| 9c | Vacinado perpétuo/carteira consolidada + relatório ano a ano (commit 695865c) — sem migration | usuário/deploy | média | feito |
| 9d-8 | Voucher p/ estrangeiro sem CPF (commit fab47d4) — migration 017 auto | usuário/deploy | média | feito |
| 13 | Observabilidade: health+versão, /metricas, /auditoria (commit 6d682b9) | usuário/deploy | média | feito |
| 10 | keyset pagination (commit 8976e5d) | usuário/deploy | baixa | feito |
| 4 | Faturamento: preços [cliente,modalidade,vacina] e [clínica,vacina] + relatórios a cobrar/a pagar (commit 356a72a) — migration 018 auto | usuário/deploy | alta | feito |
| INT-A | Fundação integração: webhooks de saída (assinatura+entrega+worker+HMAC+retry) + admin (commit 63d6429) — migration 019 auto; cron do worker | usuário/deploy | alta | feito |
| INT-A3 | API externa formalizada (contrato v1) + carteira/consulta por token 'consulta' (commit 51656eb) — sem migration | usuário/deploy | alta | feito |
| INT-B | Sync de turnover por diferença (RH) — CSV + API (commit 0d9d369) — migration 020 auto | usuário/deploy | média | feito |
| INT-C | Token de app in company (PWA/app/terceiro) (commit 00a5bfc) — sem migration; PWA offline opcional fica p/ frente | usuário/deploy | média | feito |
| PORTAL-D0 | Modelo de acesso: grupo_empresarial, unidade, usuario_atribuicao (níveis grupo/negocio/local + multi-atribuição, upper→lower) + resolução de permissão + gestão de usuários. Base de TUDO no portal (doc 04 §4.1) (commit e201ac4, mig 021) | especialista-seguranca/backend | alta | feito |
| PORTAL-D1 | Shell + login + consentimento LGPD + onboarding/assistente (commit b0ff665, mig 023) — em public/portal | usuário/deploy | alta | feito |
| PORTAL-D2a | Aplicar escopo hierárquico nas leituras/escritas (campanhas/clientes/elegíveis/tabela verdade/dashboard/export) + unidade por lotação (commit 7029e88, mig 022) — fecha isolamento | usuário/deploy | alta | feito |
| PORTAL-D2b | Fluxos do portal: dashboard, elegíveis (importar/sincronizar/listar/remover + relatório de erros), vacinados (tabela verdade/extrair), usuários (listar/criar no escopo) (commit f2b48fe) | usuário/deploy | alta | feito |
| PORTAL-D3 | Painel avançado: doc de API, gerar tokens (self-service escopado), registrar webhooks, guia Power BI/automação — aba Integrações no portal; credenciais.php/webhooks.php reforçados p/ escopo (usuario_pode_cliente/titular gerido), portal não emite rede_credenciada | backend/frontend | média | feito |
| PORTAL-AUD | Auditoria no portal (o quê/quem/quando) escopada aos clientes geridos — GET /interno/portal/auditoria (join nome do ator, metadata mascarada) + aba Auditoria; eventos elegivel.editado/situacao_definida passaram a gravar tenant_id | backend/frontend | média | feito |
| MARCA | Identidade visual "S&A Imunizações" (tema claro azul+verde; public/assets/marca.css + favicon; portal e admin) | frontend | média | feito |
| HIST-IMPORT | Importar vacinados de anos anteriores (RN-027, mig 024): auto-cria campanha modalidade 'historico' por cliente/vacina/ano; app/services/historico_import.php; POST /interno/clientes/{id}/vacinados-historico/importar (interno-only, tolera lote/prof/cidade ausentes, aceita data AAAA-MM-DD ou só o ano); console admin §10b | backend/frontend | média | feito |
| HIST-IMPORT+ | Ajustes: (a) auto-cria vacina no catálogo se não existir (nome normalizado) — retorna vacinas_criadas; (b) ASSÍNCRONO p/ lotes >2000 (mig 025 importacao_historico + worker em processar_importacoes.php) com status GET /interno/importacoes-historico/{id}; inline p/ lotes pequenos | backend/frontend | média | feito |
| BUG-001 | CSV: 1ª linha = cabeçalho e mapeamento por nome da coluna em todas as importações (leitor único csv.php/csv.js) | QA/backend/frontend | alta | feito e NO AR |
| BUG-002 | Campanha identificada pelo `codigo` no console e nos endpoints de carteira/resumo/faturamento | QA/frontend/backend | média | feito e NO AR |
| BUG-003 | Worker da fila embutido no container + progresso da importação na tela (listas de 20k–30k) | deploy/backend/frontend | alta | feito e NO AR — confirmar que storage/uploads é volume persistente |
| V2 | Autoadesão B2C (consentimento) + venda de voucher (pagamento) | — | baixa | pendente |
| RN-031 | Vacinar pelo portal/admin + importar vacinados em massa (simulação obrigatória, estorno de lote) | backend/frontend/QA | alta | feito e NO AR (migration 031 aplicada) — falta validar nas telas |
| Banco: migrations até 032 | — | — | — | **031 e 032 APLICADAS em homologação (2026-08-07)**. 026 código de campanha · 027..030 · 031 importação de vacinados em massa · 032 normaliza unidade.status |
| Banco: migrations até 025 | — | — | — | 023 consentimento · 024 import histórico · 025 fila import histórico |
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
| Ingestão síncrona não escala p/ ~100k por lista (500 clientes) | performance | alta | RESOLVIDO: importação assíncrona em chunks + worker cron + relatório de erros (mig 015, commit c98204b) | mitigado |
| Retenção/particionamento de histórico e auditoria em milhões/ano | operação/escala | alta | PENDENTE: política de arquivamento + partição; vacinado perpétuo p/ carteira (item 9) | aberto |
| Sem rate limit real nas APIs (500 clientes podem travar) | segurança/escala | alta | RESOLVIDO: rate limit por credencial + login (mig 016, commit 9048c1b) | mitigado |
| Sem módulo de faturamento (pagar clínica / cobrar cliente) | negócio | alta | RESOLVIDO: preços + relatórios a cobrar/a pagar (mig 018, commit 356a72a) | mitigado |
| Estrangeiro sem CPF | cobertura | média | RESOLVIDO: identidade por voucher/identificador (mig 017, commit fab47d4) | mitigado |
| Sem relatório longitudinal ano a ano / carteira consolidada | produto | média | RESOLVIDO: carteira por CPF + resumo ano a ano (commit 695865c) | mitigado |
| Observabilidade só /health | operação | média | RESOLVIDO: health+versão, /metricas, /auditoria, doc 14 (commit 6d682b9) | mitigado |
| Sem webhooks de saída / painel de integrações (função 7) | integração | alta | RESOLVIDO: webhooks de saída + painel (Fase A, commit 63d6429) | mitigado |
| API externa sem contrato publicado/versão (funções 3/5/8) | integração | média | RESOLVIDO: contrato v1 (doc 09 §3.9) + token consulta (commit 51656eb) | mitigado |
| Portal antes da fundação de integração = retrabalho | produto/risco | alta | RESOLVIDO: Fase A/B/C prontas | mitigado |
| Portal exige hierarquia/escopos (grupo/negocio/local) + multi-atribuição inexistente no modelo | segurança/produto | alta | RESOLVIDO: D0 (modelo, commit e201ac4) + D2a (escopo aplicado nas queries, commit 7029e88) | mitigado |
| Self-service de tokens/webhooks pelo cliente (hoje só interno) | segurança | média | RESOLVIDO: D3 expõe emissão/gestão escopada ao próprio tenant (credenciais.php/webhooks.php validam usuario_pode_cliente; portal bloqueia rede_credenciada) | mitigado |
| Paginação por OFFSET lenta em milhões | performance | média | RESOLVIDO: keyset/cursor em elegíveis e tabela verdade (commit 8976e5d) | mitigado |
| API externa (rede) com escopo mal definido | integração/segurança | alta | Credencial por parceiro, escopo por campanha (RN-009) | aberto |
| Registro de aplicação sem rastreabilidade (lote/dose) | banco/negócio | alta | RN-004 e RN-010 (imutabilidade + retificação auditada) | aberto |
| Cabeçalho do CSV importado como registro / colunas lidas por posição | integridade de dados | alta | RESOLVIDO: leitor único com cabeçalho por nome (app/helpers/csv.php + public/assets/csv.js, teste em scripts/testar_csv.php) | mitigado |
| Lixo já gravado por importações anteriores (linhas "cpf/nome/razao_social" viradas registro) | dados | média | PENDENTE: varrer elegíveis/unidades/clientes/grupos em homolog e produção e remover os registros criados a partir de cabeçalho | aberto |
| Fila de importação dependendo de Cron Job criado à mão no painel | operação | alta | RESOLVIDO: worker embutido no `docker/entrypoint.sh`, com lock e recuperação de importação travada (BUG-003) | mitigado |
| `AUTO_MIGRAR=false` no `.env` NÃO trava migration automática | deploy/banco | média | O entrypoint é shell e não lê o `.env` (lido pelo PHP). Se em produção alguém confiar nisso, a migration roda assim mesmo no deploy. Documentado no `.env.example` §2 e no doc 13 §3.1 — precisa ser definida no painel do EasyPanel | mitigado (documentado) |
| APP_VERSION não configurada: /health responde "versao: dev" e não identifica o commit publicado | operação/rastreabilidade | média | PENDENTE: definir APP_VERSION no EasyPanel a cada deploy. Hoje só dá para saber o que está no ar sondando rotas e assets | aberto |
| `storage/uploads` sem volume persistente derruba importação grande na fila | operação/dados | alta | PENDENTE DE CONFIRMAÇÃO: o arquivo do lote mora lá; redeploy no meio do processamento faz a importação virar `falha`. Checklist reforçado no doc 13 §6 | aberto |
| Importação de vacinados errada gera faturamento errado | financeiro | alta | MITIGADO: simulação obrigatória + estorno do lote por `importacao_aplicacoes_id` + histórico dos lotes na tela, para o interno achar e desfazer o que o cliente importou. Decisão: estornar é só do interno; o cliente confere mas não desfaz | mitigado |
| Ingestão linha a linha: 30k levou 285s (~105 linhas/s) | performance | baixa | Aceitável em segundo plano com progresso na tela. Se um dia precisar de 100k+ em minutos, avaliar INSERT em lote / dedup por consulta única antes do loop | aberto |
| Telas ainda exibindo `campanha.nome` cru (null desde a mig 026) | UX/dados | média | RESOLVIDO no console.html (BUG-002); as demais telas já usavam `codigo \|\| nome \|\| '#id'`. Regra registrada no doc 09 §3 para telas novas | mitigado |
| Cabeçalho com nomes totalmente fora do padrão (nenhum sinônimo reconhecido) cai na leitura posicional | importação | baixa | Lista de sinônimos documentada no doc 09 §3.1; ampliar conforme aparecerem arquivos reais de clientes | aberto |

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
| app/helpers/csv.php | Leitor canônico de CSV do backend (cabeçalho por nome) | usado por todas as importações |
| public/assets/csv.js | Espelho do leitor no frontend (`CSV.parsear(texto, 'elegiveis')`) | usado pelas 6 telas de importação |
| scripts/testar_csv.php | Teste do leitor de CSV (37 casos, sem banco) | rodar após deploy: `php scripts/testar_csv.php` |

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
DEPLOY: homologação está no commit 3a89b3b (BUG-007), com as migrations 031 e 032 já
aplicadas. Só a TL-205 (56445a8) ainda não subiu. Verificação feita por sondagem de rotas e
assets porque APP_VERSION não está configurada — configure-a no painel para o /health passar
a dizer o commit publicado (rastreabilidade de produção, orquestrador §10).

Última coisa feita (2026-08-07): TL-205 — vacinação individual. Popup de atendimento nas
telas de vacinados do admin e do portal: digita CPF/voucher/matrícula/nome, o sistema acha a
pessoa em todas as campanhas do escopo, diz o que ela pode tomar (e por que não, quando for o
caso) e abre o formulário já contextualizado. Depois de salvar volta para a fila.

Coisa feita antes (2026-08-07): BUG-007 — cabeçalho obrigatório na 1ª linha de toda
importação. Fechadas duas portas laterais que liam CSV sem conferência (histórico e console).
ATENÇÃO: mudança de contrato — CSV sem cabeçalho, antes aceito, agora é recusado. Nenhuma
integração afetada (parceiro usa JSON), mas se alguém tiver rotina que sobe arquivo sem
cabeçalho, ela para de funcionar.

Coisa feita antes (2026-08-06): BUG-006 — catálogo único das mensagens de erro das
importações, publicado em docs/19 (código -> quando acontece -> mensagem -> ação). Padrão
"<Problema>. <O que fazer>.", até 120 caracteres, sem jargão. O detalhe técnico saiu da
frase e virou campo `detalhe`. A instrução de cabeçalho das telas foi reescrita.
OBS: sobre "erro na importação de unidade", testei os cabeçalhos (modelo, sinônimos, BOM,
linha em branco no início, sem cabeçalho) e todos passam — o que estava ruim ali era o
TEXTO da instrução, não o parser. Se reaparecer, pedir o arquivo exato ao usuário.

Coisa feita antes (2026-08-06): BUG-005 — status da unidade alinhado ao vocabulário do
banco (`ativa`/`inativa`), regra "nasce ativa" explícita nos INSERTs e migration 032
normalizando o que já estava gravado. A tabela de status oficiais do doc 17 §2 foi
preenchida — estava vazia, e era a lacuna que deixou API e banco divergirem.

Coisa feita antes (2026-08-06): BUG-004 — mapeamento de erro e resposta visual das
importações (unidades, clientes, grupos, elegíveis e vacinados). A conferência do cabeçalho
virou regra única (CSV.conferir no frontend, csv_conferir no backend), o semáforo da tela
passou a ter amarelo, o relatório mostra o que foi descartado e como a 1ª linha foi lida, e
o relatório antigo é limpo a cada tentativa. Nada de banco: só frontend + 2 validações de API.

Coisa feita antes (2026-08-05): RN-031 — vacinar pelo admin/portal e importar vacinados em
massa. O gate de POST /aplicacoes deixou de ser por 'perfil' (que dava 403 no portal) e passou
a ser o acesso ao paciente na campanha; desfazer continua interno. Nova importação por CSV em
campanha ativa: simulação obrigatória, relatório de erros linha a linha, opção de criar o
elegível ausente (obedecendo RN-016/RN-018) e estorno do lote inteiro com justificativa.

AÇÃO NECESSÁRIA NO DEPLOY: aplicar a migration 031 (o AUTO_MIGRAR do entrypoint faz sozinho).
Ela cria importacao_aplicacoes/importacao_aplicacao_erro e adiciona
aplicacao.importacao_aplicacoes_id — sem essa coluna, registrar aplicação quebra.

Coisa feita antes (2026-08-05): correção do BUG-003 — importação de listas grandes.
Acima de ~2000 linhas o lote vai para uma fila; o worker que a processa dependia de um
Cron Job do EasyPanel que nunca foi criado, então toda lista grande morria em 'pendente'
(o cliente via "processando em segundo plano" para sempre e achava que havia um teto de
~1500). O worker passou a rodar dentro do container pelo entrypoint, com flock, com
recuperação de importação travada em 'processando' e com progresso acompanhado pela tela.

AÇÃO NECESSÁRIA NO DEPLOY: redeploy da imagem (o entrypoint mudou) e CONFIRMAR que
/var/www/html/storage/uploads é volume persistente — o arquivo do lote mora lá.
Se preferir manter os Cron Jobs do painel, use WORKERS_EMBUTIDOS=false.

Coisa feita antes (2026-08-05): correção do BUG-002. Campanha é identificada pelo
CÓDIGO (migration 026) em toda referência; `nome` é rótulo humano opcional e nunca
aparece sozinho. Corrigido no console.html (tabela, 5 dropdowns, resumo do cliente,
carteira e faturamento) e em 4 endpoints (carteira interna e de parceiro, resumo
ano a ano, faturamento cliente/clínicas). ATENÇÃO: `/parceiro/carteira` é contrato
público v1 — o campo `campanha` mudou de nome para código (já vinha null) e ganhou
`campanha_codigo`; avisar quem já consome.

Coisa feita antes (2026-08-05): correção do BUG-001 na leitura de CSV. A 1ª linha passou
a ser cabeçalho de verdade e as colunas são mapeadas pelo NOME (ordem indiferente), em
todas as importações: elegíveis (admin/portal/console), vacinados históricos, unidades,
clientes e grupos. Foram substituídos 8 parsers duplicados por dois: app/helpers/csv.php
e public/assets/csv.js. Evidência: php scripts/testar_csv.php (37/37) + espelho JS (35/35).
Pendente: deploy e conferir as 6 telas em homologação; depois varrer a base à procura de
registros lixo criados por importações antigas (linhas com "cpf"/"nome"/"razao_social").

OBS DE PROCESSO: a pasta protocolos/ não existe no repositório — protocolo-correcao-bug.md
foi seguido pela lógica descrita no orquestrador.md (seção "Protocolos disponíveis").
Vale materializar os protocolos como arquivos.

Coisa feita antes: Docker + deploy EasyPanel (Dockerfile, vhost docroot public/, php.ini,
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
