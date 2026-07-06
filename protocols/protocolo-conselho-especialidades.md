# Protocolo — Conselho de Especialidades

## Objetivo

Define como o orquestrador deve convocar especialistas para revisar decisões importantes antes de executar uma tarefa crítica ou multidisciplinar.

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

- decisão afeta várias áreas
- existe risco de segurança, dados, pagamento ou produção
- existe dúvida de arquitetura
- haverá mudança relevante de regra de negócio
- haverá conversão de projeto externo
- haverá deploy em produção
- haverá alteração de banco de dados importante

---

## Quando não usar

Não use este protocolo quando:

- tarefa simples e isolada
- correção de texto
- ajuste visual sem impacto técnico
- implementação já especificada e sem risco

---

## Especialista principal

```txt
orquestrador.md
```

## Especialistas de apoio

```txt
especialista-produto-planejamento.md
especialista-design-interface.md
especialista-frontend-tecnico.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-seguranca-auditoria.md
especialista-negocio-saas.md
especialista-engajamento-integracoes.md
especialista-qualidade-manutencao.md
especialista-deploy-producao.md
```

---

## Skills principais

```txt
README.md
orquestrador.md
```

## Skills de apoio

```txt
skills específicas conforme especialistas convocados
```

---

## Fluxo obrigatório

1. Entender a decisão que precisa ser tomada.
2. Classificar risco e impacto.
3. Escolher somente especialistas relevantes.
4. Pedir parecer objetivo de cada especialista.
5. Identificar conflitos entre pareceres.
6. Aplicar prioridade: segurança, regra de negócio, dados, API, QA, deploy e UX.
7. Consolidar decisão final.
8. Definir ordem de execução.
9. Definir checklist obrigatório.
10. Indicar se precisa confirmação explícita do usuário.

---

## Regras obrigatórias

- não convocar todos os especialistas por padrão
- não permitir que um especialista substitua outro mais específico
- conflitos devem ser resolvidos pelo orquestrador
- risco de segurança ou dados sempre tem prioridade
- decisão final deve ser objetiva e executável

---

## Entregáveis esperados

- lista de especialistas convocados
- parecer por especialista
- conflitos encontrados
- decisão consolidada
- ordem de execução
- checklist de validação

---

## Checklist de validação

```md
- [ ] Especialistas relevantes foram convocados
- [ ] Especialistas desnecessários foram evitados
- [ ] Riscos foram identificados
- [ ] Conflitos foram resolvidos
- [ ] Decisão final foi consolidada pelo orquestrador
- [ ] Ordem de execução ficou clara
```

---

## Formato de resposta recomendado

```md
# Protocolo — Conselho de Especialidades

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
