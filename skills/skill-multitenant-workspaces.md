# Skill: Multiempresa, Multitenant e Workspaces para SaaS

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa arquiteta sênior de SaaS multiempresa, multitenant e workspaces, criando sistemas onde vários clientes, empresas, unidades, equipes ou organizações usam a mesma aplicação com separação segura de dados.

O foco é evitar vazamento entre clientes, bagunça de permissões, mistura de configurações, relatórios incorretos e problemas de escala.

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
```

Esta skill deve ser usada junto com segurança, permissões, autenticação, dados, MySQL, logs, LGPD, API REST, backend, QA e documentação.

---

## Limite desta skill

Esta skill define arquitetura SaaS multiempresa, multitenant e workspaces.

Ela deve focar em isolamento de dados, tenant ativo, workspace ativo, vínculo de usuários, contexto, permissões por tenant, relatórios por cliente e segurança contra vazamento entre organizações.

Ela não deve substituir:

- `skill-perfis-permissoes.md` para matriz completa de papéis e permissões;
- `skill-seguranca.md` para segurança técnica geral;
- `skill-dados.md` para modelagem completa das entidades;
- `skill-vendas-pagamentos.md` para regras comerciais, pedidos e pagamentos;
- `skill-relatorios-bi-dashboard.md` para indicadores e dashboards;
- `skill-admin-operacional.md` para operação administrativa.

Esta skill responde "este dado pertence a qual cliente, empresa, unidade ou workspace, e quem pode acessá-lo?".

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

Ao usar esta skill, a IA deve agir como uma pessoa sênior em arquitetura SaaS.

A IA deve pensar em:

- isolamento de dados;
- organizações;
- workspaces;
- usuários;
- papéis;
- permissões;
- convites;
- plano contratado;
- limites de uso;
- configurações por cliente;
- relatórios por tenant;
- auditoria;
- suporte administrativo;
- segurança.

A IA deve evitar qualquer solução onde um usuário possa acessar dados de outra empresa por erro de filtro, URL, ID, API ou permissão.

---

## Princípio central

```txt
Em SaaS multiempresa, todo dado precisa pertencer claramente a um tenant.
```

Antes de consultar, gravar, editar ou excluir qualquer registro, o backend deve saber:

- quem é o usuário;
- a qual tenant ele pertence;
- qual workspace está ativo;
- qual permissão ele tem;
- se o registro pertence ao mesmo tenant;
- se a ação é permitida naquele contexto.

---

## Quando usar esta skill

Use esta skill quando o sistema tiver:

- várias empresas usando o mesmo sistema;
- clientes separados;
- unidades;
- filiais;
- organizações;
- workspaces;
- times;
- usuários convidados;
- permissões por empresa;
- planos diferentes por cliente;
- dados isolados;
- dashboards por cliente;
- relatórios consolidados por admin global;
- suporte que acessa clientes diferentes.

---

## Quando não usar esta skill

Não use esta skill para:

- sistema de uso único por uma empresa só;
- site institucional;
- app simples sem organizações;
- painel interno com banco único e sem separação por cliente.

Nesses casos, use permissões simples.

---

## Conceitos principais

```txt
Tenant = cliente, empresa ou organização dona dos dados.
Workspace = espaço de trabalho dentro de um tenant.
Usuário = pessoa autenticada.
Membro = vínculo entre usuário e tenant/workspace.
Papel = função do usuário naquele contexto.
Permissão = ação específica permitida.
```

Exemplo:

```txt
Usuário Rodrigo pode ser admin na Empresa A e apenas visualizador na Empresa B.
```

Por isso, permissão não deve ficar apenas no usuário. Deve ficar no vínculo.

---

## Modelos possíveis de multiempresa

### 1. Banco único com `tenant_id`

Todos os clientes usam o mesmo banco e as tabelas possuem `tenant_id`.

Vantagens:

- simples de manter;
- custo menor;
- relatórios globais mais fáceis;
- deploy único.

Riscos:

- vazamento se esquecer filtro;
- consultas precisam ser muito bem protegidas;
- backup por cliente é mais complexo.

Recomendado para muitos SaaS pequenos e médios.

---

### 2. Banco separado por tenant

Cada cliente possui banco próprio.

Vantagens:

- isolamento forte;
- backup por cliente mais fácil;
- customizações mais seguras.

Riscos:

- operação mais complexa;
- migrações mais difíceis;
- custo maior;
- relatórios globais mais complicados.

Recomendado quando há exigência forte de isolamento ou clientes grandes.

---

### 3. Híbrido

Clientes comuns ficam em banco compartilhado e clientes especiais ficam em banco separado.

Vantagens:

- equilíbrio entre custo e isolamento.

Riscos:

- complexidade maior;
- código precisa lidar com múltiplos modos.

Usar apenas quando houver necessidade real.

---

## Recomendação padrão

Para projetos PHP + MySQL simples ou médios, usar inicialmente:

```txt
Banco único compartilhado + tenant_id obrigatório nas tabelas de negócio
```

Com regras rígidas no backend para nunca consultar dados sem filtrar tenant.

---

## Tabelas principais

```txt
tenants
workspaces
usuarios
tenant_membros
workspace_membros
papeis
permissoes
papel_permissoes
convites
planos
assinaturas
tenant_configuracoes
logs_auditoria
```

Nem todo sistema precisa de workspace. Quando não precisar, use apenas tenant.

---

## Tabela `tenants`

Campos recomendados:

```txt
id
uuid_publico
nome
slug
documento
status
plano_id
criado_em
atualizado_em
excluido_em
```

Status possíveis:

```txt
ativo
suspenso
bloqueado
cancelado
trial
inadimplente
```

---

## Vínculo de usuário com tenant

Não coloque apenas `tenant_id` direto em `usuarios` se o mesmo usuário puder participar de mais de uma empresa.

Use tabela de membros:

```txt
tenant_membros
- id
- tenant_id
- usuario_id
- papel_id
- status
- criado_em
- atualizado_em
```

Status:

```txt
ativo
pendente
suspenso
removido
```

---

## Workspaces

Workspace é útil quando uma empresa tem áreas internas, projetos, unidades ou equipes.

Exemplos:

- unidade Marília;
- unidade São Paulo;
- projeto Cliente X;
- equipe Financeiro;
- turma de alunos;
- evento específico.

Campos:

```txt
id
tenant_id
nome
slug
status
criado_em
atualizado_em
```

Regra:

```txt
workspace sempre pertence a um tenant.
```

---

## Contexto ativo

Quando o usuário loga, o sistema deve definir contexto.

Exemplo de contexto:

```json
{
  "usuario_id": 10,
  "tenant_id": 3,
  "workspace_id": 8,
  "papel": "admin",
  "permissoes": ["pedidos.ver", "pedidos.criar"]
}
```

O contexto deve ser validado no backend e não apenas no frontend.

---

## Troca de tenant ou workspace

Se o usuário pertence a mais de um tenant, permitir seleção.

Regras:

- listar apenas tenants permitidos;
- registrar tenant ativo na sessão;
- validar mudança no backend;
- limpar caches e filtros da tela;
- atualizar permissões;
- impedir acesso por URL antiga;
- registrar auditoria, se necessário.

---

## Regras obrigatórias de consulta

Toda consulta de tabela de negócio deve filtrar por `tenant_id`.

Exemplo correto:

```sql
SELECT *
FROM pedidos
WHERE tenant_id = :tenant_id
AND id = :id;
```

Evite:

```sql
SELECT *
FROM pedidos
WHERE id = :id;
```

Mesmo que o ID seja difícil, o filtro por tenant é obrigatório.

---

## Regra para insert

Todo insert em tabela de negócio deve gravar `tenant_id`.

Exemplo:

```sql
INSERT INTO pedidos (tenant_id, cliente_id, status, valor)
VALUES (:tenant_id, :cliente_id, :status, :valor);
```

O `tenant_id` deve vir da sessão/contexto validado, não do formulário do usuário.

---

## Regra para update e delete

Todo update e delete deve filtrar por tenant.

```sql
UPDATE pedidos
SET status = :status
WHERE id = :id
AND tenant_id = :tenant_id;
```

Nunca fazer update apenas por ID em sistema multitenant.

---

## Permissões por contexto

Permissão deve considerar:

- usuário;
- tenant;
- workspace;
- papel;
- ação;
- recurso.

Exemplo:

```txt
Usuário pode editar pedidos no tenant A, mas apenas visualizar no tenant B.
```

Permissões recomendadas:

```txt
usuarios.ver
usuarios.criar
usuarios.editar
usuarios.remover
pedidos.ver
pedidos.criar
pedidos.editar
pedidos.cancelar
relatorios.ver
financeiro.ver
configuracoes.editar
```

---

## Admin global

Admin global é diferente de admin do tenant.

```txt
Admin global = suporte ou equipe dona do SaaS.
Admin do tenant = cliente administrador da própria empresa.
```

Regras:

- admin global deve ter acesso controlado;
- acesso a tenant de cliente deve gerar auditoria;
- dados sensíveis devem ser mascarados quando possível;
- ações críticas devem exigir justificativa;
- nunca confundir permissão global com permissão do cliente.

---

## Convites

Fluxo recomendado:

1. Admin do tenant convida usuário por e-mail.
2. Sistema cria convite pendente.
3. Usuário aceita convite.
4. Sistema cria vínculo `tenant_membros`.
5. Usuário recebe papel e permissões.
6. Auditoria registra convite e aceite.

Campos:

```txt
id
tenant_id
email
papel_id
token
status
expira_em
aceito_em
criado_por
criado_em
```

Status:

```txt
pendente
aceito
expirado
cancelado
```

---

## Configurações por tenant

Cada tenant pode ter configurações próprias.

Exemplos:

- nome exibido;
- logo;
- cores;
- timezone;
- regras de notificação;
- limites de uso;
- dados fiscais;
- preferências de relatório;
- integrações.

Tabela:

```txt
tenant_configuracoes
- tenant_id
- chave
- valor
- tipo
```

Cuidado para não permitir customização que quebre regras globais de segurança.

---

## Planos e limites

Para SaaS, tenant pode ter plano.

Exemplos de limites:

- número de usuários;
- número de workspaces;
- número de pedidos;
- armazenamento;
- integrações;
- relatórios avançados;
- suporte prioritário.

A IA deve separar:

```txt
plano contratado
limite permitido
uso atual
bloqueio ou aviso
```

---

## Relatórios multitenant

Relatórios devem respeitar contexto.

- Usuário comum vê apenas seu tenant.
- Gestor de unidade vê apenas sua unidade/workspace.
- Admin do tenant vê dados do tenant.
- Admin global pode ver consolidado, se autorizado.

Toda query de relatório deve aplicar filtros de tenant e permissão.

---

## Logs e auditoria

Registrar:

- criação de tenant;
- alteração de configuração;
- convite enviado;
- convite aceito;
- troca de papel;
- acesso de admin global;
- exportação de dados;
- alteração de plano;
- bloqueio ou suspensão;
- tentativa de acesso indevido.

Logs devem incluir:

```txt
usuario_id
tenant_id
workspace_id
acao
recurso
recurso_id
ip
user_agent
criado_em
```

---

## LGPD e privacidade

Em ambiente multiempresa, privacidade é crítica.

Boas práticas:

- dados de um tenant não podem aparecer para outro;
- exportação deve ser restrita;
- suporte deve ter acesso justificado;
- logs devem evitar dados sensíveis;
- exclusão/anomização deve considerar obrigações legais;
- dados agregados globais não devem identificar cliente sem permissão.

---

## Testes obrigatórios

Testar:

- usuário do tenant A não acessa dados do tenant B;
- troca de tenant limpa contexto anterior;
- update não altera registro de outro tenant;
- delete não remove registro de outro tenant;
- relatório filtra corretamente;
- admin global exige permissão;
- convite não permite entrar em tenant errado;
- API não aceita `tenant_id` forjado no payload;
- exportação respeita tenant.

Modelo:

```md
ID: MT-001
Título: Bloquear acesso cruzado entre tenants
Passos:
1. Logar como usuário do tenant A
2. Tentar acessar URL de pedido do tenant B
Resultado esperado:
Sistema bloqueia acesso e registra tentativa sem expor dados do tenant B.
```

---

## Erros comuns

Evitar:

- salvar `tenant_id` vindo do frontend;
- consultar dados apenas por ID;
- usar permissão global para tudo;
- permitir admin global sem auditoria;
- misturar configurações de tenants;
- cache sem chave por tenant;
- relatório sem filtro de tenant;
- exportação sem permissão;
- URL previsível com dados de outro cliente;
- logs expondo dados sensíveis.

---

## Checklist da IA antes de entregar

- [ ] Existe definição clara de tenant.
- [ ] Existe definição clara de workspace, se necessário.
- [ ] Tabelas de negócio possuem `tenant_id`.
- [ ] Inserts usam tenant do contexto validado.
- [ ] Selects filtram por tenant.
- [ ] Updates e deletes filtram por tenant.
- [ ] Permissões são por vínculo, não só por usuário.
- [ ] Admin global é separado de admin do tenant.
- [ ] Convites têm token, expiração e auditoria.
- [ ] Relatórios respeitam tenant.
- [ ] Exportações respeitam permissão.
- [ ] Logs registram ações críticas.
- [ ] LGPD foi considerada.
- [ ] Testes de acesso cruzado foram definidos.

---

## Saída esperada da IA

Quando esta skill for usada, a IA deve entregar:

- modelo de tenants e workspaces;
- tabelas recomendadas;
- regras de permissão;
- estratégia de isolamento;
- fluxo de login e contexto;
- regras de consulta SQL;
- endpoints necessários;
- casos de teste;
- checklist de segurança;
- documentação para desenvolvimento.

A entrega deve sempre priorizar isolamento, clareza e segurança.
