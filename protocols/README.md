# README — Protocolos da Biblioteca de IA

## Objetivo

Esta pasta contém os **protocolos de trabalho** usados pelo `orquestrador.md`.

A arquitetura correta da biblioteca é:

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

## Diferença entre protocolo, especialista e skill

```txt
Protocolo
= sequência de trabalho para uma situação grande, crítica ou recorrente.

Especialista
= papel profissional que analisa o problema por uma ótica específica.

Skill
= conhecimento técnico detalhado usado pelo especialista.
```

## Quando usar protocolos

Use protocolos quando a tarefa envolver:

- mais de uma área;
- risco técnico;
- segurança;
- banco de dados;
- pagamento;
- deploy;
- produção;
- conversão de projeto externo;
- homologação;
- incidente;
- documentação final.

## Protocolos criados

```txt
01 protocolo-conselho-especialidades.md
02 protocolo-criacao-projeto-zero.md
03 protocolo-criacao-modulo-novo.md
04 protocolo-tela-com-api.md
05 protocolo-crud-completo.md
06 protocolo-conversao-projeto-externo.md
07 protocolo-correcao-bug.md
08 protocolo-deploy-producao.md
09 protocolo-refatoracao-segura.md
10 protocolo-banco-migration.md
11 protocolo-autenticacao-permissoes.md
12 protocolo-pagamento-voucher-assinatura.md
13 protocolo-notificacoes-integracoes.md
14 protocolo-homologacao.md
15 protocolo-incidente-producao.md
16 protocolo-documentacao-handoff.md
```

## Regra final

```txt
Orquestrador escolhe o protocolo.
Protocolo define a sequência.
Especialistas analisam.
Skills orientam tecnicamente.
Execução faz.
Evidência prova.
```
