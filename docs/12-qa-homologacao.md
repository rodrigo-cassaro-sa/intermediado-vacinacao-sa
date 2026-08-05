# QA, Testes e Homologação

## Objetivo

Documentar critérios de aceite, testes funcionais, regressão, homologação e evidências.

---

# 1. Critérios de aceite

| Código | Critério | Como validar | Status |
|---|---|---|---|
| CA-001 | Toda importação de CSV trata a 1ª linha como cabeçalho quando ela traz os nomes das colunas | `php scripts/testar_csv.php` + importar pelas telas | atendido |
| CA-002 | Com cabeçalho, a ORDEM das colunas não altera o resultado da importação | Importar o mesmo arquivo com as colunas embaralhadas | atendido |
| CA-003 | Arquivo SEM cabeçalho continua sendo importado pela ordem padrão, sem perder a 1ª linha | `scripts/testar_csv.php` casos 3 e 7 | atendido |
| CA-004 | Toda referência a campanha na tela mostra o CÓDIGO, nunca `null` | Listar campanhas no console e abrir os 5 dropdowns | atendido |
| CA-005 | Campanha anterior à migration 026 (sem código) continua aparecendo pelo nome | Fixture com `codigo = NULL` na validação SQL | atendido |

---

# 2. Testes funcionais

## 2.1 Importação de CSV (BUG-001)

Automatizados — `php scripts/testar_csv.php` (backend) e `public/assets/csv.js` (mesma
lógica no frontend). O script não toca no banco e sai com código 1 se algo falhar.

| Fluxo | Passos | Resultado esperado | Status |
|---|---|---|---|
| Cabeçalho canônico + BOM do Excel | Importar `cpf,nome,data_nascimento,...` salvo pelo Excel (UTF-8 com BOM) | Só as pessoas entram; o cabeçalho não vira registro | ok (auto) |
| Colunas fora de ordem | Cabeçalho `Nome Completo;Matrícula;CPF;Data de Nascimento;Lotação` | Cada valor cai no campo certo, independente da posição | ok (auto) |
| Sinônimos de coluna | "Matrícula"→codigo_rh, "Lotação"→codigo_lotacao, "Imunizante"→vacina, "Data da Aplicação"→aplicado_em | Mapeado por nome | ok (auto) |
| Coluna ausente no cabeçalho | Cabeçalho só com `cpf;data_nascimento` | `nome` vem vazio — **não** recebe o valor do CPF | ok (auto) |
| Campo com vírgula entre aspas | `52998224725,"Silva, Maria",1990-05-10` | Nome preservado; demais campos não deslocam | ok (auto) |
| Delimitador `;`, `,`, TAB ou `|` | Mesmo conteúdo com separadores diferentes | Mesmo resultado | ok (auto) |
| Histórico de vacinados | CSV com cabeçalho fora de ordem | Vacina/data/dose corretas | ok (auto) |

## 2.1b Identidade da campanha na tela (BUG-002)

| Fluxo | Passos | Resultado esperado | Status |
|---|---|---|---|
| Listar campanhas no console | Seção 3 → "Listar campanhas" | Coluna **Código** com `VAC.TEMP.MOD.GRP.CLI.SEQ`; rótulo opcional em texto secundário | ok (auto) |
| Dropdowns de campanha | Seções 4, 5, 6, 7 e 8 (`elegCampanha`, `credCampanha`, `aplicCampanha`, `tvCampanha`, `redeCampanha`) | Código + `(#id)`, nunca `null` | ok (auto) |
| Campanha antiga (sem código) | Campanha criada antes da migration 026 | Continua listada, exibindo o nome antigo | ok (auto + SQL) |
| Campanha sem código e sem nome | Registro degenerado | Exibe `#id`, não quebra a lista | ok (auto) |
| Carteira do paciente | Seção 11 → "Ver carteira" | Coluna Campanha/Cliente com o código | ok (SQL) |
| Resumo ano a ano | Seção 12 → resumo do cliente | Coluna Campanha com o código | ok (SQL) |
| Faturamento | Seção 13 → a cobrar / a pagar | Linha de resumo identifica a campanha pelo código | ok (revisado) |

## 2.2 Telas a validar em homologação (manual)

| Tela | Caminho | O que validar |
|---|---|---|
| Elegíveis (admin) | /admin/elegiveis.html | Colar CSV e enviar arquivo; conferir o total de válidos |
| Elegíveis (portal) | /portal/elegiveis.html | Idem, no escopo do cliente |
| Unidades | /admin/unidades.html | "Baixar modelo" → reimportar o próprio modelo sem criar unidade "nome" |
| Clientes | /admin/clientes.html | Idem, sem criar cliente "razao_social" |
| Grupos | /admin/grupos.html | Idem, sem criar grupo "nome" |
| Vacinados históricos | /admin/console.html §10b | CSV com cabeçalho; conferir aplicações criadas |

---

# 3. Testes de regressão

| Área | O que não pode quebrar | Status |
|---|---|---|
| Campanhas antigas (pré-026) | Sem `codigo`, continuam visíveis pelo nome em listas e dropdowns | ok (auto + SQL com fixture `codigo = NULL`) |
| Contrato `/parceiro/carteira` | Campo `campanha` continua existindo (agora preenchido com o código em vez de null) | ok (SQL) |
| Dropdown de clínicas | `carregarClinicas()` usa `c.nome` de propósito — `clinica.nome` não é opcional | ok (não alterado) |
| Campo `nome` da campanha | Continua sendo gravado, editável e retornado pela API | ok (não alterado) |
| Importação sem cabeçalho | Arquivo posicional (sem cabeçalho) mantém a 1ª linha como pessoa/registro válido | ok (auto — casos 3 e 7) |
| Ingestão assíncrona | `importacao_contar()` desconta o cabeçalho com a mesma regra do parser, senão o lote entra na fila errada | ok (revisado) |
| Worker de importação | `scripts/processar_importacoes.php` carrega `app/helpers/csv.php` | ok (revisado) |
| API do parceiro | Ingestão por JSON não passa pelo parser de CSV — contrato inalterado | ok |
| Escopo/multi-tenant | Nenhuma query, permissão ou regra de negócio foi tocada | ok |

---

# 4. Homologação

| Data | Ambiente | Quem validou | Resultado |
|---|---|---|---|
| 2026-08-05 | local (php:8.3-cli + Node 22) | automatizado | BUG-001: backend 37/37 e frontend 35/35 casos aprovados; `php -l` limpo nos 7 arquivos PHP tocados |
| 2026-08-05 | local (mysql:8 + Node 22) | automatizado | BUG-002: 31 migrations aplicadas em banco limpo; as 3 queries alteradas rodaram contra o schema real; 16/16 casos do rótulo da campanha |
|  | homolog (EasyPanel) | pendente | validar as 6 telas da tabela 2.2 após o deploy |

---

# 5. Bugs encontrados

| Código | Descrição | Gravidade | Status | Evidência |
|---|---|---|---|---|
| BUG-002 | Campanha aparecendo `null` no console (tabela, os 5 dropdowns, resumo e carteira). A migration 026 tornou `campanha.nome` opcional e criou `campanha.codigo`; o console.html continuou imprimindo `c.nome` cru, e 4 endpoints ainda entregavam a campanha só pelo nome | média | corrigido | SQL contra o schema real: `campanha_antes = NULL` → `campanha_depois = IFT.2026.IC.GTE.CTE.1`; 16 casos no teste do rótulo |
| BUG-001 | Primeira linha (cabeçalho) importada como registro e colunas lidas por posição, em todas as telas de importação. Causas: (a) detecção de cabeçalho por igualdade exata a `cpf`/`vacina`, quebrada pelo BOM do Excel; (b) telas em JavaScript sem nenhuma detecção; (c) `array_search` devolvendo `false` fazia `$col[false]` = coluna 0, jogando o CPF no campo `nome` | alta | corrigido | `scripts/testar_csv.php` (37 casos, todos PASS) |

---

# 6. Evidências

| Tipo | Link/arquivo | Observação |
|---|---|---|
| teste automatizado | `scripts/testar_csv.php` | backend; roda sem banco, sai 1 se falhar |
| código | `app/helpers/csv.php` | leitor canônico do backend |
| código | `public/assets/csv.js` | espelho no frontend, usado pelas 6 telas |
| saída SQL | BUG-002 | `campanha_antes = NULL` / `campanha_depois = IFT.2026.IC.GTE.CTE.1` na mesma aplicação |
| código | `rotuloCampanha()` / `apelidoCampanha()` em `public/admin/console.html` | regra única de exibição: `codigo \|\| nome \|\| ('#' + id)` |

---

# 7. Checklist

```md
- [x] Fluxos principais testados (importação: elegíveis, histórico, unidades, clientes, grupos).
- [ ] Perfis testados.
- [ ] Erros testados.
- [x] Regressão testada (arquivo sem cabeçalho continua funcionando).
- [x] Evidências registradas.
- [ ] Aprovado para próxima etapa (aguarda validação em homologação).
```
