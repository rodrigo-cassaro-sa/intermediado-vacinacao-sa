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
| CA-006 | Lista de 20.000 a 30.000 elegíveis é importada por completo, sem cron externo | Subir o container e acompanhar a fila drenar | atendido |
| CA-007 | O usuário vê o progresso da importação grande até concluir, em vez de "processando em segundo plano" para sempre | Importar >2000 linhas pela tela de elegíveis | atendido (falta validar em homolog) |
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

## 2.1c Importação de listas grandes (BUG-003)

Até ~2000 linhas a importação é processada na hora (inline). Acima disso vai para a
**fila**, que agora é drenada pelo worker embutido no container (`docker/entrypoint.sh`),
sem depender de Cron Job configurado à mão no painel.

| Fluxo | Passos | Resultado esperado | Status |
|---|---|---|---|
| Fila drena sozinha | Subir o container com uma importação `pendente` de 30.000 linhas e nenhum cron | Importação vira `concluida` com 30.000 processados | ok (auto) |
| Volume de 20k–30k | CSV de 30.000 com CPFs válidos e distintos | 30.000 elegíveis e 30.000 pacientes no banco, 0 rejeitados | ok (auto) |
| Dois workers ao mesmo tempo | Rodar 2 instâncias do worker na mesma fila | O segundo sai com "Outro worker já está processando a fila" | ok (auto) |
| Importação travada | Deixar uma importação em `processando` há 90 min | O worker devolve para `pendente` e reprocessa | ok (auto) |
| Progresso na tela | Importar >2000 linhas em /admin/elegiveis.html | Mensagem vai de "recebida" → "N de M linha(s)" → "concluída" | pendente (homolog) |

## 2.1d Vacinar pelo admin/portal e importar vacinados em massa (RN-031)

Automatizados contra banco real (31 migrations + fixture de campanha ativa): 27 casos.

| Fluxo | Passos | Resultado esperado | Status |
|---|---|---|---|
| Simulação não grava | CSV com 5 linhas (3 válidas, 1 fora da lista, 1 vacina inexistente) | `simulada`, 3 seriam registradas, 2 rejeitadas, **0 aplicações e 0 elegíveis no banco** | ok (auto) |
| Motivos por linha | idem acima | `NAO_ELEGIVEL` e `VACINA_FORA_DA_CAMPANHA` no relatório | ok (auto) |
| Confirmação entrega o prometido | Confirmar o lote simulado | 3 aplicações gravadas, elegíveis viram `aplicado`, todas com `importacao_aplicacoes_id` | ok (auto) |
| Não confirma duas vezes | Confirmar de novo | `LOTE_NAO_SIMULADO` | ok (auto) |
| Estorno do lote | Estornar com justificativa | 3 estornadas, elegíveis voltam a `pendente`; 2ª tentativa dá `LOTE_JA_ESTORNADO` | ok (auto) |
| Criar elegível — sem os dados | `criar_elegivel` marcado, arquivo sem tipo_vinculo/lotação/matrícula | Simulação **recusa** com `TIPO_VINCULO_INVALIDO` (não promete o que não grava) | ok (auto) |
| Criar elegível — com os dados | idem com as colunas | Elegível criado na confirmação e vacinado | ok (auto) |
| Dose duplicada | Reimportar a mesma dose | `VACINADO_DUPLICADO` | ok (auto) |
| Fora do período | Data fora da janela da campanha | `FORA_DO_PERIODO` | ok (auto) |
| Vacina por nome e por sigla | `IF3` e `Influenza (Gripe)` | Ambas resolvem | ok (auto) |
| Cabeçalho fora de ordem | `Imunizante;CPF;Dose;...;Vacinador;...` | Mapeado por nome (reusa BUG-001) | ok (auto) |
| Histórico de lotes | Listar importações da campanha | Traz os 6 lotes, com quem importou e — no estornado — quem desfez e o motivo | ok (auto) |
| Vacinar pelo portal | Usuário de portal registra vacinação | Antes: **403**. Agora: registra, com autor no histórico | pendente (homolog) |
| Cliente confere o que enviou | Abrir "Importar vacinados" no portal | Vê o histórico dos lotes, **sem** o link de estornar | pendente (homolog) |

## 2.1e Mapeamento de erro e resposta visual das importações (BUG-004)

Regra única em `CSV.conferir()` (frontend) e `csv_conferir()` (backend). Semáforo da tela:
**verde** só quando algo entrou sem ressalva · **amarelo** entrou com ressalva · **vermelho**
nada entrou.

| Fluxo | Passos | Resultado esperado | Status |
|---|---|---|---|
| Cabeçalho sem a coluna obrigatória | `Filial;Codigo;Cidade;UF` em Unidades | 🔴 "O cabeçalho não traz a coluna *nome*… Reconheci: codigo_lotacao, cidade, uf" — **não envia** | ok (auto) |
| Nada colado | Campo vazio | 🔴 "Cole ao menos uma linha ou escolha um arquivo" | ok (auto) |
| Só o cabeçalho | Arquivo sem linhas de dados | 🔴 "só o cabeçalho, sem nenhuma linha de dados" | ok (auto) |
| Cabeçalho irreconhecível | `aaa;bbb;ccc;ddd` | Envia (leitura posicional), mas 🟡 com aviso "li pela ordem padrão; a 1ª linha entrou como registro" | ok (auto) |
| Backend rejeitou tudo | 0 inseridos, 0 atualizados | 🔴 "Nada foi importado" — **antes ficava verde "Concluído"** | ok (auto) |
| Entrou em parte | 1 de 2 linhas | 🟡 com a contagem de rejeitadas | ok (auto) |
| Coluna trocada | Cabeçalho de elegíveis na tela de unidades | Envia (não há regra que detecte), mas o relatório mostra **"Como li a 1ª linha: nome=LOT-01 …"** para conferência humana | ok (auto) |
| Linhas descartadas | Linhas sem o campo obrigatório | Aparecem como "Descartadas antes do envio" — **antes sumiam em silêncio** | ok (auto) |
| Relatório fantasma | Importar OK, depois tentar com arquivo ruim | O relatório verde anterior é limpo a cada tentativa | ok (revisado) |
| Backend: elegíveis/vacinados | Upload com cabeçalho sem `nome` ou sem `cpf`/`identificador` | 422 `CABECALHO_INVALIDO` com o motivo, antes de processar linha a linha | ok (auto) |

## 2.1f Status da unidade (BUG-005)

Vocabulário do status concorda com o gênero da entidade (doc 17 §2). Unidade é
feminina: `ativa`/`inativa`.

| Fluxo | Passos | Resultado esperado | Status |
|---|---|---|---|
| Unidade nova | Criar pela tela | Nasce `ativa`, aparece no filtro padrão "Ativas", badge verde | ok (auto) |
| Unidade importada | Importar CSV | Nasce `ativa` | ok (auto) |
| Editar | Abrir o cadastro de uma unidade ativa | Select abre em "Ativa" — **antes vinha em branco** | ok (auto) |
| Desativar | Marcar Inativa e salvar | Some do filtro "Ativas", aparece em "Inativas", badge cinza | ok (auto) |
| Reimportar desativada | Importar CSV com a mesma unidade | Continua inativa — o UPDATE do import não toca em status | ok (revisado) |
| Migration 032 | Base com `ativo`/`inativo`/vazio | Vira `ativa`/`inativa`/`ativa`; rodar de novo não muda nada | ok (auto, MySQL real) |
| Legado na tela | Linha ainda com `ativo` | Badge e formulário tratam como ativa (tolerância durante o deploy) | ok (auto) |

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
| Registro unitário de aplicação | `processar_aplicacao()` teve a validação extraída para `validar_aplicacao()`; a regra não mudou, só passou a ser compartilhada com a simulação | ok (auto — 27 casos exercitam os dois caminhos) |
| Estorno e retificação unitários | Continuam restritos a super_admin/operador_interno, com motivo | ok (não alterado) |
| Faturamento por clínica | `executor_tipo = 'clinica_credenciada'` é preservado quando o lote informa a clínica | ok (revisado) |
| Importação pequena (≤2000) | Continua inline, respondendo na hora, sem passar pela fila | ok (não alterado) |
| Worker por Cron Job | Quem já tiver os Cron Jobs do painel pode manter: o `flock` evita processamento duplicado, e `WORKERS_EMBUTIDOS=false` desliga o worker do container | ok (auto — fase B) |
| Limite `IMPORTACAO_LIMITE_SINCRONO` | Mantido em 2000 — aumentar jogaria lote grande para dentro da requisição web (60s de `max_execution_time`) | ok (não alterado) |
| Campanhas antigas (pré-026) | Sem `codigo`, continuam visíveis pelo nome em listas e dropdowns | ok (auto + SQL com fixture `codigo = NULL`) |
| Contrato `/parceiro/carteira` | Campo `campanha` continua existindo (agora preenchido com o código em vez de null) | ok (SQL) |
| Dropdown de clínicas | `carregarClinicas()` usa `c.nome` de propósito — `clinica.nome` não é opcional | ok (não alterado) |
| Campo `nome` da campanha | Continua sendo gravado, editável e retornado pela API | ok (não alterado) |
| Importação sem cabeçalho | Arquivo posicional (sem cabeçalho) mantém a 1ª linha como pessoa/registro válido — **não é bloqueado** pela conferência do BUG-004, só recebe aviso | ok (auto — casos 3 e 7 + BUG-004 caso 7) |
| Modelo oficial | O CSV baixado pelo botão "Baixar modelo" importa sem aviso nenhum (verde) | ok (auto) |
| Status de grupo e cliente | `grupo_empresarial` e `cliente_b2b` são masculinos e continuam `ativo`/`inativo` — a correção do BUG-005 não os tocou | ok (revisado) |
| Migration 032 | Não altera nenhuma linha já correta (`ativa`/`inativa`) e é idempotente | ok (auto) |
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
| 2026-08-05 | local (imagem real do projeto + mysql:8) | automatizado | BUG-003: container normal, **sem nenhum cron**, drenou 30.000 elegíveis em **285s** (0 → 29.999 no banco); trava e recuperação de importação travada validadas |
| 2026-08-06 | local (Node 22 + mysql:8) | automatizado | BUG-005: 17/17 casos da tela + ciclo de status em MySQL real (migrations 000..032). Suíte completa: 125 casos (17 + 25 + 37 + 16 + 30) |
| 2026-08-06 | local (Node 22 + php:8.3-cli) | automatizado | BUG-004: 25/25 casos de mapeamento de erro e resposta visual |
| 2026-08-05 | local (imagem real do projeto + mysql:8) | automatizado | RN-031: 27/27 casos da importação de vacinados (simulação, confirmação, criar elegível, estorno de lote, duplicidade, período, vacina por nome/sigla) |
|  | homolog (EasyPanel) | pendente | validar as 6 telas da tabela 2.2 após o deploy |

---

# 5. Bugs encontrados

| Código | Descrição | Gravidade | Status | Evidência |
|---|---|---|---|---|
| BUG-005 | Unidades só apareciam no filtro "Todas", eram exibidas como "Inativas" e abriam com o status em branco na edição. Causa: `unidade.status` nasce `'ativa'` (DEFAULT da coluna, gênero feminino como vacina/clinica/campanha), mas a API validava `['ativo','inativo']` e a tela comparava com `'ativo'` — um único descasamento de vocabulário produzindo os três sintomas | média | corrigido | 17 casos sobre as expressões reais da tela + ciclo completo em MySQL real (base suja → migration 032 → idempotência) |
| BUG-004 | Importação com cabeçalho errado: a tela acusava "Cole ao menos uma linha" para quem tinha colado dezenas (as linhas caíam no `.filter()` do frontend por falta da coluna obrigatória e sumiam sem explicação). Pior, o `msg ok` verde era aplicado mesmo com 0 inseridos, e um cabeçalho irreconhecível virava registro em silêncio — o relatório da tentativa anterior também ficava na tela | alta | corrigido | 25 casos automatizados sobre as funções reais das telas; antes/depois de cada cabeçalho documentado em 2.1e |
| BUG-003 | Importação acima de ~2000 linhas nunca acontecia: o lote ia para a fila (`pendente`) e o worker `scripts/processar_importacoes.php` dependia de um Cron Job do EasyPanel que nunca foi criado. Abaixo de 2000 funcionava (inline), o que dava a impressão de um "teto" de ~1500 | alta | corrigido | Container normal, sem cron nenhum, drenou 30.000 linhas; `worker.log` linha 1: "Processando importação de elegíveis #1 ... concluída #1" |
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
| log do worker | BUG-003 fase A | `Processando importação de elegíveis #1 ... concluída #1` — antes: `pendente 0/30000`; depois: `concluida 30000/30000`, 29.999 válidos, 1 rejeitado (CPF `00000000000`, inválido de verdade) |
| saída do worker | BUG-003 fase B | worker 1: `Outro worker já está processando a fila; saindo.` · worker 2: `Processando importação de elegíveis #2 ... concluída #2` |
| saída do worker | BUG-003 fase C | `Reenfileiradas 1 importacao(oes) travada(s).` seguida do reprocessamento até `concluída #2` |
| código | `rotuloCampanha()` / `apelidoCampanha()` em `public/admin/console.html` | regra única de exibição: `codigo \|\| nome \|\| ('#' + id)` |
| código | `CSV.conferir()` / `csv_conferir()` | regra única de conferência de cabeçalho, com `obrigatorias` por tipo de importação |

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
