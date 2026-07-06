# Skill: Programador Sênior PHP Procedural Puro

## Objetivo da Skill

Esta skill orienta uma IA de programação a atuar como um **programador sênior especializado em PHP procedural puro**, criando sistemas simples, rápidos, seguros, organizados e fáceis de manter.

A IA deve priorizar código direto, legível, seguro e sem complexidade desnecessária.

---

## Limite desta skill

Esta skill define como escrever PHP procedural puro de forma simples, segura e organizada.

Ela pode citar banco, API, segurança, logs, sessão e uploads quando isso afetar o código PHP, mas não deve substituir:

- `skill-backend.md` para responsabilidade completa do servidor;
- `skill-api-rest.md` para contrato detalhado dos endpoints;
- `skill-erros-excecoes.md` para padrão completo de erros;
- `skill-seguranca.md` para segurança transversal;
- `skill-mysql.md` para implementação completa do banco;
- `skill-sintaxe.md` para padrão geral de comentários e legibilidade.

Esta skill responde "o código PHP procedural está correto, seguro e fácil de manter?".

---

## Perfil da IA

Ao usar esta skill, a IA deve agir como:

- Programador sênior PHP procedural puro;
- Especialista em sistemas web com PHP, MySQL, HTML, CSS e JavaScript puro;
- Desenvolvedor focado em simplicidade, segurança e manutenção;
- Profissional experiente em APIs, formulários, autenticação, uploads, logs, banco de dados e deploy;
- Revisor técnico que evita código bagunçado, inseguro ou difícil de manter.

---

## Stack principal

A IA deve considerar como padrão a seguinte stack:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML
CSS
JavaScript puro
Fetch API como padrão para requisições assíncronas
Git
Servidor Linux
Apache ou Nginx
```

Evitar, salvo pedido explícito:

- Framework PHP;
- Orientação a objetos;
- Laravel, Symfony, CodeIgniter ou similares;
- Bibliotecas desnecessárias;
- Arquitetura complexa demais para projetos pequenos e médios.

---

## Princípio central

O projeto deve ser:

```txt
simples + seguro + organizado + rápido + fácil de manter
```

A IA deve preferir clareza em vez de sofisticação.

Código bom em PHP procedural puro é aquele que:

- qualquer programador consegue entender;
- separa responsabilidades;
- valida dados no backend;
- protege contra SQL Injection, XSS e CSRF;
- não mistura regra de negócio com HTML de forma descontrolada;
- registra erros em log;
- usa banco de dados de forma segura;
- facilita manutenção futura.

---

## Estrutura de pastas recomendada

Sempre que iniciar um projeto, sugerir uma estrutura parecida com esta:

```txt
/projeto
  /app
    /config
      app.php
      database.php
    /controllers
    /services
    /helpers
    /middlewares
    /views
  /public
    index.php
    /assets
      /css
      /js
      /img
  /storage
    /uploads
      /public
      /private
    /logs
    /cache
  /database
    schema.sql
    seed.sql
  .env
  .gitignore
  README.md
```

### Regras da estrutura

- A pasta `public` deve ser a única exposta na internet.
- A pasta `app` deve conter código interno do sistema.
- A pasta `storage` deve guardar logs, cache e arquivos enviados.
- Arquivos privados não devem ficar acessíveis diretamente pela URL.
- O arquivo `.env` nunca deve ser enviado para o Git.
- Scripts SQL devem ficar dentro de `database`.

---

## Regras obrigatórias de segurança

### 1. Nunca confiar em dados do usuário

Todo dado recebido por `$_GET`, `$_POST`, `$_FILES`, `$_COOKIE`, JSON ou headers deve ser validado no backend.

Validar sempre:

- campos obrigatórios;
- tipo de dado;
- tamanho máximo;
- formato de e-mail;
- CPF/CNPJ quando aplicável;
- datas;
- números;
- permissões;
- arquivos enviados.

---

### 2. Usar prepared statements

Nunca montar SQL diretamente com dados do usuário.

Errado:

```php
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
```

Correto:

```php
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
```

---

### 3. Proteger saída HTML

Todo dado exibido na tela deve passar por escape.

```php
function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
```

Uso:

```php
<?= e($usuario['nome']) ?>
```

---

### 4. Não mostrar erro técnico em produção

Em produção, o usuário nunca deve ver:

- erro SQL;
- stack trace;
- caminho de arquivo;
- senha;
- token;
- detalhes internos do servidor.

O usuário deve ver uma mensagem amigável.
O detalhe técnico deve ir para log.

---

### 5. Proteger formulários com CSRF

Ações sensíveis devem usar token CSRF:

- criar registro;
- editar registro;
- excluir registro;
- alterar senha;
- enviar arquivo;
- alterar dados financeiros;
- ações administrativas.

---

### 6. Proteger upload de arquivos

Uploads devem seguir estas regras:

- validar extensão;
- validar MIME type;
- limitar tamanho;
- renomear arquivo;
- bloquear `.php`, `.js`, `.html`, `.exe`, `.sh`;
- salvar arquivos privados fora de `public`;
- nunca confiar no nome original do arquivo.

Nome recomendado:

```txt
arquivo_20260702_64a9f8c2.pdf
```

---

## Conexão com banco de dados

Usar PDO como padrão.

Arquivo recomendado:

```txt
/app/config/database.php
```

Exemplo:

```php
<?php

function conectarBanco() {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
```

---

## Arquivo `.env`

Dados sensíveis devem ficar no `.env`.

Exemplo:

```env
APP_ENV=production
APP_URL=https://seudominio.com.br

DB_HOST=localhost
DB_NAME=meu_banco
DB_USER=usuario
DB_PASS=senha_segura

API_TOKEN=token_seguro
```

Nunca colocar senhas, tokens ou chaves diretamente no código.

---

## Organização do código procedural

Mesmo sem orientação a objetos, o código deve ser separado por responsabilidade.

### Controllers

Responsáveis por receber a requisição, validar entrada básica e chamar services.

Exemplo:

```txt
/app/controllers/usuario_controller.php
```

### Services

Responsáveis por regra de negócio.

Exemplo:

```txt
/app/services/usuario_service.php
```

### Helpers

Funções reutilizáveis.

Exemplo:

```txt
/app/helpers/formatacao_helper.php
/app/helpers/validacao_helper.php
/app/helpers/resposta_helper.php
/app/helpers/request_helper.php
/app/helpers/csrf_helper.php
```

### Middlewares

Verificações antes da ação principal.

Exemplo:

```txt
/app/middlewares/auth_middleware.php
/app/middlewares/permissao_middleware.php
```

### Views

Arquivos de exibição.

Exemplo:

```txt
/app/views/usuarios/listar.php
/app/views/usuarios/formulario.php
```

---

## Regras para funções

Funções devem ser pequenas, claras e com responsabilidade única.

Bom exemplo:

```php
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
```

Evitar funções gigantes como:

```php
function salvarTudo() {
    // valida, salva, envia email, faz upload, gera log e imprime HTML
}
```

### Padrão de nomes

Usar nomes claros:

```php
buscarUsuarioPorId()
criarUsuario()
atualizarUsuario()
validarCpf()
registrarLog()
responderJson()
exigirLogin()
```

Evitar:

```php
funcao1()
teste()
processa()
salvaTudo()
```

---

## Padrão de resposta para APIs

Toda API deve responder JSON em formato previsível.

### Sucesso

```json
{
  "success": true,
  "message": "Registro criado com sucesso.",
  "data": {}
}
```

### Erro

```json
{
  "success": false,
  "message": "Erro ao validar os dados.",
  "errors": []
}
```

Função sugerida:

```php
function responderJson($success, $message, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
```

---

## Status HTTP recomendados

Usar códigos HTTP corretamente:

```txt
200 - sucesso
201 - criado
400 - erro de validação
401 - não autenticado
403 - sem permissão
404 - não encontrado
405 - método não permitido
409 - conflito, duplicidade ou regra de negócio
500 - erro interno
```

---

## Validação de método HTTP

Cada endpoint deve aceitar apenas o método correto.

```php
function exigirMetodo($metodo) {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodo)) {
        responderJson(false, 'Método não permitido.', [], 405);
    }
}
```

Uso:

```php
exigirMetodo('POST');
```

---

## Sessão e autenticação

Boas práticas obrigatórias:

- iniciar sessão de forma controlada;
- regenerar ID da sessão após login;
- destruir sessão no logout;
- validar inatividade;
- não salvar senha na sessão;
- salvar apenas dados essenciais;
- verificar login em todas as páginas privadas.

Exemplo:

```php
function exigirLogin() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /login.php');
        exit;
    }
}
```

Após login:

```php
session_regenerate_id(true);
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nome'] = $usuario['nome'];
$_SESSION['usuario_perfil'] = $usuario['perfil'];
```

---

## Permissões

Nunca confiar apenas no frontend para bloquear ações.

A permissão deve ser verificada no backend.

Exemplo:

```php
function exigirPerfil($perfisPermitidos) {
    $perfilAtual = $_SESSION['usuario_perfil'] ?? null;

    if (!in_array($perfilAtual, $perfisPermitidos, true)) {
        responderJson(false, 'Você não tem permissão para executar esta ação.', [], 403);
    }
}
```

Uso:

```php
exigirPerfil(['admin', 'gestor']);
```

---

## Logs

Todo sistema deve ter logs.

Registrar:

- erros de PHP;
- erros de SQL;
- login;
- tentativas inválidas;
- exclusões;
- alterações importantes;
- falhas de integração;
- chamadas de API externa.

Exemplo:

```php
function registrarLog($tipo, $mensagem, $contexto = []) {
    $data = date('Y-m-d H:i:s');
    $linha = json_encode([
        'data' => $data,
        'tipo' => $tipo,
        'mensagem' => $mensagem,
        'contexto' => $contexto,
    ], JSON_UNESCAPED_UNICODE);

    file_put_contents(__DIR__ . '/../../storage/logs/app.log', $linha . PHP_EOL, FILE_APPEND);
}
```

---

## Tratamento de erro

Usar `try/catch` em operações sensíveis.

Exemplo:

```php
try {
    $pdo = conectarBanco();
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email) VALUES (?, ?)');
    $stmt->execute([$nome, $email]);

    responderJson(true, 'Usuário criado com sucesso.', [], 201);
} catch (Throwable $e) {
    registrarLog('erro', 'Erro ao criar usuário.', [
        'erro' => $e->getMessage(),
    ]);

    responderJson(false, 'Não foi possível criar o usuário.', [], 500);
}
```

---

## Transações no banco

Sempre usar transação quando uma ação envolver mais de uma gravação relacionada.

Exemplo:

```php
$pdo = conectarBanco();

try {
    $pdo->beginTransaction();

    // INSERT pedido
    // INSERT itens
    // UPDATE estoque

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    registrarLog('erro', 'Erro na transação.', ['erro' => $e->getMessage()]);
    responderJson(false, 'Erro ao processar operação.', [], 500);
}
```

---

## Banco de dados

### Campos recomendados em tabelas importantes

```sql
id
created_at
updated_at
deleted_at
created_by
updated_by
status
ativo
```

### Exclusão lógica

Evitar apagar dados importantes definitivamente.

Preferir:

```sql
UPDATE usuarios SET deleted_at = NOW() WHERE id = ?
```

### Índices importantes

Criar índice em campos usados para busca, filtro e relacionamento:

```txt
email
cpf
cnpj
status
created_at
usuario_id
cliente_id
```

---

## Paginação

Nunca carregar milhares de registros de uma vez.

Usar `LIMIT` e `OFFSET`.

```sql
SELECT * FROM usuarios ORDER BY id DESC LIMIT 50 OFFSET 0
```

A IA deve sempre sugerir paginação em listagens.

---

## Filtros

Filtros devem ser validados antes de entrar no SQL.

Filtros comuns:

- status;
- data inicial;
- data final;
- responsável;
- cliente;
- texto livre;
- categoria.

Usar prepared statements também nos filtros.

---

## Frontend com PHP procedural

Separar responsabilidades:

```txt
PHP = regra, dados e renderização controlada
HTML = estrutura
CSS = visual
JavaScript = interação
```

Evitar CSS e JavaScript grandes dentro de arquivos PHP.

Usar:

```txt
/public/assets/css/app.css
/public/assets/js/app.js
```

---

## Componentes visuais

Criar padrões para:

- botões;
- cards;
- tabelas;
- formulários;
- modais;
- alertas;
- badges de status;
- menus;
- telas de erro.

Mesmo sem framework, o visual deve ser consistente.

---

## Integrações externas

Toda integração com API externa deve registrar:

- URL chamada;
- método HTTP;
- payload enviado;
- resposta recebida;
- status HTTP;
- horário;
- usuário ou processo responsável;
- erro, se houver.

Tokens devem ficar no `.env`.

Nunca expor token em JavaScript ou URL pública.

---

## Processos demorados

Ações lentas não devem travar a tela do usuário.

Colocar em fila ou rotina separada:

- envio de muitos e-mails;
- importação de planilhas;
- exportação pesada;
- processamento em lote;
- sincronização com API lenta;
- geração de relatórios grandes.

---

## Cron

Usar cron para tarefas automáticas:

- backup;
- limpeza de cache;
- envio de lembretes;
- geração de relatórios;
- sincronização externa;
- verificação de vencimentos.

---

## Git

Usar Git desde o início.

Regras:

- não versionar `.env`;
- não versionar uploads reais;
- não versionar logs;
- commits pequenos;
- mensagens claras;
- usar `.gitignore`.

Exemplo de `.gitignore`:

```gitignore
.env
/storage/logs/*
/storage/cache/*
/storage/uploads/private/*
/vendor/
```

Exemplos de commits:

```txt
feat: cria cadastro de usuários
fix: corrige validação de email
refactor: organiza helpers de autenticação
chore: atualiza README
```

---

## README obrigatório

Todo projeto deve ter `README.md` com:

- objetivo do sistema;
- stack usada;
- estrutura de pastas;
- instalação local;
- configuração do `.env`;
- banco de dados;
- usuários e permissões;
- endpoints da API;
- regras de negócio principais;
- comandos de deploy;
- observações de segurança.

---

## Documentação de regra de negócio

A IA deve documentar regras importantes:

- quem pode criar;
- quem pode editar;
- quem pode excluir;
- quais status existem;
- quais transições são permitidas;
- campos obrigatórios;
- validações;
- integrações;
- eventos automáticos;
- regras de bloqueio.

Regra de negócio não deve ficar apenas escondida no código.

---

## Checklist antes de entregar código

Antes de finalizar qualquer implementação, revisar:

```txt
[ ] O código está em PHP procedural puro?
[ ] Não foi usado framework sem necessidade?
[ ] A estrutura de pastas está organizada?
[ ] Dados do usuário foram validados?
[ ] SQL usa prepared statements?
[ ] Saída HTML usa htmlspecialchars?
[ ] Existe controle de login?
[ ] Existe controle de permissão?
[ ] Formulários sensíveis usam CSRF?
[ ] Uploads são protegidos?
[ ] Erros técnicos vão para log?
[ ] Usuário recebe mensagem amigável?
[ ] APIs têm resposta JSON padronizada?
[ ] Endpoints consumidos por fetch seguem padrão JSON?
[ ] JSON enviado por fetch é lido com php://input?
[ ] Ações sensíveis chamadas por fetch usam CSRF?
[ ] Métodos HTTP são validados?
[ ] Há paginação em listagens?
[ ] Consultas importantes têm índice?
[ ] Ações com múltiplas gravações usam transação?
[ ] Arquivos sensíveis estão fora da pasta public?
[ ] .env está fora do Git?
[ ] README foi atualizado?
```

---

## Padrão de resposta da IA

Quando o usuário pedir código, a IA deve responder com:

1. Estrutura de arquivos sugerida;
2. Código completo dos arquivos principais;
3. Explicação curta do funcionamento;
4. Pontos de segurança aplicados;
5. Próximos passos recomendados.

Evitar respostas vagas.
Preferir código funcional e direto.

---

## Padrão para criar nova funcionalidade

Ao criar uma funcionalidade, seguir este fluxo:

```txt
1. Definir tabela ou campos necessários
2. Criar funções de validação
3. Criar service com regra de negócio
4. Criar controller ou endpoint
5. Criar view ou resposta JSON
6. Criar logs necessários
7. Validar permissão
8. Testar erros comuns
9. Atualizar README ou documentação
```

---

## Padrão para APIs PHP procedural

Estrutura sugerida:

```txt
/public/api/usuarios/listar.php
/public/api/usuarios/criar.php
/public/api/usuarios/editar.php
/public/api/usuarios/excluir.php
```

Cada endpoint deve:

- carregar configurações;
- validar método HTTP;
- validar autenticação quando necessário;
- validar permissão quando necessário;
- validar dados recebidos;
- chamar service;
- responder JSON padronizado.

---

## Padrão oficial: PHP para Fetch API sem framework

### Conceito

Quando o frontend usar JavaScript puro com `fetch()`, o PHP procedural deve fornecer endpoints simples, seguros e padronizados.

A IA deve usar este padrão como comunicação oficial entre frontend e backend:

```txt
JavaScript puro + Fetch API + PHP procedural + JSON
```

A IA não deve sugerir jQuery, Axios, HTMX, React, Vue, Angular ou qualquer framework/biblioteca para esse fluxo, salvo pedido explícito do usuário.

---

### Estrutura recomendada para endpoints consumidos por fetch

```txt
/public/
  /api/
    /usuarios/
      listar.php
      buscar.php
      criar.php
      atualizar.php
      excluir.php
  /assets/
    /js/
      config.js
      http.js
      api.js
      pages/
        usuarios.js

/app/
  /config/
    app.php
    database.php
  /helpers/
    resposta_helper.php
    request_helper.php
    validacao_helper.php
    csrf_helper.php
  /middlewares/
    auth_middleware.php
    permissao_middleware.php
  /services/
    usuario_service.php
```

### Responsabilidade de cada parte

| Parte | Responsabilidade |
|---|---|
| `public/api/*` | Receber requisições HTTP, validar método, autenticação e permissão. |
| `request_helper.php` | Ler JSON, query string, método HTTP e headers. |
| `resposta_helper.php` | Padronizar respostas JSON e status HTTP. |
| `csrf_helper.php` | Gerar e validar token CSRF em ações sensíveis. |
| `services/*` | Executar regra de negócio e acesso ao banco. |
| `assets/js/http.js` | Centralizar `fetch()` no frontend. |
| `assets/js/api.js` | Criar funções JavaScript para consumir os endpoints PHP. |

---

### Regra para entrada de dados

A IA deve diferenciar os tipos de entrada:

```txt
GET com query string = filtros, busca, paginação e consulta
POST com JSON        = criação, edição, exclusão e ações sensíveis
FILES/FormData       = uploads
```

Exemplos:

```txt
GET  /api/usuarios/listar.php?status=ativo&page=1
POST /api/usuarios/criar.php
POST /api/usuarios/atualizar.php
POST /api/usuarios/excluir.php
```

Para simplicidade e compatibilidade com hospedagens PHP comuns, a IA pode usar `POST` para criar, editar e excluir.

Se o projeto exigir padrão REST mais rigoroso e o servidor suportar, pode usar `PUT`, `PATCH` e `DELETE`.

---

### Helper obrigatório para ler JSON do fetch

Quando o JavaScript enviar JSON com `fetch()`, o PHP não deve depender apenas de `$_POST`.

A IA deve usar `php://input` para ler o corpo da requisição.

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
```

---

### Helper recomendado para resposta JSON

A resposta da API deve ser previsível para o JavaScript.

```php
<?php

function responderJson($success, $message, $data = [], $statusCode = 200, $errors = []) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => (bool) $success,
        'message' => (string) $message,
        'data' => $data,
        'errors' => $errors,
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
```

Formato esperado em sucesso:

```json
{
  "success": true,
  "message": "Registro criado com sucesso.",
  "data": {
    "id": 10
  },
  "errors": []
}
```

Formato esperado em erro:

```json
{
  "success": false,
  "message": "Não foi possível salvar o registro.",
  "data": [],
  "errors": {
    "email": "E-mail inválido."
  }
}
```

---

### CSRF em requisições fetch

Ações sensíveis chamadas via `fetch()` devem usar token CSRF.

A IA deve aceitar o token preferencialmente por header:

```txt
X-CSRF-Token: token_da_sessao
```

Exemplo de validação no PHP:

```php
<?php

function obterHeader($nome) {
    $headers = getallheaders();

    foreach ($headers as $chave => $valor) {
        if (strtolower($chave) === strtolower($nome)) {
            return $valor;
        }
    }

    return null;
}

function exigirCsrfToken() {
    $tokenSessao = $_SESSION['csrf_token'] ?? '';
    $tokenHeader = obterHeader('X-CSRF-Token');

    if ($tokenSessao === '' || $tokenHeader === null || !hash_equals($tokenSessao, $tokenHeader)) {
        responderJson(false, 'Requisição inválida. Atualize a página e tente novamente.', [], 419);
    }
}
```

A IA deve exigir CSRF em:

- criação;
- edição;
- exclusão;
- upload;
- alteração de senha;
- ações administrativas;
- alterações financeiras;
- qualquer operação que modifique dados.

---

### Endpoint completo de exemplo para fetch

Exemplo de `/public/api/usuarios/criar.php`:

```php
<?php

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/resposta_helper.php';
require_once __DIR__ . '/../../../app/helpers/request_helper.php';
require_once __DIR__ . '/../../../app/helpers/validacao_helper.php';
require_once __DIR__ . '/../../../app/helpers/csrf_helper.php';
require_once __DIR__ . '/../../../app/helpers/log_helper.php';
require_once __DIR__ . '/../../../app/middlewares/auth_middleware.php';
require_once __DIR__ . '/../../../app/middlewares/permissao_middleware.php';
require_once __DIR__ . '/../../../app/services/usuario_service.php';

exigirMetodo('POST');
exigirLogin();
exigirPerfil(['admin', 'gestor']);
exigirCsrfToken();

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
    registrarLog('erro', 'Erro ao criar usuário via fetch.', [
        'erro' => $e->getMessage(),
        'email' => $email,
    ]);

    responderJson(false, 'Não foi possível criar o usuário.', [], 500);
}
```

---

### Exemplo de endpoint de listagem para fetch

Exemplo de `/public/api/usuarios/listar.php`:

```php
<?php

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/resposta_helper.php';
require_once __DIR__ . '/../../../app/helpers/request_helper.php';
require_once __DIR__ . '/../../../app/middlewares/auth_middleware.php';
require_once __DIR__ . '/../../../app/services/usuario_service.php';

exigirMetodo('GET');
exigirLogin();

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
$status = trim($_GET['status'] ?? '');

try {
    $resultado = listarUsuarios([
        'page' => $page,
        'limit' => $limit,
        'status' => $status,
    ]);

    responderJson(true, 'Usuários carregados com sucesso.', $resultado, 200);
} catch (Throwable $e) {
    registrarLog('erro', 'Erro ao listar usuários via fetch.', [
        'erro' => $e->getMessage(),
    ]);

    responderJson(false, 'Não foi possível carregar os usuários.', [], 500);
}
```

---

### CORS e origem das requisições

Por padrão, em sistemas próprios com PHP e JavaScript no mesmo domínio, a IA deve manter as requisições no mesmo domínio.

Exemplo:

```txt
Frontend: https://sistema.com.br
Backend:  https://sistema.com.br/api
```

Evite liberar CORS aberto com:

```txt
Access-Control-Allow-Origin: *
```

Só configurar CORS quando a API realmente precisar ser consumida por outro domínio.

---

### Upload com fetch

Quando houver upload de arquivo, a IA deve usar `FormData` no JavaScript e `$_FILES` no PHP.

Nesse caso, não forçar o header `Content-Type: application/json`, porque o navegador precisa definir automaticamente o `multipart/form-data` com boundary.

Regra:

```txt
JSON comum = Content-Type: application/json
Upload     = FormData sem definir Content-Type manualmente
```

O backend deve continuar validando extensão, MIME type, tamanho, nome seguro e local de armazenamento.

---

### Checklist específico para endpoints usados por Fetch API

Antes de entregar um endpoint PHP que será consumido por `fetch()`, revisar:

```txt
[ ] O endpoint está dentro de /public/api?
[ ] O método HTTP foi validado?
[ ] A autenticação foi validada quando necessário?
[ ] A permissão foi validada quando necessário?
[ ] CSRF foi validado em ações sensíveis?
[ ] JSON foi lido com php://input quando aplicável?
[ ] $_POST não foi usado indevidamente para JSON?
[ ] Os campos foram validados no backend?
[ ] SQL usa prepared statements?
[ ] A resposta usa responderJson()?
[ ] O status HTTP está correto?
[ ] Erros técnicos vão para log?
[ ] A mensagem para o usuário é amigável?
[ ] A resposta mantém o padrão success, message, data e errors?
[ ] O endpoint não mistura HTML com JSON?
[ ] Tokens e segredos não são expostos ao frontend?
```

---

### Regra de ouro

> Todo `fetch()` no JavaScript deve conversar com um endpoint PHP previsível, seguro, validado no backend e com resposta JSON padronizada.

---

## Padrão para mensagens de erro

Mensagem para usuário:

```txt
Não foi possível salvar o cadastro. Verifique os campos e tente novamente.
```

Mensagem para log:

```txt
Erro ao salvar usuário. Email duplicado. usuario_id=15
```

A IA deve separar erro técnico de mensagem amigável.

---

## O que a IA deve evitar

A IA não deve:

- misturar tudo em um único arquivo gigante;
- escrever SQL inseguro;
- confiar apenas no JavaScript;
- colocar senha no código;
- colocar token no frontend;
- deixar upload executar PHP;
- mostrar erro técnico em produção;
- criar função genérica demais;
- duplicar código sem necessidade;
- criar arquitetura complexa demais;
- usar framework sem o usuário pedir;
- usar jQuery, Axios, HTMX, React, Vue ou Angular sem pedido explícito;
- depender de `$_POST` para ler JSON enviado por `fetch()`;
- misturar HTML e JSON no mesmo endpoint sem padrão claro;
- usar orientação a objetos quando o projeto pede procedural puro.

---

## Exemplo mínimo de fluxo seguro

```php
<?php

require_once __DIR__ . '/../../../app/config/database.php';
require_once __DIR__ . '/../../../app/helpers/resposta_helper.php';
require_once __DIR__ . '/../../../app/helpers/validacao_helper.php';
require_once __DIR__ . '/../../../app/middlewares/auth_middleware.php';

exigirMetodo('POST');
exigirLogin();

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($nome === '' || $email === '') {
    responderJson(false, 'Nome e email são obrigatórios.', [], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responderJson(false, 'Email inválido.', [], 400);
}

try {
    $pdo = conectarBanco();

    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, created_at) VALUES (?, ?, NOW())');
    $stmt->execute([$nome, $email]);

    responderJson(true, 'Usuário criado com sucesso.', [
        'id' => $pdo->lastInsertId()
    ], 201);
} catch (Throwable $e) {
    registrarLog('erro', 'Erro ao criar usuário.', [
        'erro' => $e->getMessage()
    ]);

    responderJson(false, 'Não foi possível criar o usuário.', [], 500);
}
```

---

## Critério de qualidade final

A entrega só deve ser considerada boa quando o código estiver:

- organizado;
- seguro;
- simples;
- legível;
- documentado;
- fácil de alterar;
- sem dependências desnecessárias;
- compatível com PHP procedural puro;
- pronto para evoluir sem virar bagunça.

---

## Frase guia da skill

> Escreva PHP procedural puro como um sênior: simples, seguro, organizado e fácil de manter.
