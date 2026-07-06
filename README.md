# README — Biblioteca de IA para Desenvolvimento de Projetos

## Objetivo

Esta biblioteca organiza a forma como uma IA deve planejar, desenvolver, revisar, documentar, corrigir, converter e publicar projetos de software, app, SaaS e sistemas web.

Ela foi criada para evitar:

- respostas genéricas;
- perda de contexto;
- decisões técnicas soltas;
- uso errado de stack;
- alteração fora do escopo;
- código sem documentação;
- deploy sem segurança;
- projeto sem continuidade.

---

# 1. Estrutura oficial

A estrutura oficial da biblioteca é:

```txt
/
  README.md
  orquestrador.md
  controle-projeto.md
  manifesto-biblioteca.md
  checklist-integridade-biblioteca.md

  /docs
  /prompts
  /protocols
  /skills
  /speciality
```

## Regra de nomes

Usar sempre estes nomes de pasta:

```txt
docs
prompts
protocols
skills
speciality
```

Não usar variações como:

```txt
doc
protocolos
especialistas
specialists
```

A IA deve respeitar exatamente os caminhos oficiais para não procurar arquivos em pasta errada.

---

# 2. Função de cada parte

## `orquestrador.md`

Especialista principal.

Função:

```txt
entender o pedido
consultar controle-projeto.md
escolher protocolo
convocar especialistas
selecionar skills
resolver conflitos
definir ordem de execução
exigir evidência e documentação
```

---

## `controle-projeto.md`

Memória operacional do projeto.

Função:

```txt
registrar etapa atual
registrar o que já foi feito
registrar decisões tomadas
registrar próximos passos
registrar riscos e bloqueios
permitir que outra IA ou programador continue de onde parou
```

---

## `docs/`

Documentação oficial do projeto.

Função:

```txt
guardar regras de negócio
perfis e permissões
arquitetura
telas
banco de dados
APIs
segurança
deploy
QA
changelog
handoff
```

---

## `prompts/`

Prompts prontos para execução.

Função:

```txt
iniciar projeto
continuar projeto
converter projeto externo
criar módulo
criar tela
criar CRUD
corrigir bug
resolver incidente
fazer deploy
atualizar documentação
```

---

## `protocols/`

Métodos de trabalho.

Função:

```txt
definir sequência correta para tarefas grandes ou críticas
```

Exemplo:

```txt
protocolo-criacao-projeto-zero.md
protocolo-deploy-producao.md
protocolo-correcao-bug.md
```

---

## `speciality/`

Especialistas profissionais.

Função:

```txt
analisar o problema por área
recomendar skills
apontar riscos
dar parecer ao orquestrador
```

Exemplo:

```txt
especialista-banco-dados.md
especialista-seguranca-auditoria.md
especialista-deploy-producao.md
```

---

## `skills/`

Conhecimento técnico detalhado.

Função:

```txt
orientar tecnicamente a execução
definir boas práticas
padronizar código, banco, frontend, backend, deploy e manutenção
```

---

# 3. Fluxo oficial de trabalho

```txt
Prompt do usuário
  ↓
orquestrador.md
  ↓
controle-projeto.md
  ↓
protocols/
  ↓
speciality/
  ↓
skills/
  ↓
execução
  ↓
docs/
  ↓
controle-projeto.md atualizado
```

---

# 4. Ordem obrigatória de leitura para qualquer IA

Antes de executar tarefa relevante, a IA deve ler:

```txt
1. README.md
2. orquestrador.md
3. controle-projeto.md
4. docs/README.md
5. docs/00-controle-consistencia-projeto.md
6. prompt específico dentro de prompts/
7. protocolo específico dentro de protocols/
8. especialista específico dentro de speciality/
9. skills específicas dentro de skills/
```

---

# 5. Stack padrão

```txt
PHP procedural puro
MySQL/MariaDB
phpMyAdmin
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON
Git/GitHub
Docker/Docker Compose quando aplicável
EasyPanel
Apache ou Nginx
Domínio com SSL
Sem framework por padrão
Sem orientação a objetos por padrão
```

Qualquer mudança de stack deve ser registrada em:

```txt
controle-projeto.md
docs/15-changelog-decisoes.md
docs/01-visao-geral-projeto.md
```

---

# 6. Regra para tarefas relevantes

Toda tarefa relevante deve terminar com:

```txt
resultado entregue
arquivos criados/alterados
checklist ou evidência
documentação atualizada
controle-projeto.md atualizado
próximo passo definido
```

---

# 7. Quando atualizar `controle-projeto.md`

Atualizar sempre que houver:

- nova decisão técnica;
- mudança de etapa;
- criação de módulo;
- criação de tela;
- alteração de banco;
- alteração de API;
- alteração de permissão;
- correção de bug relevante;
- deploy;
- homologação;
- incidente;
- mudança de domínio, ambiente, banco, porta ou stack;
- novo risco;
- novo bloqueio;
- próximo passo alterado.

---

# 8. Regra final

```txt
README.md explica a biblioteca.
orquestrador.md decide.
controle-projeto.md guarda a memória.
docs/ guarda a verdade oficial.
prompts/ dispara tarefas.
protocols/ conduz processos.
speciality/ analisa como especialista.
skills/ orienta tecnicamente.
```

Se uma IA ignorar essa estrutura, ela pode tomar decisão errada.
