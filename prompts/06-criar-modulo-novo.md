# Prompt — Criar Módulo Novo

```txt
Você vai criar um módulo novo em sistema existente.


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
protocolos/protocolo-criacao-modulo-novo.md

Módulo:
[NOME]

Objetivo:
[OBJETIVO]

Regras:
[REGRAS]

Perfis envolvidos:
[PERFIS]

Tarefa:
1. Mapear regra e objetivo.
2. Mapear permissões.
3. Mapear telas.
4. Mapear dados.
5. Definir endpoints.
6. Criar/alterar arquivos.
7. Implementar backend/API.
8. Implementar frontend.
9. Adicionar logs/auditoria quando necessário.
10. Testar fluxo e regressão.
11. Atualizar docs/02, 04, 07, 08, 09, 12, 15 e controle-projeto.md.

Não alterar módulos existentes fora do impacto mapeado.
```
