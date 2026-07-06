# Skill: Notificações Internas, E-mail, WhatsApp, SMS e Push

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e organizar **notificações de sistema**, incluindo mensagens internas, e-mail, WhatsApp, SMS, push notification, lembretes, alertas por prazo, comunicações transacionais e preferências do usuário.

O foco é enviar a mensagem certa, para a pessoa certa, no canal certo, no momento certo, com rastreabilidade e sem spam.

---

## Limite desta skill

Esta skill define notificações internas, e-mail, WhatsApp, SMS, push, templates, fila de envio, preferências, frequência, prioridade, rastreabilidade e anti-spam.

Ela pode citar retenção, gamificação, suporte e integrações quando isso envolver comunicação com usuário, mas não deve substituir:

- `skill-retencao.md` para estratégia de retorno;
- `skill-gamificacao.md` para regras de pontos, missões e conquistas;
- `skill-integracoes-webhooks.md` para APIs externas, webhooks, retry e idempotência;
- `skill-lgpd-privacidade.md` para regra completa de privacidade;
- `skill-logs-auditoria.md` para auditoria completa.

Esta skill responde "qual mensagem enviar, para quem, por qual canal, em qual momento e com qual limite?".

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

Ao usar esta skill, aja como uma pessoa sênior de produto, backend, UX writing, retenção, comunicação e operação de SaaS.

A IA deve pensar em:

- motivo real da notificação;
- canal adequado;
- prioridade;
- frequência;
- preferências do usuário;
- templates;
- fila de envio;
- status de entrega;
- logs;
- privacidade.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-retencao.md
skill-gamificacao.md
skill-backend.md
skill-integracoes-webhooks.md
skill-lgpd-privacidade.md
skill-logs-auditoria.md
```

---

## Princípio central

```txt
Notificação boa não interrompe por vaidade do sistema; ela ajuda o usuário a agir, decidir ou acompanhar algo importante.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Diferença entre retenção e notificação

Retenção define por que o usuário deve voltar.

Notificação define como, quando e por qual canal o sistema chama o usuário.

Uma notificação só deve existir se houver:

- ação necessária;
- informação importante;
- prazo;
- mudança relevante;
- confirmação de operação;
- risco ou oportunidade;
- progresso útil.

Não criar notificação apenas para gerar acesso artificial.

---

# 2. Tipos de notificação

Classificar por finalidade.

| Tipo | Exemplo |
|---|---|
| Transacional | senha alterada, cadastro confirmado |
| Operacional | tarefa vencendo, demanda atribuída |
| Alerta | erro, risco, bloqueio, falha de integração |
| Lembrete | evento amanhã, prazo próximo |
| Progresso | meta concluída, etapa avançada |
| Retenção | resumo semanal, pendências úteis |
| Administrativa | novo usuário, pagamento, auditoria |

Cada tipo tem prioridade e canal diferente.

---

# 3. Canais

Escolher canal conforme urgência e contexto.

```txt
Notificação interna = informação dentro do sistema.
E-mail = registro formal, menos urgente.
WhatsApp/SMS = urgente ou operacional, usar com cuidado.
Push = app/PWA, ação rápida.
```

Boas práticas:

- não mandar tudo por todos os canais;
- respeitar preferência do usuário;
- evitar mensagens fora de horário quando não urgente;
- permitir descadastro de comunicações não essenciais;
- manter transacionais importantes mesmo quando marketing estiver desativado.

---

# 4. Prioridade

Definir prioridade ajuda a evitar ruído.

```txt
Baixa = informação útil, sem urgência.
Média = ação recomendada.
Alta = prazo, bloqueio ou impacto operacional.
Crítica = segurança, falha grave ou ação imediata.
```

Notificações críticas devem ser raras e justificadas.

---

# 5. Templates

Notificações devem usar templates padronizados.

Campos comuns:

- código do template;
- título;
- corpo;
- canal;
- variáveis permitidas;
- idioma quando aplicável;
- categoria;
- ativo/inativo;
- versão.

Exemplo:

```txt
TEMPLATE: tarefa_atribuida
Título: Nova tarefa atribuída
Mensagem: Olá, {{nome}}. Você recebeu a tarefa {{titulo_tarefa}} com prazo para {{data_prazo}}.
Canal: interna + e-mail
```

Evitar texto duplicado espalhado no código.

---

# 6. Gatilhos de envio

Toda notificação deve ter gatilho claro.

Exemplos:

- usuário criado;
- tarefa atribuída;
- status alterado;
- prazo próximo;
- prazo vencido;
- comentário mencionado;
- relatório disponível;
- integração falhou;
- pagamento confirmado;
- senha alterada.

Documentar condição exata para envio e condição para não enviar.

---

# 7. Anti-spam e frequência

Evitar excesso de mensagens.

Regras:

- agrupar notificações semelhantes;
- limitar repetição;
- evitar lembrete infinito;
- não reenviar se usuário já concluiu ação;
- respeitar janela de silêncio;
- criar digest diário/semanal quando fizer sentido;
- permitir configurar preferências.

Exemplo:

```txt
Não enviar mais de 1 lembrete de tarefa vencida por dia para a mesma tarefa.
```

---

# 8. Fila e status de envio

Envios devem ser rastreáveis.

Tabela sugerida:

```txt
notifications
notification_deliveries
```

Status recomendados:

```txt
pendente
enviando
enviado
entregue
lido
erro
cancelado
ignorado
```

Registrar tentativa, canal, erro e próximo retry quando aplicável.

---

# 9. Preferências do usuário

Usuário deve controlar notificações não essenciais.

Preferências possíveis:

- receber e-mail;
- receber WhatsApp;
- receber push;
- resumo diário;
- resumo semanal;
- silenciar fora de horário;
- pausar lembretes;
- categorias permitidas.

Notificações obrigatórias de segurança e operação crítica podem ter regras diferentes, mas devem ser explicadas.

---

# 10. Conteúdo da mensagem

Mensagem deve ser clara, curta e acionável.

Boa estrutura:

```txt
O que aconteceu?
Por que importa?
Qual ação o usuário pode fazer?
Onde clicar?
```

Evitar:

- texto genérico;
- culpa;
- urgência falsa;
- excesso de emojis;
- dados sensíveis no corpo da mensagem;
- links sem contexto.

Preferir CTA claro:

```txt
Ver tarefa
Confirmar presença
Revisar pendência
```

---

# 11. Privacidade e segurança

Notificações podem vazar dados.

Cuidados:

- não enviar dados sensíveis por canal inseguro;
- não incluir senha ou token aberto;
- links de ação devem expirar quando críticos;
- validar permissão ao abrir link;
- mascarar dados quando necessário;
- registrar envio sem salvar conteúdo sensível completo.

O usuário que recebe um link ainda deve estar autorizado ao acessar.

---

# 12. Métricas

Medir qualidade das notificações.

Métricas úteis:

- enviadas;
- entregues;
- abertas/lidas;
- clicadas;
- concluíram ação;
- erros de envio;
- opt-out;
- reclamações;
- tempo até ação.

Notificação que não gera valor deve ser ajustada ou removida.

---

# Checklist obrigatório antes de concluir

- [ ] Cada notificação tem motivo real.
- [ ] Canal foi escolhido por prioridade/contexto.
- [ ] Template está padronizado.
- [ ] Gatilho está documentado.
- [ ] Existe regra anti-spam.
- [ ] Preferências do usuário foram consideradas.
- [ ] Envio tem status e logs.
- [ ] Dados sensíveis não são expostos.
- [ ] Link valida permissão ao abrir.
- [ ] Métricas foram previstas.

---

# Modelo de entrega esperado

Ao criar notificações, entregue:

1. Lista de notificações.
2. Tipo e prioridade.
3. Gatilho.
4. Público-alvo.
5. Canal.
6. Template.
7. Regra de frequência.
8. Preferências do usuário.
9. Status/logs.
10. Métricas.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
