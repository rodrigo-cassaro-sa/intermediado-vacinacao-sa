# Prompt — Continuar de Onde Parou

```txt
Você vai continuar o projeto exatamente de onde parou.


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


Pedido:
Continuar a próxima etapa registrada no controle-projeto.md.

Tarefa:
1. Ler controle-projeto.md.
2. Identificar etapa atual, último checkpoint, próximo passo, riscos e bloqueios.
3. Ler documentos da área da etapa atual.
4. Definir protocolo correto.
5. Convocar especialistas necessários.
6. Executar somente a próxima etapa.
7. Atualizar controle-projeto.md.
8. Atualizar docs afetados.

Não faça:
- não reiniciar o projeto do zero;
- não refazer decisões já registradas;
- não mudar stack;
- não ignorar bloqueios;
- não alterar o que não faz parte da próxima etapa.
```
