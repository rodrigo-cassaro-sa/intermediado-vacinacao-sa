# Skill: Integrações, Webhooks e APIs Externas

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e manter **integrações entre sistemas**, incluindo consumo de APIs externas, recebimento de webhooks, envio de webhooks, filas simples, retry, idempotência, logs e tratamento de falhas.

O foco é evitar duplicidade, perda de dados, chamadas inseguras, integrações sem rastreabilidade e bugs difíceis de reproduzir.

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

## Limite desta skill

Esta skill define integrações com sistemas externos, APIs consumidas, APIs expostas, webhooks recebidos, webhooks enviados, filas, retry, idempotência, timeout, logs e tratamento de falhas externas.

Ela pode citar notificações, backend, API REST, segurança e logs quando isso afetar integração, mas não deve substituir:

- `skill-api-rest.md` para contrato geral das APIs internas;
- `skill-backend.md` para regra de negócio do servidor;
- `skill-notificacoes.md` para estratégia de comunicação com usuário;
- `skill-seguranca.md` para política geral de proteção;
- `skill-logs-auditoria.md` para auditoria oficial;
- `skill-erros-excecoes.md` para padrão completo de falhas.

Esta skill responde "como o sistema conversa com outros sistemas sem duplicar, perder dados ou falhar silenciosamente?".

---

## Regra de origem do evento

Todo evento de engajamento, retenção, gamificação, notificação ou integração deve ter origem clara.

A IA nunca deve criar notificação, ponto, missão, webhook, retenção, automação ou integração sem saber qual evento real disparou a ação.

### Origens possíveis

```txt
usuario
sistema
backend
webhook
cron
admin
suporte
pagamento
tarefa
relatorio
integracao
gamificacao
retencao
notificacao
```

### Dados mínimos do evento

Todo evento importante deve registrar ou transportar, quando aplicável:

```txt
evento
origem
usuario_id
tenant_id
workspace_id
entidade_tipo
entidade_id
data_hora
status
request_id
metadata
```

### Regra obrigatória

O evento deve representar algo real que aconteceu no sistema.

Exemplos:

- tarefa atribuída;
- prazo próximo;
- pagamento confirmado;
- missão concluída;
- integração falhou;
- relatório gerado;
- chamado atualizado;
- usuário ficou inativo;
- webhook recebido.

Se não existe evento real, a IA não deve criar comunicação, pontuação, reativação ou integração artificial.

Essa regra evita gamificação solta, notificação sem motivo, retenção agressiva e webhooks sem rastreabilidade.


---

## Regra de nomenclatura de campos de data

Por padrão, este projeto usa nomes de campos em português para datas, auditoria, histórico, eventos e filas.

### Campos padrão

```txt
criado_em
atualizado_em
excluido_em
enviado_em
entregue_em
lido_em
clicado_em
dispensado_em
ocorreu_em
processado_em
proxima_tentativa_em
ultimo_login_em
ultima_notificacao_enviada_em
ultimo_evento_de_valor_em
concluido_em
expira_em
criado_por
atualizado_por
excluido_por
```

### Regra obrigatória

Não misturar nomes de campos em inglês com nomes em português no mesmo projeto.

Quando o projeto usar português, todos os exemplos de tabelas, eventos, filas, logs, notificações, webhooks, payloads internos e relatórios devem seguir o padrão em português.

### Tradução recomendada

```txt
criado_em                  -> criado_em
atualizado_em                  -> atualizado_em
excluido_em                  -> excluido_em
enviado_em                     -> enviado_em
entregue_em                -> entregue_em
lido_em                     -> lido_em
clicado_em                  -> clicado_em
dispensado_em                -> dispensado_em
ocorreu_em                 -> ocorreu_em
processado_em                -> processado_em
proxima_tentativa_em             -> proxima_tentativa_em
ultimo_login_em               -> ultimo_login_em
ultima_notificacao_enviada_em   -> ultima_notificacao_enviada_em
ultimo_evento_de_valor_em         -> ultimo_evento_de_valor_em
concluido_em                -> concluido_em
expira_em                  -> expira_em
criado_por                  -> criado_por
atualizado_por                  -> atualizado_por
excluido_por                  -> excluido_por
```

Exceção: termos técnicos como `tenant_id`, `workspace_id`, `request_id`, `trace_id`, `external_id`, `idempotency_key`, `metadata`, `payload`, `endpoint`, `webhook` e `status` podem ser mantidos quando esse for o padrão técnico do projeto.


---


## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora backend sênior especialista em integrações, APIs, webhooks, automações, PHP procedural, MySQL/MariaDB e operação de sistemas.

A IA deve pensar em:

- contrato de integração;
- autenticação;
- validação de payload;
- idempotência;
- retry;
- logs;
- fila;
- timeout;
- segurança;
- ambiente de teste/homologação.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-api-rest.md
skill-backend.md
skill-fetch.md
skill-logs-auditoria.md
skill-erros-excecoes.md
skill-seguranca.md
```

---

## Princípio central

```txt
Integração confiável não depende de dar certo sempre; ela sabe falhar, tentar novamente, evitar duplicidade e explicar o que aconteceu.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Tipos de integração

A IA deve identificar o tipo correto.

```txt
API consumida = nosso sistema chama sistema externo.
API exposta = sistema externo chama nossa API.
Webhook recebido = evento externo chega ao nosso sistema.
Webhook enviado = nosso sistema avisa outro sistema.
Importação = dados entram por arquivo ou rotina.
Exportação = dados saem para outro sistema.
```

Cada tipo exige segurança, logs e tratamento diferente.

---

# 2. Contrato da integração

Antes de programar, documentar:

- sistema origem;
- sistema destino;
- endpoint;
- método;
- autenticação;
- payload;
- resposta esperada;
- erros possíveis;
- timeout;
- frequência;
- volume;
- ambiente de teste;
- responsável técnico externo;
- regra de retry;
- chave de idempotência.

Sem contrato, a integração fica frágil e difícil de manter.

---

# 3. Autenticação e segurança

Integrações devem ter autenticação segura.

Opções comuns:

- API token;
- Bearer token;
- assinatura HMAC;
- Basic Auth somente com HTTPS e necessidade clara;
- IP allowlist quando aplicável;
- OAuth quando exigido pelo provedor.

Regras:

- tokens ficam no `.env`;
- nunca versionar credenciais;
- validar assinatura de webhook quando disponível;
- usar HTTPS;
- limitar permissões do token;
- registrar uso suspeito.

---

# 4. Validação de payload recebido

Webhook recebido nunca deve ser confiado cegamente.

Validar:

- método HTTP;
- autenticação/assinatura;
- content-type;
- JSON válido;
- campos obrigatórios;
- tipos;
- tamanho;
- status permitido;
- duplicidade;
- relacionamento com dados internos.

Se payload for inválido, responder erro claro e registrar log.

---

# 5. Idempotência

Idempotência evita duplicidade quando a mesma mensagem chega mais de uma vez.

Obrigatório para:

- pagamentos;
- criação de pedidos;
- envio de vacinados/registros;
- alteração de status;
- webhooks externos;
- tarefas em fila;
- notificações.

Estratégias:

- salvar `external_id` único;
- usar `idempotency_key`;
- verificar evento já processado;
- travar processamento por chave;
- retornar sucesso quando evento duplicado já foi processado corretamente.

Regra:

```txt
Receber o mesmo evento duas vezes não pode criar dois registros indevidos.
```

---

# 6. Retry e falhas temporárias

Integrações falham por rede, timeout ou instabilidade externa.

Definir retry para falhas recuperáveis:

- timeout;
- HTTP 429;
- HTTP 500/502/503/504;
- conexão recusada temporária.

Não repetir automaticamente quando:

- payload inválido;
- permissão negada;
- erro de validação permanente;
- recurso inexistente sem chance de correção.

Usar intervalo progressivo quando possível:

```txt
1ª tentativa: agora
2ª tentativa: +5 min
3ª tentativa: +30 min
4ª tentativa: +2 h
```

---

# 7. Fila de processamento

Quando o processamento for pesado ou instável, usar fila.

Fluxo:

1. Receber requisição/webhook.
2. Validar o mínimo.
3. Salvar evento bruto com segurança.
4. Responder rapidamente.
5. Processar em rotina separada.
6. Registrar sucesso ou erro.
7. Reprocessar quando necessário.

Fila pode ser uma tabela MySQL simples em projetos menores.

Campos úteis:

```txt
id, type, payload, status, attempts, proxima_tentativa_em, last_error, criado_em, processado_em
```

---

# 8. Logs de integração

Toda integração precisa de log consultável.

Registrar:

- direção: entrada ou saída;
- sistema externo;
- endpoint;
- método;
- payload mascarado;
- resposta mascarada;
- status HTTP;
- tempo de resposta;
- tentativa;
- erro;
- idempotency key;
- usuário/ação que originou quando houver.

Logs devem permitir responder: o que foi enviado, quando, para onde e qual retorno veio.

---

# 9. Timeout e limites

Nunca deixar integração externa travar o sistema indefinidamente.

Definir:

- timeout de conexão;
- timeout total;
- limite de payload;
- limite de registros por lote;
- paginação quando consumir dados externos;
- fallback quando serviço externo estiver fora.

A experiência do usuário deve informar indisponibilidade sem mostrar erro técnico.

---

# 10. Ambiente de homologação

Integrações devem ter ambiente seguro de teste.

Verificar:

- base URL de homologação;
- token de homologação;
- dados fictícios;
- webhook de teste;
- logs separados;
- forma de simular erro;
- documentação do parceiro.

Nunca testar integração nova diretamente em produção sem controle.

---

# 11. Monitoramento e reprocessamento

Sistema operacional precisa permitir correção.

Painel recomendado:

- lista de eventos;
- status: pendente, processando, sucesso, erro, ignorado;
- detalhes do erro;
- botão de reprocessar com permissão;
- histórico de tentativas;
- filtro por sistema externo;
- filtro por período;
- busca por ID externo.

Reprocessamento deve respeitar idempotência.

---

# Checklist obrigatório antes de concluir

- [ ] Contrato da integração foi documentado.
- [ ] Credenciais ficam fora do código.
- [ ] Webhook valida autenticação/assinatura.
- [ ] Payload recebido é validado.
- [ ] Existe regra de idempotência.
- [ ] Falhas temporárias têm retry controlado.
- [ ] Integração tem logs mascarados.
- [ ] Timeout foi definido.
- [ ] Existe ambiente de homologação/teste.
- [ ] Reprocessamento não duplica dados.

---

# Modelo de entrega esperado

Ao criar integração, entregue:

1. Descrição da integração.
2. Sistema origem e destino.
3. Endpoints envolvidos.
4. Payload e resposta.
5. Autenticação.
6. Regras de validação.
7. Idempotência.
8. Retry/fila.
9. Logs.
10. Testes de homologação.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
