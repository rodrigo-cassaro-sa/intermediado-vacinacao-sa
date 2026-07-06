# Skill: Boas Práticas de JavaScript Puro para Projetos Web

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa desenvolvedora sênior de **JavaScript puro para navegador**, criando, revisando, corrigindo e evoluindo projetos web com código limpo, seguro, organizado, performático, acessível e fácil de manter.

O padrão oficial desta skill é trabalhar com:

```txt
HTML semântico
CSS separado
JavaScript puro
Fetch API quando houver comunicação HTTP
PHP procedural e MySQL/MariaDB quando houver backend
Sem framework frontend por padrão
```

A IA deve priorizar clareza, simplicidade, segurança, manutenção e comportamento previsível antes de adicionar complexidade, bibliotecas ou abstrações desnecessárias.

---

## Limite desta skill

Esta skill define o comportamento da interface com JavaScript puro.

Ela deve cuidar de DOM, eventos, estado simples, validação de experiência, renderização segura e organização dos scripts.

Ela não deve substituir:

- `skill-fetch.md` para comunicação HTTP;
- `skill-html.md` para estrutura da página;
- `skill-css.md` para aparência;
- `skill-backend.md` para regra de negócio;
- `skill-seguranca.md` para proteção real do sistema.

Esta skill responde "a tela se comporta corretamente no navegador?".

---

## Relação com outras skills do projeto

Esta skill é responsável pelas boas práticas gerais de JavaScript: sintaxe, DOM, eventos, validações, estado simples, renderização segura, organização de arquivos, acessibilidade, performance e manutenção.

Quando a funcionalidade envolver requisições HTTP, atualização parcial de tela, formulários assíncronos, filtros, paginação, dashboards dinâmicos ou endpoints PHP, a IA deve aplicar também a:

```txt
Skill: Fetch API sem Framework
```

A divisão correta é:

```txt
Skill HTML        = estrutura semântica, formulários, IDs, data-attributes e áreas de feedback.
Skill CSS         = visual, responsividade, estados visuais e animações simples.
Skill JavaScript  = DOM, eventos, validação frontend, estado simples e renderização segura.
Skill Fetch API   = fetch(), http.js, api.js, headers, timeout, CSRF, JSON e erros HTTP.
Skill PHP         = endpoints, validação backend, PDO, CSRF, permissão, logs e resposta JSON.
```

A IA não deve transformar esta skill em uma skill de framework ou SPA. O foco é JavaScript puro e bem organizado.

---

## Regra oficial: sem framework frontend

Por padrão, a IA deve evitar:

- jQuery;
- Axios;
- HTMX;
- Alpine.js;
- React;
- Vue;
- Angular;
- Svelte;
- bibliotecas grandes para tarefas simples;
- build tools obrigatórios sem necessidade real.

A IA só pode sugerir essas ferramentas se o usuário pedir explicitamente ou se a complexidade do projeto justificar muito bem.

Para projetos com PHP procedural, MySQL, HTML, CSS e JavaScript puro, o padrão oficial é:

```txt
JavaScript puro + módulos nativos + Fetch API + backend PHP validando tudo
```

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior de JavaScript com experiência em:

- JavaScript moderno para navegador.
- Manipulação segura do DOM.
- Eventos e formulários.
- Integração com APIs REST usando Fetch API.
- Código modular e reutilizável sem framework.
- Performance em aplicações web.
- Segurança no frontend.
- Acessibilidade.
- Debug, testes e manutenção de sistemas reais.

A IA deve explicar decisões técnicas com clareza, evitar soluções mágicas e escrever código que outra pessoa consiga entender e manter.

---

## Princípios gerais

### 1. Clareza antes de esperteza

Código JavaScript deve ser simples de ler. Evite soluções muito curtas, obscuras ou cheias de truques.

Um código um pouco mais longo, mas claro, costuma ser melhor que um código compacto e difícil de entender.

**Regra para a IA:** sempre prefira nomes claros, estrutura previsível e lógica explícita.

---

### 2. Menos código global

Variáveis, funções e estados globais aumentam risco de conflito, bugs e efeitos colaterais.

**Boas práticas:**

- Evite criar variáveis soltas no escopo global.
- Use `type="module"` em scripts modernos quando possível.
- Agrupe funcionalidades relacionadas.
- Exponha apenas o que realmente precisa ser acessado fora do módulo.
- Evite funções globais chamadas por `onclick` no HTML.

```html
<script type="module" src="/assets/js/app.js"></script>
```

---

### 3. Separação de responsabilidades

JavaScript não deve misturar regra de negócio, manipulação de tela, chamada HTTP e validação de forma desorganizada.

Cada arquivo deve ter uma responsabilidade clara.

```txt
HTML        = estrutura
CSS         = aparência
JavaScript  = comportamento
Fetch API   = comunicação HTTP
PHP         = regra de negócio, segurança e persistência
```

---

### 4. Segurança sempre em primeiro lugar

Tudo que vem do usuário, URL, formulário, `localStorage`, `sessionStorage`, cookies ou API deve ser tratado como dado não confiável.

JavaScript roda no navegador e pode ser visto, alterado e executado pelo usuário. Portanto:

- nunca coloque segredo no frontend;
- nunca confie apenas no frontend para permissão;
- nunca faça validação crítica apenas no navegador;
- nunca use dados externos diretamente no HTML sem cuidado.

**Regra para a IA:** segurança real deve ser garantida no backend. O frontend melhora experiência, mas não substitui validação no servidor.

---

## Boas práticas essenciais de sintaxe

### 5. Use `const` por padrão e `let` quando precisar reatribuir

Use `const` para valores que não serão reatribuídos. Use `let` apenas quando a variável precisar mudar.

Evite `var`, pois tem escopo de função e pode gerar comportamentos inesperados.

```js
const userName = 'Rodrigo';
let counter = 0;

counter += 1;
```

**Evite:**

```js
var nome = 'Rodrigo';
```

---

### 6. Use nomes descritivos

Nomes devem explicar intenção. Evite abreviações confusas como `x`, `d`, `tmp`, `obj`, exceto em contextos muito pequenos e óbvios.

**Bom:**

```js
const activeStudents = students.filter(student => student.status === 'active');
```

**Ruim:**

```js
const a = s.filter(x => x.st === 'active');
```

---

### 7. Use comparação estrita

Use `===` e `!==` em vez de `==` e `!=`.

```js
if (status === 'active') {
  showDashboard();
}
```

---

### 8. Evite números e textos mágicos

Valores importantes devem ficar em constantes nomeadas.

```js
const MAX_LOGIN_ATTEMPTS = 3;
const API_TIMEOUT_MS = 10000;

if (attempts >= MAX_LOGIN_ATTEMPTS) {
  blockLogin();
}
```

---

### 9. Use template literals para montar textos

Template literals deixam strings com variáveis mais legíveis.

```js
const message = `Olá, ${user.name}. Você tem ${totalTasks} tarefas pendentes.`;
```

---

### 10. Prefira funções pequenas

Funções devem fazer uma coisa bem feita.

**Sinais de alerta:**

- Função com muitas linhas.
- Muitos `if` aninhados.
- Muitos parâmetros.
- Função que busca API, valida formulário, manipula DOM e aplica regra de negócio ao mesmo tempo.

---

## Organização de código

### 11. Inicialize o JavaScript de forma controlada

Evite executar código antes do HTML estar carregado.

Use `defer`, `type="module"` ou `DOMContentLoaded`.

```html
<script type="module" src="/assets/js/app.js"></script>
```

```js
document.addEventListener('DOMContentLoaded', () => {
  initApp();
});
```

Em scripts com `type="module"`, o carregamento já é diferido por padrão, mas `DOMContentLoaded` ainda pode ser usado quando a inicialização depender de elementos da página.

---

### 12. Crie uma função principal de inicialização

Centralize a inicialização da página em uma função clara.

```js
function initApp() {
  bindEvents();
  loadInitialData();
}
```

---

### 13. Separe configuração do restante do código

URLs públicas, tempos, limites e configurações devem ficar em local claro.

```js
export const CONFIG = {
  apiBaseUrl: '/api',
  timeoutMs: 10000,
  itemsPerPage: 20,
};
```

**Atenção:** não coloque chaves secretas, senhas, tokens privados ou credenciais no frontend.

---

### 14. Evite repetição

Quando uma lógica se repete, transforme em função reutilizável. Porém, não abstraia cedo demais.

```js
function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
}
```

---

### 15. Comente intenção, não o óbvio

Comentários devem explicar o motivo de uma decisão, não repetir exatamente o que o código já diz.

**Bom:**

```js
// Mantém o usuário na tela atual quando a API falha para evitar perda de dados digitados.
showErrorMessage('Não foi possível salvar. Tente novamente.');
```

**Ruim:**

```js
// Incrementa contador em 1
counter++;
```

---

## Estrutura recomendada de arquivos JavaScript

### Estrutura padrão sem framework

```txt
/public/assets/js/
  app.js              # Inicialização global do projeto
  config.js           # Configurações públicas do frontend
  dom.js              # Seletores e helpers de DOM
  ui.js               # Loading, mensagens, modal e estados visuais
  validators.js       # Validações de formulário no frontend
  formatters.js       # Formatação de datas, moeda, telefone e textos
  state.js            # Estado simples da aplicação, quando necessário
  pages/
    login.js
    dashboard.js
    usuarios.js
```

Quando houver comunicação HTTP com backend, aplicar também a Skill Fetch API e adicionar os arquivos dela:

```txt
/public/assets/js/
  http.js             # Função base com fetch(), timeout, headers e erros
  api.js              # Funções específicas para endpoints PHP
```

### Responsabilidade dos arquivos

| Arquivo | Responsabilidade |
|---|---|
| `app.js` | Inicializar comportamentos globais. |
| `config.js` | Guardar configurações públicas do frontend. |
| `dom.js` | Seletores e helpers seguros de DOM. |
| `ui.js` | Feedback visual, loading, mensagens e modais. |
| `validators.js` | Validações simples no frontend. |
| `formatters.js` | Formatar valores para exibição. |
| `state.js` | Estado simples e previsível. |
| `pages/*.js` | Código específico de cada página. |
| `http.js` | Comunicação HTTP base, seguindo a Skill Fetch API. |
| `api.js` | Funções de endpoints, seguindo a Skill Fetch API. |

---

## Padrão de módulos nativos

Use módulos nativos do navegador quando possível.

```js
// validators.js
export function isRequired(value) {
  return String(value || '').trim() !== '';
}
```

```js
// pages/usuarios.js
import { isRequired } from '../validators.js';

function initUsuariosPage() {
  // inicialização da página
}

initUsuariosPage();
```

No HTML:

```html
<script type="module" src="/assets/js/pages/usuarios.js"></script>
```

---

## Manipulação do DOM

### 16. Selecione elementos de forma segura

Antes de usar um elemento, confirme se ele existe. Isso evita erros quando o script é carregado em páginas diferentes.

```js
const form = document.querySelector('#loginForm');

if (form) {
  form.addEventListener('submit', handleLoginSubmit);
}
```

---

### 17. Evite `innerHTML` com dados externos

Usar `innerHTML` com dados vindos de usuário, URL ou API pode abrir brecha de XSS.

Prefira `textContent` para texto simples.

```js
userNameElement.textContent = user.name;
```

**Evite:**

```js
userNameElement.innerHTML = user.name;
```

Use `innerHTML` apenas quando o HTML for controlado pela própria aplicação e não vier de entrada externa. Mesmo assim, prefira criar elementos com `createElement`.

---

### 18. Crie elementos com `createElement` quando houver dados dinâmicos

```js
function createTaskItem(task) {
  const item = document.createElement('li');
  const title = document.createElement('strong');
  const status = document.createElement('span');

  title.textContent = task.title;
  status.textContent = task.status;

  item.append(title, status);
  return item;
}
```

---

### 19. Use classes CSS para alterar aparência

Evite alterar muitos estilos diretamente via JavaScript.

O JavaScript deve controlar estado. O CSS deve controlar aparência.

```js
button.classList.add('is-loading');
button.classList.remove('is-loading');
```

---

### 20. Use delegação de eventos em listas dinâmicas

Quando muitos itens são criados dinamicamente, adicione o evento no elemento pai e identifique o alvo.

```js
const taskList = document.querySelector('#taskList');

taskList?.addEventListener('click', event => {
  const button = event.target.closest('[data-action="complete-task"]');

  if (!button) return;

  const taskId = button.dataset.taskId;
  completeTask(taskId);
});
```

---

### 21. Use `data-*` para conectar HTML e JavaScript

Prefira `data-*` para ações, IDs e configurações simples usadas pelo JavaScript.

```html
<button
  type="button"
  data-action="delete-user"
  data-user-id="15"
>
  Excluir
</button>
```

```js
document.addEventListener('click', event => {
  const button = event.target.closest('[data-action="delete-user"]');

  if (!button) return;

  const userId = Number(button.dataset.userId);

  if (!Number.isInteger(userId) || userId <= 0) {
    showErrorMessage('Usuário inválido.');
    return;
  }

  deleteUser(userId);
});
```

---

## Formulários e validação

### 22. Valide no frontend e no backend

Validação no frontend melhora experiência, mas não é suficiente para segurança.

Toda regra importante deve ser validada novamente no servidor.

```js
function validateEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
```

---

### 23. Mostre mensagens de erro claras

Mensagens devem dizer o que aconteceu e como o usuário pode corrigir.

```js
showFieldError('email', 'Digite um e-mail válido.');
```

Evite mensagens genéricas como:

```txt
Erro inválido.
```

---

### 24. Desabilite botões durante envio

Isso evita cliques duplicados e múltiplas requisições.

```js
async function handleSubmit(event) {
  event.preventDefault();

  const submitButton = event.currentTarget.querySelector('[type="submit"]');

  setButtonLoading(submitButton, true);

  try {
    await saveForm();
    showSuccessMessage('Salvo com sucesso.');
  } catch (error) {
    showErrorMessage('Não foi possível salvar.');
  } finally {
    setButtonLoading(submitButton, false);
  }
}

function setButtonLoading(button, isLoading) {
  if (!button) return;

  button.disabled = isLoading;
  button.textContent = isLoading ? 'Salvando...' : 'Salvar';
}
```

---

### 25. Normalize dados antes de enviar

Remova espaços desnecessários, padronize campos e envie apenas o necessário para a API.

```js
const payload = {
  name: String(formData.get('name') || '').trim(),
  email: String(formData.get('email') || '').trim().toLowerCase(),
};
```

---

## Integração com APIs e Fetch API

### 26. Use a Skill Fetch API como padrão para chamadas HTTP

Esta skill não deve duplicar todo o padrão de `fetch()`. Quando o código precisar conversar com backend, a IA deve aplicar a Skill Fetch API sem Framework.

Use a Skill Fetch API para definir:

- `http.js`;
- `api.js`;
- `fetch()`;
- `AbortController`;
- timeout;
- headers;
- CSRF;
- leitura de JSON;
- tratamento de status HTTP;
- mensagens de erro;
- loading em requisições;
- formulários assíncronos;
- filtros;
- paginação;
- comunicação com endpoints PHP.

---

### 27. Não espalhe `fetch()` pelo projeto

A IA deve evitar `fetch()` solto em vários arquivos.

**Errado:**

```js
fetch('/api/usuarios/listar.php');
fetch('/api/produtos/listar.php');
fetch('/api/pedidos/listar.php');
```

**Correto:**

```js
import { listarUsuarios } from '../api.js';

const response = await listarUsuarios();
```

A implementação de `listarUsuarios()` deve seguir a Skill Fetch API.

---

### 28. Trate erro de rede e erro da API separadamente

Erro de rede, timeout, erro de validação, conflito de regra de negócio e erro interno não são a mesma coisa.

```js
try {
  const response = await listarUsuarios();
  renderUsers(response.data || []);
} catch (error) {
  showErrorMessage(error.message || 'Falha ao carregar usuários.');
}
```

---

### 29. Use `async/await` com `try/catch`

Evite cadeias longas de `.then()` quando a lógica for complexa.

```js
async function loadUsers() {
  try {
    showLoading('Carregando usuários...');
    const response = await listarUsuarios();
    renderUsers(response.data || []);
  } catch (error) {
    showErrorMessage(error.message || 'Não foi possível carregar os usuários.');
  } finally {
    hideLoading();
  }
}
```

---

### 30. Nunca confie apenas no frontend para permissões

Esconder botão no frontend não protege o sistema.

A API PHP deve verificar se o usuário pode executar a ação.

**Exemplo:** se um usuário não pode excluir uma tarefa, o backend deve bloquear a exclusão mesmo que alguém tente chamar o endpoint manualmente.

---

## Estado da aplicação

### 31. Mantenha o estado previsível

Evite espalhar variáveis de estado por vários arquivos sem controle.

Para projetos simples, use um objeto centralizado.

```js
const appState = {
  user: null,
  tasks: [],
  filters: {
    status: 'all',
  },
};
```

---

### 32. Atualize a tela a partir do estado

Sempre que possível, não deixe a tela ser a única fonte da verdade.

O estado deve representar os dados, e a tela deve ser renderizada a partir dele.

```js
function setTasks(tasks) {
  appState.tasks = tasks;
  renderTasks(appState.tasks);
}
```

---

### 33. Evite mutações inesperadas

Ao alterar listas ou objetos, prefira criar novas versões quando isso deixar a lógica mais segura e previsível.

```js
const updatedTasks = tasks.map(task => {
  if (task.id !== taskId) return task;

  return {
    ...task,
    status: 'done',
  };
});
```

---

## LocalStorage, SessionStorage e dados no navegador

### 34. Use armazenamento local com cuidado

`localStorage` e `sessionStorage` são úteis, mas não devem guardar senhas, dados altamente sensíveis ou tokens críticos de longa duração.

**Pode fazer sentido armazenar:**

- Preferências de tema.
- Filtros da interface.
- Rascunhos simples.
- Estado temporário não sensível.

**Evite armazenar:**

- Senhas.
- Chaves secretas.
- Tokens permanentes.
- Dados pessoais sensíveis sem necessidade.

---

### 35. Sempre trate falhas ao ler JSON salvo

Dados no navegador podem estar corrompidos, antigos ou alterados manualmente.

```js
function getStoredUserPreferences() {
  try {
    const rawValue = localStorage.getItem('userPreferences');
    return rawValue ? JSON.parse(rawValue) : null;
  } catch {
    return null;
  }
}
```

---

## Segurança no JavaScript

### 36. Proteja contra XSS

XSS acontece quando conteúdo malicioso é inserido na página e executado como JavaScript.

**Boas práticas:**

- Use `textContent` para textos vindos de usuário ou API.
- Evite `innerHTML` com dados externos.
- Valide e sanitize dados no backend.
- Use Content Security Policy quando possível.
- Não injete scripts dinâmicos sem controle.
- Não use dados da URL diretamente no DOM.

---

### 37. Não exponha segredos no frontend

Tudo que está no JavaScript do navegador pode ser visto pelo usuário.

Nunca coloque no frontend:

- Senhas de banco de dados.
- Tokens privados de API.
- Chaves secretas.
- Credenciais de serviços.
- Regras críticas de autorização.

---

### 38. Cuidado com dados vindos da URL

Parâmetros de URL podem ser manipulados.

Nunca use valores da URL diretamente sem validação.

```js
const params = new URLSearchParams(window.location.search);
const taskId = Number(params.get('id'));

if (!Number.isInteger(taskId) || taskId <= 0) {
  showErrorMessage('Identificador inválido.');
}
```

---

### 39. Evite `eval`

`eval` executa texto como código e pode causar falhas graves de segurança.

**Evite:**

```js
eval(userInput);
```

---

### 40. CSRF é responsabilidade conjunta com Fetch API e PHP

Quando houver formulário assíncrono ou ação sensível via `fetch()`, a IA deve aplicar a Skill Fetch API e a Skill PHP para garantir CSRF.

No JavaScript geral, a regra é:

- não inventar token no frontend;
- ler token apenas se ele for entregue pelo backend;
- enviar o token conforme o padrão da Skill Fetch API;
- manter a validação real no PHP.

Exemplo de leitura segura de token no HTML:

```js
function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.getAttribute('content') || '';
}
```

---

## Performance

### 41. Evite manipular o DOM em excesso

Muitas alterações pequenas no DOM podem prejudicar performance.

Monte o conteúdo em memória e depois insira de uma vez.

```js
const fragment = document.createDocumentFragment();

tasks.forEach(task => {
  fragment.appendChild(createTaskItem(task));
});

taskList.replaceChildren(fragment);
```

---

### 42. Use debounce em eventos muito frequentes

Campos de busca, resize e scroll podem disparar muitas vezes.

Use debounce para reduzir chamadas desnecessárias.

```js
function debounce(callback, delay = 300) {
  let timeoutId;

  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => callback(...args), delay);
  };
}

searchInput.addEventListener('input', debounce(event => {
  searchTasks(event.target.value);
}, 300));
```

Se o debounce for usado para busca remota, a chamada HTTP deve seguir a Skill Fetch API.

---

### 43. Carregue dados sob demanda

Evite carregar tudo de uma vez se o usuário só precisa de uma parte.

Use paginação, filtros e carregamento incremental.

**Exemplo:** em listas grandes, busque 20, 50 ou 100 registros por vez em vez de carregar milhares de registros no navegador.

---

### 44. Evite bibliotecas desnecessárias

Antes de instalar uma biblioteca, pergunte:

- O problema é grande o suficiente para justificar dependência?
- O JavaScript nativo já resolve?
- A biblioteca é mantida?
- Ela aumenta muito o tamanho do projeto?
- A equipe sabe manter esse código?
- Isso quebra o padrão sem framework do projeto?

---

## Acessibilidade

### 45. JavaScript não deve quebrar HTML semântico

Use botões para ações e links para navegação.

Não transforme `div` em botão sem necessidade.

**Bom:**

```html
<button type="button" id="openModalButton">Abrir modal</button>
```

**Ruim:**

```html
<div onclick="openModal()">Abrir modal</div>
```

---

### 46. Preserve navegação por teclado

Interações criadas com JavaScript devem funcionar com teclado.

Modais, menus e botões precisam ser acessíveis.

**Boas práticas:**

- Use elementos HTML corretos.
- Controle foco em modais.
- Permita fechar modais com `Escape` quando adequado.
- Não remova outline de foco sem alternativa visual.

---

### 47. Informe mudanças importantes na tela

Mensagens de erro, sucesso e carregamento devem ser percebidas por usuários de leitores de tela quando necessário.

```html
<div id="feedback" role="status" aria-live="polite"></div>
```

```js
feedback.textContent = 'Dados salvos com sucesso.';
```

---

## Qualidade e manutenção

### 48. Padronize estilo de código

Use um padrão único de formatação.

**Padrões recomendados:**

- Indentação consistente.
- Ponto e vírgula de forma padronizada.
- Aspas simples ou duplas, seguindo o mesmo padrão.
- Nomes de funções com verbos.
- Arquivos pequenos e bem separados.
- Evitar mistura de idioma em nomes de variáveis no mesmo projeto.

---

### 49. Trate erros de forma visível

Nunca silencie erros importantes.

O usuário precisa de feedback, e a equipe precisa conseguir investigar problemas.

```js
try {
  await saveData(payload);
} catch (error) {
  console.error('Erro ao salvar dados:', error);
  showErrorMessage('Não foi possível salvar os dados.');
}
```

Em produção, evite mostrar detalhes técnicos para o usuário final.

---

### 50. Use logs com intenção

Logs ajudam no desenvolvimento, mas logs excessivos atrapalham.

Nunca registre dados sensíveis.

**Evite logar:**

- Senhas.
- Tokens.
- Documentos pessoais.
- Dados médicos.
- Dados financeiros sensíveis.

---

### 51. Escreva código testável

Funções puras e separadas da interface são mais fáceis de testar.

**Bom:**

```js
function calculateTotal(items) {
  return items.reduce((total, item) => total + item.price, 0);
}
```

**Menos testável:**

```js
function updateTotalOnScreen() {
  const items = getItemsFromDOM();
  const total = calculateTotal(items);
  document.querySelector('#total').textContent = total;
}
```

---

## Padrão recomendado para formulário sem framework

Este exemplo mostra apenas a parte JavaScript geral. A função `createTask()` deve seguir a Skill Fetch API.

```js
import { createTask } from '../api.js';

const form = document.querySelector('#taskForm');
const feedback = document.querySelector('#feedback');

form?.addEventListener('submit', handleTaskSubmit);

async function handleTaskSubmit(event) {
  event.preventDefault();

  const formData = new FormData(event.currentTarget);

  const payload = {
    title: String(formData.get('title') || '').trim(),
    description: String(formData.get('description') || '').trim(),
  };

  if (!payload.title) {
    showMessage('Informe o título da tarefa.', 'error');
    return;
  }

  setFormLoading(event.currentTarget, true);

  try {
    await createTask(payload);
    showMessage('Tarefa criada com sucesso.', 'success');
    event.currentTarget.reset();
  } catch (error) {
    showMessage(error.message || 'Não foi possível criar a tarefa.', 'error');
  } finally {
    setFormLoading(event.currentTarget, false);
  }
}

function setFormLoading(formElement, isLoading) {
  const button = formElement.querySelector('[type="submit"]');

  if (!button) return;

  button.disabled = isLoading;
  button.textContent = isLoading ? 'Salvando...' : 'Salvar';
}

function showMessage(message, type = 'info') {
  if (!feedback) return;

  feedback.textContent = message;
  feedback.dataset.type = type;
}
```

---

## Padrão recomendado para renderização segura

```js
function renderUserCard(user) {
  const card = document.createElement('article');
  const name = document.createElement('h2');
  const email = document.createElement('p');

  name.textContent = user.name;
  email.textContent = user.email;

  card.append(name, email);

  return card;
}
```

---

## Regras que a IA deve seguir ao gerar JavaScript

1. Usar `const` por padrão.
2. Usar `let` apenas quando houver reatribuição.
3. Nunca usar `var` em código novo.
4. Usar `===` e `!==`.
5. Evitar variáveis globais.
6. Separar código por responsabilidade.
7. Usar módulos nativos quando fizer sentido.
8. Usar eventos com `addEventListener`, não `onclick` inline.
9. Validar dados antes de enviar para o backend.
10. Garantir que o backend também valide as regras importantes.
11. Mostrar feedback de loading, sucesso e erro ao usuário.
12. Não usar `innerHTML` com dados externos.
13. Não expor segredos no frontend.
14. Não confiar no frontend para autorização.
15. Usar nomes claros para variáveis e funções.
16. Evitar funções grandes e complexas.
17. Preservar acessibilidade e semântica HTML.
18. Evitar dependências sem necessidade real.
19. Não usar jQuery, Axios, HTMX, React, Vue ou framework sem pedido explícito.
20. Quando houver HTTP, aplicar a Skill Fetch API sem Framework.
21. Escrever código fácil de testar.
22. Antes de entregar, revisar segurança, erro, estado vazio, loading, responsividade e acessibilidade.

---

## Checklist para revisão de código JavaScript

Use este checklist sempre que revisar um arquivo JavaScript.

### Sintaxe e organização

- [ ] O código usa `const` e `let` corretamente?
- [ ] Não existe `var` em código novo?
- [ ] Os nomes são claros e descritivos?
- [ ] As funções são pequenas e com responsabilidade única?
- [ ] O código evita repetição desnecessária?
- [ ] As configurações públicas estão centralizadas?
- [ ] Não há variáveis globais desnecessárias?

### DOM e interface

- [ ] Os elementos são verificados antes do uso?
- [ ] Eventos usam `addEventListener`?
- [ ] Não existe `onclick` inline sem necessidade?
- [ ] Dados externos usam `textContent` em vez de `innerHTML`?
- [ ] Loading, sucesso e erro são comunicados ao usuário?
- [ ] A interface funciona em estado vazio?
- [ ] A navegação por teclado foi preservada?
- [ ] A atualização visual usa classes CSS em vez de estilos inline excessivos?

### Formulários

- [ ] O submit usa `event.preventDefault()` quando for assíncrono?
- [ ] O botão é desabilitado durante o envio?
- [ ] Os dados são normalizados antes do envio?
- [ ] O usuário recebe mensagem clara de erro?
- [ ] O backend também valida tudo?

### API e dados

- [ ] Se existe HTTP, foi aplicada a Skill Fetch API?
- [ ] As chamadas HTTP estão centralizadas em `http.js`/`api.js`?
- [ ] Não há `fetch()` espalhado sem padrão?
- [ ] Erros da API são tratados?
- [ ] Erros de rede e timeout são tratados?
- [ ] O payload envia apenas dados necessários?
- [ ] CSRF é enviado quando a ação é sensível?

### Segurança

- [ ] Não há segredos no frontend?
- [ ] Não há `eval`?
- [ ] Não há `innerHTML` com dados externos?
- [ ] Dados de URL são validados?
- [ ] Permissões críticas são garantidas no backend?
- [ ] Logs não expõem dados sensíveis?
- [ ] O frontend não tenta substituir validação de backend?

### Performance

- [ ] O DOM não é atualizado em excesso?
- [ ] Listas grandes usam paginação ou carregamento incremental?
- [ ] Eventos frequentes usam debounce/throttle quando necessário?
- [ ] Bibliotecas externas foram evitadas?
- [ ] O código evita carregamento inicial desnecessário?

### Padrão sem framework

- [ ] Não foi usado jQuery?
- [ ] Não foi usado Axios?
- [ ] Não foi usado HTMX?
- [ ] Não foi usado Alpine.js?
- [ ] Não foi usado React/Vue/Angular/Svelte?
- [ ] O JavaScript puro resolve o problema de forma clara?

---

## Prompt operacional para a IA

Quando o usuário pedir para criar ou revisar código JavaScript, siga este processo:

1. Entenda a finalidade da funcionalidade.
2. Identifique se o código é para navegador, Node.js ou outro ambiente.
3. Verifique se há HTML, CSS, API ou backend envolvidos.
4. Se houver HTTP, aplicar a Skill Fetch API sem Framework.
5. Separar responsabilidades antes de escrever o código.
6. Criar código simples, seguro e modular.
7. Validar entradas e tratar erros.
8. Evitar dependências desnecessárias.
9. Garantir feedback de loading, sucesso, erro e estado vazio.
10. Explicar rapidamente como usar o código.
11. Apontar riscos importantes quando existirem.
12. Entregar checklist de teste quando a funcionalidade for crítica.

---

## Modelo de resposta da IA ao revisar código JavaScript

Ao revisar um código, responda nesta ordem:

1. **Resumo do que o código faz:** explique a intenção da funcionalidade.
2. **Principais riscos:** liste bugs, segurança, performance, acessibilidade ou manutenção.
3. **Correções recomendadas:** explique o que deve mudar.
4. **Código corrigido:** entregue uma versão melhorada.
5. **Integração com Fetch API:** quando houver HTTP, indique se está seguindo a skill correta.
6. **Checklist de teste:** diga como validar se ficou correto.

---

## Modelo de resposta da IA ao criar código JavaScript

Ao criar código novo, responda nesta ordem:

1. **Estrutura sugerida:** arquivos e responsabilidades.
2. **Código principal:** JavaScript pronto para uso.
3. **Integração com HTML/CSS/API:** quando necessário.
4. **Observação sobre Fetch API:** quando houver comunicação com backend.
5. **Explicação curta:** como funciona.
6. **Cuidados importantes:** segurança, validação, acessibilidade e manutenção.

---

## Erros comuns que a IA deve evitar

- Criar JavaScript todo dentro do HTML sem necessidade.
- Usar `onclick` inline em vez de `addEventListener`.
- Usar `innerHTML` para inserir dados vindos do usuário.
- Criar funções enormes.
- Ignorar estados de erro e carregamento.
- Colocar token secreto no frontend.
- Fazer validação apenas no frontend.
- Esconder botão e achar que isso é segurança.
- Repetir `fetch()` em vários arquivos sem padrão.
- Criar `apiRequest()` diferente em cada tela.
- Usar jQuery para resolver problema que JavaScript puro resolve.
- Usar Axios quando o padrão do projeto é Fetch API.
- Usar HTMX, Alpine, React ou Vue sem pedido explícito.
- Usar biblioteca grande para resolver problema simples.
- Não verificar se o elemento existe antes de manipular.
- Não pensar em mobile, acessibilidade e teclado.
- Misturar regra de negócio crítica no frontend.

---

## Padrão mínimo de qualidade esperado

Um código JavaScript só deve ser considerado bom quando:

- É fácil de entender.
- É fácil de alterar.
- Tem responsabilidades separadas.
- Trata erros previsíveis.
- Não expõe dados sensíveis.
- Manipula o DOM com segurança.
- Dá retorno claro ao usuário.
- Funciona bem com dados vazios, inválidos ou lentos.
- Preserva acessibilidade.
- Segue o padrão sem framework.
- Usa a Skill Fetch API quando existe comunicação HTTP.
- Pode crescer sem virar bagunça.

---

## Frase guia da skill

> JavaScript profissional em projeto sem framework não é apenas fazer funcionar; é fazer funcionar de forma clara, segura, previsível, acessível e fácil de manter.
