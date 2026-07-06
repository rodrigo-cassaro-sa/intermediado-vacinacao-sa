# Skill: Fetch API sem Framework para Projetos PHP + JavaScript Puro

## Objetivo da skill

Esta skill orienta uma IA de programação a criar, revisar, corrigir e padronizar integrações entre frontend e backend usando **JavaScript puro com Fetch API**, sem frameworks, sem bibliotecas externas e com backend em **PHP procedural puro**.

O foco é atualizar partes da página sem recarregar tudo, mantendo código simples, seguro, organizado, previsível e fácil de manter.

---

## Limite desta skill

Esta skill padroniza como o frontend consome endpoints do backend usando Fetch API.

Ela deve cuidar de `fetch()`, `http.js`, `api.js`, headers, timeout, CSRF, tratamento de erro, loading e resposta JSON no navegador.

Ela não deve substituir:

- `skill-js.md` para comportamento geral da tela;
- `skill-api-rest.md` para contrato oficial dos endpoints;
- `skill-backend.md` para regra de negócio e validação real;
- `skill-seguranca.md` para segurança completa;
- `skill-erros-excecoes.md` para padrão geral de erros do sistema.

Esta skill responde "como o frontend conversa com o backend de forma segura e previsível?".

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior de JavaScript e PHP procedural, especialista em:

- JavaScript puro no navegador.
- Fetch API.
- Integração com endpoints PHP.
- APIs JSON.
- Atualização parcial de tela.
- Formulários sem reload.
- Segurança no frontend e backend.
- Código simples, modular e sem framework.

A IA deve evitar soluções mágicas, dependências desnecessárias e frameworks frontend quando o projeto puder ser resolvido com HTML, CSS, JavaScript puro, Fetch API e PHP procedural.

---

## Stack oficial

A stack padrão desta skill é:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON
Git
Servidor Linux com Apache ou Nginx
```

A IA deve evitar, salvo pedido explícito:

- React.
- Vue.
- Angular.
- Svelte.
- jQuery.
- HTMX.
- Alpine.js.
- Axios.
- Framework PHP.
- Orientação a objetos obrigatória.
- SPA complexa sem necessidade.

---

## Conceito principal

Fetch API é o padrão moderno do navegador para fazer requisições HTTP com JavaScript.

Ela substitui o uso antigo de `XMLHttpRequest` na maioria dos casos e permite buscar dados do servidor, enviar formulários, consultar APIs e atualizar partes da página sem recarregar tudo.

Nesta skill, o termo "AJAX moderno" significa:

```txt
JavaScript puro + fetch() + async/await + endpoint PHP + resposta JSON padronizada
```

---

## Quando usar Fetch API

Use Fetch API quando a tela precisar fazer alguma ação sem recarregar a página inteira.

Exemplos:

- Salvar formulário sem reload.
- Buscar dados de uma tabela.
- Aplicar filtros.
- Fazer paginação.
- Atualizar status de um registro.
- Excluir item e remover da tela.
- Abrir modal com dados carregados do servidor.
- Consultar CEP, CPF, CNPJ ou outro dado.
- Atualizar um dashboard.
- Validar um campo no backend.
- Enviar dados para uma API PHP.
- Carregar partes da tela sob demanda.

---

## Quando não usar Fetch API

Não use Fetch API quando o fluxo tradicional for mais simples e suficiente.

Exemplos:

- Página institucional simples.
- Formulário pequeno onde reload não atrapalha.
- Tela que só exibe conteúdo estático.
- Processo em que o backend precisa redirecionar após envio.
- Página onde a simplicidade do PHP renderizando HTML completo é melhor.

Regra:

```txt
Se o reload não atrapalha, PHP tradicional pode ser suficiente.
Se o reload atrapalha a experiência, use Fetch API.
```

---

## Regra de decisão da IA

A IA deve escolher assim:

```txt
Página simples:
PHP renderiza HTML completo.

Página com interação moderada:
PHP renderiza a base e JavaScript puro usa fetch() para atualizar partes da tela.

Tela administrativa com filtros, paginação e ações:
PHP fornece endpoints JSON e JavaScript puro atualiza a interface.

Interface muito complexa:
Continuar com JavaScript puro enquanto for viável.
Só sugerir framework frontend se o usuário pedir explicitamente.
```

---

## Estrutura padrão de arquivos

A IA deve organizar os arquivos desta forma:

```txt
/projeto
  /app
    /config
      database.php
    /helpers
      resposta_helper.php
      request_helper.php
      validacao_helper.php
      csrf_helper.php
      log_helper.php
    /middlewares
      auth_middleware.php
      permissao_middleware.php
    /services
      usuario_service.php

  /public
    index.php
    /api
      /usuarios
        listar.php
        criar.php
        atualizar.php
        excluir.php
    /assets
      /css
        app.css
      /js
        config.js
        http.js
        api.js
        dom.js
        ui.js
        validators.js
        formatters.js
        pages
          usuarios.js
```

---

## Responsabilidade dos arquivos JavaScript

| Arquivo | Responsabilidade |
|---|---|
| `config.js` | Configurações públicas do frontend. |
| `http.js` | Função base para chamadas Fetch. |
| `api.js` | Funções específicas para endpoints PHP. |
| `dom.js` | Seletores e helpers de DOM. |
| `ui.js` | Loading, mensagens, modal e estados visuais. |
| `validators.js` | Validações do frontend. |
| `formatters.js` | Formatação de datas, moeda, telefone e textos. |
| `pages/*.js` | Código específico de cada página. |

---

## Responsabilidade dos arquivos PHP

| Arquivo | Responsabilidade |
|---|---|
| `/public/api/...` | Endpoints acessíveis pelo navegador. |
| `/app/services/...` | Regras de negócio. |
| `/app/helpers/resposta_helper.php` | Resposta JSON padronizada. |
| `/app/helpers/request_helper.php` | Leitura segura da entrada. |
| `/app/helpers/validacao_helper.php` | Validações reutilizáveis. |
| `/app/helpers/csrf_helper.php` | Geração e validação de token CSRF. |
| `/app/middlewares/auth_middleware.php` | Verificação de login. |
| `/app/middlewares/permissao_middleware.php` | Verificação de permissão. |

---

## Padrão do HTML

Use HTML semântico e carregue JavaScript como módulo.

```html
<form id="usuarioForm">
  <label for="nome">Nome</label>
  <input type="text" id="nome" name="nome" required>

  <label for="email">E-mail</label>
  <input type="email" id="email" name="email" required>

  <button type="submit">Salvar</button>
</form>

<div id="feedback" role="status" aria-live="polite"></div>

<table>
  <thead>
    <tr>
      <th>Nome</th>
      <th>E-mail</th>
    </tr>
  </thead>
  <tbody id="usuariosTableBody"></tbody>
</table>

<script type="module" src="/assets/js/pages/usuarios.js"></script>
```

Regras:

- Use `id` apenas para elementos únicos.
- Use `name` em campos de formulário.
- Use `button type="submit"` para envio.
- Use `role="status"` e `aria-live` para mensagens dinâmicas.
- Não use `onclick` inline.
- Não escreva JavaScript grande dentro do HTML.

---

## Padrão do config.js

```js
export const CONFIG = {
  apiBaseUrl: '/api',
  requestTimeoutMs: 10000,
};
```

Nunca colocar no frontend:

- Senha.
- Token secreto.
- Chave privada.
- Credencial de banco.
- Token permanente de API.
- Regras críticas de autorização.

Tudo que está no JavaScript do navegador pode ser visto pelo usuário.

---

## Padrão do http.js

Este arquivo deve centralizar todas as chamadas Fetch.

```js
import { CONFIG } from './config.js';

export async function httpRequest(path, options = {}) {
  const controller = new AbortController();

  const timeoutId = setTimeout(() => {
    controller.abort();
  }, CONFIG.requestTimeoutMs);

  try {
    const response = await fetch(`${CONFIG.apiBaseUrl}${path}`, {
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...options.headers,
      },
      signal: controller.signal,
      ...options,
    });

    const data = await response.json().catch(() => null);

    if (!response.ok) {
      const message = data?.message || 'Erro na comunicação com o servidor.';
      throw new Error(message);
    }

    return data;
  } catch (error) {
    if (error.name === 'AbortError') {
      throw new Error('Tempo limite excedido. Tente novamente.');
    }

    throw error;
  } finally {
    clearTimeout(timeoutId);
  }
}
```

Regras:

- Nunca espalhar `fetch()` por vários arquivos.
- Toda chamada HTTP deve passar por `httpRequest()`.
- Toda resposta deve ser tratada.
- Toda falha deve gerar mensagem amigável.
- Timeout deve ser tratado quando necessário.
- Detalhes técnicos não devem aparecer para o usuário final.

---

## Regra para JSON, FormData e Upload de Arquivos

Por padrão, usar `Content-Type: application/json` apenas quando o corpo da requisição for JSON.

Quando o payload for `FormData`, upload de arquivo ou formulário `multipart/form-data`, não definir manualmente o header `Content-Type`.

O navegador deve definir automaticamente o `Content-Type` correto com `boundary`.

### Exemplo recomendado

```js
const headers = {
  Accept: 'application/json',
  ...options.headers,
};

if (!(options.body instanceof FormData)) {
  headers['Content-Type'] = 'application/json';
}

---

## Padrão do api.js

Este arquivo deve conter funções específicas para cada endpoint.

```js
import { httpRequest } from './http.js';

export function listarUsuarios(params = {}) {
  const query = new URLSearchParams(params).toString();
  const path = query ? `/usuarios/listar.php?${query}` : '/usuarios/listar.php';

  return httpRequest(path, {
    method: 'GET',
  });
}

export function criarUsuario(payload) {
  return httpRequest('/usuarios/criar.php', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export function atualizarUsuario(id, payload) {
  return httpRequest('/usuarios/atualizar.php', {
    method: 'POST',
    body: JSON.stringify({
      id,
      ...payload,
    }),
  });
}

export function excluirUsuario(id) {
  return httpRequest('/usuarios/excluir.php', {
    method: 'POST',
    body: JSON.stringify({ id }),
  });
}
```

Regras:

- O arquivo `api.js` não manipula DOM.
- O arquivo `api.js` não mostra mensagens na tela.
- O arquivo `api.js` apenas conversa com o backend.
- Cada função deve ter nome claro.
- O payload deve conter apenas os dados necessários.

---

## Padrão do ui.js

```js
export function showMessage(element, message, type = 'info') {
  if (!element) return;

  element.textContent = message;
  element.dataset.type = type;
}

export function clearMessage(element) {
  if (!element) return;

  element.textContent = '';
  element.removeAttribute('data-type');
}

export function setButtonLoading(button, isLoading, loadingText = 'Salvando...', defaultText = 'Salvar') {
  if (!button) return;

  button.disabled = isLoading;
  button.textContent = isLoading ? loadingText : defaultText;
}
```

Regras:

- Feedback visual deve ser padronizado.
- O usuário sempre deve saber se a ação está carregando, se deu certo ou se falhou.
- Não duplicar lógica de loading em várias páginas.

---

## Padrão de página com Fetch

Exemplo de `/public/assets/js/pages/usuarios.js`:

```js
import { listarUsuarios, criarUsuario } from '../api.js';
import { showMessage, clearMessage, setButtonLoading } from '../ui.js';

const form = document.querySelector('#usuarioForm');
const submitButton = form?.querySelector('button[type="submit"]');
const tableBody = document.querySelector('#usuariosTableBody');
const feedback = document.querySelector('#feedback');

document.addEventListener('DOMContentLoaded', () => {
  carregarUsuarios();

  if (form) {
    form.addEventListener('submit', handleSubmit);
  }
});

async function carregarUsuarios() {
  try {
    showMessage(feedback, 'Carregando usuários...');
    const response = await listarUsuarios();

    renderizarUsuarios(response.data || []);
    clearMessage(feedback);
  } catch (error) {
    showMessage(feedback, error.message || 'Não foi possível carregar usuários.', 'error');
  }
}

async function handleSubmit(event) {
  event.preventDefault();

  const formData = new FormData(event.currentTarget);

  const payload = {
    nome: String(formData.get('nome') || '').trim(),
    email: String(formData.get('email') || '').trim().toLowerCase(),
  };

  if (!payload.nome) {
    showMessage(feedback, 'Informe o nome.', 'error');
    return;
  }

  if (!payload.email) {
    showMessage(feedback, 'Informe o e-mail.', 'error');
    return;
  }

  setButtonLoading(submitButton, true);

  try {
    await criarUsuario(payload);

    event.currentTarget.reset();
    showMessage(feedback, 'Usuário criado com sucesso.', 'success');

    await carregarUsuarios();
  } catch (error) {
    showMessage(feedback, error.message || 'Não foi possível salvar.', 'error');
  } finally {
    setButtonLoading(submitButton, false);
  }
}

function renderizarUsuarios(usuarios) {
  if (!tableBody) return;

  tableBody.replaceChildren();

  if (usuarios.length === 0) {
    const row = document.createElement('tr');
    const cell = document.createElement('td');

    cell.colSpan = 2;
    cell.textContent = 'Nenhum usuário encontrado.';

    row.appendChild(cell);
    tableBody.appendChild(row);
    return;
  }

  const fragment = document.createDocumentFragment();

  usuarios.forEach(usuario => {
    const row = document.createElement('tr');

    const nameCell = document.createElement('td');
    const emailCell = document.createElement('td');

    nameCell.textContent = usuario.nome;
    emailCell.textContent = usuario.email;

    row.append(nameCell, emailCell);
    fragment.appendChild(row);
  });

  tableBody.appendChild(fragment);
}
```

---

## Padrão de resposta JSON do PHP

Todo endpoint consumido por Fetch deve responder neste formato.

### Sucesso

```json
{
  "success": true,
  "message": "Operação realizada com sucesso.",
  "data": []
}
```

### Erro

```json
{
  "success": false,
  "message": "Não foi possível realizar a operação.",
  "data": [],
  "errors": []
}
```

Regras:

- `success` sempre booleano.
- `message` sempre texto amigável.
- `data` sempre presente.
- `errors` opcional para validações específicas.
- Status HTTP deve ser coerente com o resultado.

---

## Status HTTP recomendados

```txt
200 - sucesso
201 - criado
400 - erro de validação
401 - não autenticado
403 - sem permissão
404 - não encontrado
405 - método não permitido
409 - conflito ou duplicidade
422 - dados válidos em formato, mas inválidos para regra de negócio
500 - erro interno
```

---

## Helper PHP para resposta JSON

Arquivo sugerido:

```txt
/app/helpers/resposta_helper.php
```

Código:

```php
<?php

function responderJson($success, $message, $data = [], $statusCode = 200, $errors = []) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    $response = [
        'success' => (bool) $success,
        'message' => (string) $message,
        'data' => $data,
    ];

    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

    exit;
}
```

---

## Helper PHP para ler JSON do Fetch

Arquivo sugerido:

```txt
/app/helpers/request_helper.php
```

Código:

```php
<?php

function lerJsonEntrada() {
    $rawInput = file_get_contents('php://input');

    if ($rawInput === false || trim($rawInput) === '') {
        return [];
    }

    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        responderJson(false, 'JSON inválido.', [], 400);
    }

    return $data;
}

function exigirMetodo($metodo) {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodo)) {
        responderJson(false, 'Método não permitido.', [], 405);
    }
}
```

---

## Exemplo de endpoint listar.php

```php
<?php

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/resposta_helper.php';
require_once __DIR__ . '/../../../app/helpers/request_helper.php';
require_once __DIR__ . '/../../../app/middlewares/auth_middleware.php';
require_once __DIR__ . '/../../../app/services/usuario_service.php';

exigirMetodo('GET');
exigirLogin();

try {
    $usuarios = listarUsuarios();

    responderJson(true, 'Usuários carregados com sucesso.', $usuarios, 200);
} catch (Throwable $e) {
    registrarLog('erro', 'Erro ao listar usuários.', [
        'erro' => $e->getMessage(),
    ]);

    responderJson(false, 'Não foi possível carregar os usuários.', [], 500);
}
```

---

## Exemplo de endpoint criar.php

```php
<?php

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/resposta_helper.php';
require_once __DIR__ . '/../../../app/helpers/request_helper.php';
require_once __DIR__ . '/../../../app/helpers/validacao_helper.php';
require_once __DIR__ . '/../../../app/middlewares/auth_middleware.php';
require_once __DIR__ . '/../../../app/services/usuario_service.php';

exigirMetodo('POST');
exigirLogin();

$input = lerJsonEntrada();

$nome = trim($input['nome'] ?? '');
$email = trim($input['email'] ?? '');

$errors = [];

if ($nome === '') {
    $errors['nome'] = 'Informe o nome.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Informe um e-mail válido.';
}

if (!empty($errors)) {
    responderJson(false, 'Verifique os campos informados.', [], 400, $errors);
}

try {
    $usuarioId = criarUsuario($nome, $email);

    responderJson(true, 'Usuário criado com sucesso.', [
        'id' => $usuarioId,
    ], 201);
} catch (Throwable $e) {
    registrarLog('erro', 'Erro ao criar usuário.', [
        'erro' => $e->getMessage(),
    ]);

    responderJson(false, 'Não foi possível criar o usuário.', [], 500);
}
```

---

## CSRF em chamadas Fetch

Ações sensíveis devem usar CSRF.

Exemplos de ações sensíveis:

- Criar registro.
- Editar registro.
- Excluir registro.
- Alterar senha.
- Fazer upload.
- Alterar permissão.
- Alterar dados financeiros.

### HTML com token CSRF

```html
<meta name="csrf-token" content="<?= e($_SESSION['csrf_token']) ?>">
```

### JavaScript lendo token

```js
export function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
}
```

### Enviando token no header

```js
import { getCsrfToken } from './csrf.js';

export async function httpRequest(path, options = {}) {
  const response = await fetch(path, {
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-Token': getCsrfToken(),
      ...options.headers,
    },
    ...options,
  });

  return response;
}
```

### PHP validando token

```php
<?php

function validarCsrfHeader() {
    $tokenSessao = $_SESSION['csrf_token'] ?? '';
    $tokenHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if ($tokenSessao === '' || $tokenHeader === '' || !hash_equals($tokenSessao, $tokenHeader)) {
        responderJson(false, 'Token de segurança inválido.', [], 403);
    }
}
```

---

## Segurança obrigatória

A IA deve sempre aplicar estas regras:

1. Validar no frontend para melhorar experiência.
2. Validar novamente no backend para segurança real.
3. Nunca confiar em dados vindos do navegador.
4. Nunca colocar segredos no JavaScript.
5. Nunca depender do frontend para permissão.
6. Usar CSRF em ações sensíveis.
7. Usar prepared statements no PHP.
8. Usar `htmlspecialchars` ao renderizar dados em HTML.
9. Usar `textContent` para inserir dados externos no DOM.
10. Evitar `innerHTML` com dados vindos da API ou do usuário.
11. Não mostrar erro técnico para o usuário final.
12. Registrar erro técnico em log no backend.
13. Validar método HTTP.
14. Validar login.
15. Validar permissão.

---

## Renderização segura no JavaScript

Prefira:

```js
nameCell.textContent = usuario.nome;
emailCell.textContent = usuario.email;
```

Evite:

```js
row.innerHTML = `
  <td>${usuario.nome}</td>
  <td>${usuario.email}</td>
`;
```

Use `innerHTML` apenas quando:

- o HTML é fixo;
- o conteúdo é controlado pela própria aplicação;
- não existe dado de usuário ou API sem sanitização.

---

## Tratamento de erro no frontend

A IA deve diferenciar:

```txt
Erro de validação:
O usuário informou dados inválidos.

Erro de autenticação:
O usuário não está logado.

Erro de permissão:
O usuário não pode executar a ação.

Erro de rede:
O navegador não conseguiu se comunicar com o servidor.

Erro interno:
O backend falhou.
```

O usuário deve receber mensagem amigável.

Exemplo:

```js
try {
  await criarUsuario(payload);
  showMessage(feedback, 'Usuário criado com sucesso.', 'success');
} catch (error) {
  showMessage(feedback, error.message || 'Não foi possível concluir a operação.', 'error');
}
```

---

## Loading e prevenção de clique duplicado

Sempre desabilitar botão durante envio.

```js
async function handleSubmit(event) {
  event.preventDefault();

  setButtonLoading(submitButton, true);

  try {
    await salvarDados();
  } catch (error) {
    showMessage(feedback, error.message, 'error');
  } finally {
    setButtonLoading(submitButton, false);
  }
}
```

---

## Paginação com Fetch

Listagens grandes devem usar paginação.

```js
export function listarUsuarios({ page = 1, limit = 50, search = '' } = {}) {
  const params = new URLSearchParams({
    page,
    limit,
    search,
  });

  return httpRequest(`/usuarios/listar.php?${params.toString()}`);
}
```

Regra:

```txt
Nunca carregar milhares de registros no navegador sem necessidade.
```

---

## Busca com debounce

Campos de busca devem usar debounce para evitar muitas chamadas.

```js
export function debounce(callback, delay = 300) {
  let timeoutId;

  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => callback(...args), delay);
  };
}
```

Uso:

```js
searchInput.addEventListener('input', debounce(event => {
  carregarUsuarios({
    search: event.target.value,
  });
}, 300));
```

---

## Upload com Fetch

Para upload, use `FormData` e não defina manualmente `Content-Type`.

```js
async function enviarArquivo(file) {
  const formData = new FormData();
  formData.append('arquivo', file);

  const response = await fetch('/api/arquivos/enviar.php', {
    method: 'POST',
    body: formData,
  });

  const data = await response.json();

  if (!response.ok) {
    throw new Error(data.message || 'Erro ao enviar arquivo.');
  }

  return data;
}
```

Regra:

```txt
Com FormData, o navegador define o Content-Type correto automaticamente.
```

No backend, validar:

- Tamanho.
- Extensão.
- MIME type.
- Nome do arquivo.
- Permissão.
- Local seguro de armazenamento.

---

## JSON ou HTML parcial

O padrão principal desta skill é JSON.

Use JSON quando:

- O JavaScript vai renderizar a interface.
- Os dados serão reutilizados.
- A resposta precisa ser previsível.
- O endpoint pode ser usado por outras telas.

HTML parcial só deve ser usado quando:

- O projeto exigir simplicidade extrema.
- O PHP já tiver uma view parcial pronta.
- A equipe decidir padronizar esse fluxo.

Para esta skill, a prioridade é:

```txt
1. JSON padronizado
2. Renderização segura no JavaScript
3. HTML parcial apenas quando houver motivo claro
```

---

## Métodos HTTP

Para simplicidade e compatibilidade com PHP procedural:

```txt
GET  = listar e consultar
POST = criar, atualizar e excluir
```

Quando o projeto permitir REST mais rigoroso:

```txt
GET    = listar e consultar
POST   = criar
PUT    = atualizar
DELETE = excluir
```

A IA deve priorizar o padrão mais simples quando o projeto for pequeno ou médio.

---

## Checklist antes de entregar

Antes de finalizar uma funcionalidade com Fetch API, a IA deve revisar:

```txt
[ ] O JavaScript está sem framework?
[ ] As chamadas passam por http.js?
[ ] Os endpoints estão organizados em /public/api?
[ ] O PHP responde JSON padronizado?
[ ] O método HTTP é validado?
[ ] Existe validação no frontend?
[ ] Existe validação no backend?
[ ] Existe tratamento de erro no frontend?
[ ] Existe log de erro no backend?
[ ] O botão é desabilitado durante envio?
[ ] Há feedback de loading, sucesso e erro?
[ ] Dados externos usam textContent?
[ ] Não há innerHTML com dados externos?
[ ] Não há token secreto no frontend?
[ ] A permissão é validada no backend?
[ ] Ações sensíveis usam CSRF?
[ ] Listagens grandes usam paginação?
[ ] Busca usa debounce quando necessário?
[ ] Estados vazios foram tratados?
[ ] Erros de rede foram tratados?
```

---

## Modelo de resposta da IA ao criar funcionalidade com Fetch

Quando o usuário pedir uma funcionalidade dinâmica, responda nesta ordem:

1. Estrutura de arquivos.
2. Endpoint PHP.
3. Função no `api.js`.
4. Código da página em JavaScript puro.
5. Exemplo de HTML necessário.
6. Pontos de segurança aplicados.
7. Checklist de teste.

---

## Modelo de resposta da IA ao revisar código com Fetch

Ao revisar código, responda nesta ordem:

1. O que o código faz.
2. Problemas encontrados.
3. Riscos de segurança.
4. Melhorias de organização.
5. Código corrigido.
6. Checklist de teste.

---

## Erros comuns que a IA deve evitar

- Usar jQuery para AJAX.
- Usar Axios sem necessidade.
- Usar React ou Vue sem pedido explícito.
- Espalhar `fetch()` em vários arquivos.
- Não tratar `response.ok`.
- Não tratar erro de rede.
- Não tratar timeout quando necessário.
- Não validar dados no backend.
- Achar que validação no frontend é segurança.
- Usar `innerHTML` com dados vindos da API.
- Não mostrar feedback ao usuário.
- Permitir clique duplicado.
- Retornar texto solto no PHP em vez de JSON padronizado.
- Mostrar erro técnico do PHP para o usuário.
- Colocar token secreto no JavaScript.
- Não usar CSRF em ações sensíveis.
- Carregar milhares de registros sem paginação.

---

## Padrão mínimo de qualidade

Uma integração com Fetch API só deve ser considerada boa quando:

- Usa JavaScript puro.
- Não depende de framework.
- Tem chamadas centralizadas.
- Tem resposta JSON padronizada.
- Trata sucesso, erro e loading.
- Valida dados no backend.
- Protege ações sensíveis.
- Não expõe segredos.
- Não injeta HTML inseguro.
- Funciona com dados vazios.
- Funciona com API lenta.
- Funciona com erro de rede.
- É fácil de manter.

---

## Frase guia da skill

> Fetch API profissional não é apenas buscar dados; é criar uma comunicação simples, segura e padronizada entre JavaScript puro e PHP procedural.
