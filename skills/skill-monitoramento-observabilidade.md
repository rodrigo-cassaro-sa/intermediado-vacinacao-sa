# Skill: Monitoramento, Observabilidade, Uptime e Saúde de Sistemas

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa sênior em **operação, monitoramento, observabilidade, suporte técnico e confiabilidade de sistemas**.

O foco é garantir que um SaaS, app, sistema web ou API em produção tenha visibilidade sobre erros, lentidão, indisponibilidade, falhas de integração, filas, jobs, pagamentos, banco de dados e experiência do usuário.

Stack padrão:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
APIs JSON
Servidor Linux com Apache ou Nginx
Docker/Docker Compose quando aplicável
```

Esta skill complementa logs, auditoria, debug visual, performance, deploy, backup, QA, segurança, erros e documentação.

---

## Limite desta skill

Esta skill define monitoramento, observabilidade, uptime, health check, métricas, alertas, incidentes, dashboard técnico e saúde de produção.

Ela pode citar logs, performance, deploy, backup, suporte e segurança quando isso afetar produção, mas não deve substituir:

- `skill-logs-auditoria.md` para trilha oficial de eventos e auditoria;
- `skill-debug.md` para debug visual administrativo;
- `skill-performance.md` para análise profunda de gargalos;
- `skill-deploy-ci-cd.md` para processo de publicação;
- `skill-backup-recuperacao.md` para política de backup e restauração;
- `skill-suporte-atendimento-sla.md` para fluxo de atendimento e SLA.

Esta skill responde "o sistema em produção está saudável, observável e preparado para avisar antes que o usuário reclame?".

---

## Regra de versão publicada

Toda publicação em homologação ou produção deve ter uma versão identificável.

A versão pode ser:

- tag Git;
- número semântico;
- hash curto do commit;
- release documentada;
- registro no changelog.

Toda versão publicada deve informar:

```txt
versao
ambiente
branch_origem
commit_hash
data_hora_deploy
responsavel
principais_mudancas
migrations_executadas
rollback_disponivel
status_pos_deploy
```

A IA nunca deve considerar um deploy profissional quando não for possível descobrir exatamente qual código está rodando no ambiente.

Essa regra conecta Git, Docker, deploy e monitoramento, garantindo rastreabilidade da versão publicada.


---

## Regra de checklist pré-produção

Antes de qualquer deploy em produção, a IA deve validar:

- branch ou tag correta;
- Git sem alterações locais pendentes;
- `.env` correto do ambiente;
- debug desligado;
- backup realizado quando houver banco ou arquivos críticos;
- migrations revisadas e testadas;
- plano de rollback definido;
- health check disponível;
- logs ativos;
- monitoramento mínimo ativo;
- testes críticos executados;
- responsável pela validação definido.

Deploy em produção sem checklist não deve ser tratado como seguro.

Quando algum item não puder ser validado, a IA deve registrar o risco, sugerir correção e pedir aprovação explícita antes de seguir.


---

## Regra de observabilidade mínima

Nenhum sistema deve ir para produção sem visibilidade mínima.

Antes de produção, garantir:

- health check da aplicação;
- log de erro do backend;
- log de acesso ou requisições importantes;
- identificação do ambiente;
- versão publicada visível;
- status de banco;
- monitoramento de espaço em disco;
- alerta para erro crítico;
- verificação de backup;
- forma de consultar últimos erros;
- registro de deploy.

Se o sistema possui pagamentos, webhooks, integrações, filas ou jobs, monitorar também:

- último evento recebido;
- último evento processado;
- eventos com erro;
- fila acumulada;
- falhas consecutivas;
- tempo de processamento.

Produção sem observabilidade mínima vira operação às cegas.


---

## Regra de nomenclatura para produção, datas e logs

Por padrão, campos internos de logs, health check, jobs, deploy e monitoramento devem seguir a nomenclatura em português usada no projeto.

### Campos recomendados

```txt
data_hora
nivel
contexto
mensagem
usuario_id
ambiente
versao
verificado_em
iniciado_em
finalizado_em
duracao_ms
mensagem_erro
registros_processados
data_hora_deploy
responsavel
principais_mudancas
status_pos_deploy
```

### Tradução recomendada

```txt
data_hora           -> data_hora
nivel               -> nivel
contexto             -> contexto
mensagem             -> mensagem
usuario_id             -> usuario_id
verificado_em          -> verificado_em
iniciado_em          -> iniciado_em
finalizado_em         -> finalizado_em
duracao_ms         -> duracao_ms
mensagem_erro       -> mensagem_erro
registros_processados   -> registros_processados
environment         -> ambiente
version             -> versao
```

### Regra obrigatória

Não misturar nomes de campos em inglês e português no mesmo projeto quando forem campos internos do sistema.

Termos técnicos como `request_id`, `trace_id`, `status_code`, `endpoint`, `health_check`, `commit_hash`, `container_id`, `APP_ENV`, `DB_HOST` e nomes oficiais de variáveis de ambiente podem ser mantidos em inglês quando forem padrão técnico.


---


## Papel da IA

Ao usar esta skill, a IA deve agir como uma pessoa especialista em sistemas em produção.

A IA deve pensar em:

- uptime;
- erros;
- lentidão;
- logs;
- métricas;
- alertas;
- saúde de serviços;
- banco de dados;
- filas;
- webhooks;
- integrações;
- backups;
- uso de recursos;
- incidentes;
- plano de resposta.

A IA não deve considerar o sistema pronto apenas porque funciona localmente. Sistema real precisa ser observável em produção.

---

## Princípio central

```txt
O sistema precisa avisar que está com problema antes do usuário precisar reclamar.
```

Monitoramento bom responde rapidamente:

- O sistema está online?
- A API está respondendo?
- O banco está funcionando?
- As integrações estão saudáveis?
- Os pagamentos estão sendo confirmados?
- Os webhooks estão processando?
- As páginas estão lentas?
- Houve aumento de erro?
- O backup rodou?
- Há espaço em disco?
- Há fila acumulada?

---

## Quando usar esta skill

Use esta skill quando o projeto estiver indo para:

- homologação;
- produção;
- ambiente com usuários reais;
- sistema com pagamento;
- sistema com webhook;
- sistema com integrações;
- sistema com dashboard crítico;
- sistema com operação diária;
- sistema com agendamento;
- sistema com jobs automáticos;
- sistema com banco de dados importante.

---

## Quando não usar esta skill

Não precisa aplicar profundamente em:

- protótipo descartável;
- landing page simples;
- estudo local;
- tela estática sem backend.

Mesmo assim, boas práticas básicas de erro e log continuam recomendadas.

---

## Diferença entre log, monitoramento e observabilidade

```txt
Log = registro de eventos.
Monitoramento = verificação contínua de saúde e métricas.
Observabilidade = capacidade de entender o que aconteceu, por que aconteceu e onde aconteceu.
```

Exemplo:

- Log: erro ao processar webhook.
- Monitoramento: alerta de aumento de webhooks com erro.
- Observabilidade: rastrear pedido, pagamento, webhook, usuário, tempo e causa da falha.

---

## Sinais vitais do sistema

Todo sistema em produção deve acompanhar:

```txt
Disponibilidade
Tempo de resposta
Taxa de erro
Uso de CPU
Uso de memória
Uso de disco
Conexões com banco
Tempo de consulta SQL
Falhas de login
Falhas de pagamento
Falhas de webhook
Falhas de e-mail/WhatsApp
Backups
Jobs automáticos
```

---

## Health check

Criar endpoint simples para verificar saúde.

Exemplo:

```txt
GET /api/health
```

Resposta:

```json
{
  "success": true,
  "status": "ok",
  "services": {
    "app": "ok",
    "database": "ok",
    "storage": "ok"
  },
  "verificado_em": "2026-07-03T10:30:00-03:00"
}
```

Regras:

- não expor dados sensíveis;
- não mostrar senha, caminho interno ou stack completa;
- retornar erro se banco essencial falhar;
- permitir modo público limitado e modo privado detalhado.

---

## Tipos de monitoramento

### 1. Uptime

Verifica se o sistema está online.

Monitorar:

- página inicial;
- login;
- API principal;
- health check;
- domínio;
- certificado SSL.

---

### 2. Performance

Verifica lentidão.

Monitorar:

- tempo médio de resposta;
- endpoints mais lentos;
- consultas SQL lentas;
- páginas pesadas;
- tempo de carregamento;
- timeout.

---

### 3. Erros

Verifica falhas.

Monitorar:

- erro 500;
- erro 403;
- erro 404 anormal;
- exceções PHP;
- falha de banco;
- falha de integração;
- falha de webhook;
- falha de pagamento.

---

### 4. Negócio

Verifica processos críticos.

Exemplos:

- pedidos criados;
- pagamentos confirmados;
- vouchers emitidos;
- webhooks processados;
- notificações enviadas;
- tarefas concluídas;
- cadastros novos.

Se o sistema costuma vender todos os dias e fica horas sem pedido, pode ser sinal de problema.

---

### 5. Infraestrutura

Verifica servidor.

Monitorar:

- CPU;
- memória;
- disco;
- rede;
- processos;
- containers;
- logs do servidor;
- banco de dados;
- uso de storage.

---

## Logs estruturados

Logs devem ser úteis para busca.

Campos recomendados:

```txt
data_hora
nivel
contexto
mensagem
usuario_id
tenant_id
request_id
ip
method
path
status_code
duracao_ms
error_code
metadata
```

Níveis:

```txt
debug
info
warning
error
critical
```

Regras:

- não registrar senha;
- não registrar token sensível;
- mascarar dados pessoais;
- incluir identificador de correlação;
- separar log técnico de auditoria;
- rotacionar logs.

---

## Request ID

Toda requisição importante deve ter um identificador.

Exemplo:

```txt
X-Request-ID: req_20260703_8f3k92qd
```

Usos:

- rastrear erro;
- relacionar frontend, backend e logs;
- investigar suporte;
- conectar webhook com pedido;
- auditar performance.

---

## Métricas essenciais

### Métricas técnicas

```txt
requests_total
requests_error_total
request_duration_ms
database_query_duration_ms
memory_usage
disk_usage
cpu_usage
queue_size
job_failures
```

### Métricas de negócio

```txt
pedidos_criados
pagamentos_aprovados
pagamentos_recusados
vouchers_emitidos
vouchers_utilizados
webhooks_recebidos
webhooks_com_erro
usuarios_ativos
logins_com_erro
notificacoes_enviadas
```

---

## Alertas

Alertas devem ser úteis e acionáveis.

Criar alerta para:

- sistema fora do ar;
- certificado SSL perto de vencer;
- erro 500 acima do normal;
- endpoint crítico lento;
- banco indisponível;
- disco quase cheio;
- backup não executado;
- webhook com erro acumulado;
- pagamento sem confirmação por muito tempo;
- fila acumulada;
- integração externa falhando.

Evite alerta para tudo. Alerta demais vira ruído.

---

## Severidade de alertas

```txt
P1 - Crítico: sistema fora do ar, pagamento parado, perda de dados
P2 - Alto: funcionalidade crítica falhando para parte dos usuários
P3 - Médio: lentidão ou falha contornável
P4 - Baixo: problema pontual sem impacto imediato
```

Cada severidade deve ter ação esperada.

---

## Dashboard de saúde

Criar painel admin técnico com:

- status da aplicação;
- status do banco;
- status de integrações;
- tempo de resposta;
- erros recentes;
- webhooks com erro;
- jobs pendentes;
- uso de disco;
- último backup;
- versão implantada;
- ambiente atual;
- links para logs.

Esse painel deve ser restrito a admin técnico, suporte ou dev.

---

## Monitoramento de integrações

Para cada integração externa, acompanhar:

```txt
nome
status
última chamada
último sucesso
último erro
tempo médio
falhas consecutivas
token válido
limite de uso
```

Exemplos:

- gateway de pagamento;
- WhatsApp;
- e-mail;
- SMS;
- API externa;
- sistema parceiro.

---

## Monitoramento de webhooks

Acompanhar:

- eventos recebidos;
- eventos processados;
- eventos com erro;
- eventos duplicados;
- tempo de processamento;
- fila de reprocessamento;
- assinatura inválida;
- payload inválido.

Toda falha de webhook deve permitir investigação e reprocessamento controlado.

---

## Monitoramento de jobs

Jobs podem ser:

- envio de notificações;
- expiração de vouchers;
- conciliação;
- backup;
- limpeza de cache;
- geração de relatórios;
- sincronização externa.

Registrar:

```txt
job_name
iniciado_em
finalizado_em
status
duracao_ms
registros_processados
mensagem_erro
```

---

## Monitoramento de banco

Acompanhar:

- consultas lentas;
- tamanho do banco;
- crescimento de tabelas;
- locks;
- falhas de conexão;
- índices ausentes;
- uso de disco;
- backups;
- tempo de restauração testado.

---

## Monitoramento de backup

Todo backup precisa ser monitorado.

Verificar:

- último backup realizado;
- tamanho do arquivo;
- duração;
- local;
- status;
- erro;
- teste de restauração;
- retenção.

Regra:

```txt
Backup que ninguém verifica pode não existir quando precisar.
```

---

## Incidentes

Todo incidente relevante deve ter registro.

Modelo:

```md
ID:
Data/hora:
Severidade:
Ambiente:
Impacto:
Sintoma:
Causa raiz:
Ação imediata:
Correção definitiva:
Responsável:
Tempo fora do ar:
Como evitar recorrência:
```

---

## Pós-incidente

Após resolver, documentar:

- o que aconteceu;
- quem foi impactado;
- por quanto tempo;
- causa provável;
- correção aplicada;
- ações preventivas;
- melhoria de monitoramento;
- melhoria de teste;
- melhoria de processo.

O objetivo não é culpar pessoas, é melhorar o sistema.

---

## Segurança

Monitoramento não pode expor:

- senhas;
- tokens;
- chaves de API;
- dados pessoais desnecessários;
- payload completo sensível;
- caminho interno do servidor;
- detalhes técnicos para usuário comum.

Painéis técnicos devem ter permissão restrita.

---

## Checklist da IA antes de entregar

- [ ] Existe health check.
- [ ] Erros críticos são registrados.
- [ ] Logs são estruturados.
- [ ] Dados sensíveis são mascarados.
- [ ] Existe monitoramento de uptime.
- [ ] Existe monitoramento de banco.
- [ ] Existe monitoramento de integração.
- [ ] Existe monitoramento de webhooks, se houver.
- [ ] Existe monitoramento de jobs, se houver.
- [ ] Existe alerta para falhas críticas.
- [ ] Backup é monitorado.
- [ ] Existe painel técnico restrito.
- [ ] Incidentes têm modelo de registro.
- [ ] Métricas de negócio críticas são acompanhadas.

---

## Saída esperada da IA

Quando esta skill for usada, a IA deve entregar:

- plano de monitoramento;
- endpoints de health check;
- estrutura de logs;
- métricas técnicas;
- métricas de negócio;
- alertas;
- painel de saúde;
- fluxo de incidentes;
- checklist de produção;
- casos de teste operacional.

A entrega deve sempre ajudar a detectar, investigar e resolver problemas reais em produção.

---

# Checklist obrigatório das regras de produção

- [ ] Versão publicada está identificável por tag, número ou commit.
- [ ] Checklist pré-produção foi validado antes de seguir.
- [ ] Observabilidade mínima está ativa no ambiente alvo.
- [ ] Campos internos de datas e logs seguem o padrão em português.
