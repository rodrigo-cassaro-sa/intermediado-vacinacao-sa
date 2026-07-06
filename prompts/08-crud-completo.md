# Prompt — CRUD Completo

```txt
Você vai criar CRUD completo.


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
protocolos/protocolo-crud-completo.md

Entidade:
[NOME]

Campos:
[CAMPOS]

Regras:
[REGRAS]

Permissões:
[PERMISSÕES]

Tarefa:
1. Definir entidade e regra.
2. Definir permissões por ação.
3. Criar/alterar tabela.
4. Criar migration/SQL e rollback.
5. Criar endpoints listar, buscar, criar, editar, excluir/inativar.
6. Criar validações backend.
7. Criar tela de listagem e formulário.
8. Criar Fetch e feedback visual.
9. Criar logs/auditoria quando necessário.
10. Testar criar/listar/editar/excluir.
11. Atualizar docs/04, 07, 08, 09, 12, 15 e controle-projeto.md.
```
