# Protocolo — Deploy em Produção

## Objetivo

Define como publicar um projeto em produção com Git, Docker, EasyPanel, MySQL/phpMyAdmin, domínio, SSL, portas, backup, rollback e monitoramento.

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

- publicação em produção
- deploy no EasyPanel
- configurar domínio
- ativar SSL
- subir MySQL e phpMyAdmin
- configurar portas e volumes
- rodar migration em produção
- validar pós-deploy

---

## Quando não usar

Não use este protocolo quando:

- desenvolvimento local
- homologação sem publicação final
- alteração de código sem deploy
- projeto sem EasyPanel quando a infraestrutura for outra

---

## Especialista principal

```txt
especialista-deploy-producao.md
```

## Especialistas de apoio

```txt
especialista-release-versionamento.md
especialista-seguranca-auditoria.md
especialista-banco-dados.md
especialista-qualidade-manutencao.md
especialista-diagnostico-incidentes.md
```

---

## Skills principais

```txt
skill-git.md
skill-dockerfile.md
skill-easypanel-mysql-phpmyadmin.md
skill-deploy-ci-cd.md
skill-monitoramento-observabilidade.md
skill-backup-recuperacao.md
```

## Skills de apoio

```txt
skill-migracoes-banco.md
skill-seguranca.md
skill-qa.md
skill-logs-auditoria.md
skill-documentacao-projeto.md
```

---

## Fluxo obrigatório

1. Validar branch/tag de produção.
2. Garantir backup antes de mudança crítica.
3. Validar Dockerfile e configuração EasyPanel.
4. Configurar variáveis de ambiente no EasyPanel.
5. Configurar MySQL/MariaDB com volume persistente.
6. Configurar phpMyAdmin protegido.
7. Configurar domínio e SSL.
8. Validar portas e exposição dos serviços.
9. Rodar migrations controladas quando necessário.
10. Executar deploy.
11. Testar health check.
12. Testar login, API, banco e upload.
13. Validar logs e monitoramento.
14. Registrar versão publicada.
15. Documentar rollback.

---

## Regras obrigatórias

- não publicar sem código versionado
- não versionar .env real
- não usar root do banco na aplicação
- não expor MySQL publicamente
- não deixar phpMyAdmin sem proteção
- não considerar produção pronta sem SSL e health check
- não rodar migration crítica sem backup

---

## Entregáveis esperados

- checklist pré-deploy
- configuração EasyPanel
- mapa de portas
- backup
- registro de versão
- checklist pós-deploy
- plano de rollback
- documentação de produção

---

## Checklist de validação

```md
- [ ] Branch/tag correta
- [ ] Backup feito
- [ ] Variáveis configuradas
- [ ] Banco e volumes persistentes
- [ ] phpMyAdmin protegido
- [ ] Domínio e SSL funcionando
- [ ] MySQL interno
- [ ] Health check responde
- [ ] Logs ativos
- [ ] Rollback documentado
```

---

## Formato de resposta recomendado

```md
# Protocolo — Deploy em Produção

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
