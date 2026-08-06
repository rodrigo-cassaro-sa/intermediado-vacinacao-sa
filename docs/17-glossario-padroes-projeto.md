# Glossário e Padrões do Projeto

## Objetivo

Padronizar nomes, termos, status e convenções para evitar inconsistência.

---

# 1. Termos do negócio

| Termo | Significado | Observação |
|---|---|---|
|  |  |  |

---

# 2. Status oficiais

> **Regra que já custou um bug (BUG-005):** o status concorda em **gênero com o
> substantivo da entidade**. Entidade masculina usa `ativo/inativo`; feminina usa
> `ativa/inativa`. `unidade` nasceu no banco como `ativa`, mas a API validava
> `ativo` e a tela comparava com `ativo` — resultado: unidade recém-criada era
> pintada como "Inativa", sumia do filtro padrão e abria com o campo em branco.
>
> Ao criar tabela nova: **confira o gênero, escreva os valores no comentário da
> coluna e repita os mesmos literais na API e na tela.**

| Tabela | Valores | Padrão | Gênero |
|---|---|---|---|
| cliente_b2b | `ativo` \| `inativo` | ativo | masculino |
| usuario | `ativo` \| `bloqueado` | ativo | masculino |
| grupo_empresarial | `ativo` \| `inativo` | ativo | masculino |
| **unidade** | `ativa` \| `inativa` | **ativa** (nasce ativa) | feminino |
| vacina | `ativa` \| `inativa` | ativa | feminino |
| clinica_credenciada | `ativa` \| `inativa` | ativa | feminino |
| campanha | `rascunho` \| `ativa` \| `encerrada` | rascunho | feminino |
| elegivel | `pendente` \| `aplicado` \| `recusado` \| `inelegivel` \| `ausente` \| `removido` | pendente | masculino |
| aplicacao | `confirmada` \| `retificada` \| `estornada` | confirmada | feminino |
| importacao_elegiveis / importacao_historico | `pendente` \| `processando` \| `concluida` \| `falha` | — | feminino |
| importacao_aplicacoes | `simulando` \| `simulada` \| `pendente` \| `processando` \| `concluida` \| `falha` \| `estornada` | simulando | feminino |
| webhook_entrega | `pendente` \| `entregue` \| `dead` | pendente | feminino |

Quem altera: o dono da entidade dentro do escopo (doc 04). Estorno e retificação de
`aplicacao` são exclusivos do interno, com motivo.

---

# 3. Padrões de nome

## Banco

```txt
criado_em
atualizado_em
excluido_em
criado_por
atualizado_por
excluido_por
```

## Técnicos permitidos em inglês

```txt
tenant_id
workspace_id
request_id
trace_id
status_code
endpoint
commit_hash
container_id
idempotency_key
metadata
payload
```

---

# 4. Padrão de commits

```txt
feat: nova funcionalidade
fix: correção
docs: documentação
refactor: refatoração
chore: tarefa técnica
```

---

# 5. Regra final

Não criar novo termo, status ou padrão sem registrar neste documento.
