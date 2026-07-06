# Prompt — Autenticação e Permissões

```txt
Você vai implementar ou revisar login, sessão, perfis e permissões.


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
protocolos/protocolo-autenticacao-permissoes.md

Objetivo:
[LOGIN/SESSÃO/PERMISSÃO/PERFIL]

Perfis:
[PERFIS]

Tarefa:
1. Definir perfis.
2. Definir permissões por tela e ação.
3. Atualizar docs/04-perfis-permissoes.md.
4. Modelar tabelas necessárias.
5. Implementar autenticação/sessão.
6. Implementar autorização no backend.
7. Proteger endpoints.
8. Ajustar frontend apenas como orientação visual.
9. Criar logs de acesso e ações críticas.
10. Testar permitido/negado.
11. Atualizar docs/10 e controle-projeto.md.
```
