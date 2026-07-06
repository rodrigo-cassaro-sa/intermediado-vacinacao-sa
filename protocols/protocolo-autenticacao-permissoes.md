# Protocolo — Autenticação e Permissões

## Objetivo

Define como implementar ou revisar login, sessão, perfis e permissões com validação real no backend.

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

- login
- logout
- sessão
- cadastro de usuário
- perfil de acesso
- permissão por tela
- permissão por ação
- controle administrativo
- área restrita

---

## Quando não usar

Não use este protocolo quando:

- tela pública sem login
- ajuste visual sem regra de acesso
- relatório interno já protegido sem mudança de permissão
- deploy sem alteração de acesso

---

## Especialista principal

```txt
especialista-seguranca-auditoria.md
```

## Especialistas de apoio

```txt
especialista-produto-planejamento.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-frontend-tecnico.md
especialista-qualidade-manutencao.md
```

---

## Skills principais

```txt
skill-seguranca.md
skill-autenticacao-sessao.md
skill-perfis-permissoes.md
skill-lgpd-privacidade.md
skill-logs-auditoria.md
skill-backend.md
skill-api-rest.md
skill-qa.md
```

## Skills de apoio

```txt
skill-telas.md
skill-mysql.md
skill-frontend.md
skill-fetch.md
skill-erros-excecoes.md
```

---

## Fluxo obrigatório

1. Definir perfis.
2. Definir permissões por tela e ação.
3. Modelar usuários, sessões e permissões.
4. Implementar autenticação.
5. Implementar autorização backend.
6. Proteger rotas e endpoints.
7. Ajustar frontend apenas como orientação visual.
8. Registrar logs de acesso e ações críticas.
9. Testar acesso permitido e negado.
10. Documentar matriz de permissões.

---

## Regras obrigatórias

- frontend não é permissão real
- backend deve validar toda ação crítica
- não expor dados de outro perfil
- sessão deve ser segura
- logs não devem vazar senha ou token

---

## Entregáveis esperados

- matriz de perfis e permissões
- fluxo de login/sessão
- endpoints protegidos
- logs de auditoria
- testes de acesso
- documentação

---

## Checklist de validação

```md
- [ ] Perfis definidos
- [ ] Permissões por ação definidas
- [ ] Backend valida acesso
- [ ] Frontend não é fonte final
- [ ] Sessão segura
- [ ] Logs implementados
- [ ] Testes de permitido/negado feitos
```

---

## Formato de resposta recomendado

```md
# Protocolo — Autenticação e Permissões

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
