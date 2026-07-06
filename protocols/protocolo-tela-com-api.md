# Protocolo — Tela com API

## Objetivo

Define como criar uma tela que consulta, grava, edita ou remove dados usando frontend técnico e API backend.

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

- tela com formulário
- tela com listagem
- tela com filtros
- dashboard com dados
- tela que usa Fetch
- tela que consome ou altera dados

---

## Quando não usar

Não use este protocolo quando:

- tela puramente estática
- endpoint sem interface
- alteração só de banco
- deploy ou configuração de servidor

---

## Especialista principal

```txt
especialista-design-interface.md
```

## Especialistas de apoio

```txt
especialista-frontend-tecnico.md
especialista-backend-api-php.md
especialista-banco-dados.md
especialista-seguranca-auditoria.md
especialista-qualidade-manutencao.md
```

---

## Skills principais

```txt
skill-telas.md
skill-ux-ui.md
skill-motion-feedback-visual.md
skill-frontend.md
skill-html.md
skill-css.md
skill-js.md
skill-fetch.md
skill-api-rest.md
```

## Skills de apoio

```txt
skill-backend.md
skill-erros-excecoes.md
skill-acessibilidade.md
skill-responsividade.md
skill-seguranca.md
skill-qa.md
```

---

## Fluxo obrigatório

1. Definir objetivo da tela.
2. Definir dados exibidos e ações possíveis.
3. Mapear estados: carregando, vazio, erro, sucesso, bloqueado.
4. Definir contrato da API.
5. Definir permissões da ação.
6. Criar HTML semântico.
7. Criar CSS responsivo.
8. Criar JS de interação.
9. Criar Fetch com tratamento de erro.
10. Garantir validação backend.
11. Testar fluxo completo.
12. Documentar contrato e comportamento.

---

## Regras obrigatórias

- não usar frontend como fonte de permissão
- não confirmar ação crítica antes do backend
- não criar loading genérico sem estado real
- não deixar erro da API invisível
- não criar tela que quebra no celular

---

## Entregáveis esperados

- mapa da tela
- estados visuais
- contrato de API
- HTML/CSS/JS
- integração Fetch
- validações
- checklist QA

---

## Checklist de validação

```md
- [ ] Tela tem objetivo claro
- [ ] Estados visuais foram criados
- [ ] API tem contrato definido
- [ ] Fetch trata sucesso e erro
- [ ] Backend valida regra e permissão
- [ ] Tela é responsiva
- [ ] QA validou fluxo principal
```

---

## Formato de resposta recomendado

```md
# Protocolo — Tela com API

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
