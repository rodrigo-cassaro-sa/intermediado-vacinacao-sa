# Protocolo — Criação de Projeto do Zero até Produção

## Objetivo

Define a sequência para criar um sistema, app ou SaaS desde o briefing inicial até publicação em produção com domínio, SSL, EasyPanel, MySQL/phpMyAdmin, backup e monitoramento.

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

- projeto novo
- SaaS novo
- app web/PWA novo
- sistema interno novo
- produto precisa sair do zero até produção
- precisa estruturar Git, pastas, banco, backend, frontend e deploy

---

## Quando não usar

Não use este protocolo quando:

- ajuste pequeno em projeto existente
- correção pontual de bug
- apenas uma tela isolada
- apenas deploy de sistema já pronto

---

## Especialista principal

```txt
especialista-produto-planejamento.md
```

## Especialistas de apoio

```txt
especialista-design-interface.md
especialista-banco-dados.md
especialista-backend-api-php.md
especialista-frontend-tecnico.md
especialista-seguranca-auditoria.md
especialista-qualidade-manutencao.md
especialista-deploy-producao.md
especialista-documentacao-memoria.md
```

---

## Skills principais

```txt
skill-briefing.md
skill-perfis-permissoes.md
skill-arquitetura.md
skill-telas.md
skill-dados.md
skill-mysql.md
skill-backend.md
skill-api-rest.md
skill-frontend.md
skill-seguranca.md
skill-qa.md
skill-git.md
skill-dockerfile.md
skill-easypanel-mysql-phpmyadmin.md
skill-deploy-ci-cd.md
```

## Skills de apoio

```txt
skill-ux-ui.md
skill-motion-feedback-visual.md
skill-acessibilidade.md
skill-responsividade.md
skill-pwa-offline-first.md
skill-documentacao-projeto.md
skill-monitoramento-observabilidade.md
skill-backup-recuperacao.md
```

---

## Fluxo obrigatório

1. Criar briefing do projeto.
2. Definir MVP, versões futuras e limites de escopo.
3. Definir perfis e permissões.
4. Definir arquitetura e estrutura de pastas.
5. Criar mapa de telas e fluxos.
6. Definir guia visual, UX, responsividade e feedback visual.
7. Modelar banco de dados.
8. Criar scripts SQL/migrations.
9. Implementar backend/API em PHP procedural.
10. Implementar frontend HTML/CSS/JS/Fetch.
11. Aplicar segurança, sessão, logs e auditoria.
12. Criar testes e checklist QA.
13. Preparar Git/GitHub.
14. Preparar Docker/EasyPanel.
15. Configurar MySQL/phpMyAdmin, volumes e portas.
16. Homologar.
17. Publicar produção com domínio e SSL.
18. Validar monitoramento, backup e rollback.
19. Documentar entrega final.

---

## Regras obrigatórias

- não começar programando sem briefing
- não misturar MVP com versão final
- não criar banco antes de entender regras principais
- não confiar em permissão apenas no frontend
- não publicar sem backup, SSL, rollback e health check
- não versionar .env real

---

## Entregáveis esperados

- documento de briefing
- mapa de perfis e permissões
- arquitetura e estrutura de pastas
- mapa de telas
- guia visual
- modelo de dados
- APIs
- frontend
- checklist QA
- documentação de deploy
- projeto publicado

---

## Checklist de validação

```md
- [ ] Briefing aprovado
- [ ] MVP separado de versões futuras
- [ ] Perfis e permissões definidos
- [ ] Banco modelado
- [ ] APIs documentadas
- [ ] Frontend integrado
- [ ] Segurança validada
- [ ] QA executado
- [ ] EasyPanel configurado
- [ ] Domínio e SSL funcionando
- [ ] Backup e rollback preparados
- [ ] Entrega documentada
```

---

## Formato de resposta recomendado

```md
# Protocolo — Criação de Projeto do Zero até Produção

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
