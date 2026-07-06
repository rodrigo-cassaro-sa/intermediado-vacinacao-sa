# Skill: Painel Administrativo e Operação de SaaS

## Objetivo da skill

Esta skill orienta uma IA a planejar, criar e revisar **painéis administrativos e ferramentas operacionais** para SaaS, sistemas internos e aplicativos, incluindo usuários, permissões, suporte, logs, configurações, auditoria, relatórios, debug controlado e ações administrativas.

O foco é permitir que o sistema seja operado, monitorado e corrigido sem depender de acesso direto ao banco ou ao servidor.

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

Esta skill define o painel administrativo e as ferramentas operacionais para operar o SaaS com segurança.

Ela deve focar em telas administrativas, usuários, configurações, ações críticas, modo manutenção, consulta de logs, suporte interno, relatórios administrativos e ferramentas de operação.

Ela não deve substituir:

- `skill-perfis-permissoes.md` para matriz completa de permissões;
- `skill-logs-auditoria.md` para estrutura oficial de logs e auditoria;
- `skill-debug.md` para debug visual administrativo;
- `skill-suporte-atendimento-sla.md` para fluxo completo de chamados e SLA;
- `skill-relatorios-bi-dashboard.md` para BI e indicadores;
- `skill-multitenant-workspaces.md` para isolamento entre tenants.

Esta skill responde "como a equipe opera, configura, acompanha e corrige o SaaS sem acessar banco ou servidor diretamente?".

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

## Regra para admin global em SaaS

Quando o sistema for multitenant, o painel admin deve separar:

- admin global da plataforma;
- admin do tenant;
- suporte interno;
- financeiro;
- auditor.

Admin global não deve acessar dados de cliente sem necessidade operacional.

Quando acessar, registrar:

- quem acessou;
- tenant acessado;
- motivo;
- data e hora;
- ação executada;
- dados visualizados ou alterados, quando aplicável.

Acesso global não deve ignorar LGPD, logs e permissões.

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa gerente de produto técnico, desenvolvedora sênior e especialista em operação de sistemas.

A IA deve pensar em:

- administrador;
- suporte;
- usuários;
- permissões;
- configurações;
- logs;
- auditoria;
- relatórios;
- ações críticas;
- segurança operacional.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-permissoes.md
skill-debug.md
skill-logs-auditoria.md
skill-seguranca.md
skill-lgpd-privacidade.md
skill-telas.md
```

---

## Princípio central

```txt
SaaS profissional precisa de uma área administrativa que permita operar o sistema com segurança, rastreabilidade e mínimo acesso direto ao código ou banco.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Objetivo do admin

O painel administrativo deve permitir operar o sistema.

Funções comuns:

- consultar usuários;
- alterar status;
- gerenciar permissões;
- ver logs;
- consultar auditoria;
- configurar sistema;
- acompanhar integrações;
- reprocessar falhas;
- consultar métricas;
- dar suporte ao usuário;
- bloquear abusos;
- acompanhar saúde do sistema.

---

# 2. Perfis administrativos

Nem todo admin deve poder tudo.

Perfis possíveis:

```txt
super_admin = controle total restrito
admin = gestão operacional
suporte = consulta e ações limitadas
financeiro = pagamentos/cobranças
qa = debug e teste em ambientes permitidos
desenvolvedor = logs técnicos e ferramentas de diagnóstico
```

Cada perfil deve ter permissões explícitas.

---

# 3. Dashboard admin

Dashboard deve mostrar visão operacional.

Cards úteis:

- usuários ativos;
- novos usuários;
- erros recentes;
- integrações com falha;
- tarefas pendentes;
- notificações com erro;
- uso por período;
- status do sistema;
- eventos críticos de segurança.

O dashboard deve priorizar ação, não apenas números decorativos.

---

# 4. Gestão de usuários

Tela de usuários deve permitir:

- buscar;
- filtrar por status/perfil;
- visualizar detalhes;
- ativar/inativar;
- redefinir senha com fluxo seguro;
- alterar perfil;
- ver histórico de acesso;
- ver auditoria do usuário;
- bloquear usuário suspeito.

Ações críticas exigem confirmação e auditoria.

---

# 5. Configurações do sistema

Configurações devem ficar centralizadas.

Exemplos:

- nome do sistema;
- e-mail remetente;
- limites;
- templates;
- flags de funcionalidades;
- parâmetros de notificação;
- integrações;
- modo manutenção;
- termos/política ativos.

Configuração crítica precisa de permissão alta e log de alteração.

---

# 6. Logs e auditoria no admin

Admin deve conseguir investigar sem acessar servidor.

Telas recomendadas:

- logs técnicos;
- auditoria de ações;
- histórico de alteração;
- tentativas de login;
- eventos de integração;
- fila de notificações;
- fila de webhooks.

Filtros:

- período;
- usuário;
- evento;
- entidade;
- status;
- request_id.

Dados sensíveis devem ser mascarados.

---

# 7. Ações críticas

Ações administrativas críticas devem ter controle extra.

Exemplos:

- excluir registro;
- alterar permissão;
- inativar conta;
- reprocessar integração;
- exportar dados;
- entrar em modo manutenção;
- ativar debug em produção controlada.

Regras:

- confirmação explícita;
- motivo obrigatório quando necessário;
- auditoria;
- permissão específica;
- impossibilidade de alterar a si mesmo em certas ações críticas.

---

# 8. Suporte ao usuário

Admin operacional deve ajudar suporte.

Recursos úteis:

- buscar usuário por nome/e-mail;
- ver status da conta;
- ver últimos acessos;
- ver últimas ações;
- consultar erros relacionados;
- reenviar convite ou confirmação;
- resetar sessão quando necessário;
- abrir histórico de solicitações.

Evitar mostrar senha, token ou dados sensíveis desnecessários.

---

# 9. Simulação/impersonate com segurança

Alguns sistemas precisam simular acesso do usuário para suporte.

Se existir, aplicar regras fortes:

- somente perfil autorizado;
- motivo obrigatório;
- tempo limitado;
- banner visível informando simulação;
- auditoria completa;
- bloquear ações sensíveis durante simulação quando possível;
- nunca capturar senha do usuário.

Sem esses controles, evitar implementar impersonate.

---

# 10. Relatórios administrativos

Relatórios admin devem apoiar decisão.

Exemplos:

- uso por período;
- crescimento de usuários;
- tarefas concluídas;
- erros por módulo;
- integrações com mais falha;
- notificações enviadas;
- exportações realizadas;
- usuários inativos.

Relatórios com dados pessoais devem respeitar LGPD e permissões.

---

# 11. Modo manutenção

O admin pode precisar colocar sistema em manutenção.

Definir:

- quem pode ativar;
- mensagem exibida;
- exceções para admins/desenvolvedores;
- registro de auditoria;
- tempo previsto;
- desativação segura.

Nunca deixar usuários comuns executarem ações durante manutenção crítica.

---

# Checklist obrigatório antes de concluir

- [ ] Perfis administrativos foram definidos.
- [ ] Permissões são específicas.
- [ ] Dashboard mostra dados acionáveis.
- [ ] Usuários podem ser gerenciados com auditoria.
- [ ] Configurações críticas têm log.
- [ ] Logs/auditoria são consultáveis.
- [ ] Ações críticas exigem confirmação.
- [ ] Suporte não vê dados sensíveis sem necessidade.
- [ ] Exportações são controladas.
- [ ] Modo debug/manutenção é restrito.

---

# Modelo de entrega esperado

Ao planejar admin operacional, entregue:

1. Perfis administrativos.
2. Permissões por perfil.
3. Telas do admin.
4. Ações disponíveis.
5. Dados exibidos.
6. Logs/auditoria.
7. Ações críticas e confirmações.
8. Relatórios.
9. Regras de segurança.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
