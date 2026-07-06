# Protocolo — Banco de Dados e Migration

## Objetivo

Define como criar ou alterar estrutura de banco com modelagem, SQL, migration, backup, teste e rollback.

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

- nova tabela
- novo campo
- índice
- relacionamento
- status
- migration
- seed
- alteração de schema
- backup ou restore

---

## Quando não usar

Não use este protocolo quando:

- consulta simples sem alterar estrutura
- ajuste visual
- frontend sem persistência
- regra de negócio ainda não definida

---

## Especialista principal

```txt
especialista-banco-dados.md
```

## Especialistas de apoio

```txt
especialista-seguranca-auditoria.md
especialista-backend-api-php.md
especialista-qualidade-manutencao.md
especialista-deploy-producao.md
```

---

## Skills principais

```txt
skill-dados.md
skill-mysql.md
skill-migracoes-banco.md
skill-backup-recuperacao.md
```

## Skills de apoio

```txt
skill-lgpd-privacidade.md
skill-performance.md
skill-qa.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Entender regra e entidade envolvida.
2. Modelar dados.
3. Definir campos e tipos.
4. Definir chaves, relacionamentos e índices.
5. Criar SQL ou migration.
6. Criar backup antes de alteração crítica.
7. Testar em ambiente seguro.
8. Definir rollback.
9. Atualizar backend/API se necessário.
10. Atualizar documentação do banco.

---

## Regras obrigatórias

- não alterar produção sem backup
- não criar campo sem regra clara
- não misturar padrão de nomes
- não usar ENUM como padrão sem necessidade
- não rodar migration irreversível sem confirmação
- não ignorar LGPD quando houver dados pessoais

---

## Entregáveis esperados

- modelo de dados
- SQL/migration
- plano de backup
- plano de rollback
- testes de integridade
- documentação do banco

---

## Checklist de validação

```md
- [ ] Regra da entidade está clara
- [ ] Modelo foi validado
- [ ] SQL/migration foi criada
- [ ] Backup foi previsto
- [ ] Rollback foi previsto
- [ ] Ambiente seguro foi testado
- [ ] Documentação atualizada
```

---

## Formato de resposta recomendado

```md
# Protocolo — Banco de Dados e Migration

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
