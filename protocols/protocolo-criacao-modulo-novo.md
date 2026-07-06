# Protocolo — Criação de Módulo Novo

## Objetivo

Define como adicionar uma funcionalidade grande em um sistema existente sem quebrar comportamento anterior.

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

- novo módulo em sistema existente
- nova área funcional
- nova rotina administrativa
- nova tela com várias regras
- nova funcionalidade com banco, API e frontend

---

## Quando não usar

Não use este protocolo quando:

- projeto totalmente novo
- ajuste visual simples
- correção pontual de bug
- CRUD muito simples sem regra relevante

---

## Especialista principal

```txt
especialista-produto-planejamento.md
```

## Especialistas de apoio

```txt
especialista-banco-dados.md
especialista-backend-api-php.md
especialista-frontend-tecnico.md
especialista-design-interface.md
especialista-seguranca-auditoria.md
especialista-qualidade-manutencao.md
especialista-documentacao-memoria.md
```

---

## Skills principais

```txt
skill-briefing.md
skill-perfis-permissoes.md
skill-telas.md
skill-dados.md
skill-backend.md
skill-api-rest.md
skill-frontend.md
skill-qa.md
```

## Skills de apoio

```txt
skill-ux-ui.md
skill-motion-feedback-visual.md
skill-mysql.md
skill-migracoes-banco.md
skill-seguranca.md
skill-logs-auditoria.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Entender objetivo do módulo.
2. Mapear regras de negócio.
3. Mapear perfis e permissões envolvidos.
4. Mapear telas e fluxos.
5. Mapear dados novos ou alterados.
6. Definir APIs/endpoints.
7. Definir impactos em módulos existentes.
8. Implementar backend.
9. Implementar frontend.
10. Aplicar segurança e logs.
11. Testar módulo novo.
12. Testar regressão nos módulos impactados.
13. Atualizar documentação.

---

## Regras obrigatórias

- não criar módulo sem mapear impacto no sistema atual
- não alterar comportamento existente sem autorização
- não duplicar regra já existente
- não criar endpoint sem permissão e validação backend
- não deixar documentação desatualizada

---

## Entregáveis esperados

- mapa do módulo
- regras de negócio
- telas
- modelo de dados
- APIs
- frontend integrado
- testes de regressão
- documentação atualizada

---

## Checklist de validação

```md
- [ ] Objetivo do módulo está claro
- [ ] Impacto em módulos existentes foi mapeado
- [ ] Permissões foram definidas
- [ ] Banco/API/frontend estão coerentes
- [ ] Regressão foi testada
- [ ] Documentação foi atualizada
```

---

## Formato de resposta recomendado

```md
# Protocolo — Criação de Módulo Novo

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
