# Protocolo — Pagamento, Voucher e Assinatura

## Objetivo

Define como tratar venda, pagamento, voucher, assinatura, crédito ou liberação de acesso com segurança, idempotência e confirmação backend.

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

- pagamento
- venda
- pedido
- checkout
- voucher
- assinatura
- crédito
- liberação de acesso
- webhook de pagamento
- cobrança

---

## Quando não usar

Não use este protocolo quando:

- sistema sem dinheiro ou crédito
- cadastro simples sem venda
- relatório financeiro somente leitura
- ajuste visual em tela de preço sem regra de pagamento

---

## Especialista principal

```txt
especialista-negocio-saas.md
```

## Especialistas de apoio

```txt
especialista-seguranca-auditoria.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-engajamento-integracoes.md
especialista-qualidade-manutencao.md
```

---

## Skills principais

```txt
skill-vendas-pagamentos.md
skill-seguranca.md
skill-logs-auditoria.md
skill-integracoes-webhooks.md
skill-notificacoes.md
skill-api-rest.md
skill-mysql.md
skill-qa.md
```

## Skills de apoio

```txt
skill-lgpd-privacidade.md
skill-erros-excecoes.md
skill-backend.md
skill-retencao.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Definir regra comercial.
2. Definir status do pedido/pagamento/voucher.
3. Modelar banco com rastreabilidade.
4. Criar endpoints backend.
5. Criar integração ou webhook quando existir.
6. Garantir idempotência.
7. Registrar logs e auditoria.
8. Liberar acesso apenas após confirmação backend.
9. Notificar usuário quando fizer sentido.
10. Testar casos de sucesso, falha, duplicidade e cancelamento.
11. Documentar fluxo financeiro.

---

## Regras obrigatórias

- nunca liberar venda, voucher, crédito ou assinatura apenas pelo frontend
- não confiar em retorno visual como pagamento confirmado
- webhook precisa de idempotência
- toda mudança financeira precisa de log
- não expor dados financeiros sensíveis

---

## Entregáveis esperados

- fluxo comercial
- modelo de status
- tabelas e APIs
- webhook idempotente
- logs financeiros
- testes de pagamento
- documentação

---

## Checklist de validação

```md
- [ ] Regra comercial clara
- [ ] Status definidos
- [ ] Backend confirma pagamento
- [ ] Webhook idempotente
- [ ] Logs/auditoria ativos
- [ ] Casos de falha testados
- [ ] Liberação não depende do frontend
```

---

## Formato de resposta recomendado

```md
# Protocolo — Pagamento, Voucher e Assinatura

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
