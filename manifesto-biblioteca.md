# Manifesto da Biblioteca de IA

## Objetivo

Este manifesto é o mapa geral da biblioteca.

Ele mostra quais arquivos e pastas devem existir, qual é a função de cada um e como a IA deve navegar pelo projeto.

---

# 1. Estrutura esperada

```txt
/
  README.md
  orquestrador.md
  controle-projeto.md
  manifesto-biblioteca.md
  checklist-integridade-biblioteca.md

  /docs
    README.md
    00-controle-consistencia-projeto.md
    00-template-documento.md
    01-visao-geral-projeto.md
    02-briefing-regras-negocio.md
    03-mvp-versoes-roadmap.md
    04-perfis-permissoes.md
    05-arquitetura-pastas.md
    06-guia-visual-ux-ui.md
    07-mapa-telas-fluxos.md
    08-modelagem-banco-dados.md
    09-contrato-api-endpoints.md
    10-seguranca-lgpd-auditoria.md
    11-integracoes-webhooks-notificacoes.md
    12-qa-homologacao.md
    13-deploy-easypanel-producao.md
    14-backup-rollback-monitoramento.md
    15-changelog-decisoes.md
    16-handoff-proxima-ia-programador.md
    17-glossario-padroes-projeto.md
    18-incidentes-pos-mortem.md

  /prompts
    README.md
    MANIFESTO-PROMPTS.md
    00-template-prompt-padrao.md
    01-prompt-mestre-execucao.md
    ...

  /protocols
    README.md
    MANIFESTO-PROTOCOLOS.md
    00-template-protocolo.md
    protocolo-conselho-especialidades.md
    protocolo-criacao-projeto-zero.md
    protocolo-conversao-projeto-externo.md
    protocolo-correcao-bug.md
    protocolo-deploy-producao.md
    ...

  /speciality
    README.md
    MANIFESTO-ESPECIALISTAS.md
    00-template-especialista.md
    especialista-produto-planejamento.md
    especialista-design-interface.md
    especialista-frontend-tecnico.md
    especialista-backend-api-php.md
    especialista-banco-dados.md
    especialista-seguranca-auditoria.md
    especialista-negocio-saas.md
    especialista-engajamento-integracoes.md
    especialista-qualidade-manutencao.md
    especialista-deploy-producao.md
    ...

  /skills
    README.md
    skill-briefing.md
    skill-arquitetura.md
    skill-telas.md
    skill-php.md
    skill-mysql.md
    skill-easypanel-mysql-phpmyadmin.md
    ...
```

---

# 2. Camadas da biblioteca

| Camada | Caminho | Função |
|---|---|---|
| Entrada | `prompts/` | Disparar tarefas com padrão correto |
| Decisão | `orquestrador.md` | Escolher caminho, protocolo, especialistas e skills |
| Memória | `controle-projeto.md` | Registrar etapa, decisões, progresso e próximo passo |
| Processo | `protocols/` | Definir sequência para tarefas grandes |
| Especialidade | `speciality/` | Analisar por área profissional |
| Técnica | `skills/` | Orientar execução técnica |
| Documentação | `docs/` | Guardar verdade oficial do projeto |

---

# 3. Fontes de verdade

| Assunto | Fonte |
|---|---|
| Estado atual | `controle-projeto.md` |
| Decisão de execução | `orquestrador.md` |
| Consistência geral | `docs/00-controle-consistencia-projeto.md` |
| Regras de negócio | `docs/02-briefing-regras-negocio.md` |
| MVP e versões | `docs/03-mvp-versoes-roadmap.md` |
| Permissões | `docs/04-perfis-permissoes.md` |
| Arquitetura | `docs/05-arquitetura-pastas.md` |
| UX/UI | `docs/06-guia-visual-ux-ui.md` |
| Telas | `docs/07-mapa-telas-fluxos.md` |
| Banco | `docs/08-modelagem-banco-dados.md` |
| API | `docs/09-contrato-api-endpoints.md` |
| Segurança | `docs/10-seguranca-lgpd-auditoria.md` |
| Integrações | `docs/11-integracoes-webhooks-notificacoes.md` |
| QA | `docs/12-qa-homologacao.md` |
| Deploy | `docs/13-deploy-easypanel-producao.md` |
| Backup/rollback | `docs/14-backup-rollback-monitoramento.md` |
| Decisões/changelog | `docs/15-changelog-decisoes.md` |
| Continuidade | `docs/16-handoff-proxima-ia-programador.md` |

---

# 4. Regras que a IA nunca deve ignorar

- Ler `controle-projeto.md` antes de continuar projeto.
- Não refazer decisão já registrada sem motivo.
- Não alterar stack sem autorização.
- Não usar framework por padrão.
- Não usar orientação a objetos por padrão.
- Não confiar no frontend para regra crítica.
- Não alterar banco de produção sem backup.
- Não fazer deploy sem branch/tag, rollback, SSL e health check.
- Não considerar tarefa relevante pronta sem atualizar documentação e checkpoint.
- Não usar caminhos antigos como `doc/`, `protocolos/` ou `especialistas/`.

---

# 5. Fluxo de início recomendado

```txt
1. Ler README.md
2. Ler orquestrador.md
3. Ler controle-projeto.md
4. Ler docs/00-controle-consistencia-projeto.md
5. Escolher prompt em prompts/
6. Aplicar protocolo em protocols/
7. Convocar especialista em speciality/
8. Aplicar skill em skills/
9. Executar
10. Atualizar docs/
11. Atualizar controle-projeto.md
```

---

# 6. Regra final

A biblioteca só está íntegra quando todos os caminhos, nomes, prompts, protocolos, especialistas e skills apontam para a mesma estrutura oficial.
