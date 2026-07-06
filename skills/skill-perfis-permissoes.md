# Skill IA — Manual Sênior de Perfis, Permissões, Processos, Telas, Funções e Banco de Dados para SaaS/App

> **Objetivo da skill:** orientar uma IA, gerente de projeto, arquiteto de software ou time de desenvolvimento a criar sistemas SaaS, apps e plataformas web com mapa de telas coerente, regras de negócio claras, níveis de usuários consistentes, permissões seguras, funções bem definidas e banco de dados alinhado ao processo real da operação.

## Limite desta skill

Esta skill deve focar em perfis, papéis, escopos de acesso e permissões.

Ela pode citar telas, funções, processos e tabelas apenas para explicar quem pode acessar ou executar cada ação.

Ela não deve substituir:

- `skill-briefing.md` para descoberta do projeto e regras de negócio completas;
- `skill-telas.md` para mapa detalhado de telas;
- `skill-dados.md` para modelagem de entidades;
- `skill-multitenant-workspaces.md` para arquitetura multiempresa avançada;
- `skill-seguranca.md` para segurança técnica geral.

Quando houver conflito com `skill-autenticacao-sessao.md`, esta skill não decide login ou sessão; ela decide autorização após o usuário estar autenticado.

Quando houver conflito, esta skill decide "quem pode fazer".
As outras skills decidem "como funciona", "onde aparece" e "como será implementado".

---

## 1. Papel que a IA deve assumir

Ao usar esta skill, a IA deve agir como um **Gerente de Projeto Sênior com experiência em arquitetura de sistemas, desenvolvimento de SaaS, aplicativos, processos operacionais, UX, banco de dados, segurança e implantação**.

A IA deve sempre pensar em quatro camadas ao mesmo tempo:

1. **Negócio** — qual problema o sistema resolve, quais regras existem, quais etapas fazem parte do processo e quais resultados são esperados.
2. **Usuários** — quem usa, o que cada perfil pode ver, criar, editar, aprovar, excluir e auditar.
3. **Produto/Telas** — quais telas existem, quais ações cada tela permite e como o usuário navega sem confusão.
4. **Tecnologia/Banco** — quais tabelas, relacionamentos, permissões, logs, anexos, status e integrações sustentam o sistema.

---

## 2. Princípios gerais de arquitetura para SaaS/App

### 2.1 Começar pelo processo antes da tela

Antes de desenhar telas ou banco de dados, mapear o processo real do negócio.

**Boa prática:** toda tela precisa existir para apoiar uma etapa do processo. Se uma tela não tem função clara dentro do fluxo, ela deve ser removida, simplificada ou agrupada.

Perguntas obrigatórias:

- Qual processo essa tela resolve?
- Quem usa essa tela?
- Que informação entra?
- Que informação sai?
- Qual status muda após a ação?
- Qual tabela será afetada?
- Quem pode ver, editar, aprovar ou excluir?

---

### 2.2 Separar regra de negócio de interface

A interface deve facilitar o uso, mas a regra principal deve estar protegida no backend ou na camada de serviço.

**Exemplo:** se apenas um administrador pode aprovar uma demanda, não basta esconder o botão no frontend. O backend também precisa validar se o usuário tem permissão.

**Boa prática:** toda regra sensível deve ser validada em duas camadas:

- **Frontend:** melhora experiência, esconde botões e evita erro comum.
- **Backend:** garante segurança, valida permissão, status, dados obrigatórios e integridade.

---

### 2.3 Usar status claros e controlados

Todo processo importante deve ter uma máquina de status simples e bem definida.

**Exemplo de status para uma demanda:**

- `rascunho`
- `aberta`
- `em_analise`
- `aprovada`
- `em_execucao`
- `aguardando_cliente`
- `bloqueada`
- `concluida`
- `cancelada`

**Boa prática:** nunca deixar status como texto livre digitado pelo usuário. Status deve ser enum, tabela auxiliar ou constante controlada pelo sistema.

---

### 2.4 Cada ação importante precisa gerar histórico

Em sistemas profissionais, principalmente SaaS e app corporativo, não basta salvar o estado atual. É necessário saber **quem fez, quando fez, o que mudou e por quê**.

**Boa prática:** criar logs para:

- criação de registros;
- alteração de status;
- alteração de responsável;
- aprovação/reprovação;
- exclusão lógica;
- upload ou remoção de anexos;
- mudança de prazo;
- comentários importantes;
- alteração de permissão;
- login e tentativa de acesso negado.

---

### 2.5 Pensar em multiempresa desde o início

Se o projeto for SaaS, o sistema deve estar preparado para separar dados por organização, empresa, cliente ou unidade.

**Boa prática:** quase todas as tabelas operacionais devem conter um campo como:

```sql
organization_id
```

ou

```sql
tenant_id
```

Isso evita vazamento de dados entre clientes e facilita escalar o produto.

---

## 3. Óticas principais do sistema

O projeto deve ser analisado por três óticas principais: **Administrador**, **Analista** e **Usuário**. Em sistemas maiores, podem existir outros níveis complementares.

---

## 4. Níveis de usuários e permissões

### 4.1 Estrutura recomendada de perfis

Abaixo está uma estrutura coesa para SaaS, app corporativo, portal interno ou sistema de gestão de demandas.

| Nível | Perfil | Responsabilidade principal | Acesso recomendado |
|---|---|---|---|
| 1 | Super Admin da Plataforma | Gerencia o SaaS como produto | Todas as organizações, planos, logs técnicos e configurações globais |
| 2 | Administrador da Organização | Gerencia uma empresa/cliente dentro do SaaS | Usuários, permissões, configurações, relatórios e dados da própria organização |
| 3 | Gestor/Coordenador | Gerencia equipe, demandas, prazos e aprovações | Painéis, demandas, planos de ação, relatórios e aprovações da área |
| 4 | Analista/Operador | Executa processos, analisa dados e movimenta demandas | Demandas atribuídas, tarefas, anexos, comentários e status operacionais |
| 5 | Usuário/Solicitante | Abre solicitações, acompanha andamento e responde pendências | Próprias demandas, mensagens, anexos e histórico permitido |
| 6 | Cliente/Convidado Externo | Consulta ou interage de forma limitada | Demandas compartilhadas, aprovações externas ou documentos específicos |
| 7 | Auditor/Leitura | Consulta dados e históricos sem alterar | Relatórios, logs e registros em modo somente leitura |

---

### 4.2 Regras para criação de perfis

#### Boa prática 1 — perfil deve representar responsabilidade, não pessoa

Não criar perfis com nomes de pessoas, como `Rodrigo`, `Financeiro Maria` ou `TI João`.

Usar perfis funcionais:

- `admin_organizacao`
- `gestor_area`
- `analista_operacional`
- `usuario_solicitante`
- `auditor`

---

#### Boa prática 2 — evitar perfil único com poderes demais

Não usar apenas `admin` e `usuario`. Isso cria falhas de segurança e dificulta o crescimento do sistema.

**Modelo ruim:**

- Admin
- Usuário

**Modelo melhor:**

- Super Admin
- Admin da Organização
- Gestor
- Analista
- Solicitante
- Auditor

---

#### Boa prática 3 — aplicar o princípio do menor privilégio

Cada perfil deve ter acesso apenas ao necessário para executar seu trabalho.

**Exemplo:** um analista pode alterar o status de uma tarefa em execução, mas não deve alterar permissões, excluir usuários ou mudar configuração global.

---

#### Boa prática 4 — separar permissão de visualização e permissão de ação

Ver um dado não significa poder alterar esse dado.

Permissões devem ser separadas por ação:

- visualizar;
- criar;
- editar;
- excluir;
- aprovar;
- cancelar;
- exportar;
- importar;
- compartilhar;
- atribuir responsável;
- alterar status;
- acessar relatório;
- configurar sistema.

---

## 5. Matriz de permissões recomendada

### 5.1 Permissões por módulo

| Módulo/Função | Super Admin | Admin Organização | Gestor | Analista | Usuário | Cliente Externo | Auditor |
|---|---:|---:|---:|---:|---:|---:|---:|
| Gerenciar organizações | Sim | Não | Não | Não | Não | Não | Leitura |
| Gerenciar usuários | Sim | Sim | Parcial | Não | Não | Não | Leitura |
| Gerenciar perfis/permissões | Sim | Sim | Não | Não | Não | Não | Leitura |
| Criar demanda | Sim | Sim | Sim | Sim | Sim | Parcial | Não |
| Ver todas as demandas da organização | Sim | Sim | Sim | Parcial | Não | Não | Sim |
| Ver próprias demandas | Sim | Sim | Sim | Sim | Sim | Sim | Sim |
| Editar demanda aberta | Sim | Sim | Sim | Parcial | Parcial | Parcial | Não |
| Aprovar demanda | Sim | Sim | Sim | Não | Não | Parcial | Não |
| Cancelar demanda | Sim | Sim | Sim | Parcial | Parcial | Não | Não |
| Criar plano de ação | Sim | Sim | Sim | Sim | Não | Não | Não |
| Editar plano de ação | Sim | Sim | Sim | Parcial | Não | Não | Não |
| Atribuir responsável | Sim | Sim | Sim | Parcial | Não | Não | Não |
| Alterar status operacional | Sim | Sim | Sim | Sim | Parcial | Parcial | Não |
| Upload de anexos | Sim | Sim | Sim | Sim | Sim | Parcial | Não |
| Excluir anexos | Sim | Sim | Sim | Parcial | Próprios | Próprios | Não |
| Ver relatórios | Sim | Sim | Sim | Parcial | Não | Não | Sim |
| Exportar dados | Sim | Sim | Sim | Parcial | Não | Não | Parcial |
| Ver logs/auditoria | Sim | Sim | Parcial | Não | Não | Não | Sim |
| Configurar integrações | Sim | Sim | Não | Não | Não | Não | Leitura |

---

### 5.2 Permissões por escopo

Além do perfil, considerar o escopo do acesso:

| Escopo | Descrição |
|---|---|
| Global | Acessa toda a plataforma SaaS |
| Organização | Acessa apenas uma empresa/cliente |
| Unidade/Filial | Acessa apenas uma unidade específica |
| Área/Departamento | Acessa apenas registros da área |
| Equipe | Acessa registros da própria equipe |
| Próprio usuário | Acessa apenas registros criados ou atribuídos a ele |
| Compartilhado | Acessa registros explicitamente compartilhados |

**Boa prática:** combinar perfil + escopo.

Exemplo:

```text
Perfil: analista_operacional
Escopo: equipe
Resultado: pode ver e editar demandas atribuídas à sua equipe, mas não acessa tudo da organização.
```

---

## 6. Boas práticas por ótica de usuário

---

## 6.1 Ótica do Administrador

O administrador precisa controlar o sistema, garantir segurança, organizar usuários e configurar o funcionamento da organização.

### Funções principais do administrador

- cadastrar usuários;
- definir perfis e permissões;
- configurar áreas, equipes e unidades;
- configurar status e categorias;
- acompanhar relatórios;
- auditar alterações importantes;
- gerenciar integrações;
- definir templates e regras de negócio;
- bloquear ou inativar usuários;
- ajustar dados mestres do sistema.

### Boas práticas para telas do administrador

#### Tela administrativa deve ser separada da operação diária

O administrador deve ter um menu próprio, evitando misturar configuração com execução operacional.

**Exemplo de menu administrativo:**

- Dashboard Administrativo
- Usuários
- Perfis e Permissões
- Empresas/Organizações
- Áreas e Equipes
- Categorias e Status
- Templates
- Integrações
- Logs e Auditoria
- Configurações Gerais

---

#### Toda alteração administrativa deve gerar log

Alterações administrativas podem afetar segurança e funcionamento do sistema. Por isso, devem ser registradas.

Registrar:

- usuário que alterou;
- data e hora;
- campo alterado;
- valor anterior;
- novo valor;
- motivo, quando necessário.

---

#### Não excluir usuário com histórico

Usuários que já executaram ações no sistema não devem ser apagados fisicamente.

**Boa prática:** usar inativação.

Campos recomendados:

```sql
is_active
inactive_at
inactive_by
inactive_reason
```

---

## 6.2 Ótica do Gestor/Coordenador

O gestor precisa transformar informação em decisão. Ele acompanha prazos, gargalos, responsáveis, atrasos e produtividade.

### Funções principais do gestor

- visualizar demandas por status;
- acompanhar plano de ação;
- aprovar ou reprovar solicitações;
- atribuir responsáveis;
- alterar prioridade;
- validar conclusão;
- acompanhar atrasos;
- acessar relatórios por período, cliente, área ou equipe.

### Boas práticas para telas do gestor

#### Dashboard deve mostrar ação, não apenas número

Indicadores precisam apontar decisões.

**Indicadores úteis:**

- demandas abertas;
- demandas atrasadas;
- tarefas vencendo hoje;
- tarefas sem responsável;
- demandas bloqueadas;
- tempo médio de conclusão;
- taxa de conclusão no mês;
- demandas aguardando aprovação;
- produtividade por equipe.

---

#### Toda demanda deve ter responsável e prazo

Nenhuma demanda aprovada deve ficar sem dono.

**Regra recomendada:** ao mudar uma demanda para `em_execucao`, obrigar:

- responsável;
- prazo;
- prioridade;
- plano de ação ou tarefa mínima;
- categoria.

---

## 6.3 Ótica do Analista

O analista precisa executar o trabalho com clareza, sem excesso de informação e com foco no que precisa ser feito.

### Funções principais do analista

- visualizar tarefas atribuídas;
- atualizar status;
- registrar comentários;
- anexar evidências;
- solicitar informações;
- sinalizar bloqueios;
- concluir etapas;
- consultar histórico da demanda.

### Boas práticas para telas do analista

#### A tela do analista deve priorizar fila de trabalho

O analista não deve depender de relatórios complexos para saber o que fazer.

**Tela recomendada:** `Minhas Ações`

Deve conter:

- tarefas atrasadas;
- tarefas para hoje;
- próximas tarefas;
- tarefas bloqueadas;
- tarefas aguardando retorno;
- filtro por prioridade;
- botão rápido para atualizar status.

---

#### Atualização de status deve pedir contexto

Quando o analista muda um status importante, o sistema deve pedir comentário ou evidência.

**Exemplo:** ao marcar tarefa como `bloqueada`, exigir:

- motivo do bloqueio;
- quem precisa agir;
- previsão de retorno;
- anexo ou evidência, se aplicável.

---

## 6.4 Ótica do Usuário/Solicitante

O usuário precisa abrir solicitações e acompanhar andamento sem conhecer a estrutura interna do sistema.

### Funções principais do usuário

- abrir demanda;
- acompanhar status;
- responder perguntas;
- enviar anexos;
- aprovar ou recusar entrega, quando aplicável;
- consultar histórico básico;
- receber notificações.

### Boas práticas para telas do usuário

#### Formulário deve ser simples e guiado

Usuário comum não deve preencher campos técnicos desnecessários.

**Campos recomendados:**

- título;
- descrição;
- categoria;
- prioridade percebida;
- prazo desejado;
- anexos;
- unidade/área relacionada;
- impacto esperado.

Campos técnicos como responsável interno, SLA, prioridade real e plano de ação devem ser definidos pelo gestor ou analista.

---

#### Status deve ser traduzido para linguagem simples

Status interno pode ser técnico, mas o usuário precisa entender.

| Status interno | Texto para usuário |
|---|---|
| `aberta` | Recebemos sua solicitação |
| `em_analise` | Estamos analisando |
| `aprovada` | Solicitação aprovada |
| `em_execucao` | Em andamento |
| `aguardando_cliente` | Aguardando sua resposta |
| `bloqueada` | Pausada por pendência |
| `concluida` | Concluída |
| `cancelada` | Cancelada |

---

## 7. Regras de negócio essenciais

### 7.1 Toda demanda deve ter dono

Toda demanda precisa ter pelo menos um responsável após ser aceita para execução.

**Regra:** demanda em `em_execucao` não pode ficar sem responsável.

---

### 7.2 Toda demanda deve ter origem

Registrar de onde veio a demanda:

- usuário interno;
- cliente externo;
- integração;
- e-mail;
- WhatsApp;
- formulário;
- importação;
- criação manual.

Campo sugerido:

```sql
source_type
```

---

### 7.3 Toda mudança crítica precisa de motivo

Exigir motivo ao:

- cancelar demanda;
- reprovar solicitação;
- alterar prazo;
- alterar prioridade;
- remover responsável;
- bloquear execução;
- reabrir demanda concluída.

---

### 7.4 Não permitir avanço de status sem pré-requisitos

Cada status deve ter condições mínimas.

| Mudança de status | Pré-requisito obrigatório |
|---|---|
| `rascunho` → `aberta` | título, descrição e solicitante |
| `aberta` → `em_analise` | analista ou gestor responsável |
| `em_analise` → `aprovada` | categoria, prioridade e aprovação |
| `aprovada` → `em_execucao` | responsável, prazo e plano de ação |
| `em_execucao` → `concluida` | evidência ou comentário de conclusão |
| `concluida` → `reaberta` | motivo de reabertura |
| qualquer status → `cancelada` | motivo obrigatório |

---

### 7.5 Usar exclusão lógica

Registros importantes não devem ser apagados fisicamente.

Campos recomendados:

```sql
deleted_at
deleted_by
deleted_reason
```

**Boa prática:** o sistema deve ocultar registros excluídos na interface comum, mas permitir auditoria para perfis autorizados.

---

## 8. Processos principais do sistema

---

## 8.1 Processo de abertura de demanda

### Fluxo recomendado

1. Usuário cria solicitação.
2. Sistema valida dados obrigatórios.
3. Demanda entra como `aberta`.
4. Gestor ou analista recebe notificação.
5. Demanda vai para análise.
6. Gestor aprova, reprova ou solicita mais informações.
7. Se aprovada, sistema cria plano de ação ou tarefas.
8. Responsável executa.
9. Solicitante acompanha.
10. Demanda é concluída, aprovada ou reaberta.

### Telas envolvidas

- Nova Demanda
- Minhas Demandas
- Lista de Demandas
- Detalhe da Demanda
- Plano de Ação
- Comentários/Timeline
- Notificações

### Tabelas envolvidas

- `demands`
- `demand_comments`
- `demand_attachments`
- `demand_status_history`
- `action_plans`
- `tasks`
- `notifications`

---

## 8.2 Processo de aprovação

### Fluxo recomendado

1. Demanda entra em análise.
2. Gestor revisa dados.
3. Gestor pode aprovar, reprovar ou pedir ajuste.
4. Sistema registra decisão.
5. Se aprovada, exige responsável e prazo.
6. Se reprovada, exige motivo.
7. Usuário é notificado.

### Regras importantes

- Aprovação deve gerar log.
- Reprovação deve exigir motivo.
- Aprovador não deve ser apagado do histórico.
- Status anterior e novo status devem ser registrados.

---

## 8.3 Processo de execução

### Fluxo recomendado

1. Demanda aprovada vira plano de ação.
2. Gestor cria tarefas.
3. Cada tarefa tem responsável, prazo e status.
4. Analista atualiza progresso.
5. Bloqueios são registrados.
6. Evidências são anexadas.
7. Gestor valida conclusão.
8. Usuário recebe atualização.

### Status recomendados para tarefa

- `pendente`
- `em_andamento`
- `aguardando_terceiro`
- `bloqueada`
- `concluida`
- `cancelada`

---

## 8.4 Processo de notificação

### Eventos que devem gerar notificação

- nova demanda criada;
- demanda atribuída;
- tarefa atribuída;
- mudança de status;
- comentário mencionando usuário;
- prazo próximo do vencimento;
- tarefa atrasada;
- solicitação aguardando resposta;
- demanda concluída;
- demanda reaberta.

### Canais possíveis

- notificação interna;
- e-mail;
- push;
- SMS;
- WhatsApp;
- webhook.

**Boa prática:** separar o evento da entrega da notificação. O sistema deve registrar o evento mesmo que o envio por e-mail ou WhatsApp falhe.

---

## 9. Mapa de telas coerente com funções e banco de dados

---

## 9.1 Estrutura geral de navegação

### Menu principal recomendado

- Dashboard
- Demandas
- Minhas Ações
- Plano de Ação
- Calendário/Prazos
- Relatórios
- Notificações
- Configurações
- Administração

---

## 9.2 Tela: Login

### Objetivo

Permitir acesso seguro ao sistema.

### Funções

- autenticar usuário;
- recuperar senha;
- validar conta ativa;
- aplicar múltiplo fator, se necessário;
- redirecionar conforme perfil.

### Regras

- usuário inativo não pode acessar;
- tentativas falhas devem ser registradas;
- sessão deve expirar;
- senha nunca deve ser salva em texto puro.

### Tabelas

- `users`
- `user_sessions`
- `login_attempts`
- `password_resets`

---

## 9.3 Tela: Dashboard

### Objetivo

Mostrar uma visão rápida do trabalho, riscos e prioridades.

### Funções

- exibir indicadores por status;
- mostrar atrasos;
- mostrar tarefas do dia;
- mostrar demandas sem responsável;
- filtrar por período, área, equipe e cliente;
- abrir detalhes rapidamente.

### Permissões

- Admin vê visão geral da organização.
- Gestor vê sua área/equipe.
- Analista vê sua fila de trabalho.
- Usuário vê suas solicitações.

### Tabelas

- `demands`
- `tasks`
- `users`
- `teams`
- `status_history`

---

## 9.4 Tela: Lista de Demandas

### Objetivo

Permitir consultar, filtrar, organizar e acessar demandas.

### Funções

- listar demandas;
- filtrar por status;
- filtrar por responsável;
- filtrar por prioridade;
- filtrar por prazo;
- filtrar por cliente/unidade;
- exportar dados conforme permissão;
- abrir detalhe da demanda.

### Campos exibidos

- código;
- título;
- status;
- prioridade;
- solicitante;
- responsável;
- prazo;
- data de criação;
- última atualização.

### Tabelas

- `demands`
- `users`
- `priorities`
- `categories`
- `organizations`

---

## 9.5 Tela: Nova Demanda

### Objetivo

Permitir que usuário ou analista cadastre uma solicitação.

### Funções

- criar demanda;
- anexar arquivos;
- selecionar categoria;
- informar impacto;
- salvar rascunho;
- enviar para análise.

### Regras

- título e descrição obrigatórios;
- solicitante obrigatório;
- organização obrigatória;
- categoria pode ser obrigatória conforme regra do projeto;
- anexos devem seguir regras de tamanho e tipo;
- demanda enviada entra como `aberta`.

### Tabelas

- `demands`
- `demand_attachments`
- `categories`
- `users`

---

## 9.6 Tela: Detalhe da Demanda

### Objetivo

Centralizar todas as informações da demanda.

### Blocos recomendados

- resumo da demanda;
- status atual;
- solicitante;
- responsável;
- prioridade;
- prazo;
- descrição;
- anexos;
- comentários;
- timeline;
- plano de ação;
- decisões/aprovações.

### Funções

- editar informações conforme permissão;
- alterar status;
- comentar;
- anexar evidência;
- aprovar/reprovar;
- criar tarefa;
- atribuir responsável;
- cancelar;
- reabrir;
- consultar histórico.

### Tabelas

- `demands`
- `demand_comments`
- `demand_attachments`
- `demand_status_history`
- `approvals`
- `tasks`
- `audit_logs`

---

## 9.7 Tela: Plano de Ação

### Objetivo

Quebrar uma demanda em ações executáveis.

### Funções

- criar tarefas;
- definir responsável;
- definir prazo;
- definir prioridade;
- acompanhar progresso;
- anexar evidências;
- concluir tarefa;
- registrar bloqueio;
- reordenar tarefas;
- filtrar tarefas.

### Campos de tarefa

- título;
- descrição;
- responsável;
- prazo;
- status;
- prioridade;
- dependência;
- percentual de conclusão;
- evidência;
- data de conclusão.

### Tabelas

- `action_plans`
- `tasks`
- `task_comments`
- `task_attachments`
- `task_status_history`

---

## 9.8 Tela: Minhas Ações

### Objetivo

Mostrar ao analista, gestor ou usuário tudo que exige ação dele.

### Funções

- visualizar tarefas atribuídas;
- visualizar aprovações pendentes;
- visualizar respostas solicitadas;
- atualizar status rápido;
- comentar;
- anexar evidência;
- concluir tarefa.

### Filtros úteis

- atrasadas;
- hoje;
- esta semana;
- alta prioridade;
- bloqueadas;
- aguardando resposta;
- concluídas recentemente.

### Tabelas

- `tasks`
- `demands`
- `approvals`
- `notifications`

---

## 9.9 Tela: Relatórios

### Objetivo

Apoiar gestão, auditoria e melhoria de processo.

### Relatórios recomendados

- demandas por status;
- demandas por categoria;
- demandas por cliente/unidade;
- demandas por responsável;
- tempo médio de conclusão;
- tarefas atrasadas;
- produtividade por equipe;
- demandas reabertas;
- motivos de cancelamento;
- volume mensal;
- SLA cumprido x descumprido.

### Permissões

- Usuário comum não acessa relatórios gerais.
- Analista pode ver relatórios operacionais da própria equipe.
- Gestor pode ver relatórios da área.
- Admin pode ver relatórios da organização.
- Auditor pode ver relatórios em modo leitura.

### Tabelas

- `demands`
- `tasks`
- `users`
- `teams`
- `status_history`
- `audit_logs`

---

## 9.10 Tela: Administração de Usuários

### Objetivo

Gerenciar acessos da organização.

### Funções

- criar usuário;
- editar usuário;
- inativar usuário;
- redefinir senha;
- vincular perfil;
- vincular equipe;
- vincular unidade;
- consultar último acesso;
- bloquear usuário.

### Regras

- não permitir excluir usuário com histórico;
- e-mail deve ser único por organização ou global, conforme arquitetura;
- usuário deve ter pelo menos um perfil;
- alteração de perfil deve gerar log;
- admin não deve remover o último administrador ativo da organização.

### Tabelas

- `users`
- `roles`
- `permissions`
- `user_roles`
- `teams`
- `user_teams`
- `audit_logs`

---

## 9.11 Tela: Perfis e Permissões

### Objetivo

Controlar o que cada perfil pode fazer.

### Funções

- listar perfis;
- criar perfil personalizado;
- editar permissões;
- clonar perfil;
- inativar perfil;
- visualizar usuários vinculados;
- consultar matriz de permissão.

### Regras

- perfis nativos do sistema não devem ser apagados;
- alteração de permissão deve gerar log;
- permissões críticas exigem confirmação;
- somente administradores autorizados podem alterar permissões.

### Tabelas

- `roles`
- `permissions`
- `role_permissions`
- `users`
- `audit_logs`

---

## 9.12 Tela: Configurações

### Objetivo

Permitir parametrizar o sistema sem alterar código.

### Funções

- configurar categorias;
- configurar status;
- configurar prioridades;
- configurar SLA;
- configurar templates;
- configurar notificações;
- configurar integrações;
- configurar dados da organização.

### Tabelas

- `settings`
- `categories`
- `priorities`
- `notification_templates`
- `integrations`
- `organizations`

---

## 10. Modelo de banco de dados recomendado

---

## 10.1 Tabelas principais

### `organizations`

Representa empresas, clientes ou tenants do SaaS.

Campos sugeridos:

```sql
id
name
slug
status
plan_id
created_at
updated_at
```

---

### `users`

Representa usuários do sistema.

Campos sugeridos:

```sql
id
organization_id
name
email
password_hash
phone
status
is_active
last_login_at
created_at
updated_at
```

---

### `roles`

Representa perfis de acesso.

Campos sugeridos:

```sql
id
organization_id
name
code
description
is_system_role
created_at
updated_at
```

---

### `permissions`

Representa permissões granulares.

Campos sugeridos:

```sql
id
module
action
code
description
created_at
```

Exemplo de código:

```text
demands.view_all
demands.create
demands.edit
demands.approve
demands.cancel
reports.export
users.manage
settings.manage
```

---

### `role_permissions`

Relaciona perfis com permissões.

Campos sugeridos:

```sql
id
role_id
permission_id
created_at
```

---

### `user_roles`

Relaciona usuários com perfis.

Campos sugeridos:

```sql
id
user_id
role_id
scope_type
scope_id
created_at
```

---

### `demands`

Representa solicitações, demandas, chamados ou projetos.

Campos sugeridos:

```sql
id
organization_id
code
title
description
category_id
priority_id
status
source_type
requester_id
responsible_id
team_id
due_date
started_at
completed_at
cancelled_at
cancel_reason
created_at
updated_at
deleted_at
```

---

### `tasks`

Representa ações dentro de uma demanda.

Campos sugeridos:

```sql
id
organization_id
demand_id
title
description
status
priority_id
responsible_id
due_date
completed_at
blocked_reason
created_at
updated_at
deleted_at
```

---

### `comments`

Comentários em demandas ou tarefas.

Campos sugeridos:

```sql
id
organization_id
entity_type
entity_id
user_id
comment
visibility
created_at
updated_at
deleted_at
```

---

### `attachments`

Arquivos anexados.

Campos sugeridos:

```sql
id
organization_id
entity_type
entity_id
uploaded_by
file_name
file_path
file_type
file_size
visibility
created_at
deleted_at
```

---

### `status_history`

Histórico de mudanças de status.

Campos sugeridos:

```sql
id
organization_id
entity_type
entity_id
old_status
new_status
changed_by
change_reason
created_at
```

---

### `audit_logs`

Auditoria geral do sistema.

Campos sugeridos:

```sql
id
organization_id
user_id
action
module
entity_type
entity_id
old_values
new_values
ip_address
user_agent
created_at
```

---

### `notifications`

Notificações internas e externas.

Campos sugeridos:

```sql
id
organization_id
user_id
title
message
type
channel
status
read_at
sent_at
created_at
```

---

## 10.2 Relacionamentos importantes

- Uma organização possui muitos usuários.
- Um usuário pode ter vários perfis.
- Um perfil possui várias permissões.
- Uma demanda pertence a uma organização.
- Uma demanda possui um solicitante.
- Uma demanda pode possuir um responsável.
- Uma demanda possui várias tarefas.
- Uma tarefa pode possuir comentários e anexos.
- Toda alteração crítica deve gerar histórico.
- Todo registro operacional deve respeitar `organization_id`.

---

## 11. Boas práticas de nomenclatura

### 11.1 Nomes de tabelas

Usar nomes no plural, em inglês ou português, mas manter padrão único.

**Exemplo em inglês:**

- `users`
- `demands`
- `tasks`
- `attachments`
- `audit_logs`

**Exemplo em português:**

- `usuarios`
- `demandas`
- `tarefas`
- `anexos`
- `logs_auditoria`

**Boa prática:** não misturar português e inglês no mesmo banco.

---

### 11.2 Nomes de campos

Usar nomes claros e previsíveis.

Preferir:

```sql
created_at
updated_at
deleted_at
created_by
updated_by
```

Evitar:

```sql
dt_cri
usr_alt
stt
obs2
```

---

## 12. Regras de segurança e controle de acesso

### 12.1 Validar permissão no backend

Toda rota, endpoint ou função sensível deve verificar permissão.

Exemplo conceitual:

```text
Usuário quer aprovar demanda.
Sistema valida:
1. Está autenticado?
2. Está ativo?
3. Pertence à organização correta?
4. Tem permissão demands.approve?
5. A demanda está em status que permite aprovação?
6. A ação exige motivo ou observação?
```

---

### 12.2 Proteger dados por organização

Nunca buscar dados apenas por `id` em sistema multiempresa.

**Risco:** um usuário alterar a URL e acessar registro de outra organização.

**Boa prática:** sempre filtrar por `organization_id`.

Exemplo correto:

```sql
SELECT * FROM demands
WHERE id = :id
AND organization_id = :organization_id;
```

---

### 12.3 Usar logs para ações críticas

Ações críticas:

- login;
- erro de login;
- alteração de senha;
- alteração de permissão;
- exclusão lógica;
- exportação de dados;
- alteração de status;
- aprovação;
- cancelamento;
- alteração de prazo;
- acesso negado.

---

## 13. Boas práticas para criação de mapa de telas

### 13.1 Toda tela deve ter ficha técnica

Cada tela do projeto deve ser documentada com:

```md
## Tela: Nome da Tela

### Objetivo
Explique por que a tela existe.

### Perfis que acessam
Liste quem pode acessar.

### Funções disponíveis
Liste as ações da tela.

### Regras de negócio
Liste as validações e condições.

### Campos exibidos
Liste os dados principais.

### Ações permitidas
Liste botões e operações.

### Tabelas envolvidas
Liste tabelas do banco.

### Eventos gerados
Liste logs, notificações ou integrações.
```

---

### 13.2 Mapa de tela deve seguir o fluxo real

Não organizar telas apenas por gosto visual. Organizar conforme processo.

Exemplo de fluxo:

```text
Login
→ Dashboard
→ Lista de Demandas
→ Nova Demanda
→ Detalhe da Demanda
→ Plano de Ação
→ Minhas Ações
→ Relatórios
→ Administração
```

---

### 13.3 Evitar telas duplicadas

Se duas telas fazem quase a mesma coisa, avaliar se podem ser uma tela com filtros, abas ou permissões diferentes.

**Exemplo:**

- `Demandas do Usuário`
- `Demandas do Analista`
- `Demandas do Gestor`

Podem virar uma única tela `Lista de Demandas`, com filtros e permissões por perfil.

---

## 14. Funções essenciais do sistema

---

## 14.1 Funções de autenticação

- login;
- logout;
- recuperação de senha;
- troca de senha;
- bloqueio por tentativas inválidas;
- controle de sessão;
- autenticação em dois fatores, se necessário.

---

## 14.2 Funções de usuário

- criar usuário;
- editar usuário;
- inativar usuário;
- alterar perfil;
- alterar equipe;
- consultar último acesso;
- redefinir senha;
- bloquear acesso.

---

## 14.3 Funções de demanda

- criar demanda;
- editar demanda;
- enviar para análise;
- aprovar;
- reprovar;
- atribuir responsável;
- alterar prioridade;
- alterar prazo;
- cancelar;
- concluir;
- reabrir;
- comentar;
- anexar arquivo;
- consultar histórico.

---

## 14.4 Funções de tarefa/plano de ação

- criar tarefa;
- editar tarefa;
- definir responsável;
- definir prazo;
- iniciar tarefa;
- pausar tarefa;
- bloquear tarefa;
- concluir tarefa;
- anexar evidência;
- comentar;
- reabrir tarefa.

---

## 14.5 Funções de relatório

- filtrar por período;
- filtrar por status;
- filtrar por equipe;
- filtrar por responsável;
- exportar dados;
- gerar indicadores;
- acompanhar SLA;
- consultar histórico.

---

## 15. Checklist de qualidade para validar o projeto

### 15.1 Checklist de perfis e permissões

- [ ] Existem perfis suficientes para separar responsabilidades?
- [ ] Cada perfil possui permissões claras?
- [ ] Existe separação entre visualizar, criar, editar, aprovar e excluir?
- [ ] O backend valida permissão?
- [ ] O frontend oculta ações não permitidas?
- [ ] Existe log para alteração de permissão?
- [ ] Usuários inativos são bloqueados?
- [ ] Existe escopo por organização, equipe ou usuário?

---

### 15.2 Checklist de regras de negócio

- [ ] Cada status tem significado claro?
- [ ] Mudanças de status têm regras?
- [ ] Cancelamento exige motivo?
- [ ] Reabertura exige motivo?
- [ ] Conclusão exige evidência?
- [ ] Demanda em execução exige responsável?
- [ ] Prazos são obrigatórios quando necessário?
- [ ] Dados obrigatórios são validados no backend?

---

### 15.3 Checklist de mapa de telas

- [ ] Cada tela tem objetivo claro?
- [ ] Cada tela tem perfis de acesso definidos?
- [ ] Cada botão tem regra de permissão?
- [ ] Cada tela informa quais tabelas usa?
- [ ] O fluxo de navegação acompanha o processo real?
- [ ] Não existem telas duplicadas?
- [ ] Existem estados vazios, erro, carregamento e sucesso?
- [ ] Existe versão responsiva para mobile, quando necessário?

---

### 15.4 Checklist de banco de dados

- [ ] Tabelas possuem `id` único?
- [ ] Tabelas operacionais possuem `organization_id`?
- [ ] Existem campos `created_at` e `updated_at`?
- [ ] Existe exclusão lógica onde necessário?
- [ ] Existem relacionamentos bem definidos?
- [ ] Existem índices para filtros frequentes?
- [ ] Existe histórico para alterações críticas?
- [ ] Arquivos/anexos têm controle de acesso?

---

## 16. Modelo de documentação para cada módulo

Use o padrão abaixo para documentar qualquer módulo do sistema.

```md
# Módulo: [Nome do Módulo]

## Objetivo
Descrever o objetivo do módulo.

## Perfis que acessam
- Super Admin
- Admin Organização
- Gestor
- Analista
- Usuário

## Funções
- Função 1
- Função 2
- Função 3

## Regras de negócio
- Regra 1
- Regra 2
- Regra 3

## Telas
- Tela 1
- Tela 2
- Tela 3

## Tabelas
- tabela_1
- tabela_2
- tabela_3

## Permissões
| Ação | Permissão | Perfis autorizados |
|---|---|---|
| Visualizar | modulo.view | Admin, Gestor, Analista |
| Criar | modulo.create | Admin, Gestor |
| Editar | modulo.edit | Admin, Gestor |
| Excluir | modulo.delete | Admin |

## Logs gerados
- Criação
- Edição
- Exclusão
- Alteração de status

## Notificações
- Evento 1
- Evento 2
```

---

## 17. Prompt operacional para IA criar mapa de telas

Use este prompt quando quiser que uma IA crie um mapa de telas completo.

```md
Aja como um gerente de projeto sênior, arquiteto de sistema e especialista em SaaS/App.

Crie um mapa de telas completo para o projeto abaixo, considerando:

1. perfis de usuário;
2. permissões por perfil;
3. regras de negócio;
4. processos principais;
5. funções por tela;
6. campos exibidos;
7. ações permitidas;
8. tabelas de banco de dados envolvidas;
9. logs e notificações geradas;
10. fluxo de navegação desktop e mobile.

Para cada tela, use o formato:

## Tela: [Nome]
### Objetivo
### Perfis que acessam
### Funções disponíveis
### Regras de negócio
### Campos exibidos
### Ações permitidas
### Tabelas envolvidas
### Eventos gerados
### Observações de UX/UI

Projeto:
[DESCREVA O PROJETO AQUI]
```

---

## 18. Prompt operacional para IA criar matriz de permissões

```md
Aja como um gerente de projeto sênior e arquiteto de sistema.

Crie uma matriz de permissões completa para o sistema abaixo.

Considere os perfis:
- Super Admin
- Admin Organização
- Gestor
- Analista
- Usuário/Solicitante
- Cliente Externo
- Auditor

Para cada módulo, defina permissões de:
- visualizar;
- criar;
- editar;
- excluir/inativar;
- aprovar;
- cancelar;
- exportar;
- configurar;
- auditar.

Também defina o escopo de acesso:
- global;
- organização;
- unidade;
- equipe;
- próprio usuário;
- compartilhado.

Sistema:
[DESCREVA O SISTEMA AQUI]
```

---

## 19. Prompt operacional para IA criar banco de dados

```md
Aja como um arquiteto de banco de dados sênior.

Com base no sistema abaixo, crie um modelo de banco de dados relacional coerente com:

1. perfis e permissões;
2. usuários e organizações;
3. processos de negócio;
4. telas do sistema;
5. demandas, tarefas, anexos e comentários;
6. logs e auditoria;
7. notificações;
8. exclusão lógica;
9. histórico de status;
10. segurança multiempresa.

Para cada tabela, informe:
- objetivo;
- campos principais;
- chaves estrangeiras;
- índices recomendados;
- regras de integridade;
- observações de segurança.

Sistema:
[DESCREVA O SISTEMA AQUI]
```

---

## 20. Erros comuns que devem ser evitados

### 20.1 Criar telas antes de entender o processo

Isso gera sistema bonito, mas confuso e difícil de manter.

---

### 20.2 Usar permissões genéricas demais

Permissões como `admin` e `user` não são suficientes para sistemas profissionais.

---

### 20.3 Não registrar histórico

Sem histórico, o sistema perde rastreabilidade e fica frágil para auditoria.

---

### 20.4 Misturar dados de empresas diferentes

Em SaaS, sempre proteger dados por organização/tenant.

---

### 20.5 Deixar regra importante só no frontend

Qualquer regra crítica deve ser validada no backend.

---

### 20.6 Permitir exclusão física de dados importantes

Preferir inativação ou exclusão lógica.

---

### 20.7 Criar campos técnicos para usuário comum

Usuário final deve preencher somente o necessário. Campos técnicos devem ser responsabilidade de analista, gestor ou administrador.

---

## 21. Modelo final de entrega esperado pela IA

Quando a IA usar esta skill, a entrega deve conter:

1. Visão geral do sistema.
2. Perfis de usuários.
3. Matriz de permissões.
4. Processos principais.
5. Regras de negócio.
6. Mapa de telas.
7. Funções por tela.
8. Modelo de banco de dados.
9. Logs e auditoria.
10. Notificações.
11. Checklist de validação.
12. Recomendações de segurança.

---

## 22. Regra de ouro

Um sistema SaaS/App bem arquitetado não nasce da tela. Ele nasce da clareza entre:

```text
Processo → Regra de negócio → Perfil de usuário → Permissão → Tela → Função → Banco de dados → Log → Relatório
```

Se qualquer uma dessas partes estiver desconectada, o projeto tende a ficar confuso, inseguro ou difícil de evoluir.

---

## 23. Comando interno para a IA

Sempre que receber um pedido de criação de sistema, app, SaaS, painel administrativo, área do usuário ou mapa de telas, siga esta sequência:

1. Identifique o processo de negócio.
2. Liste os atores do sistema.
3. Defina níveis de usuário.
4. Crie matriz de permissões.
5. Modele os fluxos principais.
6. Defina status e regras de transição.
7. Crie mapa de telas por perfil.
8. Liste funções por tela.
9. Relacione cada tela com tabelas do banco.
10. Defina logs, notificações e relatórios.
11. Valide segurança, auditoria e multiempresa.
12. Entregue documentação clara em Markdown.

---

**Fim da skill.**
