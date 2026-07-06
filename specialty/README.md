# README — Especialistas da Biblioteca de IA

## Objetivo

Esta pasta contém os **especialistas** que serão convocados pelo `orquestrador.md` conforme o tipo de tarefa.

A arquitetura mental correta é:

```txt
Prompt do usuário
  ↓
Orquestrador
  ↓
Protocolo
  ↓
Especialistas
  ↓
Skills
  ↓
Execução
  ↓
Evidência / Checklist / Documentação
```

## Diferença entre especialista e skill

```txt
Especialista = papel profissional que analisa, decide, questiona e recomenda.
Skill = conhecimento técnico detalhado usado pelo especialista.
```

Exemplo:

```txt
Especialista de Banco de Dados
  usa:
  - skill-dados.md
  - skill-mysql.md
  - skill-migracoes-banco.md
  - skill-backup-recuperacao.md
```

## Regra principal

O especialista não deve executar tudo sozinho.

Ele deve:

1. analisar o pedido pela sua ótica;
2. identificar riscos;
3. recomendar skills;
4. definir ordem de execução;
5. dizer o que não deve ser feito;
6. entregar checklist de validação;
7. devolver parecer ao orquestrador.

## Especialistas principais

```txt
01 especialista-produto-planejamento.md
02 especialista-design-interface.md
03 especialista-frontend-tecnico.md
04 especialista-backend-api-php.md
05 especialista-banco-dados.md
06 especialista-seguranca-auditoria.md
07 especialista-negocio-saas.md
08 especialista-engajamento-integracoes.md
09 especialista-qualidade-manutencao.md
10 especialista-deploy-producao.md
```

## Especialistas transversais

```txt
11 especialista-conversao-projeto-externo.md
12 especialista-documentacao-memoria.md
13 especialista-release-versionamento.md
14 especialista-diagnostico-incidentes.md
```

## Quando usar especialistas transversais

Especialistas transversais entram quando o problema atravessa vários grupos.

Exemplos:

```txt
Projeto Lovable para converter → Conversão de Projeto Externo
Projeto grande com muitas decisões → Documentação e Memória
Publicação com várias versões → Release e Versionamento
Sistema fora do ar → Diagnóstico e Incidentes
```

## Regra final

```txt
O orquestrador convoca.
O especialista analisa.
A skill orienta tecnicamente.
O protocolo conduz a sequência.
A evidência prova a entrega.
```
