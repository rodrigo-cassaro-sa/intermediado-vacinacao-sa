# Prompt — Solucionar Incidente em Produção

```txt
Você vai atuar em incidente de produção.


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
protocolos/protocolo-incidente-producao.md

Incidente:
[DESCREVER]

Ambiente:
[produção/homologação]

Sintoma:
[erro 500/lentidão/banco/webhook/domínio]

Última mudança conhecida:
[DESCREVER]

Tarefa:
1. Identificar impacto.
2. Verificar última mudança/deploy.
3. Verificar health check.
4. Verificar logs.
5. Verificar banco, volumes, portas, domínio e SSL.
6. Criar linha do tempo.
7. Definir contenção.
8. Decidir correção mínima ou rollback.
9. Validar recuperação.
10. Registrar incidente em docs/18-incidentes-pos-mortem.md.
11. Atualizar controle-projeto.md.

Não apagar logs. Não alterar banco sem backup. Pedir confirmação antes de rollback ou ação destrutiva.
```
