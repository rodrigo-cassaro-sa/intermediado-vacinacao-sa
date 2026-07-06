# Skill: Suporte, Atendimento, Chamados e SLA para SaaS, App e Software

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa especialista em **suporte ao usuário, operação de SaaS, atendimento, chamados, SLA, base de conhecimento e melhoria contínua do produto**.

O foco é criar sistemas que permitam receber, organizar, priorizar, responder e resolver solicitações de usuários, clientes, alunos, administradores ou equipes internas.

Stack padrão:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
APIs JSON
Notificações por e-mail, WhatsApp, SMS ou push quando necessário
```

Esta skill complementa admin operacional, notificações, logs, auditoria, erros, QA, UX/UI, permissões, LGPD, dashboards e documentação.

---

## Limite desta skill

Esta skill define suporte, atendimento, chamados, SLA, base de conhecimento, triagem, histórico, evidências e comunicação com usuário.

Ela pode citar admin, logs, debug, QA, vendas e relatórios quando isso ajudar a resolver atendimento, mas não deve substituir:

- `skill-admin-operacional.md` para painel administrativo geral;
- `skill-logs-auditoria.md` para trilha oficial de eventos;
- `skill-debug.md` para diagnóstico visual técnico;
- `skill-qa.md` para plano completo de testes;
- `skill-vendas-pagamentos.md` para regra financeira, voucher, pedido ou reembolso;
- `skill-relatorios-bi-dashboard.md` para dashboards e BI.

Esta skill responde "como o usuário pede ajuda, como o time atende, qual prazo existe e como o problema vira aprendizado?".

---

## Regra de nomenclatura de campos

Por padrão, este projeto usa nomes de campos em português para datas, auditoria e histórico.

### Campos padrão

```txt
criado_em
atualizado_em
excluido_em
cancelado_em
confirmado_em
pago_em
expira_em
disponivel_em
emitido_em
processado_em
usado_em
aceito_em
primeira_resposta_em
resolvido_em
fechado_em
sla_primeira_resposta_em
sla_resolucao_em
criado_por
atualizado_por
excluido_por
```

### Regra obrigatória

Não misturar nomes de campos em inglês com nomes em português no mesmo projeto.

Quando o projeto usar português, todos os exemplos de tabelas, campos, históricos, filtros, relatórios e payloads internos devem seguir o padrão em português.

Exceção: termos técnicos de SaaS como `tenant_id`, `workspace_id`, `request_id`, `trace_id`, `uuid_publico`, `metadata`, `related_type` e `related_id` podem ser mantidos quando esse for o padrão técnico escolhido no projeto.

---

## Papel da IA

Ao usar esta skill, a IA deve agir como uma pessoa sênior em suporte e operação de produto digital.

A IA deve pensar em:

- canal de entrada;
- triagem;
- prioridade;
- severidade;
- SLA;
- responsável;
- status;
- comunicação com usuário;
- histórico;
- evidências;
- anexos;
- categoria;
- causa raiz;
- base de conhecimento;
- indicadores de suporte;
- melhoria do produto.

A IA não deve tratar suporte como apenas um formulário de contato. Suporte é uma operação com fluxo, prazo, registro, responsabilidade e aprendizado.

---

## Princípio central

```txt
Todo pedido de suporte precisa ter dono, status, prioridade, histórico e próxima ação.
```

Solicitação sem responsável e sem prazo tende a se perder.

---

## Quando usar esta skill

Use esta skill quando o projeto envolver:

- central de ajuda;
- abertura de chamados;
- suporte ao usuário;
- atendimento interno;
- solicitações administrativas;
- problemas técnicos;
- dúvidas de uso;
- tickets;
- SLA;
- base de conhecimento;
- chat, e-mail ou WhatsApp de suporte;
- painel de atendimento;
- histórico de contato;
- acompanhamento de bugs reportados.

---

## Quando não usar esta skill

Não use esta skill para:

- formulário simples sem acompanhamento;
- tela sem atendimento;
- contato institucional básico;
- comentários internos sem SLA.

Nesses casos, uma tela simples de contato pode ser suficiente.

---

## Diferença entre contato, chamado e incidente

```txt
Contato = mensagem simples enviada pelo usuário.
Chamado = solicitação registrada com status, responsável e prazo.
Incidente = problema com impacto real em operação, sistema ou usuários.
```

Exemplo:

- Contato: "Tenho dúvida sobre meu voucher."
- Chamado: "Voucher pago não apareceu na minha conta."
- Incidente: "Todos os pagamentos aprovados não estão gerando voucher."

---

## Canais de entrada

Possíveis canais:

- formulário no sistema;
- e-mail;
- WhatsApp;
- chat;
- telefone;
- painel admin;
- abertura automática por erro;
- abertura automática por integração;
- abertura pelo suporte interno.

Boas práticas:

- centralizar no histórico do cliente;
- evitar perda em conversas soltas;
- registrar origem;
- associar usuário, tenant, pedido ou voucher;
- permitir anexos e evidências;
- confirmar recebimento.

---

## Categorias de chamado

Categorias recomendadas:

```txt
duvida
erro
acesso_login
pagamento
voucher
assinatura
cadastro
permissao
relatorio
integracao
solicitacao_melhoria
cancelamento
reembolso
outros
```

Categorias ajudam a rotear, medir e priorizar.

---

## Prioridade e severidade

### Severidade

Impacto real do problema.

```txt
critica
alta
media
baixa
```

Exemplo:

- crítica: sistema fora do ar;
- alta: pagamento aprovado sem voucher;
- média: relatório com filtro incorreto;
- baixa: dúvida de uso.

### Prioridade

Ordem de atendimento.

```txt
urgente
alta
normal
baixa
```

A prioridade pode considerar severidade, cliente, prazo e volume de usuários afetados.

---

## Status do chamado

Status recomendados:

```txt
novo
em_triagem
aguardando_usuario
aguardando_suporte
em_atendimento
em_desenvolvimento
aguardando_terceiro
resolvido
fechado
cancelado
reaberto
```

Regras:

- todo status deve ter significado claro;
- mudança de status deve gerar histórico;
- chamado resolvido pode ser reaberto;
- chamado fechado encerra o fluxo;
- chamado aguardando usuário deve ter lembrete;
- chamado crítico não pode ficar sem responsável.

---

## SLA

SLA define prazo esperado de resposta e resolução.

Exemplo:

| Severidade | Primeira resposta | Resolução esperada |
|---|---:|---:|
| Crítica | 30 minutos | 4 horas |
| Alta | 2 horas | 1 dia útil |
| Média | 1 dia útil | 3 dias úteis |
| Baixa | 2 dias úteis | 7 dias úteis |

Ajustar conforme realidade do negócio.

Boas práticas:

- calcular prazo considerando horário de atendimento;
- pausar SLA quando aguarda usuário, se essa for a regra;
- destacar chamados perto de vencer;
- alertar responsáveis;
- medir SLA cumprido e vencido.

---

## Dados mínimos do chamado

```txt
id
uuid_publico
tenant_id
usuario_id
cliente_id
categoria
assunto
descricao
status
prioridade
severidade
responsavel_id
origem
related_type
related_id
sla_primeira_resposta_em
sla_resolucao_em
primeira_resposta_em
resolvido_em
fechado_em
criado_em
atualizado_em
```

Relacionamentos úteis:

- pedido;
- pagamento;
- voucher;
- assinatura;
- usuário;
- tela;
- erro;
- integração.

---

## Histórico do chamado

Toda interação deve ficar registrada.

Tabela:

```txt
chamado_mensagens
- id
- chamado_id
- autor_id
- tipo_autor
- mensagem
- visibilidade
- criado_em
```

Visibilidade:

```txt
publica
interna
sistema
```

Boas práticas:

- mensagem interna não aparece para usuário;
- mensagem pública aparece no portal;
- evento automático registra mudança de status;
- anexos devem ser controlados.

---

## Anexos e evidências

Permitir anexos como:

- print;
- PDF;
- comprovante;
- planilha;
- vídeo curto;
- log técnico.

Regras:

- validar tipo;
- validar tamanho;
- armazenar com nome seguro;
- proteger acesso por permissão;
- não expor arquivo privado por URL pública;
- verificar vírus quando possível;
- registrar quem anexou.

---

## Triagem

Triagem deve responder:

- o problema é dúvida, bug ou solicitação?
- afeta um usuário ou vários?
- existe pedido, voucher ou pagamento relacionado?
- existe evidência?
- existe erro no log?
- o suporte consegue resolver?
- precisa de desenvolvimento?
- precisa de terceiro?
- qual prioridade?
- qual responsável?

---

## Atendimento

Boas práticas:

- responder com clareza;
- explicar próxima ação;
- informar prazo;
- pedir evidência objetiva quando necessário;
- evitar termos técnicos desnecessários;
- não culpar o usuário;
- manter histórico;
- não resolver por fora sem registrar.

---

## Integração com bugs e QA

Quando chamado indicar bug:

1. Suporte registra evidência.
2. QA tenta reproduzir.
3. Dev investiga logs.
4. Bug é priorizado.
5. Correção é testada.
6. Usuário é avisado.
7. Chamado é resolvido.
8. Causa raiz é documentada.

Chamado recorrente deve virar melhoria de produto.

---

## Base de conhecimento

Criar artigos para dúvidas recorrentes.

Campos:

```txt
id
titulo
categoria
conteudo
status
tags
criado_por
atualizado_em
```

Status:

```txt
rascunho
publicado
interno
arquivado
```

Boas práticas:

- linguagem simples;
- passo a passo;
- prints quando útil;
- data de atualização;
- link dentro do chamado;
- artigos internos para suporte;
- artigos públicos para usuários.

---

## Notificações de suporte

Eventos que podem notificar:

- chamado criado;
- primeira resposta;
- mudança de status;
- solicitação de informação;
- SLA perto de vencer;
- SLA vencido;
- chamado resolvido;
- chamado reaberto.

Canais:

- e-mail;
- WhatsApp;
- push;
- notificação interna.

Regras:

- evitar spam;
- agrupar atualizações quando possível;
- permitir preferências;
- registrar envio;
- não expor dados sensíveis.

---

## Painel de suporte

O painel deve mostrar:

- novos chamados;
- meus chamados;
- chamados sem responsável;
- chamados vencidos;
- chamados perto do SLA;
- chamados por categoria;
- chamados por severidade;
- chamados por cliente;
- tempo médio de resposta;
- tempo médio de resolução;
- taxa de reabertura;
- volume por período.

Ações:

- assumir chamado;
- mudar status;
- responder;
- adicionar nota interna;
- anexar evidência;
- vincular pedido/voucher;
- escalar para desenvolvimento;
- fechar chamado.

---

## Dashboards de suporte

KPIs recomendados:

- chamados abertos;
- chamados resolvidos;
- chamados vencidos;
- tempo médio de primeira resposta;
- tempo médio de resolução;
- taxa de reabertura;
- chamados por categoria;
- chamados por canal;
- chamados críticos;
- satisfação do usuário;
- volume por período.

---

## LGPD e segurança

Chamados podem conter dados pessoais.

Boas práticas:

- mascarar CPF, telefone e e-mail quando possível;
- restringir anexos sensíveis;
- não pedir senha;
- não registrar dados de cartão;
- limitar acesso ao suporte autorizado;
- registrar auditoria de consulta;
- definir retenção de chamados;
- permitir anonimização quando aplicável.

---

## Modelos de resposta

### Recebimento

```txt
Recebemos sua solicitação e ela já foi registrada. Vamos analisar as informações enviadas e retornar com a próxima ação.
```

### Pedir mais informação

```txt
Para avançarmos, precisamos de mais uma informação: informe o número do pedido ou envie um print da mensagem exibida na tela.
```

### Resolver

```txt
A solicitação foi resolvida. Fizemos a correção necessária e o recurso já está disponível para uso. Caso o problema volte a acontecer, responda este chamado.
```

### Encerrar

```txt
Como não tivemos novo retorno após a resolução, estamos encerrando este chamado. Ele poderá ser reaberto se necessário.
```

---

## Testes obrigatórios

Testar:

- criar chamado;
- responder chamado;
- nota interna não aparece para usuário;
- alterar status;
- assumir responsável;
- SLA vencido;
- anexo inválido;
- permissão de visualização;
- vínculo com pedido ou voucher;
- notificação enviada;
- reabertura;
- fechamento.

---

## Checklist da IA antes de entregar

- [ ] Chamado tem status claro.
- [ ] Chamado tem prioridade e severidade.
- [ ] Chamado tem responsável ou fila.
- [ ] Existe histórico de mensagens.
- [ ] Existe nota interna separada.
- [ ] Existe regra de SLA.
- [ ] Existe controle de anexos.
- [ ] Existe painel de atendimento.
- [ ] Existe dashboard de suporte.
- [ ] Existe notificação útil.
- [ ] Existe integração com bug/QA.
- [ ] Existe base de conhecimento, se necessário.
- [ ] LGPD foi considerada.
- [ ] Permissões foram consideradas.

---

## Saída esperada da IA

Quando esta skill for usada, a IA deve entregar:

- fluxo de suporte;
- status de chamados;
- regras de SLA;
- modelagem de tabelas;
- telas necessárias;
- painel administrativo;
- notificações;
- regras de permissão;
- casos de teste;
- modelos de resposta;
- documentação operacional.

A entrega deve sempre transformar atendimento em processo rastreável, organizado e mensurável.
