# Prompt — Planejar Release e Versionamento

```txt
Você vai planejar uma release com rastreabilidade.


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


Release:
[VERSÃO]

Ambiente:
[homologação/produção]

Tarefa:
1. Listar mudanças desde a última versão.
2. Definir versão/tag.
3. Validar branch de origem.
4. Validar QA/homologação.
5. Atualizar changelog.
6. Preparar checklist de release.
7. Definir rollback.
8. Atualizar controle-projeto.md.

Não publicar sem changelog, QA, branch/tag correta, rollback e registro da versão.
```
