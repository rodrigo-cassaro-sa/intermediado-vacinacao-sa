# Protocolo — Documentação e Handoff

## Objetivo

Define como documentar um projeto para outra IA, programador, equipe ou versão futura conseguir continuar sem perder contexto.

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

- handoff
- documentação final
- README
- continuidade por outra IA
- projeto grande
- entrega para cliente/equipe
- registro de decisões
- changelog

---

## Quando não usar

Não use este protocolo quando:

- tarefa descartável
- ajuste mínimo sem impacto
- rascunho que não será mantido
- experimento sem continuidade

---

## Especialista principal

```txt
especialista-documentacao-memoria.md
```

## Especialistas de apoio

```txt
especialista-produto-planejamento.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-deploy-producao.md
especialista-qualidade-manutencao.md
```

---

## Skills principais

```txt
skill-documentacao-projeto.md
skill-briefing.md
skill-arquitetura.md
skill-api-rest.md
skill-mysql.md
skill-deploy-ci-cd.md
```

## Skills de apoio

```txt
skill-qa.md
skill-git.md
skill-monitoramento-observabilidade.md
skill-backup-recuperacao.md
```

---

## Fluxo obrigatório

1. Mapear objetivo do projeto.
2. Documentar stack.
3. Documentar estrutura de pastas.
4. Documentar regras de negócio.
5. Documentar telas e fluxos.
6. Documentar APIs.
7. Documentar banco.
8. Documentar deploy.
9. Documentar variáveis de ambiente.
10. Documentar testes e QA.
11. Registrar decisões técnicas.
12. Registrar pendências e próximos passos.
13. Criar changelog.

---

## Regras obrigatórias

- não deixar README genérico
- não omitir decisões importantes
- não documentar senha real
- não deixar API sem contrato
- não deixar deploy sem instrução
- não entregar sem próximos passos

---

## Entregáveis esperados

- README
- mapa de pastas
- documentação de regras
- documentação de APIs
- documentação de banco
- documentação de deploy
- changelog
- pendências
- guia para continuidade

---

## Checklist de validação

```md
- [ ] README atualizado
- [ ] Regras documentadas
- [ ] APIs documentadas
- [ ] Banco documentado
- [ ] Deploy documentado
- [ ] Variáveis sem segredos reais
- [ ] Changelog criado
- [ ] Pendências listadas
```

---

## Formato de resposta recomendado

```md
# Protocolo — Documentação e Handoff

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
