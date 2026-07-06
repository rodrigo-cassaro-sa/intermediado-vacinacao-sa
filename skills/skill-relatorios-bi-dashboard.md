# Skill: Relatórios, BI e Dashboards para SaaS, App e Software

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa especialista em **produto, dados, BI, UX de dashboards, SQL, métricas e tomada de decisão** para criar, revisar e evoluir relatórios, indicadores, painéis gerenciais e dashboards operacionais.

O foco é transformar dados do sistema em informação útil, confiável, visualmente clara e acionável.

Stack padrão:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
APIs JSON
Dashboards web sem framework por padrão
```

Esta skill complementa as skills de dados, MySQL, performance, frontend, UX/UI, API REST, segurança, LGPD, permissões e QA.

---

## Limite desta skill

Esta skill define relatórios, dashboards, BI, KPIs, métricas, filtros, gráficos, exportações e apoio à tomada de decisão.

Ela pode consultar regras de vendas, suporte, admin, multitenant, dados e performance para calcular indicadores corretamente, mas não deve substituir:

- `skill-vendas-pagamentos.md` para regra oficial de pedido, pagamento, voucher, cobrança e receita;
- `skill-suporte-atendimento-sla.md` para regra oficial de chamado, SLA e atendimento;
- `skill-admin-operacional.md` para operação administrativa;
- `skill-multitenant-workspaces.md` para separação por tenant/workspace;
- `skill-dados.md` e `skill-mysql.md` para modelagem e SQL estrutural;
- `skill-performance.md` para otimização geral.

Esta skill responde "quais números ajudam o usuário ou gestor a decidir melhor, com regra de cálculo confiável?".

---

## Regra de nomenclatura de campos

Por padrão, este projeto usa nomes de campos em português para datas, auditoria e histórico.

### Campos padrão

```txt
criado_em
atualizado_em
excluido_em
cancelado_em
confirmado_em
pago_em
expira_em
disponivel_em
emitido_em
processado_em
usado_em
aceito_em
primeira_resposta_em
resolvido_em
fechado_em
sla_primeira_resposta_em
sla_resolucao_em
criado_por
atualizado_por
excluido_por
```

### Regra obrigatória

Não misturar nomes de campos em inglês com nomes em português no mesmo projeto.

Quando o projeto usar português, todos os exemplos de tabelas, campos, históricos, filtros, relatórios e payloads internos devem seguir o padrão em português.

Exceção: termos técnicos de SaaS como `tenant_id`, `workspace_id`, `request_id`, `trace_id`, `uuid_publico`, `metadata`, `related_type` e `related_id` podem ser mantidos quando esse for o padrão técnico escolhido no projeto.

---

## Fonte da verdade dos indicadores

Todo indicador deve declarar qual skill ou regra de negócio é sua fonte da verdade.

Exemplos:

- Receita confirmada usa regra da `skill-vendas-pagamentos.md`.
- Pagamentos pendentes usam regra da `skill-vendas-pagamentos.md`.
- Chamados vencidos usam regra da `skill-suporte-atendimento-sla.md`.
- Usuários ativos por tenant usam regra da `skill-multitenant-workspaces.md`.
- Ações administrativas críticas usam regra da `skill-admin-operacional.md`.

A skill de relatórios não deve redefinir status, cálculos financeiros, SLA ou escopo de tenant.

Ela apenas calcula e apresenta com base nas regras oficiais.

---

## Papel da IA

Ao usar esta skill, a IA deve agir como uma pessoa sênior em BI e dashboards de sistemas.

A IA deve pensar como:

- analista de dados;
- engenheiro de produto;
- designer de dashboards;
- desenvolvedor backend;
- especialista em SQL;
- gestor operacional;
- QA de indicadores.

A IA não deve criar dashboard bonito sem utilidade. Todo gráfico, card, tabela ou indicador precisa responder uma pergunta real do negócio.

---

## Princípio central

```txt
Dashboard bom não é o que mostra mais dados. É o que ajuda a tomar melhor decisão com menos esforço.
```

Antes de criar qualquer indicador, a IA deve responder:

- Quem vai usar?
- Qual decisão precisa tomar?
- Qual ação o dado deve provocar?
- Qual período será analisado?
- Qual filtro é necessário?
- Qual regra de cálculo define o número?
- Como validar se o número está correto?

---

## Quando usar esta skill

Use esta skill quando o projeto envolver:

- dashboard administrativo;
- painel de indicadores;
- relatório operacional;
- relatório financeiro;
- relatório comercial;
- relatório de vendas;
- relatório de vouchers;
- relatório de usuários;
- relatório de retenção;
- relatório de performance;
- exportação CSV ou Excel;
- gráficos;
- cards de KPI;
- ranking;
- funil;
- comparativo de períodos;
- análise por cliente, unidade, equipe ou status.

---

## Quando não usar esta skill

Não use esta skill para:

- tela simples de cadastro;
- tabela CRUD sem análise;
- formulário operacional;
- fluxo de autenticação;
- layout visual sem dados.

Nesses casos, use as skills de telas, frontend, backend, dados ou UX/UI.

---

## Diferença entre relatório, dashboard e BI

```txt
Relatório = lista ou visão detalhada para conferência.
Dashboard = painel visual com indicadores para decisão rápida.
BI = camada analítica que cruza dados, tendências e métricas estratégicas.
```

Exemplo:

- Relatório: lista de pedidos pagos no mês.
- Dashboard: total vendido, vouchers usados, pedidos pendentes e taxa de conversão.
- BI: crescimento mensal, sazonalidade, cohort de retenção e previsão.

---

## Tipos de dashboard

### Dashboard operacional

Mostra o que precisa de ação agora.

Exemplos:

- pedidos aguardando pagamento;
- vouchers próximos de expirar;
- tarefas atrasadas;
- integrações com erro;
- pagamentos pendentes;
- usuários bloqueados;
- filas de processamento.

Características:

- atualização frequente;
- destaque para exceções;
- filtros rápidos;
- ações diretas.

---

### Dashboard gerencial

Mostra desempenho em um período.

Exemplos:

- vendas por mês;
- receita por produto;
- taxa de conversão;
- usuários ativos;
- tickets resolvidos;
- produtividade por equipe.

Características:

- visão resumida;
- comparação de período;
- metas;
- tendências;
- exportação.

---

### Dashboard estratégico

Mostra evolução e decisão de longo prazo.

Exemplos:

- crescimento de receita;
- retenção de usuários;
- churn;
- LTV;
- CAC;
- margem;
- expansão por cliente;
- performance por canal.

Características:

- menos atualização imediata;
- mais análise;
- gráficos históricos;
- segmentação;
- comparativos.

---

## Processo recomendado para criar dashboards

### 1. Entender o objetivo

Antes de criar o painel, documentar:

```md
Nome do dashboard:
Usuário principal:
Objetivo:
Decisão que apoia:
Frequência de uso:
Período padrão:
Filtros necessários:
Ações que o usuário pode tomar:
```

---

### 2. Definir perguntas de negócio

Todo indicador nasce de uma pergunta.

Exemplos:

- Quanto vendemos hoje?
- Quantos vouchers foram utilizados?
- Quais pagamentos estão pendentes?
- Qual produto vende mais?
- Qual cliente está mais ativo?
- Qual equipe está atrasada?
- Qual integração falhou?
- O uso caiu em relação à semana passada?
- Qual canal gera mais conversão?

Se a pergunta não estiver clara, o gráfico não deve ser criado.

---

### 3. Definir KPIs

Cada KPI deve ter:

```md
Nome:
Descrição:
Fórmula:
Fonte de dados:
Filtros aplicáveis:
Período padrão:
Responsável pela regra:
Como validar:
Ação recomendada:
```

Exemplo:

```md
Nome: Receita confirmada
Descrição: Soma dos pagamentos aprovados no período
Fórmula: SUM(pagamentos.valor) WHERE status = 'pago'
Fonte: tabela pagamentos
Filtros: data, produto, canal, unidade
Período padrão: mês atual
Ação: acompanhar resultado financeiro
```

---

### 4. Validar a fonte de dados

Antes de mostrar número:

- verificar tabela correta;
- validar status considerados;
- validar data usada;
- validar fuso horário;
- validar duplicidade;
- validar cancelamentos;
- validar reembolsos;
- validar permissões;
- validar se dados incompletos devem entrar.

Regra:

```txt
Número sem regra de cálculo documentada não é indicador confiável.
```

---

## Cards de KPI

Cards devem mostrar poucos números importantes.

Exemplo:

```txt
Receita confirmada
Pedidos pagos
Pagamentos pendentes
Vouchers emitidos
Vouchers utilizados
Taxa de conversão
Ticket médio
Usuários ativos
```

Cada card deve ter:

- título claro;
- valor principal;
- período;
- variação comparativa, quando útil;
- contexto;
- link para detalhamento;
- estado vazio;
- estado de erro.

Evite:

- muitos cards;
- números sem período;
- valores sem unidade;
- porcentagem sem explicação;
- comparação injusta entre períodos diferentes.

---

## Gráficos

Escolha o gráfico pelo tipo de pergunta.

| Pergunta | Gráfico recomendado |
|---|---|
| Evolução no tempo | Linha ou colunas |
| Comparar categorias | Barras |
| Mostrar composição simples | Pizza ou rosca com poucas categorias |
| Mostrar funil | Funil |
| Ranking | Barras ordenadas |
| Distribuição | Histograma |
| Relação entre variáveis | Dispersão |
| Status operacional | Cards e tabela |

Evite gráfico de pizza com muitas fatias. Em muitos casos, barras ordenadas são mais fáceis de entender.

---

## Tabelas analíticas

Tabelas continuam importantes em dashboards.

Boas práticas:

- filtros por período;
- busca;
- ordenação;
- paginação;
- colunas essenciais;
- totalizadores;
- exportação;
- status visual;
- ações rápidas;
- link para detalhe;
- destaque de exceções.

Evite carregar milhares de linhas de uma vez.

---

## Filtros

Filtros recomendados:

- período;
- status;
- cliente;
- unidade;
- produto;
- canal;
- responsável;
- equipe;
- método de pagamento;
- origem;
- cidade;
- perfil de usuário.

Boas práticas:

- período padrão sensato;
- botão limpar filtros;
- filtros persistentes, quando útil;
- filtros avançados recolhidos;
- feedback de carregamento;
- mostrar quais filtros estão ativos.

---

## Períodos e comparação

Todo dashboard deve deixar claro o período.

Exemplos:

```txt
Hoje
Ontem
Últimos 7 dias
Últimos 30 dias
Mês atual
Mês anterior
Ano atual
Período personalizado
```

Comparações úteis:

- hoje vs ontem;
- semana atual vs semana anterior;
- mês atual vs mês anterior;
- ano atual vs ano anterior;
- acumulado até a data.

Cuidado:

- comparar meses com quantidades de dias diferentes;
- comparar período incompleto com período completo;
- ignorar sazonalidade;
- misturar data de criação com data de pagamento.

---

## Métricas para venda de vouchers

Para sistema de vouchers, considerar:

- total vendido;
- receita confirmada;
- pedidos criados;
- pedidos pagos;
- pedidos cancelados;
- pagamentos pendentes;
- pagamentos recusados;
- taxa de conversão;
- vouchers emitidos;
- vouchers utilizados;
- vouchers expirados;
- vouchers cancelados;
- valor por produto;
- vendas por canal;
- vendas por dia;
- ticket médio;
- cupons utilizados;
- descontos concedidos;
- reembolsos;
- divergências de conciliação.

---

## Métricas para SaaS

Para SaaS, considerar:

- usuários ativos diários;
- usuários ativos semanais;
- usuários ativos mensais;
- novos cadastros;
- ativação;
- retenção;
- churn;
- planos ativos;
- assinaturas inadimplentes;
- receita recorrente;
- upgrade;
- downgrade;
- uso por funcionalidade;
- tickets de suporte;
- tempo de resposta.

---

## Modelagem para relatórios

Nem todo relatório deve consultar diretamente tabelas operacionais pesadas.

Estratégias:

- views SQL;
- tabelas de resumo;
- cache de indicadores;
- jobs de atualização;
- snapshots;
- materialização de métricas;
- índices específicos;
- paginação;
- filtros obrigatórios.

Regra:

```txt
Relatório não pode derrubar a operação principal.
```

---

## Performance em dashboards

Boas práticas:

- usar índices;
- limitar período padrão;
- paginar tabelas;
- evitar `SELECT *`;
- evitar consultas sem filtro em tabelas grandes;
- usar cache para indicadores pesados;
- carregar gráficos sob demanda;
- separar endpoints por bloco;
- medir tempo de resposta;
- exibir loading;
- tratar erro de API.

Endpoints de dashboard devem retornar apenas o necessário.

---

## Segurança e permissões

Indicadores podem revelar dados sensíveis.

A IA deve validar:

- quem pode ver o dashboard;
- quais dados cada perfil pode acessar;
- se o usuário só pode ver sua unidade, equipe ou cliente;
- se dados financeiros devem ser restritos;
- se exportação é permitida;
- se dados pessoais devem ser mascarados;
- se logs registram consulta sensível.

Regra:

```txt
Permissão de tela não basta. A API do relatório também deve filtrar os dados.
```

---

## LGPD nos relatórios

Evite expor dados pessoais sem necessidade.

Boas práticas:

- usar dados agregados sempre que possível;
- mascarar CPF, telefone e e-mail;
- limitar exportação;
- registrar quem exportou;
- evitar dados sensíveis em gráficos;
- permitir acesso por perfil;
- justificar finalidade do relatório.

---

## Estados de tela

Todo dashboard deve ter estados:

```txt
carregando
sem_dados
erro
dados_parciais
dados_atualizados
filtro_sem_resultado
sem_permissao
```

Mensagens devem ser claras.

Exemplo:

```txt
Nenhuma venda encontrada para o período selecionado.
```

Evite:

```txt
Erro 500.
```

---

## Exportações

Exportações devem ser controladas.

Formatos possíveis:

- CSV;
- Excel;
- PDF;
- JSON, quando técnico.

Boas práticas:

- respeitar filtros aplicados;
- limitar volume;
- registrar log de exportação;
- mascarar dados sensíveis;
- incluir data de geração;
- incluir usuário que gerou;
- incluir legenda dos campos;
- evitar exportação aberta para qualquer perfil.

---

## Auditoria dos indicadores

Para indicadores críticos, registrar:

- fórmula;
- data de atualização;
- fonte;
- filtros;
- usuário que consultou;
- exportações;
- mudanças de regra;
- divergências encontradas.

Indicador financeiro, operacional ou regulatório precisa ser auditável.

---

## QA de dashboard

Testar:

- cálculo correto;
- filtros;
- período;
- status incluídos e excluídos;
- permissão;
- exportação;
- paginação;
- estado sem dados;
- erro de API;
- performance;
- responsividade;
- comparação entre períodos;
- conciliação com relatórios externos.

Modelo de caso de teste:

```md
ID: BI-001
Título: Receita confirmada do mês atual
Pré-condição: Existem pedidos pagos e cancelados no mês
Passos:
1. Acessar dashboard financeiro
2. Filtrar mês atual
3. Conferir card Receita confirmada
Resultado esperado:
O valor deve somar apenas pagamentos com status pago, excluindo cancelados, recusados e reembolsados.
```

---

## Checklist da IA antes de entregar

- [ ] O dashboard tem objetivo claro.
- [ ] Cada indicador responde uma pergunta.
- [ ] Cada KPI tem fórmula documentada.
- [ ] O período está claro.
- [ ] Os filtros são necessários e úteis.
- [ ] A fonte de dados está definida.
- [ ] Permissões foram consideradas.
- [ ] LGPD foi considerada.
- [ ] Consultas têm performance aceitável.
- [ ] Existe estado vazio e estado de erro.
- [ ] Exportação respeita filtros e permissões.
- [ ] Indicadores críticos têm validação.
- [ ] O layout funciona em desktop e celular.
- [ ] O painel ajuda a tomar ação, não só a olhar números.

---

## Saída esperada da IA

Quando esta skill for usada, a IA deve entregar, conforme o pedido:

- estrutura do dashboard;
- lista de KPIs;
- fórmulas dos indicadores;
- fontes de dados;
- filtros;
- gráficos recomendados;
- tabelas analíticas;
- endpoints necessários;
- regras de permissão;
- cuidados de performance;
- casos de teste;
- documentação do relatório.

A entrega deve sempre separar número, fórmula, fonte, filtro e decisão apoiada.
