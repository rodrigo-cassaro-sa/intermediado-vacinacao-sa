# Protocolo — Conversão de Projeto Externo

## Objetivo

Define como estudar e converter projetos feitos em Lovable, Bolt, v0, Bubble, React, Next, Supabase, Firebase ou outras stacks para a stack padrão PHP/MySQL/HTML/CSS/JS.

Este protocolo deve ser usado pelo `orquestrador.md` quando a tarefa exigir uma sequência de trabalho maior do que uma skill isolada.

Ele segue a arquitetura:

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

---

## Quando usar

Use este protocolo quando houver:

- projeto Lovable
- projeto Bolt
- projeto v0
- projeto Bubble
- React ou Next
- Supabase ou Firebase
- projeto gerado por IA
- migração para PHP/MySQL

---

## Quando não usar

Não use este protocolo quando:

- projeto criado do zero
- ajuste pontual em sistema já convertido
- deploy sem análise de código externo
- tela isolada sem dependência do projeto original

---

## Especialista principal

```txt
especialista-conversao-projeto-externo.md
```

## Especialistas de apoio

```txt
especialista-produto-planejamento.md
especialista-design-interface.md
especialista-frontend-tecnico.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-seguranca-auditoria.md
especialista-qualidade-manutencao.md
especialista-documentacao-memoria.md
```

---

## Skills principais

```txt
skill-conversao-projeto-externo.md
skill-briefing.md
skill-arquitetura.md
skill-telas.md
skill-dados.md
skill-backend.md
skill-frontend.md
skill-qa.md
```

## Skills de apoio

```txt
skill-mysql.md
skill-api-rest.md
skill-seguranca.md
skill-lgpd-privacidade.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Inventariar arquivos, pastas e stack atual.
2. Mapear telas, rotas e fluxos.
3. Extrair regras de negócio.
4. Mapear entidades, tabelas, coleções ou schemas.
5. Mapear autenticação, permissões e segurança.
6. Mapear APIs, webhooks e integrações.
7. Mapear componentes visuais e estados de tela.
8. Identificar regras escondidas no frontend.
9. Definir plano de conversão por fases.
10. Converter para PHP/MySQL/HTML/CSS/JS preservando comportamento.
11. Testar comparando antes/depois.
12. Documentar diferenças e pendências.

---

## Regras obrigatórias

- não reescrever antes de entender
- não copiar arquitetura inadequada da ferramenta original
- não perder regra escondida no frontend
- não converter autenticação sem mapear permissões
- não mudar comportamento sem autorização

---

## Entregáveis esperados

- inventário do projeto
- mapa de telas e rotas
- mapa de regras
- mapa de dados
- mapa de APIs
- plano de conversão
- execução por fases
- QA comparativo

---

## Checklist de validação

```md
- [ ] Projeto original foi inventariado
- [ ] Regras foram extraídas
- [ ] Dados foram mapeados
- [ ] Autenticação foi entendida
- [ ] Plano de conversão foi criado
- [ ] Comportamento importante foi preservado
- [ ] QA comparativo foi feito
```

---

## Formato de resposta recomendado

```md
# Protocolo — Conversão de Projeto Externo

## 1. Entendimento do pedido

## 2. Especialistas convocados

## 3. Skills aplicadas

## 4. Fluxo executado

## 5. Decisões tomadas

## 6. Riscos e cuidados

## 7. Entregáveis

## 8. Evidências

## 9. Checklist final
```



---

## Regra final

Este protocolo não substitui as skills.

Ele organiza a ordem correta de uso dos especialistas e das skills para reduzir erro, retrabalho, improviso e decisão técnica solta.
