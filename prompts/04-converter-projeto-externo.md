# Prompt — Converter Projeto Externo

```txt
Você vai analisar e converter um projeto externo para minha stack padrão.


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
protocolos/protocolo-conversao-projeto-externo.md

Especialista principal:
speciality/especialista-conversao-projeto-externo.md

Projeto de origem:
[Lovable/Bolt/v0/Bubble/React/Next/Supabase/Firebase/outro]

Local do projeto:
[CAMINHO]

Objetivo:
Converter para PHP procedural puro + MySQL/MariaDB + HTML/CSS/JS + Fetch + JSON.

Fase 1 — Análise obrigatória:
1. Inventariar arquivos e pastas.
2. Identificar stack atual.
3. Mapear telas, rotas e fluxos.
4. Mapear regras de negócio.
5. Mapear dados e autenticação.
6. Mapear APIs/webhooks/integrações.
7. Mapear estados visuais.
8. Identificar regras escondidas no frontend.
9. Gerar riscos.
10. Gerar plano de conversão por fases.

Fase 2 — Conversão, somente após análise:
1. Criar estrutura da nova stack.
2. Converter telas.
3. Converter backend.
4. Criar APIs JSON.
5. Criar banco MySQL.
6. Migrar regras críticas para backend.
7. Testar comparação antes/depois.
8. Atualizar documentação e controle-projeto.md.

Não reescrever antes de entender.
```
