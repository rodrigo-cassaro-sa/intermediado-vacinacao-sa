# README — Biblioteca de Skills para IA de Desenvolvimento

## Objetivo deste README

Este README explica a função de cada skill da biblioteca, quando usar, quando não usar e como cada uma ajuda o `orquestrador.md` a escolher o caminho correto.

Use este arquivo como **catálogo de referência rápida**.

Use o `orquestrador.md` como **regra de decisão principal**.

```txt
README.md = catálogo das skills
orquestrador.md = decide quais skills usar, em qual ordem e com quais limites
skill-*.md = regra detalhada de cada assunto
```

---

## Como a IA deve usar esta biblioteca

Antes de executar qualquer tarefa, a IA deve:

1. ler o pedido do usuário;
2. identificar o tipo de tarefa;
3. consultar o `orquestrador.md`;
4. escolher a skill principal;
5. escolher skills de apoio;
6. consultar este README para confirmar função, uso e limite;
7. aplicar somente as skills necessárias;
8. evitar carregar todas as skills sem motivo.

---

## Regra principal

```txt
A skill mais específica decide o detalhe.
A skill mais ampla orienta o contexto.
O orquestrador decide a ordem.
O README explica a função.
```

Exemplo:

```txt
Pedido: criar endpoint de cadastro de cliente.

Skill principal:
- skill-api-rest.md

Skills de apoio:
- skill-backend.md
- skill-php.md
- skill-mysql.md
- skill-seguranca.md
- skill-erros-excecoes.md
- skill-qa.md
```

---

# Estrutura geral dos grupos

```txt
GRUPO 01 — Produto e Planejamento
GRUPO 02 — Design e Interface
GRUPO 03 — Frontend Técnico
GRUPO 04 — Backend, API e PHP
GRUPO 05 — Banco de Dados
GRUPO 06 — Segurança e Auditoria
GRUPO 07 — Negócio e SaaS
GRUPO 08 — Engajamento e Integrações
GRUPO 09 — Qualidade e Manutenção
GRUPO 10 — Deploy e Produção
```

---

# GRUPO 01 — Produto e Planejamento

Use este grupo no começo de projeto novo, módulo novo ou grande alteração funcional.

## `skill-briefing.md`

**Função:** levantar e organizar o entendimento inicial do projeto, problema, público, objetivo, regra de negócio, escopo, MVP e versões futuras.

**Quando usar:**

- projeto novo;
- módulo novo;
- funcionalidade grande;
- regra de negócio ainda confusa;
- necessidade de definir MVP, versões ou prioridades;
- antes de arquitetura, telas, banco e desenvolvimento.

**Quando não usar:**

- correção pequena de bug;
- ajuste visual simples;
- mudança técnica já bem definida;
- deploy;
- refatoração sem alteração funcional.

**Ajuda o orquestrador a decidir:**

- qual é o objetivo do projeto;
- quais regras existem;
- quais áreas precisam de skill específica;
- quais dúvidas precisam ser resolvidas antes de programar.

---

## `skill-perfis-permissoes.md`

**Função:** definir perfis, papéis, níveis de acesso, escopos, ações permitidas e matriz de permissões.

**Quando usar:**

- login com diferentes tipos de usuário;
- admin, gestor, operador, cliente, aluno ou usuário comum;
- sistema com permissões por tela, botão, ação, API ou relatório;
- SaaS com tenant, workspace ou unidade;
- qualquer ação crítica que precise de autorização.

**Quando não usar:**

- tela pública sem login;
- landing page simples;
- protótipo sem controle de acesso;
- regra de negócio que não depende de usuário ou perfil.

**Ajuda o orquestrador a decidir:**

- quem pode fazer cada ação;
- quais telas/endpoints exigem autorização;
- onde permissões afetam banco, backend, frontend e logs.

---

## `skill-arquitetura.md`

**Função:** definir a organização técnica do projeto, camadas, pastas, módulos, fluxo geral, dependências e decisões estruturais.

**Quando usar:**

- projeto novo;
- reorganização técnica;
- criação de módulo grande;
- definição de stack;
- separação de frontend, backend, banco, APIs e arquivos;
- antes de começar desenvolvimento.

**Quando não usar:**

- correção pontual;
- ajuste de CSS;
- mudança pequena em uma função;
- tarefa operacional sem impacto estrutural.

**Ajuda o orquestrador a decidir:**

- onde cada parte do sistema deve ficar;
- qual camada deve resolver cada problema;
- quais skills técnicas serão necessárias.

---

## `skill-telas.md`

**Função:** mapear telas, funções, botões, campos, filtros, estados, navegação, dados usados e permissões por tela.

**Quando usar:**

- criação de nova tela;
- redesenho de fluxo;
- mapa de telas de projeto;
- definir o que cada tela mostra, cria, edita, exclui ou consulta;
- conectar tela com API, banco e permissões.

**Quando não usar:**

- ajuste pequeno de cor ou espaçamento;
- endpoint sem interface;
- mudança puramente backend;
- deploy ou infraestrutura.

**Ajuda o orquestrador a decidir:**

- quais telas existem;
- quais dados cada tela usa;
- quais skills de UX, frontend, backend, API e banco precisam ser chamadas.

---

# GRUPO 02 — Design e Interface

Use este grupo quando o projeto precisa de experiência visual, usabilidade, feedback visual, microinterações, responsividade, acessibilidade ou comportamento de app/PWA.

## `skill-ux-ui.md`

**Função:** definir experiência, fluxo, hierarquia visual, aparência, componentes, usabilidade e consistência da interface.

**Quando usar:**

- tela nova;
- melhoria de experiência;
- criação de guia visual;
- fluxo confuso;
- sistema com aparência genérica;
- necessidade de identidade visual profissional.

**Quando não usar:**

- erro de backend;
- query SQL;
- deploy;
- bug técnico sem relação com interface;
- integração externa.

**Ajuda o orquestrador a decidir:**

- como a tela deve parecer e se comportar para o usuário;
- quais decisões pertencem ao design antes do código.

---

## `skill-motion-feedback-visual.md`

**Função:** definir animações úteis, microinterações, feedback visual imediato, skeleton screens, optimistic UI segura, estados de loading, onboarding, empty states e performance percebida.

**Quando usar:**

- tela nova com interação;
- formulário que salva, envia ou processa dados;
- dashboard, lista, tabela, filtro ou busca;
- tela que parece lenta ou travada;
- botão sem resposta visual;
- integração, webhook, n8n, fila ou backend demorado;
- onboarding de usuário novo;
- empty state que precisa ensinar a próxima ação;
- PWA, celular ou máquina fraca;
- necessidade de conforto de uso e sensação de sistema rápido.

**Quando não usar:**

- animação decorativa sem propósito;
- tela estática simples;
- ação crítica onde optimistic UI pode mentir;
- pagamento, voucher, permissão ou exclusão definitiva sem confirmação real do backend;
- quando o problema real é backend, banco, payload ou consulta lenta e precisa de `skill-performance.md`.

**Ajuda o orquestrador a decidir:**

- quando o problema é percepção de velocidade, não apenas performance real;
- onde usar skeleton, loading local, feedback em até 100ms ou estado intermediário;
- quando optimistic UI é seguro e quando deve ser proibido;
- como criar movimento útil respeitando acessibilidade e máquinas fracas.

---

## `skill-acessibilidade.md`

**Função:** garantir que telas sejam claras, navegáveis, legíveis e utilizáveis por diferentes usuários, dispositivos e contextos.

**Quando usar:**

- formulários;
- botões importantes;
- menus;
- dashboards;
- telas públicas;
- sistemas com usuários variados;
- revisão de contraste, foco, teclado, labels e mensagens.

**Quando não usar:**

- rotina de backend sem interface;
- ajuste de banco;
- integração externa sem UI;
- deploy.

**Ajuda o orquestrador a decidir:**

- se a interface pode ser usada com clareza;
- quais ajustes de HTML, CSS e UX são necessários para inclusão.

---

## `skill-responsividade.md`

**Função:** adaptar telas e componentes para desktop, tablet, celular e diferentes tamanhos de tela.

**Quando usar:**

- tela precisa funcionar no celular;
- dashboard com tabela;
- formulário grande;
- menu lateral;
- modal;
- card, grid ou lista;
- layout quebrando em algum dispositivo.

**Quando não usar:**

- regra de negócio backend;
- banco de dados;
- API sem interface;
- integração externa.

**Ajuda o orquestrador a decidir:**

- como uma tela muda por dispositivo;
- quais componentes precisam virar card, coluna, tabela compacta ou navegação mobile.

---

## `skill-pwa-offline-first.md`

**Função:** definir comportamento de aplicativo web instalável, offline, cache, fila local, sincronização e uso de recursos nativos.

**Quando usar:**

- app PWA;
- sistema precisa funcionar com internet ruim;
- instalar no celular;
- cache de dados;
- fila offline;
- sincronização posterior;
- notificações push;
- recurso nativo do navegador.

**Quando não usar:**

- site institucional simples;
- sistema que sempre depende de conexão online em tempo real;
- tela sem necessidade offline;
- projeto sem uso mobile/app-like.

**Ajuda o orquestrador a decidir:**

- se o sistema precisa comportamento de app;
- quais cuidados de cache, IndexedDB, service worker, sincronização e segurança entram no projeto.

---

# GRUPO 03 — Frontend Técnico

Use este grupo para implementar interface no navegador.

## `skill-frontend.md`

**Função:** coordenar a implementação técnica da interface usando HTML, CSS, JavaScript e Fetch.

**Quando usar:**

- transformar tela planejada em frontend;
- organizar arquivos de interface;
- montar fluxo visual com interação;
- conectar HTML, CSS e JS;
- criar padrão de componentes simples.

**Quando não usar:**

- regra backend pura;
- modelagem de banco;
- deploy;
- relatório sem interface.

**Ajuda o orquestrador a decidir:**

- quais skills técnicas de interface entram;
- o que fica no HTML, CSS, JS ou Fetch.

---

## `skill-html.md`

**Função:** criar estrutura semântica, organizada e acessível da página.

**Quando usar:**

- criar página;
- formulário;
- tabela;
- card;
- modal;
- estrutura de layout;
- preparar elementos para CSS e JS.

**Quando não usar:**

- estilização profunda;
- lógica JavaScript;
- regra backend;
- banco de dados.

**Ajuda o orquestrador a decidir:**

- se a estrutura da página está correta;
- quais elementos precisam de semântica, labels, IDs, classes e `data-*`.

---

## `skill-css.md`

**Função:** organizar aparência, layout, componentes, estados visuais, responsividade e consistência visual com CSS.

**Quando usar:**

- estilizar tela;
- corrigir layout;
- criar componentes;
- trabalhar espaçamento, grid, flex, cards, tabelas, botões e estados;
- adaptar visual ao guia UX/UI.

**Quando não usar:**

- comportamento JS;
- validação backend;
- query SQL;
- deploy.

**Ajuda o orquestrador a decidir:**

- o que é estilo;
- o que deve ficar separado de HTML e JavaScript.

---

## `skill-js.md`

**Função:** definir comportamento da tela com JavaScript puro: DOM, eventos, estado simples, validações visuais e interação.

**Quando usar:**

- clique em botão;
- abrir modal;
- validar campo na tela;
- manipular estado visual;
- renderizar lista;
- controlar loading;
- comportamento de formulário;
- interação sem recarregar página.

**Quando não usar:**

- regra crítica de segurança;
- permissão real;
- pagamento;
- validação final de dados;
- persistência no banco.

**Ajuda o orquestrador a decidir:**

- o que é comportamento do navegador;
- o que precisa ser validado novamente no backend.

---

## `skill-fetch.md`

**Função:** padronizar comunicação HTTP do frontend com backend usando Fetch API.

**Quando usar:**

- consumir endpoint;
- enviar formulário por AJAX/fetch;
- tratar loading, erro e resposta JSON;
- timeout;
- CSRF;
- upload com `FormData`;
- atualizar parte da tela sem recarregar.

**Quando não usar:**

- endpoint ainda não definido;
- regra backend;
- integração servidor-servidor;
- tela estática sem API.

**Ajuda o orquestrador a decidir:**

- como o frontend conversa com a API;
- como tratar resposta, erro, autenticação e payload.

---

# GRUPO 04 — Backend, API e PHP

Use este grupo para regra de negócio, servidor, endpoints, validações e respostas.

## `skill-sintaxe.md`

**Função:** garantir código claro, comentado, bem organizado, com nomes bons, tabulação, blocos legíveis e fácil manutenção.

**Quando usar:**

- qualquer código novo;
- manutenção;
- refatoração;
- arquivo confuso;
- função sem comentário;
- lógica difícil de entender.

**Quando não usar:**

- decisão de negócio;
- design visual;
- deploy;
- banco sem código.

**Ajuda o orquestrador a decidir:**

- se o código está legível;
- como comentar sem exagero;
- onde melhorar manutenção.

---

## `skill-php.md`

**Função:** orientar PHP procedural puro com segurança, organização, validação, sessões, includes, funções e boas práticas.

**Quando usar:**

- criar arquivo PHP;
- endpoint PHP;
- regra backend procedural;
- conexão com MySQL;
- validação no servidor;
- sessão, upload, resposta JSON.

**Quando não usar:**

- projeto em outra linguagem;
- regra só de frontend;
- design visual;
- SQL puro sem PHP.

**Ajuda o orquestrador a decidir:**

- como escrever PHP seguro e simples;
- como evitar misturar regra crítica com HTML solto.

---

## `skill-backend.md`

**Função:** definir responsabilidade do servidor: validar, decidir, processar, salvar, integrar e responder.

**Quando usar:**

- regra de negócio;
- processamento de formulário;
- autenticação;
- autorização;
- upload;
- integração;
- gravação no banco;
- endpoints internos.

**Quando não usar:**

- tela estática;
- CSS;
- ajuste visual;
- documentação sem execução.

**Ajuda o orquestrador a decidir:**

- o que deve ser decidido no servidor;
- o que nunca pode depender apenas do frontend.

---

## `skill-api-rest.md`

**Função:** definir contrato oficial das APIs: rotas, métodos, payloads, respostas, erros, paginação, filtros e versionamento.

**Quando usar:**

- criar endpoint;
- documentar API;
- padronizar JSON;
- definir GET, POST, PUT/PATCH, DELETE;
- integrar frontend com backend;
- API pública ou interna.

**Quando não usar:**

- função interna que não é endpoint;
- layout visual;
- regra de banco sem API;
- documentação geral sem contrato.

**Ajuda o orquestrador a decidir:**

- qual é o contrato entre quem chama e quem responde;
- como padronizar resposta JSON.

---

## `skill-erros-excecoes.md`

**Função:** padronizar mensagens de erro, códigos internos, resposta JSON de falha, tratamento seguro e recuperação.

**Quando usar:**

- validação;
- exceção;
- erro de API;
- erro de formulário;
- falha de integração;
- mensagens para usuário;
- logs técnicos de erro.

**Quando não usar:**

- resposta de sucesso simples sem risco;
- design visual;
- regra de negócio sem falha prevista;
- deploy.

**Ajuda o orquestrador a decidir:**

- como informar erro ao usuário;
- o que vai para log técnico;
- como evitar expor SQL, stack trace, token ou segredo.

---

# GRUPO 05 — Banco de Dados

Use este grupo para modelagem, MySQL, migrations e recuperação.

## `skill-dados.md`

**Função:** modelar dados: entidades, relacionamentos, histórico, auditoria, integridade, dados sensíveis e regras do domínio.

**Quando usar:**

- criar banco do projeto;
- nova entidade;
- relação entre tabelas;
- definição de histórico;
- auditoria;
- dicionário de dados;
- decisão sobre status, tipos e integridade.

**Quando não usar:**

- escrever SQL final sem modelagem;
- ajustar CSS;
- criar endpoint sem persistência;
- deploy sem mudança de dados.

**Ajuda o orquestrador a decidir:**

- quais dados existem;
- como se relacionam;
- quais regras precisam ser preservadas.

---

## `skill-mysql.md`

**Função:** implementar tecnicamente o banco em MySQL/MariaDB: tabelas, tipos, índices, constraints, queries, transações e performance.

**Quando usar:**

- criar tabela;
- escrever SQL;
- melhorar consulta;
- revisar índice;
- usar prepared statements;
- corrigir lentidão de banco;
- implementar relacionamento.

**Quando não usar:**

- modelagem conceitual sem SQL;
- design visual;
- regra de interface;
- deploy sem banco.

**Ajuda o orquestrador a decidir:**

- como transformar modelo em SQL;
- como manter banco seguro e performático.

---

## `skill-migracoes-banco.md`

**Função:** controlar alterações versionadas no banco: scripts, ordem, rollback, seeds, impacto e compatibilidade com produção.

**Quando usar:**

- adicionar coluna;
- alterar tabela;
- criar índice;
- criar seed;
- mudança estrutural;
- deploy com alteração de banco;
- compatibilidade entre versões.

**Quando não usar:**

- banco novo ainda em rascunho sem versionamento;
- consulta simples;
- ajuste visual;
- regra sem mudança de schema.

**Ajuda o orquestrador a decidir:**

- como alterar banco sem perder dados;
- quando exigir backup, teste e rollback.

---

## `skill-backup-recuperacao.md`

**Função:** definir backup, restauração, retenção, teste de recuperação e plano de desastre.

**Quando usar:**

- antes de migration crítica;
- produção;
- dados importantes;
- uploads;
- logs;
- plano de recuperação;
- teste de restore.

**Quando não usar:**

- protótipo descartável;
- tela estática;
- ajuste de CSS;
- ambiente local sem dados relevantes.

**Ajuda o orquestrador a decidir:**

- quando backup é obrigatório;
- como recuperar sistema e dados se algo der errado.

---

# GRUPO 06 — Segurança e Auditoria

Use este grupo para proteção, login, permissão, LGPD, logs e debug.

## `skill-seguranca.md`

**Função:** definir política geral de segurança técnica: validação, SQL Injection, XSS, CSRF, uploads, sessão, segredos, logs e proteção.

**Quando usar:**

- qualquer entrada de usuário;
- login;
- upload;
- API;
- pagamento;
- dados pessoais;
- permissões;
- produção;
- integração externa.

**Quando não usar:**

- texto estático sem entrada;
- protótipo sem dados;
- arte visual isolada;
- documentação sem risco técnico.

**Ajuda o orquestrador a decidir:**

- quais riscos precisam ser tratados;
- o que nunca pode confiar no frontend.

---

## `skill-autenticacao-sessao.md`

**Função:** definir cadastro, login, logout, sessão, recuperação de senha, proteção de páginas e APIs privadas.

**Quando usar:**

- tela de login;
- área restrita;
- sessão PHP;
- cookies;
- lembrar usuário;
- recuperação de senha;
- bloquear usuário inativo;
- expiração de sessão.

**Quando não usar:**

- tela pública;
- permissão depois do login sem mexer na sessão;
- regra financeira;
- layout sem autenticação.

**Ajuda o orquestrador a decidir:**

- quem é o usuário;
- se a sessão está segura;
- onde autenticação termina e autorização começa.

---

## `skill-lgpd-privacidade.md`

**Função:** orientar privacidade, minimização, finalidade, consentimento, acesso, exclusão, retenção e proteção de dados pessoais.

**Quando usar:**

- CPF, e-mail, telefone, endereço;
- dados sensíveis;
- exportação;
- relatórios com dados pessoais;
- logs com usuário;
- consentimento;
- exclusão/anomização;
- retenção.

**Quando não usar:**

- sistema sem dado pessoal;
- mockup sem dados reais;
- ajuste técnico sem impacto em privacidade.

**Ajuda o orquestrador a decidir:**

- quais dados podem ser coletados;
- quem pode acessar;
- por quanto tempo manter;
- como evitar exposição indevida.

---

## `skill-logs-auditoria.md`

**Função:** definir logs técnicos, logs de negócio, auditoria, histórico de alterações e rastreabilidade.

**Quando usar:**

- ações críticas;
- login;
- alteração de permissão;
- pagamento;
- exclusão;
- integração;
- erro;
- admin;
- suporte;
- auditoria.

**Quando não usar:**

- tela estática;
- protótipo sem persistência;
- ação sem impacto ou risco.

**Ajuda o orquestrador a decidir:**

- o que deve ser registrado;
- quem fez, quando fez, onde fez e qual impacto.

---

## `skill-debug.md`

**Função:** implementar debug visual administrativo em tela, com timeline, API, estado, regras, erro e inspeção controlada.

**Quando usar:**

- telas críticas;
- painel admin;
- checkout;
- pagamento;
- upload;
- integrações;
- bugs difíceis;
- homologação;
- suporte técnico;
- QA.

**Quando não usar:**

- tela institucional simples;
- página estática;
- usuário comum;
- produção sem permissão;
- quando logs oficiais bastam.

**Ajuda o orquestrador a decidir:**

- quando criar diagnóstico visual;
- como instalar/remover debug sem quebrar regra de negócio;
- como não expor dados sensíveis.

---

# GRUPO 07 — Negócio e SaaS

Use este grupo para SaaS real, operação, venda, pagamento, admin, suporte e relatórios.

## `skill-multitenant-workspaces.md`

**Função:** definir arquitetura SaaS multiempresa, tenants, workspaces, isolamento de dados e escopo por cliente/unidade.

**Quando usar:**

- SaaS com várias empresas;
- unidades;
- workspaces;
- cliente com dados isolados;
- usuário em múltiplas organizações;
- admin global;
- relatórios por tenant.

**Quando não usar:**

- sistema de uma única empresa sem separação;
- app pessoal simples;
- site institucional;
- protótipo sem dados por cliente.

**Ajuda o orquestrador a decidir:**

- a quem pertence cada dado;
- qual escopo aplicar em consultas, relatórios e permissões.

---

## `skill-vendas-pagamentos.md`

**Função:** definir venda, pedido, checkout, cobrança, pagamento, voucher, assinatura, crédito, entrega, cancelamento, reembolso e conciliação.

**Quando usar:**

- venda online;
- pagamento;
- voucher;
- ingresso;
- assinatura;
- crédito;
- plano;
- pedido;
- checkout;
- reembolso;
- comissão;
- conciliação.

**Quando não usar:**

- sistema sem operação comercial;
- cadastro interno sem cobrança;
- relatório sem regra financeira;
- notificação sem venda.

**Ajuda o orquestrador a decidir:**

- quando uma venda está realmente confirmada;
- quais eventos financeiros exigem backend, webhook, log e auditoria.

---

## `skill-admin-operacional.md`

**Função:** definir painel administrativo e ferramentas internas para operar o SaaS com segurança.

**Quando usar:**

- painel admin;
- configurações do sistema;
- gestão de usuários;
- ações críticas;
- manutenção;
- suporte interno;
- consulta de logs;
- ferramentas operacionais.

**Quando não usar:**

- área do usuário comum;
- tela pública;
- sistema sem operação interna;
- relatório gerencial puro sem ação administrativa.

**Ajuda o orquestrador a decidir:**

- como a equipe opera o sistema sem acessar banco ou servidor direto;
- quais ações administrativas exigem permissão e auditoria.

---

## `skill-suporte-atendimento-sla.md`

**Função:** definir suporte, atendimento, chamados, SLA, triagem, histórico, evidências, escalonamento e base de conhecimento.

**Quando usar:**

- sistema com suporte ao usuário;
- chamados;
- SLA;
- fila de atendimento;
- histórico de contato;
- suporte técnico;
- incidentes;
- base de conhecimento.

**Quando não usar:**

- projeto sem atendimento;
- contato simples sem acompanhamento;
- bug interno sem fluxo de suporte;
- relatório sem ação de atendimento.

**Ajuda o orquestrador a decidir:**

- como o usuário pede ajuda;
- como o time atende;
- quando suporte vira bug, incidente ou melhoria.

---

## `skill-relatorios-bi-dashboard.md`

**Função:** definir relatórios, dashboards, KPIs, métricas, filtros, gráficos, exportações e tomada de decisão.

**Quando usar:**

- dashboard;
- relatório;
- KPI;
- indicador;
- exportação;
- ranking;
- visão gerencial;
- análise por período;
- acompanhamento operacional.

**Quando não usar:**

- regra de venda ainda não definida;
- consulta técnica sem visualização;
- tela simples sem métrica;
- banco sem objetivo de decisão.

**Ajuda o orquestrador a decidir:**

- qual número ajuda a decidir;
- qual regra é fonte da verdade do indicador;
- como evitar dashboard bonito com número errado.

---

# GRUPO 08 — Engajamento e Integrações

Use este grupo para retenção, gamificação, comunicação, automações, APIs externas e webhooks.

## `skill-retencao.md`

**Função:** definir estratégia para o usuário voltar ao sistema com motivo real, valor claro e ciclo de uso.

**Quando usar:**

- recorrência de uso;
- usuário precisa voltar todo dia/semana/mês;
- reativação;
- progresso a acompanhar;
- tarefas pendentes;
- ciclo de hábito saudável;
- reduzir abandono.

**Quando não usar:**

- sistema usado uma única vez;
- comunicação sem valor real;
- gamificação isolada;
- notificação sem ação necessária.

**Ajuda o orquestrador a decidir:**

- por que o usuário deve voltar;
- quando chamar o usuário;
- qual retorno tem valor real.

---

## `skill-gamificacao.md`

**Função:** definir gamificação responsável: pontos, níveis, missões, selos, conquistas, progresso e feedback útil.

**Quando usar:**

- incentivar conclusão de tarefa;
- mostrar progresso;
- reconhecer ações úteis;
- criar missões;
- ranking saudável;
- evolução de usuário;
- engajamento sem manipulação.

**Quando não usar:**

- forçar uso artificial;
- criar vício, culpa ou pressão;
- ranking humilhante;
- produto sem ação recorrente;
- quando bastar uma notificação simples.

**Ajuda o orquestrador a decidir:**

- como reconhecer progresso;
- quando gamificação ajuda ou atrapalha;
- como evitar manipulação.

---

## `skill-notificacoes.md`

**Função:** definir notificações internas, e-mail, WhatsApp, SMS, push, templates, frequência, preferências e anti-spam.

**Quando usar:**

- lembrete;
- prazo;
- alerta;
- confirmação;
- mudança de status;
- tarefa pendente;
- pagamento;
- suporte;
- retenção;
- comunicação transacional.

**Quando não usar:**

- mensagem sem ação ou valor;
- spam;
- falsa urgência;
- dado sensível exposto;
- comunicação que deveria ser apenas na tela.

**Ajuda o orquestrador a decidir:**

- qual mensagem enviar;
- para quem;
- por qual canal;
- em qual momento;
- com qual limite.

---

## `skill-integracoes-webhooks.md`

**Função:** definir integrações externas, APIs consumidas, webhooks recebidos/enviados, filas, retry, idempotência e rastreabilidade.

**Quando usar:**

- API externa;
- webhook;
- gateway de pagamento;
- WhatsApp;
- e-mail;
- SMS;
- integração com parceiro;
- fila de reprocessamento;
- retry;
- idempotência.

**Quando não usar:**

- chamada frontend simples para API interna;
- tela estática;
- regra sem sistema externo;
- notificação sem provedor externo.

**Ajuda o orquestrador a decidir:**

- como conversar com outros sistemas;
- como evitar duplicidade;
- como registrar falhas e reprocessar.

---

# GRUPO 09 — Qualidade e Manutenção

Use este grupo para teste, evidência, performance, documentação, revisão e manutenção.

## `skill-documentacao-projeto.md`

**Função:** criar e manter documentação técnica e funcional do projeto: README, regras, telas, API, banco, deploy, testes, decisões e changelog.

**Quando usar:**

- projeto novo;
- mudança de regra;
- endpoint novo;
- tabela nova;
- deploy;
- decisão técnica;
- orientar IA de coding;
- manter contexto.

**Quando não usar:**

- correção urgente sem mudança relevante, desde que depois seja registrada;
- protótipo descartável;
- ajuste visual sem impacto.

**Ajuda o orquestrador a decidir:**

- o que precisa ser documentado;
- como evitar perda de contexto;
- quando atualização de documentação é obrigatória.

---

## `skill-qa.md`

**Função:** planejar, executar e documentar testes funcionais, regressão, integração, segurança, performance e operação.

**Quando usar:**

- antes de aprovar entrega;
- bug;
- homologação;
- regra crítica;
- fluxo financeiro;
- login;
- permissão;
- integração;
- alteração com risco.

**Quando não usar:**

- rascunho ainda sem regra;
- brainstorm inicial;
- tarefa sem comportamento testável.

**Ajuda o orquestrador a decidir:**

- quais testes são necessários;
- qual evidência gerar;
- se a entrega pode ser considerada aprovada.

---

## `skill-performance.md`

**Função:** medir e otimizar desempenho de frontend, backend, banco, APIs, imagens, cache, payloads e relatórios.

**Quando usar:**

- lentidão;
- relatório pesado;
- consulta lenta;
- payload grande;
- muita requisição;
- tela carregando muito;
- alto volume;
- timeout;
- otimização antes/depois.

**Quando não usar:**

- sem gargalo real;
- otimização estética sem métrica;
- refatoração sem problema de desempenho;
- protótipo sem volume.

**Ajuda o orquestrador a decidir:**

- onde está o gargalo;
- como medir antes/depois;
- quando chamar MySQL, backend, frontend ou monitoramento.

---

## `skill-refatoracao-code-review.md`

**Função:** revisar e melhorar código com escopo controlado, preservando comportamento existente.

**Quando usar:**

- código duplicado;
- função longa;
- nome confuso;
- regra misturada;
- revisão antes de merge;
- manutenção;
- melhoria sem mudar regra;
- reduzir risco técnico.

**Quando não usar:**

- reescrever tudo sem autorização;
- mudar regra de negócio;
- trocar stack;
- converter procedural para OOP;
- adicionar framework sem pedido.

**Ajuda o orquestrador a decidir:**

- como melhorar sem quebrar;
- qual escopo preservar;
- quais testes rodar depois.

---

# GRUPO 10 — Deploy e Produção

Use este grupo para versionamento, Docker, publicação, rollback, monitoramento e produção.

## `skill-git.md`

**Função:** orientar versionamento Git, branches, commits, PR, tags, releases, histórico e recuperação.

**Quando usar:**

- iniciar repositório;
- criar branch;
- commit;
- PR;
- merge;
- tag;
- release;
- preparar deploy;
- recuperar erro;
- revisar histórico.

**Quando não usar:**

- projeto sem versionamento;
- ajuste puramente visual sem mexer em arquivo;
- operação de banco sem código;
- deploy sem relação com Git.

**Ajuda o orquestrador a decidir:**

- qual código está versionado;
- qual branch/tag será publicada;
- como garantir rastreabilidade.

---

## `skill-dockerfile.md`

**Função:** definir Dockerfile, Docker Compose, containers, imagens, volumes, redes, `.env`, ambientes e isolamento.

**Quando usar:**

- rodar projeto local;
- criar container;
- separar dev/hml/prod;
- configurar PHP/MySQL;
- volumes;
- uploads/logs;
- healthcheck;
- EasyPanel/Portainer/Docker.

**Quando não usar:**

- hospedagem sem Docker;
- ajuste de regra de negócio;
- tela simples;
- script que roda fora de container.

**Ajuda o orquestrador a decidir:**

- como o projeto roda em cada ambiente;
- quais dados são persistentes;
- como isolar banco e configuração.

---

## `skill-deploy-ci-cd.md`

**Função:** definir publicação, ambientes, checklist de deploy, CI/CD simples, rollback, pós-deploy e comunicação de versão.

**Quando usar:**

- subir homologação;
- subir produção;
- configurar pipeline;
- checklist de deploy;
- rollback;
- health check;
- deploy com migration;
- pós-deploy.

**Quando não usar:**

- desenvolvimento local sem publicação;
- mudança sem entrega;
- teste isolado;
- documentação sem ambiente.

**Ajuda o orquestrador a decidir:**

- como publicar com segurança;
- quais passos precisam ser validados;
- quando exigir backup, rollback e monitoramento.

---

## `skill-monitoramento-observabilidade.md`

**Função:** definir monitoramento, observabilidade, uptime, health check, métricas, alertas, incidentes e saúde de produção.

**Quando usar:**

- produção;
- homologação crítica;
- sistema com pagamento;
- webhooks;
- integrações;
- jobs;
- filas;
- backup;
- erro 500;
- lentidão;
- painel de saúde.

**Quando não usar:**

- protótipo descartável;
- landing page simples;
- tela estática sem backend;
- estudo local sem usuário real.

**Ajuda o orquestrador a decidir:**

- se o sistema está saudável;
- como detectar problema antes do usuário reclamar;
- como investigar incidente.

---

# Skills transversais mais importantes

Estas skills podem aparecer em vários grupos:

| Skill | Por que é transversal |
|---|---|
| `skill-seguranca.md` | Segurança afeta frontend, backend, banco, API, deploy e integrações |
| `skill-perfis-permissoes.md` | Permissão afeta telas, APIs, dados, relatórios, admin e SaaS |
| `skill-lgpd-privacidade.md` | Dados pessoais podem aparecer em telas, banco, logs, relatórios e notificações |
| `skill-logs-auditoria.md` | Rastreabilidade é necessária em ações críticas, admin, pagamento e suporte |
| `skill-qa.md` | Toda entrega relevante precisa validação e evidência |
| `skill-documentacao-projeto.md` | Toda mudança relevante precisa contexto atualizado |
| `skill-motion-feedback-visual.md` | Feedback visual, loading, onboarding, microinterações e performance percebida afetam UX, frontend, CSS, JS, acessibilidade e PWA |
| `skill-performance.md` | Lentidão pode estar em frontend, backend, banco, API ou infra |
| `skill-monitoramento-observabilidade.md` | Produção precisa visibilidade contínua |

---

# Tabela rápida — quando usar cada grupo

| Situação | Grupo principal | Skills prováveis |
|---|---|---|
| Projeto novo | Grupo 01 | briefing, permissões, arquitetura, telas |
| Tela nova | Grupo 02 + 03 | ux-ui, motion-feedback-visual, acessibilidade, responsividade, frontend, html, css, js |
| Endpoint novo | Grupo 04 | backend, api-rest, erros-excecoes, php |
| Nova tabela | Grupo 05 | dados, mysql, migracoes-banco |
| Login/permissão | Grupo 06 | seguranca, autenticacao-sessao, perfis-permissoes |
| SaaS multiempresa | Grupo 07 | multitenant-workspaces, admin, relatorios |
| Venda/pagamento | Grupo 07 + 08 | vendas-pagamentos, integracoes-webhooks, notificacoes |
| Notificação | Grupo 08 | notificacoes, retencao, integracoes-webhooks |
| Bug | Grupo 09 | qa, debug, logs, refatoracao |
| Lentidão ou tela parecendo travada | Grupo 09 + 02 | performance, motion-feedback-visual, mysql, backend, frontend |
| Deploy | Grupo 10 | git, dockerfile, deploy-ci-cd, monitoramento |
| Produção instável | Grupo 10 + 09 | monitoramento, logs, performance, qa |

---

# Regra final deste README

Este README não substitui o `orquestrador.md`.

Ele serve para a IA localizar rapidamente:

```txt
função da skill
quando usar
quando não usar
como ela ajuda o orquestrador
```

O fluxo correto é:

```txt
1. Orquestrador decide.
2. README confirma a função das skills.
3. Skill específica detalha a execução.
```
