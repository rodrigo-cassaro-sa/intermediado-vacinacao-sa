# Protocolo — CRUD Completo

## Objetivo

Define como criar CRUD de forma profissional, com regra, permissão, banco, API, frontend, logs, QA e documentação.

Este protocolo deve ser usado pelo `orquestrador.md` quando a tarefa exigir uma sequência de trabalho maior do que uma skill isolada.

Ele segue a arquitetura:

```txt
Prompt do usuário
  ↓
Orquestrador
  ↓
Protocolo
  ↓
Especialistas
  ↓
Skills
  ↓
Execução
  ↓
Evidência / Checklist / Documentação
```

---

## Quando usar

Use este protocolo quando houver:

- cadastro com criar, listar, editar e excluir
- gestão de entidade
- administração de registros
- CRUD com permissão
- CRUD com status, filtros ou auditoria

---

## Quando não usar

Não use este protocolo quando:

- apenas consulta sem alteração
- relatório somente leitura
- formulário sem persistência
- módulo grande que exige protocolo próprio

---

## Especialista principal

```txt
especialista-backend-api-php.md
```

## Especialistas de apoio

```txt
especialista-produto-planejamento.md
especialista-banco-dados.md
especialista-frontend-tecnico.md
especialista-design-interface.md
especialista-seguranca-auditoria.md
especialista-qualidade-manutencao.md
```

---

## Skills principais

```txt
skill-briefing.md
skill-perfis-permissoes.md
skill-dados.md
skill-mysql.md
skill-backend.md
skill-api-rest.md
skill-erros-excecoes.md
skill-frontend.md
skill-fetch.md
skill-qa.md
```

## Skills de apoio

```txt
skill-logs-auditoria.md
skill-debug.md
skill-acessibilidade.md
skill-responsividade.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Definir entidade e regra de negócio.
2. Definir permissões por ação.
3. Modelar tabela e relacionamentos.
4. Criar endpoints de listar, buscar, criar, editar, excluir ou inativar.
5. Padronizar resposta JSON.
6. Criar validações backend.
7. Criar tela de listagem.
8. Criar formulário de criação/edição.
9. Criar confirmação segura para exclusão.
10. Adicionar logs/auditoria quando necessário.
11. Testar CRUD completo.
12. Documentar endpoints, tabela e fluxo.

---

## Regras obrigatórias

- não fazer exclusão física quando soft delete for mais seguro
- não permitir edição sem permissão backend
- não deixar status sem regra
- não aceitar payload sem validação
- não deixar CRUD sem testes mínimos

---

## Entregáveis esperados

- modelo de dados
- endpoints CRUD
- telas CRUD
- validações
- logs/auditoria
- testes
- documentação

---

## Checklist de validação

```md
- [ ] Permissões por ação definidas
- [ ] Tabela criada com campos padrão
- [ ] API segue JSON oficial
- [ ] Frontend trata estados
- [ ] Backend valida dados
- [ ] Exclusão é segura
- [ ] QA validou criar/listar/editar/excluir
- [ ] Documentação atualizada
```

---

## Formato de resposta recomendado

```md
# Protocolo — CRUD Completo

## 1. Entendimento do pedido

## 2. Especialistas convocados

## 3. Skills aplicadas

## 4. Fluxo executado

## 5. Decisões tomadas

## 6. Riscos e cuidados

## 7. Entregáveis

## 8. Evidências

## 9. Checklist final
```



---

## Regra final

Este protocolo não substitui as skills.

Ele organiza a ordem correta de uso dos especialistas e das skills para reduzir erro, retrabalho, improviso e decisão técnica solta.
