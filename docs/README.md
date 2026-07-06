# README — Pasta doc do Projeto

## Objetivo

Esta pasta guarda os documentos que mantêm a consistência do projeto.

Ela deve trabalhar junto com:

```txt
orquestrador.md
controle-projeto.md
/protocolos
/especialistas
/skills
```

## Função da pasta doc

```txt
controle-projeto.md = estado atual do projeto
doc/ = documentação estruturada do projeto
```

O `controle-projeto.md` responde:

```txt
onde estamos?
o que foi feito?
qual é o próximo passo?
quais decisões já foram tomadas?
```

A pasta `doc/` responde:

```txt
qual é a regra oficial?
qual é a arquitetura?
quais telas existem?
como é o banco?
como são as APIs?
como publicar?
como testar?
como outra IA continua?
```

## Regra principal

Antes de tomar decisão importante, a IA deve consultar:

```txt
1. controle-projeto.md
2. doc/01-visao-geral-projeto.md
3. documento específico da área
4. orquestrador.md
5. protocolo/especialista/skill necessários
```

Depois de mudança relevante, a IA deve atualizar:

```txt
controle-projeto.md
documento da área afetada dentro de doc/
```

## Ordem recomendada de leitura para nova IA

```txt
1. controle-projeto.md
2. doc/README.md
3. doc/01-visao-geral-projeto.md
4. doc/02-briefing-regras-negocio.md
5. doc/03-mvp-versoes-roadmap.md
6. doc/04-perfis-permissoes.md
7. doc/05-arquitetura-pastas.md
8. documento específico da tarefa atual
```

## Documentos criados

```txt
00-template-documento.md
00-controle-consistencia-projeto.md
01-visao-geral-projeto.md
02-briefing-regras-negocio.md
03-mvp-versoes-roadmap.md
04-perfis-permissoes.md
05-arquitetura-pastas.md
06-guia-visual-ux-ui.md
07-mapa-telas-fluxos.md
08-modelagem-banco-dados.md
09-contrato-api-endpoints.md
10-seguranca-lgpd-auditoria.md
11-integracoes-webhooks-notificacoes.md
12-qa-homologacao.md
13-deploy-easypanel-producao.md
14-backup-rollback-monitoramento.md
15-changelog-decisoes.md
16-handoff-proxima-ia-programador.md
17-glossario-padroes-projeto.md
18-incidentes-pos-mortem.md
```

## Regra final

```txt
controle-projeto.md mantém o estado.
doc/ mantém a verdade oficial.
orquestrador.md decide usando os dois.
```
