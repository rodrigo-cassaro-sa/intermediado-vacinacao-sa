# Protocolo — Incidente em Produção

## Objetivo

Define como agir quando sistema estiver fora do ar, lento, com erro 500, falha de banco, webhook quebrado ou outro problema crítico em produção.

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

- sistema fora do ar
- erro 500
- lentidão grave
- falha de banco
- falha de webhook
- erro após deploy
- rollback
- incidente em produção

---

## Quando não usar

Não use este protocolo quando:

- bug comum sem impacto crítico
- homologação
- desenvolvimento local
- refatoração planejada

---

## Especialista principal

```txt
especialista-diagnostico-incidentes.md
```

## Especialistas de apoio

```txt
especialista-deploy-producao.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-seguranca-auditoria.md
especialista-qualidade-manutencao.md
especialista-release-versionamento.md
```

---

## Skills principais

```txt
skill-monitoramento-observabilidade.md
skill-logs-auditoria.md
skill-erros-excecoes.md
skill-performance.md
skill-backup-recuperacao.md
skill-deploy-ci-cd.md
```

## Skills de apoio

```txt
skill-mysql.md
skill-backend.md
skill-api-rest.md
skill-qa.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Identificar impacto e urgência.
2. Verificar última mudança/deploy.
3. Consultar health check.
4. Consultar logs de aplicação, servidor, banco e integração.
5. Verificar banco e volumes.
6. Verificar portas, domínio e SSL quando aplicável.
7. Definir contenção imediata.
8. Decidir correção rápida ou rollback.
9. Executar ação mínima segura.
10. Validar recuperação.
11. Registrar linha do tempo.
12. Criar plano de prevenção.

---

## Regras obrigatórias

- não apagar logs antes da análise
- não alterar banco sem backup
- não fazer deploy de emergência sem registrar versão
- não corrigir no escuro
- não ignorar segurança durante incidente
- não encerrar sem relatório

---

## Entregáveis esperados

- diagnóstico inicial
- linha do tempo
- causa provável
- ação de contenção
- rollback ou correção
- validação pós-incidente
- relatório de incidente
- ações preventivas

---

## Checklist de validação

```md
- [ ] Impacto identificado
- [ ] Última mudança verificada
- [ ] Logs analisados
- [ ] Health check avaliado
- [ ] Ação de contenção definida
- [ ] Rollback considerado
- [ ] Sistema validado após correção
- [ ] Relatório criado
```

---

## Formato de resposta recomendado

```md
# Protocolo — Incidente em Produção

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
