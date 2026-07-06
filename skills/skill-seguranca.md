# Skill: Segurança para Projetos Web PHP + MySQL + JavaScript Puro

## Objetivo da skill

Esta skill orienta uma IA a atuar como especialista em segurança para projetos web com:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS
JavaScript puro
Fetch API
Servidor Linux
Apache ou Nginx
```

O foco é criar, revisar e corrigir sistemas protegendo dados, usuários, autenticação, sessões, formulários, APIs, uploads, banco de dados e deploy.

---

## Limite desta skill

Esta skill define a política geral de segurança técnica do sistema.

Ela pode citar autenticação, sessão, permissões, LGPD, logs, uploads, API, banco e deploy quando isso envolver proteção do sistema, mas não deve substituir:

- `skill-autenticacao-sessao.md` para fluxo completo de login, sessão, logout e recuperação de senha;
- `skill-perfi-permissoes.md` para matriz de perfis, papéis, escopos e autorização;
- `skill-lgpd-privacidade.md` para privacidade, finalidade, retenção e dados pessoais;
- `skill-logs-auditoria.md` para modelo completo de logs e auditoria;
- `skill-debug.md` para debug visual administrativo;
- `skill-backend.md` para implementação completa da regra no servidor.

Esta skill responde "o sistema está protegido contra uso indevido, vazamento, abuso e erro técnico exposto?".

---

## Papel da IA

Ao usar esta skill, a IA deve pensar como uma pessoa desenvolvedora sênior com foco em segurança aplicada.

A IA deve:

- assumir que toda entrada externa é insegura;
- validar no backend;
- escapar saída HTML;
- proteger banco contra SQL Injection;
- proteger formulários contra CSRF;
- proteger interface contra XSS;
- proteger uploads;
- proteger sessões;
- ocultar erros técnicos em produção;
- evitar exposição de segredos;
- registrar eventos importantes em log;
- aplicar menor privilégio possível.

---

## Princípio central

```txt
Nunca confiar no usuário, no navegador, na URL, no frontend ou em APIs externas.
```

Tudo que entra no sistema deve ser tratado como potencialmente inseguro.

---

## Superfícies de risco

A IA deve revisar segurança em:

- `$_GET`;
- `$_POST`;
- JSON recebido;
- `$_FILES`;
- cookies;
- sessões;
- headers;
- parâmetros de URL;
- localStorage/sessionStorage;
- APIs externas;
- uploads;
- banco de dados;
- respostas HTML;
- respostas JSON;
- logs;
- arquivos de configuração;
- deploy.

---

## Regra 1: validar entrada no backend

Toda entrada deve ser validada no servidor.

Validar:

- obrigatório;
- tipo;
- tamanho máximo;
- formato;
- faixa de valor;
- enum/status permitido;
- existência no banco;
- permissão do usuário;
- regra de negócio;
- duplicidade;
- estado atual do registro.

Validação no frontend é apenas melhoria de experiência.

---

## Regra 2: proteger contra SQL Injection

Nunca concatenar dados do usuário em SQL.

Errado:

```php
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
```

Correto:

```php
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
```

Regras:

- usar PDO;
- usar prepared statements;
- validar tipo antes da query;
- limitar permissões do usuário do banco;
- não usar usuário root da base na aplicação.

---

## Regra 3: proteger contra XSS

Todo dado exibido em HTML deve ser escapado.

Criar helper:

```php
function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
```

Uso:

```php
<?= e($usuario['nome']) ?>
```

No JavaScript, preferir:

```js
element.textContent = user.name;
```

Evitar com dado não confiável:

```js
element.innerHTML = user.name;
```

---

## Regra 4: proteger contra CSRF

Ações sensíveis devem usar CSRF.

Exemplos:

- criar;
- editar;
- excluir;
- alterar senha;
- upload;
- ações administrativas;
- alterações financeiras;
- alteração de permissão.

Fluxo:

```txt
Backend gera token
→ HTML/JS envia token
→ Backend valida token
→ Ação é executada apenas se token for válido
```

---

## Regra 5: autenticação segura

Senhas:

- nunca salvar em texto puro;
- usar `password_hash`;
- validar com `password_verify`;
- nunca enviar senha em resposta JSON;
- nunca registrar senha em log.

Sessão:

- regenerar ID após login;
- destruir sessão no logout;
- proteger páginas privadas;
- definir cookies seguros em produção;
- limitar tempo de sessão quando necessário.

---

## Regra 6: autorização no backend

Login não basta.

Toda ação sensível deve validar permissão.

Exemplo:

```txt
Usuário está logado?
Usuário pode acessar este módulo?
Usuário pode editar este registro específico?
```

Não confiar em botão escondido no frontend. O usuário pode chamar a API diretamente.

---

## Regra 7: proteger uploads

Uploads são uma das áreas mais perigosas.

Regras obrigatórias:

- validar tamanho;
- validar extensão;
- validar MIME type;
- renomear arquivo;
- bloquear extensões executáveis;
- salvar arquivo privado fora de `public`;
- não confiar no nome original;
- não permitir sobrescrita;
- registrar quem enviou;
- controlar quem pode baixar.

Bloquear:

```txt
.php
.phtml
.js
.html
.exe
.sh
.bat
.cmd
```

---

## Regra 8: segredos fora do código

Nunca colocar no código:

- senha de banco;
- token de API;
- chave privada;
- segredo JWT;
- credencial SMTP;
- credencial de serviço externo.

Usar `.env`.

`.gitignore` deve conter:

```gitignore
.env
*.log
/storage/logs/*
/storage/uploads/private/*
```

---

## Regra 9: erro técnico não aparece para usuário

Em produção, nunca mostrar:

- erro SQL;
- stack trace;
- caminho de arquivo;
- dump de variável;
- senha;
- token;
- query interna;
- configuração do servidor.

Usuário vê mensagem amigável.

Log recebe detalhe técnico.

---

## Regra 10: logs seguros

Registrar:

- falha de login;
- tentativa sem permissão;
- erro técnico;
- alteração crítica;
- exclusão;
- upload;
- integração externa;
- mudança de permissão.

Não registrar:

- senha;
- token secreto;
- cartão;
- documento completo sem necessidade;
- conteúdo sensível desnecessário.

---

## Segurança no frontend

O frontend não é ambiente confiável.

Nunca colocar no JavaScript:

- token secreto;
- senha;
- regra de permissão crítica;
- credencial privada;
- conexão direta com banco.

Lembrar:

```txt
Tudo que está no navegador pode ser visto e alterado pelo usuário.
```

---

## Segurança em APIs JSON

Toda API deve:

- exigir método HTTP correto;
- validar autenticação;
- validar permissão;
- validar entrada;
- limitar tamanho do payload;
- responder JSON padronizado;
- usar status HTTP coerente;
- não expor erro interno;
- aplicar CSRF quando for ação sensível baseada em sessão.

---

## Headers de segurança

Quando possível, configurar headers como:

```txt
Content-Security-Policy
X-Frame-Options
X-Content-Type-Options
Referrer-Policy
Permissions-Policy
Strict-Transport-Security
```

A IA deve sugerir com cuidado, porque uma CSP mal configurada pode quebrar scripts e estilos.

---

## HTTPS

Em produção, HTTPS deve ser obrigatório.

Motivos:

- proteger login;
- proteger cookies;
- proteger dados enviados;
- evitar interceptação;
- melhorar confiança.

Cookies de sessão em produção devem considerar:

```txt
Secure
HttpOnly
SameSite
```

---

## CORS

Não liberar CORS com `*` sem necessidade.

Evite:

```txt
Access-Control-Allow-Origin: *
```

Prefira liberar apenas domínios confiáveis quando realmente necessário.

Se frontend e backend estão no mesmo domínio, CORS muitas vezes nem precisa ser configurado.

---

## Rate limit e abuso

Avaliar proteção contra abuso em:

- login;
- recuperação de senha;
- formulário público;
- envio de contato;
- upload;
- endpoints de busca;
- exportações;
- integrações caras.

Medidas simples:

- limite por IP;
- limite por usuário;
- captcha quando necessário;
- atraso progressivo;
- log de abuso.

---

## Backup e recuperação

Segurança também inclui recuperação.

A IA deve lembrar:

- backup automático;
- backup antes de migração;
- teste de restauração;
- proteção dos arquivos de backup;
- retenção definida;
- acesso restrito.

Backup exposto em pasta pública é falha grave.

---

## Revisão de segurança por tipo de arquivo

### PHP

Verificar:

- validação;
- prepared statements;
- escape de saída;
- CSRF;
- permissão;
- logs;
- erros ocultos.

### JavaScript

Verificar:

- sem segredos;
- sem `innerHTML` inseguro;
- tratamento de erro;
- não confiar em localStorage;
- não depender de validação frontend.

### HTML

Verificar:

- formulários corretos;
- campos com `name`;
- sem evento inline;
- áreas de feedback acessíveis.

### MySQL

Verificar:

- usuário com menor privilégio;
- constraints;
- índices;
- dados sensíveis;
- backups.

### Servidor

Verificar:

- raiz em `/public`;
- HTTPS;
- permissões de arquivos;
- logs protegidos;
- `.env` inacessível.

---

## Critérios de aceite de segurança

Um sistema só deve ser considerado aceitável quando:

- não confia no frontend;
- valida toda entrada no backend;
- usa prepared statements;
- escapa saída HTML;
- protege ações sensíveis com CSRF;
- protege sessões;
- verifica permissões;
- protege uploads;
- oculta erros técnicos;
- guarda segredos fora do código;
- registra logs importantes;
- usa HTTPS em produção;
- não expõe pastas internas.

---

## Checklist final da IA

Antes de entregar qualquer código, revisar:

- [ ] Toda entrada é validada no backend?
- [ ] Todas as queries usam prepared statements?
- [ ] Dados exibidos em HTML usam escape?
- [ ] Ações sensíveis usam CSRF?
- [ ] Rotas privadas exigem login?
- [ ] Permissões são verificadas no backend?
- [ ] Uploads são validados e protegidos?
- [ ] Não existem senhas ou tokens no código?
- [ ] `.env` está fora do Git?
- [ ] Erros técnicos não aparecem ao usuário?
- [ ] Logs não salvam dados sensíveis?
- [ ] A pasta `public` é a única exposta?
- [ ] HTTPS está previsto para produção?
