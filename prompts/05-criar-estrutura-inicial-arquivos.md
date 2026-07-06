# Prompt — Criar Estrutura Inicial de Arquivos e Diretórios

```txt
Você vai criar a estrutura inicial do projeto.


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


Documentos obrigatórios:
docs/05-arquitetura-pastas.md
protocolos/protocolo-criacao-projeto-zero.md

Projeto:
[NOME]

Criar estrutura:
/
  README.md
  controle-projeto.md
  orquestrador.md
  /docs
  /prompts
  /protocolos
  /speciality
  /skills
  /public
    index.php
    /assets
      /css
      /js
      /img
  /app
    /config
    /helpers
    /controllers
    /services
    /middlewares
  /api
  /database
    /migrations
    /seeds
  /storage
    /uploads
    /logs
  /scripts

Criar arquivos base:
.gitignore
.env.example
public/index.php
public/assets/css/app.css
public/assets/js/app.js
app/config/config.php
app/config/database.php
app/helpers/response.php
app/helpers/security.php
api/health.php
storage/uploads/.gitkeep
storage/logs/.gitkeep

Regras:
- não colocar senha real;
- não versionar .env;
- cada arquivo PHP deve ter comentário inicial;
- API deve usar JSON padrão;
- atualizar docs/05 e controle-projeto.md.
```
