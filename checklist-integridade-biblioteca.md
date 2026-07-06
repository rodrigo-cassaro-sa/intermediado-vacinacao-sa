# Checklist de Integridade da Biblioteca

## Objetivo

Conferir se a biblioteca está pronta para ser usada por uma IA de programação sem se perder.

---

# 1. Estrutura raiz

```md
- [ ] `README.md` existe.
- [ ] `orquestrador.md` existe.
- [ ] `controle-projeto.md` existe.
- [ ] `manifesto-biblioteca.md` existe.
- [ ] `checklist-integridade-biblioteca.md` existe.
```

---

# 2. Pastas oficiais

```md
- [ ] `docs/` existe.
- [ ] `prompts/` existe.
- [ ] `protocols/` existe.
- [ ] `skills/` existe.
- [ ] `speciality/` existe.
```

---

# 3. Nomes proibidos

Verificar se os arquivos não estão apontando para nomes antigos:

```md
- [ ] Nenhum arquivo usa `doc/` quando deveria usar `docs/`.
- [ ] Nenhum arquivo usa `protocolos/` quando deveria usar `protocols/`.
- [ ] Nenhum arquivo usa `especialistas/` quando deveria usar `speciality/`.
- [ ] Nenhum arquivo usa `specialists/` quando deveria usar `speciality/`.
```

---

# 4. Documentação

```md
- [ ] `docs/README.md` existe.
- [ ] `docs/00-controle-consistencia-projeto.md` existe.
- [ ] `docs/01-visao-geral-projeto.md` existe.
- [ ] `docs/02-briefing-regras-negocio.md` existe.
- [ ] `docs/03-mvp-versoes-roadmap.md` existe.
- [ ] `docs/04-perfis-permissoes.md` existe.
- [ ] `docs/05-arquitetura-pastas.md` existe.
- [ ] `docs/06-guia-visual-ux-ui.md` existe.
- [ ] `docs/07-mapa-telas-fluxos.md` existe.
- [ ] `docs/08-modelagem-banco-dados.md` existe.
- [ ] `docs/09-contrato-api-endpoints.md` existe.
- [ ] `docs/10-seguranca-lgpd-auditoria.md` existe.
- [ ] `docs/11-integracoes-webhooks-notificacoes.md` existe.
- [ ] `docs/12-qa-homologacao.md` existe.
- [ ] `docs/13-deploy-easypanel-producao.md` existe.
- [ ] `docs/14-backup-rollback-monitoramento.md` existe.
- [ ] `docs/15-changelog-decisoes.md` existe.
- [ ] `docs/16-handoff-proxima-ia-programador.md` existe.
```

---

# 5. Prompts

```md
- [ ] `prompts/README.md` existe.
- [ ] `prompts/MANIFESTO-PROMPTS.md` existe.
- [ ] Existe prompt para iniciar projeto.
- [ ] Existe prompt para continuar projeto.
- [ ] Existe prompt para converter projeto externo.
- [ ] Existe prompt para criar módulo.
- [ ] Existe prompt para criar tela com API.
- [ ] Existe prompt para CRUD.
- [ ] Existe prompt para corrigir bug.
- [ ] Existe prompt para incidente.
- [ ] Existe prompt para deploy produção.
- [ ] Existe prompt para atualizar controle e docs.
```

---

# 6. Protocolos

```md
- [ ] `protocols/README.md` existe.
- [ ] `protocols/MANIFESTO-PROTOCOLOS.md` existe.
- [ ] `protocols/protocolo-conselho-especialidades.md` existe.
- [ ] `protocols/protocolo-criacao-projeto-zero.md` existe.
- [ ] `protocols/protocolo-conversao-projeto-externo.md` existe.
- [ ] `protocols/protocolo-correcao-bug.md` existe.
- [ ] `protocols/protocolo-deploy-producao.md` existe.
```

---

# 7. Especialistas

```md
- [ ] `speciality/README.md` existe.
- [ ] `speciality/MANIFESTO-ESPECIALISTAS.md` existe.
- [ ] Especialista de produto existe.
- [ ] Especialista de design existe.
- [ ] Especialista de frontend existe.
- [ ] Especialista de backend existe.
- [ ] Especialista de banco existe.
- [ ] Especialista de segurança existe.
- [ ] Especialista de SaaS existe.
- [ ] Especialista de integração existe.
- [ ] Especialista de QA/manutenção existe.
- [ ] Especialista de deploy existe.
- [ ] Especialistas transversais existem.
```

---

# 8. Skills

```md
- [ ] `skills/README.md` existe.
- [ ] Skills do Grupo 01 existem.
- [ ] Skills do Grupo 02 existem.
- [ ] Skills do Grupo 03 existem.
- [ ] Skills do Grupo 04 existem.
- [ ] Skills do Grupo 05 existem.
- [ ] Skills do Grupo 06 existem.
- [ ] Skills do Grupo 07 existem.
- [ ] Skills do Grupo 08 existem.
- [ ] Skills do Grupo 09 existem.
- [ ] Skills do Grupo 10 existem.
- [ ] `skill-easypanel-mysql-phpmyadmin.md` existe.
- [ ] `skill-conversao-projeto-externo.md` existe.
```

---

# 9. Regras de funcionamento

```md
- [ ] O orquestrador manda ler `controle-projeto.md`.
- [ ] O orquestrador exige atualização do `controle-projeto.md` ao final.
- [ ] Os prompts exigem atualização de docs e controle.
- [ ] Os protocolos indicam especialistas e skills.
- [ ] Os especialistas indicam skills.
- [ ] As skills usam a stack padrão.
- [ ] EasyPanel, MySQL, phpMyAdmin, domínio, SSL e portas estão cobertos.
```

---

# 10. Checklist final

```md
- [ ] Caminhos padronizados.
- [ ] README raiz criado.
- [ ] Manifesto criado.
- [ ] Checklist criado.
- [ ] Controle-projeto.md criado.
- [ ] Docs criados.
- [ ] Prompts criados.
- [ ] Protocols criados.
- [ ] Skills criadas.
- [ ] Speciality criada.
- [ ] Tudo pronto para usar em projeto real.
```
