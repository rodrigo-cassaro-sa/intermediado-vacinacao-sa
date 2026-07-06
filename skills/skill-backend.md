# Skill: Backend para Projetos PHP Procedural + MySQL

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa desenvolvedora backend sênior em projetos web com **PHP procedural puro** e **MySQL/MariaDB**, criando APIs, regras de negócio, validações, autenticação, permissões, logs, uploads e integrações de forma simples, segura e organizada.

Esta skill complementa a skill específica de PHP procedural. O foco aqui é a **responsabilidade do backend dentro do sistema**, não apenas a sintaxe PHP.

---

## Limite desta skill

Esta skill define a responsabilidade do backend: validar, decidir, processar, salvar, integrar e responder.

Ela pode citar PHP, API, autenticação, permissões, logs e erros quando fizerem parte do fluxo do servidor, mas não deve substituir:

- `skill-php.md` para sintaxe e boas práticas específicas de PHP procedural;
- `skill-api-rest.md` para contrato oficial dos endpoints;
- `skill-autenticacao-sessao.md` para login, sessão e recuperação de senha;
- `skill-permissoes.md` para matriz de autorização;
- `skill-erros-excecoes.md` para padrão completo de erros;
- `skill-logs-auditoria.md` para auditoria detalhada.

Esta skill responde "o servidor está processando a regra de negócio de forma segura e organizada?".

---

## Stack oficial

```txt
PHP procedural puro
MySQL ou MariaDB
PDO
HTML/CSS/JavaScript puro no frontend
Fetch API para chamadas assíncronas
JSON como padrão para APIs
Servidor Linux com Apache ou Nginx
Sem framework PHP por padrão
```

---

## O que é backend neste projeto

Backend é a camada responsável por processar tudo que não pode ficar somente no navegador.

Inclui:

- regra de negócio;
- validação real;
- autenticação;
- autorização/permissão;
- conexão com banco;
- leitura e gravação de dados;
- endpoints JSON;
- upload seguro;
- logs;
- auditoria;
- envio de e-mail ou notificações;
- integração com APIs externas;
- tratamento de erros;
- proteção contra abusos.

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora backend sênior, prática e objetiva.

A IA deve:

- priorizar segurança;
- validar tudo no servidor;
- separar responsabilidades;
- escrever código procedural claro;
- evitar framework salvo pedido explícito;
- evitar orientação a objetos obrigatória;
- padronizar respostas JSON;
- proteger dados sensíveis;
- registrar erros sem expor detalhes ao usuário;
- pensar em manutenção real.

---

## Relação com outras skills

```txt
Frontend = envia requisições e exibe respostas.
Backend = valida, decide, salva e responde.
Banco de dados = persiste e organiza dados.
Segurança = aplica proteção transversal.
Arquitetura = define organização do projeto.
```

Regra central:

```txt
Nunca confiar no frontend.
```

---

## Estrutura recomendada

```txt
/projeto
  /app
    /config
      app.php
      database.php
    /controllers
      usuario_controller.php
    /services
      usuario_service.php
    /helpers
      resposta_helper.php
      request_helper.php
      validacao_helper.php
      csrf_helper.php
      auth_helper.php
      log_helper.php
    /middlewares
      auth_middleware.php
      permissao_middleware.php
    /views
  /public
    index.php
    /api
      /usuarios
        listar.php
        criar.php
        atualizar.php
        excluir.php
  /storage
    /logs
    /uploads
      /public
      /private
  /database
    schema.sql
    seed.sql
  .env
  .gitignore
  README.md
```

Regras:

- `public` é a única pasta exposta na internet.
- `app` contém código interno.
- `storage/private` não pode ser acessado por URL direta.
- `.env` nunca vai para o Git.
- Endpoints ficam em `public/api` e chamam controllers/services.

---

## Fluxo padrão de uma requisição

```txt
Navegador
→ endpoint PHP em /public/api
→ middleware de autenticação/permissão
→ leitura segura da entrada
→ validação
→ service de regra de negócio
→ banco de dados via PDO
→ log/auditoria quando necessário
→ resposta JSON padronizada
```

---

## Separação de responsabilidades

### Endpoint

Responsável por ser a porta de entrada HTTP.

Deve fazer pouco:

- carregar arquivos necessários;
- exigir método HTTP;
- chamar middleware;
- chamar controller;
- retornar resposta.

### Controller

Responsável por coordenar a requisição.

Pode:

- ler entrada;
- chamar validações;
- chamar service;
- formatar resposta.

### Service

Responsável pela regra de negócio.

Pode:

- verificar duplicidade;
- calcular status;
- aplicar regras;
- decidir se pode criar/editar/excluir;
- chamar banco de dados.

### Helper

Funções reutilizáveis.

Exemplos:

- resposta JSON;
- validação de e-mail;
- escape HTML;
- leitura de JSON;
- log;
- CSRF.

### Middleware

Verificações antes da ação principal.

Exemplos:

- exigir login;
- exigir permissão;
- limitar método HTTP;
- validar CSRF.

---

## Padrão de endpoint

```php
<?php

require_once __DIR__ . '/../../../app/bootstrap.php';

exigirMetodo('POST');
exigirLogin();
validarCsrf();

criarUsuarioController();
```

O endpoint não deve conter regra de negócio grande.

---

## Padrão de resposta JSON

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
  "errors": {
    "email": "Informe um e-mail válido."
  }
}
```

Regras:

- sempre responder JSON em APIs;
- sempre usar `Content-Type: application/json; charset=utf-8`;
- não retornar HTML em endpoint JSON;
- não expor erro SQL para usuário;
- usar HTTP status coerente.

---

## Códigos HTTP recomendados

```txt
200 = sucesso
201 = criado
400 = erro de validação
401 = não autenticado
403 = sem permissão
404 = não encontrado
405 = método não permitido
409 = conflito ou duplicidade
422 = dados válidos no formato, mas inválidos na regra
429 = muitas requisições
500 = erro interno
```

---

## Validação no backend

Todo dado deve ser validado no backend, mesmo se já foi validado no frontend.

Validar:

- obrigatoriedade;
- tipo;
- tamanho;
- formato;
- faixa de valor;
- existência no banco;
- permissão do usuário;
- duplicidade;
- regra de negócio;
- estado atual do registro.

Exemplo:

```txt
Não basta validar que tarefa_id é número.
Também precisa validar se a tarefa existe e se o usuário pode acessá-la.
```

---

## Banco de dados

O backend deve acessar banco usando PDO e prepared statements.

Proibido:

```php
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
```

Padrão:

```php
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
```

---

## Transações

Use transações quando uma operação envolver múltiplas gravações dependentes.

Exemplos:

- criar pedido e itens;
- lançar pagamento e atualizar saldo;
- criar demanda e plano de ação;
- registrar usuário e permissões;
- mover dados entre tabelas.

Fluxo:

```txt
beginTransaction
→ executar operações
→ commit se tudo der certo
→ rollback se qualquer etapa falhar
```

---

## Autenticação

A IA deve implementar autenticação com cuidado.

Regras:

- senha sempre com `password_hash`;
- validar com `password_verify`;
- regenerar sessão após login;
- destruir sessão no logout;
- proteger rotas privadas;
- limitar tentativas quando necessário;
- nunca salvar senha em texto puro;
- nunca retornar senha em resposta JSON.

---

## Autorização e permissões

Autenticação responde:

```txt
Quem é o usuário?
```

Autorização responde:

```txt
O que esse usuário pode fazer?
```

Toda ação sensível deve verificar permissão no backend:

- visualizar;
- criar;
- editar;
- excluir;
- exportar;
- aprovar;
- cancelar;
- administrar.

---

## Uploads

Uploads devem ser tratados como risco.

Regras:

- validar extensão;
- validar MIME type;
- limitar tamanho;
- renomear arquivo;
- salvar fora de `public` quando privado;
- bloquear scripts executáveis;
- registrar quem enviou;
- não confiar no nome original;
- não permitir sobrescrever arquivo existente.

---

## Logs

O backend deve registrar eventos importantes.

Tipos de log:

- erro técnico;
- falha de login;
- ação administrativa;
- alteração crítica;
- integração externa;
- upload;
- exclusão;
- tentativa sem permissão.

Logs não devem conter:

- senha;
- token secreto;
- dados sensíveis desnecessários;
- conteúdo completo de documentos privados.

---

## Integrações externas

Ao integrar APIs externas:

- armazenar token no `.env`;
- definir timeout;
- tratar erro de rede;
- tratar resposta inesperada;
- registrar log técnico;
- não travar tela sem necessidade;
- validar dados recebidos;
- não confiar cegamente na API externa.

---

## Performance backend

A IA deve evitar:

- consultas dentro de loops sem necessidade;
- carregar todos os registros de uma tabela grande;
- endpoints sem paginação;
- `SELECT *` em listagens grandes;
- falta de índice em filtros comuns;
- processamento pesado em requisição síncrona quando pode ser assíncrono.

Boas práticas:

- paginação;
- filtros claros;
- índices adequados;
- cache simples quando fizer sentido;
- logs para gargalos;
- resposta enxuta.

---

## Erros e exceções

Em produção:

- usuário vê mensagem amigável;
- detalhe técnico vai para log;
- resposta JSON mantém padrão;
- status HTTP deve ser coerente.

Mensagem ruim:

```txt
SQLSTATE[23000]: Integrity constraint violation...
```

Mensagem melhor:

```txt
Não foi possível salvar porque já existe um registro com estes dados.
```

---

## Critérios de aceite backend

Um backend só deve ser considerado pronto quando:

- valida tudo no servidor;
- usa prepared statements;
- protege rotas privadas;
- verifica permissões;
- padroniza respostas JSON;
- registra erros importantes;
- não expõe dados sensíveis;
- usa status HTTP correto;
- trata uploads com segurança;
- separa endpoint, controller, service, helper e middleware;
- possui estrutura simples de manter.

---

## Checklist final da IA

Antes de entregar backend, verificar:

- [ ] O endpoint aceita apenas o método correto?
- [ ] A entrada é validada?
- [ ] A permissão é verificada?
- [ ] Existe proteção CSRF quando necessário?
- [ ] O banco usa prepared statements?
- [ ] A resposta JSON está padronizada?
- [ ] Os erros técnicos não aparecem para o usuário?
- [ ] Existe log para falhas importantes?
- [ ] Uploads estão protegidos?
- [ ] Dados sensíveis ficam fora do código?
- [ ] A regra de negócio está fora do endpoint?
