# Skill: Análise e Conversão de Projeto Externo para Stack Padrão

## Objetivo da skill

Esta skill orienta uma IA a receber, estudar, documentar, comparar e converter projetos criados em ferramentas externas, low-code, no-code, AI builders ou stacks diferentes para o padrão técnico definido nesta biblioteca.

Exemplos de origem:

```txt
Lovable
Bolt
v0
Bubble
FlutterFlow
Webflow com lógica
projeto React/Next
projeto Node
projeto Supabase
projeto Firebase
projeto sem documentação
projeto gerado por IA
```

O objetivo não é copiar código automaticamente.

O objetivo é primeiro **entender o projeto**, extrair regras, telas, dados, fluxos, integrações e comportamento, para depois planejar uma conversão segura para a stack padrão.

---

## Stack padrão de destino

Por padrão, converter ou replanejar para:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON como padrão de comunicação
Git
Docker/Docker Compose quando aplicável
Servidor Linux com Apache ou Nginx
Sem framework por padrão
Sem orientação a objetos por padrão
```

Qualquer exceção deve ser documentada e justificada.

---

## Limite desta skill

Esta skill cuida da **análise, documentação e plano de conversão** de um projeto existente.

Ela pode identificar frontend, backend, banco, API, autenticação, permissões, integrações, componentes, bibliotecas, deploy e regras de negócio.

Ela não deve substituir:

- `skill-briefing.md` para redefinir o negócio;
- `skill-arquitetura.md` para desenhar a arquitetura final;
- `skill-telas.md` para detalhar o mapa final de telas;
- `skill-dados.md` para modelar o banco definitivo;
- `skill-backend.md` para implementar regras no servidor;
- `skill-api-rest.md` para contrato final dos endpoints;
- `skill-frontend.md` para implementação final da interface;
- `skill-qa.md` para validação completa;
- `skill-deploy-ci-cd.md` para publicação.

Esta skill responde:

```txt
O que existe neste projeto externo, como ele funciona, quais riscos existem e como converter para nosso padrão sem perder regra, tela, dado ou comportamento?
```

---

## Princípio central

```txt
Antes de converter, entender.
Antes de reescrever, mapear.
Antes de remover tecnologia, preservar regra.
Antes de mudar stack, documentar impacto.
```

A IA nunca deve começar convertendo arquivo por arquivo sem primeiro entender o comportamento do sistema.

---

# 1. Quando usar esta skill

Use quando o usuário enviar ou apontar um projeto existente feito em:

- Lovable;
- Bolt;
- v0;
- Bubble;
- FlutterFlow;
- Webflow;
- React;
- Next.js;
- Vue;
- Node;
- Supabase;
- Firebase;
- outro framework;
- projeto gerado por IA;
- projeto antigo sem padrão;
- projeto que precisa migrar para PHP/MySQL/HTML/CSS/JS.

Também usar quando o usuário pedir:

```txt
converta esse projeto para minha stack
estude esse projeto
extraia regras desse projeto
transforme esse app em PHP
refaça esse sistema no meu padrão
organize esse código gerado por IA
migra de Lovable para PHP e MySQL
```

---

# 2. Quando não usar esta skill

Não usar quando:

- o projeto será criado do zero sem base externa;
- a alteração é pequena e localizada;
- o usuário só quer corrigir bug pontual;
- o usuário já forneceu briefing completo e não existe código legado;
- o sistema externo não será reaproveitado;
- a tarefa é apenas deploy, QA ou documentação de projeto já convertido.

---

# 3. Ordem correta de trabalho

A IA deve seguir esta ordem:

```txt
1. Inventário do projeto atual
2. Identificação da stack atual
3. Mapa de telas e fluxos
4. Extração de regras de negócio
5. Mapa de dados e entidades
6. Mapa de autenticação e permissões
7. Mapa de APIs, webhooks e integrações
8. Mapa de componentes visuais e UX
9. Mapa de estados, loading, feedback e animações
10. Identificação de riscos
11. Plano de conversão para stack padrão
12. Priorização por MVP e fases
13. Checklist de validação
```

---

# 4. Inventário inicial obrigatório

Ao receber um projeto externo, a IA deve listar:

```txt
nome do projeto
tecnologias usadas
frameworks
bibliotecas
serviços externos
estrutura de pastas
arquivos principais
rotas
telas
componentes
modelos de dados
variáveis de ambiente
scripts de execução
forma de deploy
dependências
```

Se houver `package.json`, `vite.config`, `next.config`, `supabase`, `.env.example`, migrations, schema ou arquivos de configuração, eles devem ser analisados.

---

# 5. Identificação da stack atual

A IA deve identificar se o projeto usa:

```txt
React
Next.js
Vite
TypeScript
Tailwind
shadcn/ui
Supabase
Firebase
Node
Express
Prisma
Drizzle
Postgres
API externa
serverless
edge functions
auth externo
storage externo
```

Depois deve comparar com a stack de destino:

```txt
PHP procedural
MySQL/MariaDB
HTML
CSS
JavaScript puro
Fetch
JSON
Docker
```

---

# 6. Mapa de telas

A IA deve extrair todas as telas do projeto.

Para cada tela, registrar:

```txt
nome da tela
rota/caminho
objetivo
usuário que acessa
componentes principais
dados exibidos
ações possíveis
formulários
validações
APIs chamadas
estados de loading
estados vazios
mensagens de erro
permissões necessárias
```

Depois conectar com:

```txt
skill-telas.md
skill-ux-ui.md
skill-motion-feedback-visual.md
skill-frontend.md
```

---

# 7. Extração de regras de negócio

A IA deve procurar regras escondidas em:

- componentes;
- hooks;
- funções JavaScript;
- validações de formulário;
- chamadas de API;
- políticas de banco;
- edge functions;
- triggers;
- automações;
- webhooks;
- textos de tela;
- permissões;
- estados de status.

Para cada regra, registrar:

```txt
regra
onde foi encontrada
qual tela usa
qual entidade afeta
qual usuário executa
qual validação existe
qual validação falta
como será implementada no backend PHP
```

Regra de ouro:

```txt
Regra de negócio crítica encontrada no frontend deve ser movida ou repetida no backend PHP.
```

---

# 8. Mapa de dados

A IA deve identificar:

```txt
entidades
tabelas
coleções
campos
tipos
relacionamentos
status
histórico
dados sensíveis
arquivos/uploads
logs
auditoria
```

Depois deve planejar conversão para MySQL/MariaDB usando:

```txt
skill-dados.md
skill-mysql.md
skill-migracoes-banco.md
skill-backup-recuperacao.md
```

---

# 9. Autenticação, sessão e permissões

A IA deve identificar:

```txt
como o login funciona
onde fica o usuário
como a sessão é mantida
quais perfis existem
quais permissões existem
quais telas são protegidas
quais APIs são protegidas
se existe admin global
se existe tenant/workspace
```

Depois converter para padrão do projeto usando:

```txt
skill-seguranca.md
skill-autenticacao-sessao.md
skill-perfis-permissoes.md
skill-lgpd-privacidade.md
skill-logs-auditoria.md
```

---

# 10. APIs, webhooks e integrações

A IA deve mapear:

```txt
endpoints chamados pelo frontend
endpoints internos
webhooks recebidos
webhooks enviados
integrações externas
serviços de e-mail
WhatsApp
pagamento
storage
n8n
Supabase functions
Firebase functions
```

Para cada integração:

```txt
origem
destino
método
payload
resposta esperada
erro esperado
retry
idempotência
log
segurança
```

Depois converter usando:

```txt
skill-api-rest.md
skill-integracoes-webhooks.md
skill-erros-excecoes.md
skill-logs-auditoria.md
```

---

# 11. Interface, componentes e design

A IA deve identificar:

```txt
componentes reutilizáveis
layout
menu
cards
tabelas
formulários
modais
toasts
loading
skeleton
empty states
animações
responsividade
acessibilidade
```

Depois converter para:

```txt
HTML semântico
CSS organizado
JavaScript puro
Fetch API
```

Usar:

```txt
skill-ux-ui.md
skill-motion-feedback-visual.md
skill-acessibilidade.md
skill-responsividade.md
skill-html.md
skill-css.md
skill-js.md
skill-fetch.md
```

---

# 12. Conversão de frontend moderno para stack simples

Quando o projeto original usa React, Next, componentes ou estado moderno, a IA deve converter conceito por conceito.

Exemplo:

```txt
componente React        -> bloco HTML + CSS + JS
state/useState          -> variável JS ou estado no DOM
useEffect               -> função inicializadora JS
props                   -> atributos data-* ou parâmetros de função
router                  -> páginas PHP/HTML ou rotas simples
API call                -> fetch padronizado
toast                   -> componente JS simples
modal                   -> HTML/CSS/JS acessível
```

A IA não deve tentar copiar o padrão React para PHP.

Ela deve preservar comportamento e simplificar implementação.

---

# 13. Conversão de Supabase/Firebase para MySQL/PHP

Quando o projeto usa Supabase ou Firebase, mapear:

```txt
auth
tables/collections
storage
realtime
policies
edge functions
triggers
functions
rules
```

Converter para:

```txt
sessão PHP
tabelas MySQL
uploads públicos/privados
endpoints PHP JSON
permissões no backend
logs de auditoria
jobs/cron quando necessário
```

Atenção:

```txt
RLS/policies do Supabase devem virar validação de permissão no backend PHP.
Rules do Firebase devem virar validação de permissão no backend PHP.
```

---

# 14. Plano de conversão por fases

A IA deve propor fases:

## Fase 1 — Diagnóstico

- inventário;
- stack atual;
- telas;
- regras;
- dados;
- riscos.

## Fase 2 — Modelo novo

- briefing ajustado;
- arquitetura final;
- mapa de telas;
- modelagem MySQL;
- permissões;
- APIs.

## Fase 3 — MVP convertido

- login;
- telas principais;
- banco base;
- APIs essenciais;
- fluxo crítico.

## Fase 4 — Recursos avançados

- relatórios;
- notificações;
- integrações;
- gamificação;
- PWA/offline;
- admin operacional.

## Fase 5 — Qualidade e produção

- QA;
- performance;
- documentação;
- Docker;
- deploy;
- monitoramento.

---

# 15. Critérios de comparação antes/depois

Para cada fluxo importante, comparar:

```txt
como funciona no projeto original
como funcionará no projeto convertido
o que será mantido
o que será removido
o que será melhorado
o que depende de decisão do usuário
risco de perda de comportamento
teste necessário
```

---

# 16. Riscos comuns em projeto gerado por IA/low-code

A IA deve procurar:

- regra crítica só no frontend;
- banco sem relacionamento claro;
- permissão apenas visual;
- código duplicado;
- componentes bonitos sem regra robusta;
- falta de logs;
- falta de auditoria;
- falta de tratamento de erro;
- dependência excessiva de serviço externo;
- status sem padronização;
- payload sem validação;
- formulário sem backend seguro;
- falta de backup;
- falta de deploy claro;
- dependências desnecessárias;
- performance ruim;
- tela bonita mas difícil de manter.

---

# 17. Checklist obrigatório de diagnóstico

Antes de propor conversão, validar:

```md
- [ ] Stack atual foi identificada.
- [ ] Estrutura de pastas foi mapeada.
- [ ] Telas foram listadas.
- [ ] Rotas foram listadas.
- [ ] Componentes principais foram identificados.
- [ ] Regras de negócio foram extraídas.
- [ ] Entidades e dados foram identificados.
- [ ] Autenticação foi analisada.
- [ ] Permissões foram analisadas.
- [ ] APIs foram mapeadas.
- [ ] Integrações/webhooks foram mapeados.
- [ ] Uploads/storage foram analisados.
- [ ] Estados de loading/erro/sucesso foram identificados.
- [ ] Riscos foram documentados.
- [ ] Plano de conversão foi separado por fases.
```

---

# 18. Modelo de relatório esperado

Ao analisar um projeto externo, entregar:

```md
# Relatório de Análise e Conversão

## 1. Resumo do projeto original

## 2. Stack atual encontrada

## 3. Estrutura de pastas

## 4. Telas e rotas

| Tela | Rota | Função | Dados | Ações |
|---|---|---|---|---|

## 5. Regras de negócio encontradas

| Regra | Onde está | Risco | Conversão |
|---|---|---|---|

## 6. Dados e entidades

| Entidade | Origem | Campos | Conversão MySQL |
|---|---|---|---|

## 7. Autenticação e permissões

## 8. APIs e integrações

## 9. Componentes e UX

## 10. Riscos encontrados

## 11. Plano de conversão para stack padrão

## 12. Fases recomendadas

## 13. Checklist de validação
```

---

# 19. Regra final

A conversão só é segura quando a IA consegue explicar:

```txt
o que o projeto faz
quais telas existem
quais regras existem
quais dados existem
quais integrações existem
quais riscos existem
como cada parte será convertida
como validar se nada importante foi perdido
```

Se a IA não consegue explicar isso, ainda não deve converter.

```txt
Não converter no escuro.
Não reescrever sem mapa.
Não confiar em regra crítica no frontend.
Não perder comportamento existente.
Não migrar sem checklist.
```
