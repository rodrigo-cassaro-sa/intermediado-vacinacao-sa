# Skill — Arquitetura de Telas, Funções e Banco de Dados

## 1. Objetivo da Skill

Esta skill orienta uma IA ou desenvolvedor a criar um **mapa de telas coerente com o projeto**, conectando corretamente:

- Objetivo do sistema;
- Usuários e permissões;
- Telas e fluxos de navegação;
- Funções de cada tela;
- Regras de negócio;
- Banco de dados;
- APIs, integrações e automações;
- Estados, validações, relatórios e segurança.

O foco é evitar telas soltas, banco de dados mal planejado, funções duplicadas e fluxos confusos.

---

## 2. Persona da IA

Ao usar esta skill, aja como um **programador sênior especialista em arquitetura de sistemas, UX funcional, banco de dados e organização de telas**.

A IA deve pensar como alguém que conecta:

- Necessidade real do usuário;
- Experiência de uso;
- Estrutura lógica do sistema;
- Banco de dados relacional;
- Segurança e permissões;
- Manutenção futura;
- Evolução do projeto.

A IA não deve apenas desenhar telas bonitas. Ela deve criar uma arquitetura funcional, organizada e possível de desenvolver.

---

## 3. Quando usar esta Skill

Use esta skill sempre que for necessário:

- Criar um sistema web, app, painel administrativo ou SaaS;
- Organizar um projeto antes de programar;
- Criar mapa de telas;
- Definir funções de cada página;
- Relacionar telas com banco de dados;
- Criar documentação para desenvolvimento;
- Planejar CRUDs, dashboards, relatórios e permissões;
- Preparar prompts para IA de coding;
- Evitar retrabalho antes de iniciar o código.

---

## 4. Princípios Fundamentais

### 4.1 Comece pelo objetivo do sistema

Antes de criar qualquer tela, entenda o que o sistema precisa resolver.

Toda tela precisa existir por um motivo claro. Se uma tela não ajuda o usuário a executar uma tarefa, consultar uma informação ou tomar uma decisão, ela deve ser removida, simplificada ou incorporada a outra tela.

Perguntas importantes:

- Qual problema o sistema resolve?
- Quem vai usar?
- Qual tarefa principal o usuário precisa fazer?
- O sistema é operacional, administrativo, financeiro, comercial, educacional ou analítico?
- O objetivo é cadastrar, acompanhar, aprovar, vender, controlar, comunicar ou medir?

---

### 4.2 Separe tela, função e dado

Uma boa arquitetura diferencia três coisas:

| Camada | Pergunta principal | Exemplo |
|---|---|---|
| Tela | Onde o usuário interage? | Tela de cadastro de cliente |
| Função | O que o sistema faz? | Criar, editar, excluir, validar, aprovar |
| Banco de dados | Onde a informação fica salva? | Tabela `clientes` |

Nunca planeje apenas a tela visual. Cada tela precisa ter funções e cada função precisa saber quais dados lê, grava, altera ou exclui.

---

### 4.3 Crie o mapa de telas antes do layout

O mapa de telas deve vir antes do design visual.

Primeiro defina:

- Quais telas existem;
- Como elas se conectam;
- Qual tela vem antes e depois;
- Quais ações o usuário pode executar;
- Quais dados aparecem;
- Quais permissões são necessárias.

Depois disso, o design visual fica muito mais simples e coerente.

---

### 4.4 Toda tela deve ter uma responsabilidade principal

Evite telas que fazem coisas demais.

Uma tela boa tem uma função dominante, por exemplo:

- Listar registros;
- Cadastrar um item;
- Ver detalhes;
- Editar informações;
- Acompanhar status;
- Aprovar ou reprovar;
- Exibir indicadores;
- Gerar relatório.

Se uma tela tem muitas responsabilidades, divida em abas, etapas, cards ou telas separadas.

---

### 4.5 O banco de dados deve nascer junto com as telas

Não crie banco de dados depois das telas. O banco precisa ser planejado junto com o fluxo.

Para cada tela, defina:

- Quais tabelas ela consulta;
- Quais tabelas ela altera;
- Quais campos são obrigatórios;
- Quais campos são calculados;
- Quais relacionamentos existem;
- Quais registros precisam de histórico;
- Quais dados precisam de auditoria.

---

## 5. Processo Recomendado de Arquitetura

### Etapa 1 — Levantamento do contexto do projeto

Antes de montar telas, documente o contexto.

Itens obrigatórios:

- Nome do projeto;
- Objetivo principal;
- Tipo de sistema;
- Público usuário;
- Perfis de acesso;
- Principais processos;
- Regras de negócio conhecidas;
- Dados principais;
- Integrações externas;
- Relatórios esperados;
- Dispositivos de uso: desktop, tablet ou mobile.

Modelo:

```md
## Contexto do Projeto

Nome do projeto:
Objetivo:
Usuários principais:
Perfis de acesso:
Problema que resolve:
Processos principais:
Dispositivos de uso:
Integrações:
Relatórios necessários:
```

---

### Etapa 2 — Identificação dos módulos

Módulos são agrupamentos lógicos de telas e funções.

Exemplos:

- Autenticação;
- Dashboard;
- Clientes;
- Usuários;
- Produtos;
- Pedidos;
- Financeiro;
- Agenda;
- Relatórios;
- Configurações;
- Notificações;
- Auditoria.

Boa prática:

- Cada módulo deve representar uma área clara do sistema;
- Evite nomes genéricos como “Outros” ou “Diversos”;
- Um módulo pode ter várias telas;
- Um módulo pode usar várias tabelas;
- Um módulo deve ter um responsável funcional claro.

---

### Etapa 3 — Criação do mapa de telas

O mapa de telas mostra a estrutura geral do sistema.

Modelo recomendado:

```md
## Mapa de Telas

1. Login
2. Recuperar senha
3. Dashboard
4. Lista de registros
5. Detalhe do registro
6. Cadastro de registro
7. Edição de registro
8. Relatórios
9. Configurações
```

Para sistemas maiores, agrupe por módulo:

```md
## Módulo: Clientes

- Lista de clientes
- Cadastro de cliente
- Detalhe do cliente
- Edição de cliente
- Histórico do cliente
- Anexos do cliente
```

---

### Etapa 4 — Definição da hierarquia de navegação

A navegação precisa ser previsível.

Defina:

- Tela inicial depois do login;
- Menu principal;
- Submenus;
- Botões de ação rápida;
- Atalhos;
- Breadcrumbs;
- Fluxos de retorno;
- Links entre telas relacionadas.

Exemplo:

```md
Login → Dashboard → Lista de Clientes → Detalhe do Cliente → Editar Cliente
Login → Dashboard → Minhas Tarefas → Detalhe da Tarefa → Concluir Tarefa
```

Boa prática:

- O usuário nunca deve ficar sem saber onde está;
- Toda tela interna deve ter caminho de volta;
- Ações críticas devem pedir confirmação;
- Fluxos longos devem ser divididos em etapas.

---

### Etapa 5 — Matriz Tela x Função x Banco de Dados

Esta é uma das partes mais importantes da arquitetura.

Use uma matriz para conectar cada tela às suas funções e tabelas.

Modelo:

```md
| Tela | Objetivo | Funções | Tabelas usadas | Permissão | Observações |
|---|---|---|---|---|---|
| Lista de Clientes | Consultar clientes cadastrados | Buscar, filtrar, ordenar, abrir detalhe | clientes | Admin, Operador | Deve paginar resultados |
| Cadastro de Cliente | Criar novo cliente | Validar, salvar, cancelar | clientes, usuarios_log | Admin, Operador | CPF/e-mail não podem duplicar |
| Detalhe do Cliente | Ver dados completos | Visualizar, editar, anexar arquivo | clientes, anexos, historico | Admin, Operador | Exibir histórico de alterações |
```

Toda tela importante deve aparecer nessa matriz.

---

## 6. Boas Práticas para Telas

### 6.1 Tela de Login

A tela de login deve ser simples e segura.

Funções comuns:

- Entrar no sistema;
- Validar usuário e senha;
- Recuperar senha;
- Manter sessão;
- Redirecionar conforme perfil;
- Bloquear tentativas suspeitas.

Tabelas comuns:

- `usuarios`;
- `sessoes`;
- `logs_acesso`;
- `tokens_recuperacao_senha`.

Boas práticas:

- Não revelar se o erro foi no usuário ou na senha;
- Usar senha com hash seguro;
- Registrar tentativas de login;
- Encerrar sessão por inatividade;
- Redirecionar o usuário para a área correta após login.

---

### 6.2 Dashboard

O dashboard deve mostrar informações úteis para decisão rápida.

Funções comuns:

- Exibir indicadores;
- Mostrar pendências;
- Mostrar alertas;
- Mostrar atalhos para ações principais;
- Filtrar por período, status ou responsável.

Boas práticas:

- Não transforme o dashboard em relatório gigante;
- Mostre poucos indicadores, mas relevantes;
- Cada card deve permitir ação ou navegação;
- Indicadores devem ter origem clara no banco;
- Dados críticos devem ter data/hora da última atualização.

---

### 6.3 Tela de Listagem

A tela de listagem é usada para consultar registros.

Funções comuns:

- Buscar;
- Filtrar;
- Ordenar;
- Paginar;
- Abrir detalhe;
- Criar novo registro;
- Exportar dados;
- Aplicar ações em lote.

Boas práticas:

- Nunca carregue milhares de registros de uma vez;
- Use paginação;
- Defina filtros principais;
- Mostre status visual claro;
- Permita limpar filtros;
- Evite colunas demais;
- A coluna de ações deve ser objetiva.

---

### 6.4 Tela de Cadastro

A tela de cadastro deve coletar apenas o necessário.

Funções comuns:

- Preencher dados;
- Validar campos;
- Salvar;
- Cancelar;
- Exibir erros;
- Criar registros relacionados.

Boas práticas:

- Campos obrigatórios devem ser claros;
- Validação deve acontecer no frontend e no backend;
- Mensagens de erro devem explicar o problema;
- Evite formulários longos sem divisão;
- Use máscaras quando fizer sentido;
- Não confie apenas na validação visual.

---

### 6.5 Tela de Detalhe

A tela de detalhe mostra o registro completo.

Funções comuns:

- Visualizar dados principais;
- Ver histórico;
- Editar;
- Anexar arquivos;
- Alterar status;
- Executar ações contextuais;
- Consultar registros relacionados.

Boas práticas:

- Organize informações em seções;
- Mostre dados principais no topo;
- Separe dados, histórico, anexos e ações;
- Exiba status atual com destaque;
- Ações perigosas devem pedir confirmação.

---

### 6.6 Tela de Edição

A tela de edição deve preservar o controle das alterações.

Funções comuns:

- Carregar dados existentes;
- Validar alterações;
- Salvar mudanças;
- Cancelar edição;
- Registrar histórico;
- Impedir edição indevida por permissão ou status.

Boas práticas:

- Não permitir edição de campos críticos sem regra clara;
- Registrar quem alterou, quando alterou e o que foi alterado;
- Evitar perda de dados ao sair da tela;
- Bloquear edição quando o registro estiver finalizado, cancelado ou aprovado, se essa for a regra.

---

### 6.7 Tela de Relatórios

Relatórios devem responder perguntas de negócio.

Funções comuns:

- Filtrar por período;
- Filtrar por status;
- Filtrar por usuário, cliente ou categoria;
- Gerar tabela;
- Exportar PDF, CSV ou Excel;
- Exibir gráficos;
- Salvar filtros favoritos.

Boas práticas:

- Comece pela pergunta que o relatório responde;
- Não misture relatório operacional com relatório gerencial;
- Use filtros obrigatórios para grandes volumes;
- Deixe claro se os dados são em tempo real ou consolidados;
- Garanta que os números batam com as regras de negócio.

---

### 6.8 Tela de Configurações

Configurações devem ser acessadas apenas por perfis autorizados.

Funções comuns:

- Gerenciar usuários;
- Gerenciar permissões;
- Definir parâmetros do sistema;
- Configurar integrações;
- Gerenciar templates;
- Ativar ou desativar funcionalidades.

Boas práticas:

- Separar configuração operacional de configuração técnica;
- Registrar alteração de parâmetros importantes;
- Evitar que usuários comuns acessem opções sensíveis;
- Explicar o impacto de cada configuração.

---

## 7. Boas Práticas para Funções

### 7.1 Nomeie funções de forma clara

Funções devem ter nomes objetivos e previsíveis.

Exemplos bons:

- `criar_cliente`;
- `editar_cliente`;
- `listar_clientes`;
- `buscar_cliente_por_id`;
- `aprovar_solicitacao`;
- `cancelar_pedido`;
- `gerar_relatorio_mensal`.

Evite nomes vagos:

- `processar`;
- `executar`;
- `fazer`;
- `acao`;
- `salvar_dados_geral`.

---

### 7.2 Cada função deve ter responsabilidade única

Uma função deve fazer uma coisa principal.

Exemplo ruim:

```md
salvar_cliente_e_enviar_email_e_gerar_relatorio()
```

Exemplo melhor:

```md
salvar_cliente()
enviar_email_boas_vindas()
registrar_log_cliente()
```

Isso melhora manutenção, testes e reaproveitamento.

---

### 7.3 Diferencie funções de leitura e escrita

Funções que apenas consultam dados devem ser separadas das funções que alteram dados.

Tipos:

- Leitura: listar, buscar, consultar, carregar;
- Escrita: criar, editar, excluir, aprovar, cancelar;
- Processamento: calcular, validar, consolidar;
- Integração: enviar, receber, sincronizar;
- Auditoria: registrar, logar, rastrear.

Essa separação ajuda na segurança e na organização do backend.

---

### 7.4 Toda função crítica precisa de validação

Funções críticas devem validar:

- Permissão do usuário;
- Campos obrigatórios;
- Tipo dos dados;
- Regras de negócio;
- Status atual do registro;
- Duplicidade;
- Integridade relacional;
- Limites operacionais.

Nunca confie apenas no frontend.

---

### 7.5 Registre logs de funções importantes

Toda ação relevante deve gerar log.

Exemplos:

- Login;
- Criação de registro;
- Edição de dados sensíveis;
- Exclusão;
- Aprovação;
- Cancelamento;
- Exportação de dados;
- Erro de integração;
- Alteração de permissão.

Campos úteis para logs:

- ID do usuário;
- Nome da ação;
- Tabela afetada;
- ID do registro;
- Dados anteriores;
- Dados novos;
- IP;
- Data e hora;
- Resultado da ação.

---

## 8. Boas Práticas para Banco de Dados

### 8.1 Modele entidades antes das tabelas

Antes de criar tabelas, identifique as entidades do negócio.

Exemplos de entidades:

- Usuário;
- Cliente;
- Produto;
- Pedido;
- Tarefa;
- Anexo;
- Pagamento;
- Notificação;
- Histórico;
- Permissão.

Depois transforme as entidades em tabelas.

---

### 8.2 Defina relacionamentos claramente

Para cada tabela, defina seus relacionamentos.

Tipos comuns:

- Um para um;
- Um para muitos;
- Muitos para muitos.

Exemplo:

```md
Um cliente pode ter muitos pedidos.
Um pedido pertence a um cliente.
Um usuário pode participar de várias demandas.
Uma demanda pode ter vários usuários participantes.
```

Relações muitos para muitos normalmente precisam de tabela intermediária.

Exemplo:

```md
demandas
usuarios
demanda_participantes
```

---

### 8.3 Padronize nomes de tabelas e campos

Escolha um padrão e mantenha até o fim.

Sugestão:

- Tabelas no plural: `usuarios`, `clientes`, `pedidos`;
- Chave primária: `id`;
- Chave estrangeira: `cliente_id`, `usuario_id`;
- Datas: `criado_em`, `atualizado_em`, `excluido_em`;
- Status: `status`;
- Ativo/inativo: `ativo`.

Evite misturar português e inglês no mesmo banco sem necessidade.

---

### 8.4 Use status bem definidos

Status são fundamentais para telas e regras de negócio.

Exemplo:

```md
pendente
em_andamento
aprovado
reprovado
cancelado
concluido
arquivado
```

Boa prática:

- Documente o significado de cada status;
- Defina quem pode mudar o status;
- Defina quais ações são permitidas em cada status;
- Evite status genéricos como “ok”, “normal” ou “feito” sem padrão.

---

### 8.5 Não apague dados importantes sem estratégia

Para dados relevantes, prefira exclusão lógica.

Campos comuns:

```sql
ativo
excluido_em
excluido_por
motivo_exclusao
```

Use exclusão física apenas quando fizer sentido técnico, legal ou operacional.

---

### 8.6 Planeje anexos corretamente

Anexos não devem ser salvos de qualquer forma.

Tabela sugerida:

```md
anexos
- id
- entidade_tipo
- entidade_id
- nome_original
- nome_arquivo
- caminho_arquivo
- tipo_mime
- tamanho
- publico
- criado_por
- criado_em
```

Boa prática:

- Separar arquivos públicos e privados;
- Validar extensão e tamanho;
- Evitar executar arquivos enviados;
- Salvar metadados no banco;
- Controlar permissão de acesso.

---

### 8.7 Inclua auditoria desde o início

Sistemas profissionais precisam rastrear alterações.

Campos mínimos recomendados nas tabelas principais:

```sql
criado_em
criado_por
atualizado_em
atualizado_por
```

Para registros sensíveis, use tabela de histórico.

Exemplo:

```md
historico_alteracoes
- id
- tabela
- registro_id
- acao
- dados_anteriores
- dados_novos
- usuario_id
- criado_em
```

---

## 9. Matriz de Permissões

Permissões devem ser planejadas junto com telas e funções.

Modelo:

```md
| Perfil | Pode ver | Pode criar | Pode editar | Pode excluir | Pode aprovar | Observações |
|---|---|---|---|---|---|---|
| Admin | Tudo | Sim | Sim | Sim | Sim | Acesso total |
| Gestor | Demandas da equipe | Sim | Sim | Não | Sim | Não acessa configurações técnicas |
| Operador | Próprias tarefas | Sim | Parcial | Não | Não | Acesso limitado |
| Leitor | Relatórios | Não | Não | Não | Não | Somente consulta |
```

Boa prática:

- Não controle permissão apenas escondendo botão no frontend;
- O backend também deve validar permissão;
- Cada função crítica deve verificar o perfil;
- Permissões devem ser simples o suficiente para manter.

---

## 10. Estados de Tela

Toda tela deve prever estados diferentes.

Estados comuns:

- Carregando;
- Vazio;
- Com dados;
- Sem permissão;
- Erro;
- Sucesso;
- Validação pendente;
- Registro bloqueado;
- Offline, quando aplicável.

Exemplo:

```md
Tela: Lista de Clientes

Estados:
- Carregando clientes;
- Nenhum cliente encontrado;
- Clientes encontrados;
- Erro ao buscar clientes;
- Usuário sem permissão para ver clientes.
```

Boa prática:

- Nunca deixe a tela em branco sem explicação;
- Mensagens devem orientar a próxima ação;
- Erros devem ser claros e seguros;
- Sucesso deve confirmar o resultado.

---

## 11. Regras de Negócio

Regras de negócio devem ser documentadas de forma objetiva.

Modelo:

```md
## Regra: Aprovação de solicitação

Descrição:
Uma solicitação só pode ser aprovada por usuário com perfil de gestor ou admin.

Condições:
- A solicitação deve estar com status `pendente`;
- O usuário deve ter permissão de aprovação;
- A solicitação deve possuir todos os campos obrigatórios preenchidos.

Resultado:
- Status muda para `aprovado`;
- Sistema registra log;
- Sistema envia notificação ao responsável.
```

Boa prática:

- Cada regra deve ter condição e consequência;
- Regras devem ser implementadas no backend;
- Regras críticas devem gerar log;
- Regras que afetam status devem estar no mapa de fluxo.

---

## 12. Fluxo de Dados

O fluxo de dados mostra como a informação entra, muda e sai do sistema.

Modelo:

```md
Usuário preenche formulário
→ Frontend valida campos básicos
→ Backend valida regras de negócio
→ Banco salva registro
→ Sistema registra log
→ Sistema retorna resposta
→ Tela exibe confirmação
```

Boa prática:

- Nunca pule validação no backend;
- Defina claramente quem cria cada dado;
- Defina quando o dado muda de status;
- Defina quem pode consultar;
- Defina se o dado será exportado, integrado ou arquivado.

---

## 13. Integrações e APIs

Quando a tela depender de API, documente a integração.

Modelo:

```md
## Integração: Consulta de cliente externo

Tela de origem:
Cadastro de cliente

Ação:
Buscar dados pelo CPF

Endpoint:
GET /api/clientes/consulta?cpf={cpf}

Dados enviados:
- cpf

Dados recebidos:
- nome
- data_nascimento
- telefone
- email

Erros possíveis:
- CPF não encontrado
- API indisponível
- Tempo limite excedido
```

Boa prática:

- Documentar payload de entrada e saída;
- Tratar erro de API de forma clara;
- Evitar travar a tela sem mensagem;
- Registrar falhas importantes;
- Prever reprocessamento quando necessário.

---

## 14. Checklist de Cada Tela

Antes de considerar uma tela pronta na arquitetura, responda:

```md
## Checklist da Tela

Nome da tela:
Módulo:
Objetivo:
Usuários que acessam:
Permissões necessárias:
Dados exibidos:
Tabelas consultadas:
Tabelas alteradas:
Funções principais:
Regras de negócio:
Campos obrigatórios:
Validações:
Estados da tela:
Ações disponíveis:
Mensagens de erro:
Mensagens de sucesso:
Logs gerados:
Integrações:
Telas relacionadas:
```

Se a tela não conseguir responder esse checklist, ela ainda não está pronta para desenvolvimento.

---

## 15. Modelo Completo de Documentação de Tela

Use este modelo para detalhar cada tela do projeto.

```md
# Tela: [Nome da Tela]

## 1. Objetivo
Explique para que esta tela existe.

## 2. Usuários e Permissões
Liste quem pode acessar e quais ações cada perfil pode fazer.

## 3. Entrada da Tela
Explique como o usuário chega nesta tela.

Exemplo:
- Menu lateral;
- Botão no dashboard;
- Link vindo de outra tela;
- Notificação;
- Redirecionamento após login.

## 4. Dados Exibidos
Liste todos os dados mostrados na tela.

## 5. Funções da Tela
Liste as ações possíveis.

Exemplo:
- Buscar;
- Filtrar;
- Criar;
- Editar;
- Excluir;
- Aprovar;
- Exportar;
- Anexar arquivo.

## 6. Banco de Dados

### Tabelas consultadas
- tabela_1
- tabela_2

### Tabelas alteradas
- tabela_1
- tabela_log

## 7. Regras de Negócio
Liste as regras aplicadas nesta tela.

## 8. Validações
Liste validações de campos e permissões.

## 9. Estados da Tela
- Carregando;
- Vazio;
- Com dados;
- Erro;
- Sem permissão;
- Sucesso.

## 10. Mensagens

### Sucesso
- Registro salvo com sucesso.

### Erro
- Não foi possível salvar. Verifique os campos obrigatórios.

## 11. Logs
Informe quais ações geram log.

## 12. Telas Relacionadas
Liste telas conectadas a esta.
```

---

## 16. Modelo de Mapa de Telas por Módulo

```md
# Mapa de Telas do Projeto

## Módulo: Autenticação

| Tela | Objetivo | Funções | Tabelas | Permissões |
|---|---|---|---|---|
| Login | Entrar no sistema | Autenticar, validar, registrar acesso | usuarios, logs_acesso | Público |
| Recuperar senha | Redefinir senha | Gerar token, enviar link, alterar senha | usuarios, tokens_recuperacao | Público |

## Módulo: Dashboard

| Tela | Objetivo | Funções | Tabelas | Permissões |
|---|---|---|---|---|
| Dashboard geral | Exibir visão geral | Indicadores, atalhos, alertas | demandas, tarefas, usuarios | Usuário logado |

## Módulo: Cadastros

| Tela | Objetivo | Funções | Tabelas | Permissões |
|---|---|---|---|---|
| Lista de clientes | Consultar clientes | Buscar, filtrar, paginar, abrir detalhe | clientes | Admin, Operador |
| Cadastro de cliente | Criar cliente | Validar, salvar, cancelar | clientes, logs | Admin, Operador |
| Detalhe do cliente | Ver cliente completo | Visualizar, editar, anexar, histórico | clientes, anexos, historico | Admin, Operador |

## Módulo: Relatórios

| Tela | Objetivo | Funções | Tabelas | Permissões |
|---|---|---|---|---|
| Relatório mensal | Analisar resultados | Filtrar, gerar, exportar | registros, usuarios, clientes | Admin, Gestor |
```

---

## 17. Modelo de Banco de Dados Inicial

```md
# Banco de Dados Inicial

## usuarios
- id
- nome
- email
- senha_hash
- perfil_id
- ativo
- criado_em
- atualizado_em

## perfis
- id
- nome
- descricao
- ativo

## permissoes
- id
- perfil_id
- modulo
- funcao
- pode_acessar
- pode_criar
- pode_editar
- pode_excluir
- pode_aprovar

## logs_sistema
- id
- usuario_id
- acao
- tabela_afetada
- registro_id
- dados_anteriores
- dados_novos
- ip
- criado_em

## anexos
- id
- entidade_tipo
- entidade_id
- nome_original
- nome_arquivo
- caminho_arquivo
- tipo_mime
- tamanho
- publico
- criado_por
- criado_em
```

Adapte as tabelas conforme o projeto.

---

## 18. Boas Práticas para Projetos com PHP, MySQL, HTML, CSS e JavaScript

Quando o projeto usar uma stack simples como PHP procedural, MySQL, HTML, CSS e JavaScript, mantenha separação clara de responsabilidades.

Estrutura sugerida:

```md
/app
  /controllers
  /functions
  /models
  /views
  /services
  /helpers
  /config
  /public
  /storage
    /public
    /private
  /logs
  /database
```

Boas práticas:

- HTML monta a estrutura da tela;
- CSS cuida do visual;
- JavaScript cuida da interação e chamadas assíncronas;
- PHP processa regras, validações e acesso ao banco;
- MySQL armazena os dados;
- Funções PHP devem ser pequenas e bem nomeadas;
- Arquivos de configuração não devem ficar públicos;
- Uploads privados não devem ficar acessíveis diretamente pela URL;
- Consultas SQL devem usar prepared statements;
- Erros técnicos não devem aparecer para o usuário final.

---

## 19. Boas Práticas para Branches e Ambientes

Para projetos com produção, homologação e desenvolvimento, separe ambientes e bancos.

Ambientes recomendados:

```md
Desenvolvimento
- Usado pelos programadores
- Pode ter dados fictícios
- Pode mudar com frequência

Homologação
- Usado para testes antes da produção
- Deve simular produção
- Não deve afetar cliente real

Produção
- Ambiente oficial
- Dados reais
- Mudanças apenas após validação
```

Boa prática:

- Cada ambiente deve ter banco de dados separado;
- Nunca testar função nova direto em produção;
- Homologação deve validar telas, funções, permissões e banco;
- Scripts de banco devem ser versionados;
- Alterações críticas devem ter plano de rollback.

---

## 20. Erros Comuns que Devem Ser Evitados

### 20.1 Criar telas sem função clara

Erro:

- Criar uma tela apenas porque parece bonito ou comum.

Correção:

- Toda tela deve ter objetivo, funções, dados e permissões definidos.

---

### 20.2 Criar banco depois do layout

Erro:

- Fazer o visual primeiro e tentar adaptar o banco depois.

Correção:

- Definir entidades, relacionamentos e regras junto com o mapa de telas.

---

### 20.3 Misturar permissões apenas no frontend

Erro:

- Esconder botão na tela, mas permitir ação no backend.

Correção:

- Validar permissão no frontend e obrigatoriamente no backend.

---

### 20.4 Não prever estados de erro

Erro:

- Pensar apenas no fluxo perfeito.

Correção:

- Prever erro, carregamento, vazio, sem permissão e falha de integração.

---

### 20.5 Duplicar funções parecidas

Erro:

- Criar várias funções que fazem quase a mesma coisa.

Correção:

- Padronizar nomes, reaproveitar funções e separar responsabilidades.

---

### 20.6 Não documentar regras de negócio

Erro:

- Deixar regra apenas na cabeça do desenvolvedor.

Correção:

- Escrever regra com condição, ação e resultado esperado.

---

## 21. Prompt Base para Usar com IA

Use este prompt para solicitar arquitetura de telas, funções e banco de dados:

```md
Aja como um programador sênior especialista em arquitetura de sistemas, UX funcional, banco de dados e organização de telas.

Crie uma arquitetura completa para o projeto abaixo, conectando mapa de telas, funções, regras de negócio e banco de dados.

## Dados do Projeto

Nome do projeto:
Tipo de sistema:
Objetivo principal:
Usuários/perfis:
Processos principais:
Regras conhecidas:
Integrações:
Relatórios desejados:
Stack tecnológica:
Dispositivos de uso:

## Entregáveis obrigatórios

1. Resumo do sistema;
2. Lista de módulos;
3. Mapa de telas por módulo;
4. Fluxo principal do usuário;
5. Matriz Tela x Função x Banco de Dados;
6. Matriz de permissões;
7. Lista de tabelas sugeridas;
8. Relacionamentos entre tabelas;
9. Regras de negócio principais;
10. Estados de tela;
11. Validações importantes;
12. Logs e auditoria;
13. Pontos de segurança;
14. Ordem recomendada de desenvolvimento.

Não crie telas genéricas sem função clara.
Não crie banco de dados desconectado das telas.
Não ignore permissões, validações, logs e estados de erro.
```

---

## 22. Ordem Recomendada de Desenvolvimento

Depois que o mapa estiver pronto, desenvolva em ordem lógica.

Sugestão:

1. Banco de dados base;
2. Login e sessão;
3. Permissões;
4. Layout base;
5. Dashboard simples;
6. CRUDs principais;
7. Telas de detalhe;
8. Anexos;
9. Logs;
10. Relatórios;
11. Notificações;
12. Integrações;
13. Testes;
14. Homologação;
15. Produção.

Boa prática:

- Comece pelo núcleo do sistema;
- Evite iniciar por relatórios complexos;
- Não desenvolva telas avançadas antes dos dados principais;
- Valide cada módulo antes de avançar.

---

## 23. Checklist Final da Arquitetura

Antes de iniciar o desenvolvimento, confirme:

```md
[ ] O objetivo do sistema está claro
[ ] Os usuários e perfis foram definidos
[ ] Os módulos foram organizados
[ ] O mapa de telas foi criado
[ ] Cada tela tem objetivo claro
[ ] Cada tela tem funções definidas
[ ] Cada função tem dados relacionados
[ ] As tabelas principais foram definidas
[ ] Os relacionamentos foram descritos
[ ] As permissões foram mapeadas
[ ] As regras de negócio foram documentadas
[ ] Os estados de tela foram previstos
[ ] As mensagens de erro e sucesso foram pensadas
[ ] Os logs e auditoria foram planejados
[ ] As integrações foram descritas
[ ] Os relatórios foram vinculados aos dados
[ ] Os ambientes foram separados
[ ] A ordem de desenvolvimento foi definida
```

---

## 24. Resultado Esperado

Ao aplicar esta skill, a IA deve entregar uma documentação que permita ao desenvolvedor entender:

- Quais telas precisam existir;
- Por que cada tela existe;
- O que cada tela faz;
- Quais dados cada tela usa;
- Quais funções precisam ser criadas;
- Quais tabelas serão necessárias;
- Quais permissões controlam o acesso;
- Quais regras de negócio devem ser respeitadas;
- Qual ordem lógica seguir no desenvolvimento.

O resultado final deve ser claro o suficiente para virar base de desenvolvimento, prompt de IA de coding, documentação técnica ou especificação funcional do projeto.
