# Protocolo — Homologação

## Objetivo

Define como validar funcionalidade, módulo ou projeto com checklist por tela, perfil, regra, API e fluxo antes da produção.

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

- homologação
- validação com cliente/equipe
- antes de produção
- teste de módulo novo
- teste de release
- aprovação final

---

## Quando não usar

Não use este protocolo quando:

- teste técnico local simples
- rascunho sem comportamento funcional
- deploy emergencial de incidente
- mudança sem impacto no usuário

---

## Especialista principal

```txt
especialista-qualidade-manutencao.md
```

## Especialistas de apoio

```txt
especialista-produto-planejamento.md
especialista-design-interface.md
especialista-backend-api-php.md
especialista-seguranca-auditoria.md
especialista-release-versionamento.md
especialista-documentacao-memoria.md
```

---

## Skills principais

```txt
skill-qa.md
skill-documentacao-projeto.md
skill-logs-auditoria.md
```

## Skills de apoio

```txt
skill-telas.md
skill-api-rest.md
skill-seguranca.md
skill-performance.md
skill-deploy-ci-cd.md
```

---

## Fluxo obrigatório

1. Definir escopo da homologação.
2. Listar telas e fluxos a validar.
3. Listar perfis envolvidos.
4. Listar regras críticas.
5. Listar APIs e integrações envolvidas.
6. Criar checklist de testes.
7. Executar homologação.
8. Registrar bugs e ajustes.
9. Revalidar correções.
10. Obter aprovação final.
11. Preparar release/produção.

---

## Regras obrigatórias

- não homologar sem escopo claro
- não aprovar sem testar perfis diferentes
- não ignorar bugs críticos
- não enviar produção sem aprovação ou justificativa
- não perder registro dos ajustes

---

## Entregáveis esperados

- plano de homologação
- checklist por tela
- checklist por perfil
- lista de bugs
- evidências
- aprovação final
- pendências

---

## Checklist de validação

```md
- [ ] Escopo definido
- [ ] Telas testadas
- [ ] Perfis testados
- [ ] Regras críticas testadas
- [ ] Bugs registrados
- [ ] Correções revalidadas
- [ ] Aprovação final registrada
```

---

## Formato de resposta recomendado

```md
# Protocolo — Homologação

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
