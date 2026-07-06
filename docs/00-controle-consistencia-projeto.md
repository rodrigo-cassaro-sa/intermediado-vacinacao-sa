# Controle de Consistência do Projeto

## Objetivo

Este documento guia a IA para tomar decisões consistentes com o projeto.

Ele deve ser consultado antes de decisões importantes, junto com o `controle-projeto.md`.

---

# 1. Fontes de verdade

| Assunto | Fonte principal |
|---|---|
| Estado atual do projeto | `controle-projeto.md` |
| Objetivo e contexto | `doc/01-visao-geral-projeto.md` |
| Regras de negócio | `doc/02-briefing-regras-negocio.md` |
| MVP e versões | `doc/03-mvp-versoes-roadmap.md` |
| Perfis e permissões | `doc/04-perfis-permissoes.md` |
| Arquitetura e pastas | `doc/05-arquitetura-pastas.md` |
| Design e UX/UI | `doc/06-guia-visual-ux-ui.md` |
| Telas e fluxos | `doc/07-mapa-telas-fluxos.md` |
| Banco de dados | `doc/08-modelagem-banco-dados.md` |
| APIs | `doc/09-contrato-api-endpoints.md` |
| Segurança e auditoria | `doc/10-seguranca-lgpd-auditoria.md` |
| Integrações | `doc/11-integracoes-webhooks-notificacoes.md` |
| Testes e homologação | `doc/12-qa-homologacao.md` |
| Deploy | `doc/13-deploy-easypanel-producao.md` |
| Backup e rollback | `doc/14-backup-rollback-monitoramento.md` |
| Decisões e changelog | `doc/15-changelog-decisoes.md` |
| Continuação do projeto | `doc/16-handoff-proxima-ia-programador.md` |

---

# 2. Regra de decisão

Antes de executar, a IA deve responder internamente:

```txt
Essa decisão afeta regra de negócio?
Essa decisão afeta permissão?
Essa decisão afeta banco?
Essa decisão afeta API?
Essa decisão afeta segurança?
Essa decisão afeta deploy?
Essa decisão muda comportamento existente?
Essa decisão precisa atualizar documentação?
```

Se a resposta for sim, atualizar o documento da área.

---

# 3. Prioridade em caso de conflito

```txt
1. Segurança, LGPD e integridade dos dados
2. Regra de negócio oficial
3. Permissões
4. Contrato de API
5. Banco de dados
6. Comportamento existente aprovado
7. QA e evidência
8. Deploy seguro
9. Performance medida
10. UX/UI e estética
```

---

# 4. Regra anti-improviso

A IA não deve:

- inventar regra não documentada;
- mudar padrão sem registrar decisão;
- criar campo de banco fora do padrão;
- criar endpoint fora do padrão JSON;
- confiar no frontend para permissão;
- ignorar `controle-projeto.md`;
- ignorar documento oficial da área;
- concluir tarefa relevante sem checkpoint.

---

# 5. Checklist de consistência

```md
- [ ] O controle-projeto.md foi consultado.
- [ ] O documento da área foi consultado.
- [ ] A decisão respeita a stack definida.
- [ ] A decisão respeita permissões.
- [ ] A decisão respeita banco/API.
- [ ] A decisão respeita segurança.
- [ ] A decisão preserva comportamento existente ou tem autorização.
- [ ] A documentação afetada foi atualizada.
- [ ] O controle-projeto.md foi atualizado.
```
