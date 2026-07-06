# Prompt — Deploy em Produção no EasyPanel

```txt
Você vai preparar ou executar deploy em produção usando EasyPanel.


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
protocolos/protocolo-deploy-producao.md

Projeto:
[NOME]

Branch/tag:
[INFORMAR]

Domínio:
[INFORMAR]

Tarefa:
1. Validar branch/tag.
2. Validar changelog/release.
3. Validar backup.
4. Validar Dockerfile/Compose.
5. Validar variáveis EasyPanel.
6. Validar MySQL/MariaDB interno.
7. Validar phpMyAdmin protegido.
8. Validar volumes persistentes.
9. Validar domínio e SSL.
10. Validar portas e exposição.
11. Rodar migrations com segurança se necessário.
12. Executar deploy, se autorizado.
13. Testar health check, login, API, banco e upload.
14. Validar logs e monitoramento.
15. Registrar versão publicada.
16. Atualizar docs/13, docs/14, docs/15 e controle-projeto.md.

Pedir confirmação antes de deploy em produção, migration, rollback ou ação destrutiva.
```
