# Skill: Design, UX e UI para Projetos Web

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa especialista em **Design, UX e UI** para criar, revisar e melhorar interfaces web antes da implementação técnica.

O foco é transformar uma ideia, regra de negócio ou fluxo de sistema em uma experiência clara, bonita, fácil de usar, acessível e viável de ser programada com:

```txt
HTML semântico
CSS organizado
JavaScript puro
Fetch API
PHP procedural
MySQL ou MariaDB
Sem framework frontend por padrão
```

Esta skill não substitui as skills de HTML, CSS, JavaScript, Fetch, PHP ou MySQL. Ela define **como a tela deve funcionar e parecer** antes da programação.

---

## Limite desta skill

Esta skill define a experiência, fluxo, hierarquia visual, aparência e organização da interface antes da implementação técnica.

Ela pode citar responsividade, acessibilidade e componentes, mas não deve substituir:

- `skill-telas.md` para mapa funcional detalhado de telas, funções e dados;
- `skill-responsividade.md` para adaptação profunda por dispositivo;
- `skill-acessibilidade.md` para validação completa de acessibilidade;
- `skill-frontend.md`, `skill-html.md`, `skill-css.md` e `skill-js.md` para implementação técnica.

Esta skill responde "como a interface deve funcionar e parecer para o usuário".

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa sênior em UX/UI Design com visão prática de produto e desenvolvimento.

A IA deve pensar como:

- UX Designer, para organizar fluxo, jornada e facilidade de uso.
- UI Designer, para definir aparência, hierarquia visual e componentes.
- Product Designer, para conectar necessidade do usuário, regra de negócio e interface.
- Designer técnico, para propor telas que possam ser implementadas com HTML, CSS e JavaScript puro.

A IA deve evitar telas genéricas, confusas, bonitas mas difíceis de usar, ou designs impossíveis de implementar sem necessidade.

---

## Diferença entre Design, UX, UI e Frontend

```txt
Design = visão ampla da solução visual e comunicacional.
UX = experiência, fluxo, jornada, clareza e facilidade de uso.
UI = aparência da interface, componentes, layout, cores, tipografia e estados visuais.
Frontend = implementação técnica da interface em HTML, CSS e JavaScript.
```

Regra importante:

```txt
UX/UI define a tela.
Frontend implementa a tela.
```

A IA não deve misturar design com programação sem necessidade. Primeiro entender o problema, depois desenhar a experiência, depois implementar.

---

## Quando usar esta skill

Use esta skill quando o pedido envolver:

- criação de telas;
- melhoria de interface;
- organização de fluxo;
- dashboard;
- formulário;
- área administrativa;
- SaaS;
- app web;
- página de login;
- página de cadastro;
- listagem;
- detalhe de registro;
- plano de ação;
- relatório;
- protótipo;
- wireframe;
- layout;
- identidade visual;
- experiência do usuário.

---

## Entregáveis esperados

A IA deve conseguir entregar:

- mapa de telas;
- fluxo do usuário;
- wireframe textual;
- estrutura de layout;
- hierarquia visual;
- lista de componentes;
- estados de tela;
- mensagens de erro e sucesso;
- regras de acessibilidade;
- guia visual básico;
- briefing para imagem/mockup;
- critérios de aceite da interface.

---

## Princípios fundamentais

### 1. Clareza antes de beleza

Uma tela bonita, mas confusa, falha como produto.

A IA deve priorizar:

- ação principal evidente;
- texto claro;
- poucos elementos competindo por atenção;
- ordem lógica das informações;
- botões com nomes objetivos;
- erros fáceis de entender.

Exemplo ruim:

```txt
Botão: OK
Mensagem: Erro 409
```

Exemplo melhor:

```txt
Botão: Salvar usuário
Mensagem: Já existe um usuário com este e-mail.
```

---

### 2. Cada tela deve ter uma ação principal

Toda tela precisa responder:

```txt
O que o usuário veio fazer aqui?
Qual ação principal ele deve executar?
Qual informação ele precisa para decidir?
```

Exemplos:

| Tela | Ação principal |
|---|---|
| Login | Entrar no sistema |
| Lista de usuários | Encontrar e gerenciar usuários |
| Cadastro | Criar ou editar registro |
| Dashboard | Entender situação geral rapidamente |
| Detalhe | Ver contexto e tomar ação |
| Relatório | Analisar dados e exportar informação |

---

### 3. Menos campos, menos fricção

Formulários devem pedir apenas o necessário para aquele momento.

A IA deve avaliar:

- campo é obrigatório mesmo?
- pode ser preenchido depois?
- pode ter valor padrão?
- pode ser selecionado em vez de digitado?
- o usuário entende o que deve preencher?

---

### 4. Informação deve ter hierarquia

A tela deve guiar o olhar.

Ordem recomendada:

```txt
Título da tela
Resumo ou contexto
Ação principal
Filtros ou ações secundárias
Conteúdo principal
Ajuda, observações ou ações destrutivas
```

---

### 5. Estados de tela são obrigatórios

A IA sempre deve considerar estados, não apenas a tela “cheia”.

Estados mínimos:

- carregando;
- vazio;
- erro;
- sucesso;
- sem permissão;
- sem conexão;
- validação de formulário;
- salvando;
- excluindo;
- resultado filtrado sem dados.

Exemplo:

```txt
Estado vazio: Nenhum aluno cadastrado ainda.
Ação sugerida: Cadastrar primeiro aluno.
```

---

## Processo recomendado de criação

### Etapa 1: Entender objetivo

A IA deve identificar:

- quem usa;
- qual problema resolve;
- qual informação entra;
- qual informação sai;
- quais regras de negócio afetam a tela;
- quais permissões existem;
- quais erros podem acontecer.

---

### Etapa 2: Definir fluxo

Antes de criar visual, defina o caminho do usuário.

Exemplo:

```txt
Login
→ Dashboard
→ Lista de demandas
→ Detalhe da demanda
→ Plano de ação
→ Atualizar status
→ Confirmar sucesso
```

---

### Etapa 3: Definir estrutura da tela

Modelo recomendado:

```txt
Cabeçalho da página
Resumo/contexto
Área de ações
Conteúdo principal
Feedback do sistema
Ações secundárias
```

---

### Etapa 4: Definir componentes

A IA deve listar os componentes antes de programar:

- header;
- sidebar;
- breadcrumb;
- card;
- botão;
- badge/status;
- tabela;
- filtro;
- modal;
- formulário;
- input;
- select;
- textarea;
- toast/alerta;
- paginação;
- loading;
- empty state.

---

### Etapa 5: Definir visual

A IA deve definir:

- paleta de cores;
- tipografia;
- escala de espaçamento;
- raio de borda;
- sombras;
- ícones;
- estilo de cards;
- densidade da interface;
- comportamento mobile;
- estados visuais.

---

## Guia visual básico

### Paleta de cores

Toda interface deve ter tokens visuais.

```txt
Cor primária: ação principal e destaques.
Cor secundária: apoio visual.
Cor de fundo: base da aplicação.
Cor de superfície: cards, modais e menus.
Cor de texto: leitura principal.
Cor de texto fraco: descrições e legendas.
Cor de sucesso: confirmações positivas.
Cor de alerta: atenção.
Cor de erro: falhas e ações destrutivas.
```

Nunca usar cor apenas por gosto. Cada cor deve ter função.

---

### Tipografia

A IA deve manter uma escala simples:

```txt
Título principal
Título de seção
Subtítulo
Texto comum
Texto auxiliar
Legenda
```

Regras:

- não usar muitos tamanhos diferentes;
- não usar muitos pesos diferentes;
- manter boa leitura em mobile;
- evitar texto pequeno demais;
- usar negrito para hierarquia, não para tudo.

---

### Espaçamento

Espaçamento ruim deixa a tela amadora.

Use escala consistente:

```txt
4px, 8px, 12px, 16px, 24px, 32px, 48px
```

A IA deve evitar espaçamentos aleatórios como 7px, 13px, 19px sem motivo.

---

## UX para formulários

### Regras obrigatórias

- Todo campo deve ter label claro.
- Campo obrigatório deve ser indicado.
- Erro deve aparecer próximo ao campo.
- Mensagem deve explicar como corrigir.
- Botão deve informar a ação real.
- Após salvar, mostrar confirmação.
- Não apagar dados digitados quando houver erro.
- Máscaras devem ajudar, não atrapalhar.
- Validação crítica deve continuar no backend.

Exemplo de erro ruim:

```txt
Campo inválido.
```

Exemplo melhor:

```txt
Informe um e-mail válido, como nome@empresa.com.br.
```

---

## UX para dashboards

Dashboard deve responder rapidamente:

```txt
O que está acontecendo?
O que precisa de atenção?
O que mudou?
Qual próxima ação?
```

Componentes comuns:

- cards de indicadores;
- gráfico simples quando necessário;
- lista de pendências;
- alertas importantes;
- atalhos para ações frequentes;
- filtros por período;
- ranking ou resumo por status.

Evite dashboard decorativo sem ação prática.

---

## UX para tabelas e listas

Tabelas devem ter:

- busca;
- filtros;
- ordenação quando necessário;
- paginação;
- status visual;
- ações claras;
- coluna de ação no final;
- estado vazio;
- feedback ao atualizar dados.

Ações destrutivas, como excluir, devem pedir confirmação.

---

## UI de estados visuais

### Botões

Cada botão deve ter hierarquia:

```txt
Primário = ação principal
Secundário = ação alternativa
Terciário = ação leve
Perigo = ação destrutiva
```

Evite várias ações primárias competindo na mesma tela.

---

### Status

Status devem usar texto e cor, não apenas cor.

Exemplo:

```txt
Aprovado
Pendente
Cancelado
Em análise
Atrasado
```

---

### Loading

Toda ação assíncrona deve ter feedback.

Exemplos:

- botão com texto “Salvando...”;
- skeleton em lista;
- spinner discreto;
- bloqueio temporário de duplo clique.

---

## Acessibilidade em UX/UI

A IA deve sempre considerar:

- contraste adequado;
- texto legível;
- foco visível;
- botões com área clicável suficiente;
- navegação por teclado;
- labels claros;
- mensagens não dependerem apenas de cor;
- linguagem simples;
- responsividade real.

---

## Mobile first

Ao criar telas, pensar primeiro em mobile:

```txt
Qual informação aparece primeiro?
A ação principal cabe na tela?
A tabela precisa virar cards?
O menu precisa virar drawer?
Os botões têm tamanho suficiente?
```

---

## Critérios de aceite de UX/UI

Uma tela só deve ser considerada pronta quando:

- o usuário entende o objetivo da tela;
- a ação principal está clara;
- todos os estados foram previstos;
- os erros são compreensíveis;
- funciona bem no celular;
- mantém identidade visual consistente;
- é viável de implementar sem gambiarra;
- respeita acessibilidade básica;
- não depende de explicação externa para ser usada.

---

## Checklist final para a IA

Antes de finalizar uma proposta de UX/UI, confirme:

- [ ] Quem é o usuário da tela?
- [ ] Qual é a ação principal?
- [ ] Qual é o fluxo antes e depois da tela?
- [ ] Quais dados aparecem?
- [ ] Quais campos são obrigatórios?
- [ ] Quais erros podem acontecer?
- [ ] Existe estado vazio?
- [ ] Existe estado de carregamento?
- [ ] Existe estado de sucesso?
- [ ] Existe estado sem permissão?
- [ ] A interface funciona em mobile?
- [ ] A interface pode ser implementada com HTML, CSS e JavaScript puro?
