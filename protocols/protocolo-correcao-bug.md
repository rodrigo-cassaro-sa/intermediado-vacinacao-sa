# Protocolo — Correção de Bug

## Objetivo

Define como corrigir erro sem improviso, identificando causa, reproduzindo o problema, corrigindo o mínimo necessário e validando regressão.

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

- bug
- erro funcional
- erro visual
- erro de API
- comportamento inesperado
- falha intermitente
- correção em sistema existente

---

## Quando não usar

Não use este protocolo quando:

- projeto novo
- refatoração sem bug
- melhoria estética sem falha
- incidente crítico de produção fora do ar, que deve usar protocolo de incidente

---

## Especialista principal

```txt
especialista-qualidade-manutencao.md
```

## Especialistas de apoio

```txt
especialista-diagnostico-incidentes.md
especialista-frontend-tecnico.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-seguranca-auditoria.md
```

---

## Skills principais

```txt
skill-qa.md
skill-erros-excecoes.md
skill-debug.md
skill-logs-auditoria.md
skill-refatoracao-code-review.md
```

## Skills de apoio

```txt
skill-frontend.md
skill-backend.md
skill-api-rest.md
skill-mysql.md
skill-performance.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Entender o bug relatado.
2. Definir comportamento esperado.
3. Reproduzir o erro.
4. Identificar camada afetada.
5. Consultar logs, debug ou evidência.
6. Corrigir o mínimo necessário.
7. Testar o caso corrigido.
8. Testar regressão dos fluxos relacionados.
9. Registrar evidência.
10. Atualizar documentação se o comportamento esperado mudou.

---

## Regras obrigatórias

- não corrigir sem reproduzir quando for possível
- não reescrever módulo inteiro por bug pontual
- não alterar regra de negócio sem autorização
- não apagar evidência antes de analisar
- não considerar pronto sem teste de regressão

---

## Entregáveis esperados

- descrição do bug
- passos para reproduzir
- causa provável
- correção aplicada
- testes feitos
- evidência
- risco residual

---

## Checklist de validação

```md
- [ ] Bug foi entendido
- [ ] Comportamento esperado foi definido
- [ ] Erro foi reproduzido ou evidência foi registrada
- [ ] Causa foi localizada
- [ ] Correção foi mínima
- [ ] Regressão foi testada
- [ ] Evidência foi registrada
```

---

## Formato de resposta recomendado

```md
# Protocolo — Correção de Bug

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
