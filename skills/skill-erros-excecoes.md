# Skill: Tratamento de Erros, Exceções e Mensagens do Sistema

## Objetivo da skill

Esta skill orienta uma IA a padronizar **erros técnicos, erros de validação, exceções, mensagens de usuário, códigos internos, respostas JSON e páginas de erro** em sistemas web, SaaS e apps.

O foco é tornar falhas compreensíveis para o usuário, rastreáveis para o suporte e seguras para produção.

---

## Limite desta skill

Esta skill define o padrão de tratamento de erros, exceções, mensagens, códigos internos, respostas JSON e páginas de erro.

Ela pode orientar backend, API, frontend e logs quando o assunto for falha, mas não deve substituir:

- `skill-api-rest.md` para contrato completo dos endpoints;
- `skill-backend.md` para regra de negócio do servidor;
- `skill-fetch.md` para tratamento de erro no consumo HTTP;
- `skill-logs-auditoria.md` para auditoria completa;
- `skill-monitoramento-observabilidade.md` para alertas e saúde de produção;
- `skill-qa.md` para plano de testes.

Esta skill responde "quando algo dá errado, o sistema informa, registra e permite recuperação de forma segura?".

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

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior especialista em backend, frontend, segurança, UX writing, QA e observabilidade.

A IA deve pensar em:

- mensagem amigável;
- detalhe técnico em log;
- código interno do erro;
- resposta HTTP correta;
- campo com erro;
- request_id;
- fallback;
- não exposição de dados sensíveis;
- recuperação da ação.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-api-rest.md
skill-fetch.md
skill-backend.md
skill-debug.md
skill-logs-auditoria.md
skill-qa.md
```

---

## Princípio central

```txt
Erro profissional não assusta o usuário, não vaza detalhe técnico e ajuda a equipe a encontrar a causa.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Tipos de erro

Classificar erros ajuda a tratar corretamente.

Tipos comuns:

- erro de validação;
- erro de autenticação;
- erro de permissão;
- erro de recurso não encontrado;
- erro de conflito/duplicidade;
- erro de regra de negócio;
- erro de integração externa;
- erro de banco;
- erro inesperado;
- erro de rede no frontend;
- erro de timeout.

---

# 2. Mensagem para usuário

Mensagem para usuário deve ser clara e acionável.

Evitar:

```txt
Erro 500.
Falha SQLSTATE[23000].
Undefined index.
```

Preferir:

```txt
Não foi possível salvar agora. Tente novamente em alguns instantes.
```

Ou, em validação:

```txt
Informe um e-mail válido para continuar.
```

A mensagem deve explicar o que fazer quando possível.

---

# 3. Detalhe técnico em log

O detalhe técnico deve ir para log.

Registrar:

- request_id;
- usuário;
- rota;
- método;
- payload seguro;
- arquivo/função;
- mensagem técnica;
- stack quando permitido;
- erro original;
- contexto de negócio.

Nunca expor isso diretamente ao usuário em produção.

---

# 4. Padrão JSON de erro

APIs devem responder erro em formato único.

```json
{
  "success": false,
  "message": "Verifique os campos destacados.",
  "data": null,
  "meta": {
    "request_id": "req_123"
  },
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

- `message` é geral;
- `errors` detalha campos ou causas;
- `code` ajuda frontend e suporte;
- `request_id` ajuda rastreio.

---

# 5. Códigos internos de erro

Criar códigos previsíveis.

Exemplos:

```txt
AUTH_CREDENCIAIS_INVALIDAS
AUTH_SESSAO_EXPIRADA
PERMISSAO_NEGADA
VALIDACAO_CAMPO_OBRIGATORIO
CLIENTE_EMAIL_DUPLICADO
INTEGRACAO_TIMEOUT
BANCO_ERRO_GRAVACAO
```

Boas práticas:

- código em caixa alta;
- categoria + motivo;
- não mudar código sem necessidade;
- documentar códigos usados em API.

---

# 6. Erros de validação

Validação deve apontar campo e correção.

Exemplo:

```json
{
  "field": "telefone",
  "code": "TELEFONE_INVALIDO",
  "message": "Informe um telefone com DDD."
}
```

No frontend:

- mostrar erro próximo ao campo;
- manter dados preenchidos;
- focar primeiro campo com erro quando útil;
- não apagar formulário inteiro;
- permitir correção rápida.

---

# 7. Erros de permissão

Permissão negada deve ser clara sem revelar dado protegido.

Mensagem:

```txt
Você não tem permissão para acessar esta informação.
```

Boas práticas:

- retornar 401 se não autenticado;
- retornar 403 se autenticado sem permissão;
- registrar tentativa quando relevante;
- não informar detalhes do recurso protegido quando isso gerar vazamento.

---

# 8. Erros de integração

Falha externa deve ser tratada sem quebrar tudo.

Exemplos:

- timeout;
- serviço fora do ar;
- token expirado;
- payload rejeitado;
- limite de requisições.

Boas práticas:

- registrar log de integração;
- usar retry quando apropriado;
- informar usuário com mensagem simples;
- manter status pendente quando puder reprocessar;
- não duplicar dados em nova tentativa.

---

# 9. Páginas de erro

Criar páginas amigáveis.

Páginas comuns:

- 403: acesso negado;
- 404: página não encontrada;
- 500: erro interno;
- manutenção: sistema temporariamente indisponível;
- sessão expirada.

Cada página deve ter:

- título claro;
- explicação curta;
- ação possível;
- botão voltar/início/login;
- request_id quando for erro técnico.

---

# 10. Frontend e erros de rede

Fetch API deve tratar falhas.

Cenários:

- sem internet;
- timeout;
- JSON inválido;
- status HTTP de erro;
- sessão expirada;
- permissão negada.

Frontend deve:

- parar loading;
- reativar botão;
- mostrar mensagem;
- redirecionar para login quando sessão expirar;
- preservar dados do formulário quando possível.

---

# 11. Fallback seguro

Quando algo falhar, o sistema deve tentar manter estado seguro.

Exemplos:

- se envio de e-mail falhar, salvar pendência de envio;
- se integração falhar, manter status pendente;
- se upload falhar, não gravar registro incompleto sem marcar erro;
- se pagamento não confirmar, não liberar recurso automaticamente;
- se validação falhar, não alterar dados.

Falha não deve deixar o banco inconsistente.

---

# Checklist obrigatório antes de concluir

- [ ] Erro técnico não aparece em produção.
- [ ] Usuário recebe mensagem clara.
- [ ] Log técnico registra contexto suficiente.
- [ ] Resposta JSON de erro é padronizada.
- [ ] Códigos internos foram usados.
- [ ] Validação aponta campo e correção.
- [ ] 401 e 403 são diferenciados.
- [ ] Erros de integração têm tratamento e log.
- [ ] Frontend remove loading após erro.
- [ ] Falha mantém estado seguro.

---

# Modelo de entrega esperado

Ao tratar erros, entregue:

1. Tipos de erro esperados.
2. Mensagens para usuário.
3. Códigos internos.
4. Status HTTP.
5. Formato JSON.
6. Logs necessários.
7. Comportamento no frontend.
8. Fallback/recuperação.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
