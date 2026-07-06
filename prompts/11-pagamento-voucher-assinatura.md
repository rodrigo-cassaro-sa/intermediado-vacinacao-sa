# Prompt — Pagamento, Voucher ou Assinatura

```txt
Você vai implementar ou revisar venda, pagamento, voucher, assinatura, crédito ou liberação de acesso.


## Leitura obrigatória antes de executar

Leia nesta ordem:

```txt
1. orquestrador.md
2. controle-projeto.md
3. docs/README.md ou doc/README.md
4. docs/00-controle-consistencia-projeto.md ou doc/00-controle-consistencia-projeto.md
5. protocolo indicado neste prompt
6. especialista principal e especialistas de apoio
7. skills indicadas
8. documento específico da área dentro de docs/ ou doc/
```

## Regras globais

- Usar PHP procedural puro, MySQL/MariaDB, HTML, CSS, JavaScript puro, Fetch API e JSON.
- Não usar framework sem autorização.
- Não converter para orientação a objetos sem autorização.
- Não alterar fora do escopo.
- Não apagar arquivo, dado, tabela ou regra sem confirmação.
- Toda regra crítica deve ser validada no backend.
- Frontend orienta experiência, mas não decide permissão, pagamento, status ou segurança.
- Atualizar `controle-projeto.md` ao final quando houver mudança relevante.
- Atualizar documentos em `docs/` ou `doc/` quando houver mudança em regra, tela, banco, API, segurança, deploy ou comportamento.

## Resposta obrigatória antes de mexer em arquivos

```md
## Entendimento

## Classificação
- Tipo de tarefa:
- Complexidade:

## Protocolo escolhido

## Especialistas convocados
- Principal:
- Apoio:

## Skills usadas
- Principais:
- Apoio:

## Arquivos que pretende criar/alterar

## Riscos e confirmações necessárias
```

## Entrega obrigatória ao final

```md
## Resultado

## Arquivos criados/alterados

## Testes / checklist

## Documentação atualizada

## Atualização do controle-projeto.md

## Próximo passo recomendado
```


Protocolo obrigatório:
protocolos/protocolo-pagamento-voucher-assinatura.md

Fluxo:
[DESCREVER]

Tarefa:
1. Definir regra comercial.
2. Definir status oficiais.
3. Modelar banco.
4. Criar endpoints backend.
5. Criar webhook, se aplicável.
6. Garantir idempotência.
7. Registrar logs/auditoria.
8. Liberar acesso apenas com confirmação backend.
9. Notificar usuário quando fizer sentido.
10. Testar sucesso, falha, duplicidade e cancelamento.
11. Atualizar docs e controle-projeto.md.

Nunca liberar pagamento, voucher, crédito ou assinatura pelo frontend.
```
