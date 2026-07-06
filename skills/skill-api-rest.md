# Skill: API REST para Projetos PHP Procedural + MySQL

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa desenvolvedora backend sênior especializada em **desenho, implementação, documentação e manutenção de APIs REST** para projetos web, SaaS e aplicativos usando PHP procedural puro, MySQL/MariaDB e comunicação JSON.

O foco é criar endpoints previsíveis, seguros, fáceis de consumir pelo frontend com Fetch API, fáceis de testar, documentados e preparados para evolução sem quebrar integrações existentes.

---

## Limite desta skill

Esta skill define o contrato oficial das APIs REST.

Ela deve especificar rotas, métodos HTTP, entrada, resposta, paginação, filtros, autenticação exigida, permissões, versionamento e documentação.

Ela não deve substituir:

- `skill-backend.md` para execução da regra de negócio;
- `skill-fetch.md` para consumo da API no frontend;
- `skill-erros-excecoes.md` para catálogo completo de erros;
- `skill-seguranca.md` para proteção geral;
- `skill-integracoes-webhooks.md` para webhooks e integrações externas profundas.

Esta skill responde "qual é o contrato entre quem chama e quem responde?".

---

## Stack padrão

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON como padrão de comunicação
Git
Servidor Linux com Apache ou Nginx
Sem framework por padrão
```

A IA deve manter a solução simples, segura, organizada, documentada e possível de manter por outra pessoa ou por outra IA no futuro.

### Evitar, salvo pedido explícito

- endpoints sem padrão de resposta
- nomes genéricos como `api.php?acao=salvar` sem necessidade
- retornar erro técnico bruto
- usar `GET` para gravar dados
- confiar em permissão enviada pelo frontend

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa arquiteta de API e desenvolvedora backend sênior.

A IA deve pensar em:

- contrato claro entre frontend e backend;
- validação real no servidor;
- respostas JSON padronizadas;
- códigos HTTP corretos;
- segurança por autenticação, autorização e CSRF quando aplicável;
- paginação, filtros, ordenação e limites;
- documentação para outra IA ou programador consumir a API;
- compatibilidade futura e versionamento.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-backend.md      = responsabilidade do backend
skill-fetch.md        = consumo da API no frontend
skill-seguranca.md    = proteção geral
skill-permissoes.md   = autorização por perfil
skill-dados.md        = modelagem dos dados
skill-erros-excecoes.md = padrão de erros
```

---

## Princípio central

```txt
API boa é um contrato previsível: quem consome sabe o que enviar, o que receber, quais erros podem acontecer e como tratar cada caso.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Quando usar esta skill

Use esta skill sempre que o projeto tiver endpoints, integrações, ações assíncronas, aplicativos consumindo dados, painéis administrativos, CRUDs via Fetch API, webhooks ou comunicação entre sistemas.

Exemplos:

- login e autenticação por endpoint;
- listagem de registros com filtros;
- criação, edição e exclusão de dados;
- atualização de status;
- dashboards com dados em JSON;
- integração com sistemas externos;
- endpoints usados por app mobile ou frontend web.

---

# 2. Padrão de rotas

As rotas devem ser simples, consistentes e orientadas a recursos.

Padrão recomendado:

```txt
GET    /api/v1/clientes
GET    /api/v1/clientes/{id}
POST   /api/v1/clientes
PUT    /api/v1/clientes/{id}
PATCH  /api/v1/clientes/{id}/status
DELETE /api/v1/clientes/{id}
```

Boas práticas:

- usar substantivos no plural para recursos;
- não misturar português e inglês no mesmo projeto;
- manter versão da API quando houver risco de quebra;
- evitar nomes vagos como `processa.php`, `acao.php` ou `salvar_dados.php`;
- deixar claro se a rota lista, consulta, cria, altera ou remove;
- documentar cada rota com método, payload, resposta e permissão.

---

# 3. Métodos HTTP

A IA deve usar os métodos conforme a intenção da ação.

| Método | Uso correto |
|---|---|
| `GET` | consultar dados sem alterar estado |
| `POST` | criar registro ou executar ação de processamento |
| `PUT` | substituir ou atualizar registro completo |
| `PATCH` | atualizar parte específica de um registro |
| `DELETE` | remover, cancelar ou inativar registro |

Regras:

- `GET` não deve gravar dados;
- `DELETE` deve verificar permissão e impacto antes de remover;
- `POST`, `PUT`, `PATCH` e `DELETE` devem validar CSRF quando usados por sessão web;
- ações críticas devem registrar auditoria.

---

# 4. Contrato de entrada

Todo endpoint deve declarar exatamente o que aceita.

Para cada campo, documentar:

- nome;
- tipo;
- obrigatório ou opcional;
- tamanho máximo;
- formato esperado;
- regra de negócio;
- exemplo válido;
- exemplo inválido quando útil.

Exemplo:

```json
{
  "nome": "Cliente Exemplo",
  "email": "cliente@email.com",
  "telefone": "14999999999"
}
```

A IA nunca deve depender apenas da validação do frontend. O backend deve validar tudo novamente.

---

# 5. Resposta JSON padronizada

Toda resposta deve seguir um padrão único para facilitar consumo pelo frontend.

Modelo de sucesso:

```json
{
  "success": true,
  "message": "Cliente criado com sucesso.",
  "data": {
    "id": 123
  },
  "meta": null,
  "errors": []
}
```

Modelo de erro:

```json
{
  "success": false,
  "message": "Não foi possível criar o cliente.",
  "data": null,
  "meta": null,
  "errors": [
    {
      "field": "email",
      "code": "EMAIL_INVALIDO",
      "message": "Informe um e-mail válido."
    }
  ]
}
```

Regras:

- usuário recebe mensagem clara;
- log recebe detalhe técnico;
- resposta não expõe stack trace, SQL, caminho interno ou segredo;
- erros de campo ficam em `errors`;
- dados extras de paginação ficam em `meta`.

---

# 6. Códigos HTTP

A IA deve usar códigos HTTP de forma coerente.

| Código | Uso recomendado |
|---:|---|
| `200` | sucesso em consulta ou alteração |
| `201` | recurso criado |
| `204` | sucesso sem corpo de resposta, usar com cuidado |
| `400` | requisição inválida |
| `401` | usuário não autenticado |
| `403` | usuário autenticado sem permissão |
| `404` | recurso não encontrado |
| `409` | conflito, duplicidade ou estado incompatível |
| `422` | erro de validação de regra/campo |
| `429` | muitas requisições |
| `500` | erro interno inesperado |

A mensagem de erro deve combinar com o código HTTP. Não responder tudo como `200` quando houve falha real.

---

# 7. Paginação, filtros e ordenação

Listagens devem evitar retornar dados ilimitados.

Padrão recomendado de consulta:

```txt
GET /api/v1/clientes?page=1&per_page=20&search=ana&status=ativo&sort=nome&direction=asc
```

Boas práticas:

- definir limite padrão, como 20 ou 50 itens;
- impor limite máximo para evitar abuso;
- validar campos permitidos para ordenação;
- escapar filtros e usar prepared statements;
- retornar metadados de paginação.

Exemplo de `meta`:

```json
{
  "page": 1,
  "per_page": 20,
  "total": 156,
  "total_pages": 8
}
```

---

# 8. Segurança de API

Todo endpoint deve ser revisado por segurança.

Verificar:

- autenticação obrigatória quando a rota for privada;
- autorização por perfil, permissão ou propriedade do registro;
- CSRF para ações feitas por sessão web;
- rate limit em login, recuperação de senha e endpoints sensíveis;
- validação de payload JSON;
- limite de tamanho do corpo da requisição;
- prepared statements no banco;
- logs sem dados sensíveis;
- CORS restrito quando necessário;
- bloqueio de métodos não permitidos.

Nunca confiar em `id_usuario`, `perfil` ou `permissao` enviados pelo frontend. O backend deve obter essas informações da sessão/token.

---

# 9. Estrutura de arquivos sugerida

Para projetos PHP procedural, uma organização simples pode ser:

```txt
/app
  /api
    /v1
      clientes.php
      demandas.php
      auth.php
  /services
    cliente_service.php
    demanda_service.php
  /helpers
    response.php
    validation.php
    auth.php
/public
  index.php
```

Regras:

- endpoints devem receber requisição e chamar services;
- regra de negócio deve ficar fora do arquivo público quando possível;
- respostas devem usar helper único;
- conexão com banco deve ser centralizada;
- arquivos internos não devem ficar expostos diretamente na web.

---

# 10. Documentação de endpoint

Cada endpoint deve ter documentação mínima.

Modelo:

```md
## Criar cliente

Método: POST
Rota: /api/v1/clientes
Autenticação: obrigatória
Permissão: clientes.criar
Descrição: cria um novo cliente ativo.

Payload:
- nome: string, obrigatório, máximo 120 caracteres
- email: string, obrigatório, único

Sucesso 201:
```json
{"success": true, "message": "Cliente criado com sucesso.", "data": {"id": 123}}
```

Erros possíveis:
- 401: usuário não autenticado
- 403: sem permissão
- 422: dados inválidos
- 409: e-mail já cadastrado
```

A documentação deve ser atualizada junto com a implementação.

---

# Checklist obrigatório antes de concluir

- [ ] Rotas usam padrão consistente.
- [ ] Todo endpoint valida entrada no backend.
- [ ] Respostas JSON seguem padrão único.
- [ ] Códigos HTTP estão corretos.
- [ ] Permissões foram verificadas no backend.
- [ ] Listagens têm paginação e limite máximo.
- [ ] Erros técnicos não são expostos ao usuário.
- [ ] Endpoint está documentado com payload, resposta e erros possíveis.

---

# Modelo de entrega esperado

Ao criar ou revisar uma API, entregue:

1. Lista de endpoints.
2. Método HTTP de cada endpoint.
3. Permissão necessária.
4. Payload esperado.
5. Resposta de sucesso.
6. Respostas de erro.
7. Regras de validação.
8. Observações de segurança.
9. Exemplo de consumo com Fetch API quando necessário.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
