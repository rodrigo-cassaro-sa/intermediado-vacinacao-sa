# Protocolo — Refatoração Segura

## Objetivo

Define como melhorar código sem alterar comportamento de negócio sem autorização.

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

- refatoração
- limpeza de código
- organização de funções
- redução de duplicidade
- melhoria de legibilidade
- code review
- manutenção sem mudança funcional

---

## Quando não usar

Não use este protocolo quando:

- bug urgente
- nova funcionalidade
- mudança de regra de negócio
- reescrita total sem justificativa

---

## Especialista principal

```txt
especialista-qualidade-manutencao.md
```

## Especialistas de apoio

```txt
especialista-documentacao-memoria.md
especialista-backend-api-php.md
especialista-frontend-tecnico.md
especialista-banco-dados.md
```

---

## Skills principais

```txt
skill-refatoracao-code-review.md
skill-sintaxe.md
skill-qa.md
skill-documentacao-projeto.md
```

## Skills de apoio

```txt
skill-php.md
skill-js.md
skill-frontend.md
skill-backend.md
skill-mysql.md
skill-performance.md
```

---

## Fluxo obrigatório

1. Mapear comportamento atual.
2. Definir limite da refatoração.
3. Identificar arquivos impactados.
4. Criar teste/checklist antes da alteração.
5. Refatorar em partes pequenas.
6. Evitar mudar contrato de API.
7. Testar comportamento depois.
8. Comparar antes/depois.
9. Documentar o que mudou.
10. Registrar riscos residuais.

---

## Regras obrigatórias

- refatoração não muda regra de negócio
- não alterar contrato público sem autorização
- não misturar refatoração com feature nova
- não apagar código sem entender impacto
- não considerar pronto sem teste comparativo

---

## Entregáveis esperados

- plano de refatoração
- lista de arquivos impactados
- checklist antes/depois
- diff controlado
- testes executados
- documentação atualizada

---

## Checklist de validação

```md
- [ ] Comportamento atual foi mapeado
- [ ] Escopo da refatoração está claro
- [ ] Arquivos impactados foram listados
- [ ] Antes/depois foi comparado
- [ ] Regressão foi testada
- [ ] Documentação foi atualizada
```

---

## Formato de resposta recomendado

```md
# Protocolo — Refatoração Segura

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
