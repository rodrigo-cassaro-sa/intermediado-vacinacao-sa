# Mensagens de Erro das Importações

## Objetivo

Fonte oficial do que o usuário lê quando uma importação falha. Cada problema tem
**um código estável** e **uma mensagem só** — a mesma no relatório da tela, no CSV
de erros baixado e na resposta da API.

Existe porque as mensagens estavam espalhadas: o mesmo problema chegava ao usuário
escrito de três jeitos diferentes, em frases longas que não diziam o que fazer.

---

# 1. Padrão da mensagem

```txt
"<Problema>."  +  opcionalmente " <O que fazer>."
```

| Regra | Certo | Errado |
|---|---|---|
| Direta, até ~120 caracteres | "CPF inválido." | "O CPF informado nesta linha não passou na validação de dígito verificador." |
| Ação imperativa, dizendo onde mexer | "Falta a coluna "nome" no cabeçalho. Corrija a 1ª linha do arquivo." | "O cabeçalho não traz a coluna nome, então nenhuma linha pôde ser lida." |
| Sem jargão interno | "Sem CPF e sem identificador. Informe um dos dois." | "Campo obrigatório do ctx ausente: identidade null." |
| Sem culpa nem desculpa | "Vacina não prevista nesta campanha." | "Você errou a vacina." / "Desculpe, algo deu errado." |

Detalhe técnico longo (o que foi reconhecido, o que era esperado) vai no campo
**`detalhe`**, separado da mensagem — a frase principal continua curta.

> **O código é contrato.** Ele aparece na API e na coluna `codigo_erro` do CSV de
> erros; integrações podem depender dele. A **mensagem** pode ser reescrita a
> qualquer momento; o **código, não**.

---

# 2. Arquivo e cabeçalho

Conferidos **antes** de processar, para o usuário não descobrir o problema linha a
linha. Bloqueiam a importação, exceto onde indicado.

| Código | Quando acontece | Mensagem ao usuário | O que o usuário faz |
|---|---|---|---|
| `ARQUIVO_VAZIO` | Nada colado e nenhum arquivo escolhido | Nenhuma linha para importar. Cole as linhas ou escolha um arquivo. | Cola o conteúdo ou anexa o CSV |
| `SEM_LINHAS_DADOS` | Só o cabeçalho, sem dados | O arquivo tem só o cabeçalho. Inclua ao menos uma linha de dados. | Adiciona as linhas |
| `COLUNA_OBRIGATORIA_AUSENTE` | Cabeçalho reconhecido, mas sem uma coluna sem a qual nenhuma linha pode ser lida | Falta a coluna {colunas} no cabeçalho. Corrija a 1ª linha do arquivo. | Corrige o nome da coluna na 1ª linha |
| `CABECALHO_NAO_RECONHECIDO` | Nenhum nome de coluna reconhecido na 1ª linha | Nomes de coluna não reconhecidos na 1ª linha. Use: {ordem}. | Corrige os nomes na 1ª linha (o `detalhe` mostra o que foi lido) |
| `NENHUMA_LINHA_UTIL` | Cabeçalho ok, mas nenhuma linha tem o campo obrigatório preenchido | Nenhuma das {total} linha(s) pôde ser usada. Confira as colunas obrigatórias. | Preenche o campo obrigatório |
| `ARQUIVO_GRANDE` | Upload acima de 20 MB | Arquivo muito grande. Envie até 20 MB. | Divide o arquivo |
| `ARQUIVO_INVALIDO` | Extensão diferente de .csv/.txt | Formato não aceito. Envie um arquivo .csv. | Salva como CSV |

> **A 1ª linha é sempre o cabeçalho.** Não existe leitura posicional: se os nomes não forem
> reconhecidos, a importação para. Adivinhar a ordem fazia a própria linha de cabeçalho
> entrar como registro quando o usuário errava um nome de coluna.

## Colunas obrigatórias por importação

| Importação | Obrigatórias | Nomes canônicos das colunas |
|---|---|---|
| Elegíveis | `nome` + (`cpf` **ou** `identificador`) | cpf, nome, data_nascimento, tipo_vinculo, cpf_titular, codigo_lotacao, codigo_rh, identificador |
| Vacinados em massa | `cpf` **ou** `identificador` | cpf, nome, vacina, dose, lote, aplicado_em, profissional_nome, profissional_cpf, cidade, uf, unidade, … |
| Unidades | `nome` | nome, codigo_lotacao, cidade, uf |
| Clientes | `razao_social` | razao_social, cnpj, sigla, grupo_sigla |
| Grupos | `nome` | nome, sigla |

---

# 3. Identidade da pessoa

| Código | Quando acontece | Mensagem ao usuário |
|---|---|---|
| `CPF_INVALIDO` | CPF com dígito verificador errado | CPF inválido. |
| `SEM_IDENTIDADE` | Linha sem CPF e sem identificador/voucher | Sem CPF e sem identificador. Informe um dos dois. |
| `NOME_OBRIGATORIO` | Nome em branco | Nome não informado. |
| `DATA_NASCIMENTO_INVALIDA` | Data que não existe ou fora do formato | Data de nascimento inválida. Use AAAA-MM-DD. |

---

# 4. Vínculo e códigos do cliente (lista de elegíveis)

| Código | Quando acontece | Mensagem ao usuário |
|---|---|---|
| `TIPO_VINCULO_INVALIDO` | Vínculo fora da lista (RN-016) | Tipo de vínculo inválido. Use colaborador, dependente ou terceiro. |
| `CPF_TITULAR_INVALIDO` | Dependente com CPF de titular inválido (RN-017) | CPF do titular inválido. |
| `CPF_TITULAR_NAO_ELEGIVEL` | Titular não é colaborador elegível na campanha | O titular não é colaborador elegível nesta campanha. |
| `CODIGO_LOTACAO_OBRIGATORIO` | Lotação em branco (RN-018) | Código de lotação não informado. |
| `CODIGO_RH_OBRIGATORIO` | Matrícula em branco (RN-018) | Matrícula não informada. |

---

# 5. Vacinação (registro de dose)

| Código | Quando acontece | Mensagem ao usuário |
|---|---|---|
| `NAO_ELEGIVEL` | CPF fora da lista da campanha | Não está na lista de elegíveis da campanha. |
| `ELEGIVEL_NAO_CRIADO` | "Criar elegível" marcado, mas os dados da linha não bastam | Não foi possível criar o elegível com os dados desta linha. |
| `VACINA_OBRIGATORIA` | Sem vacina na linha e sem vacina nos dados do lote | Vacina não informada na linha nem nos dados do lote. |
| `VACINA_FORA_DA_CAMPANHA` | Vacina não está em `campanha_vacina` (RN-003) | Vacina não prevista nesta campanha. |
| `VACINADO_DUPLICADO` | Mesma dose da mesma vacina já registrada (RN-013) | Esta dose desta vacina já consta para o paciente. |
| `FORA_DO_PERIODO` | Data fora da janela da campanha (RN-003) | Data de aplicação fora do período da campanha. |
| `DATA_INVALIDA` | Data de aplicação ilegível | Data de aplicação inválida. Use AAAA-MM-DD. |
| `CAMPO_OBRIGATORIO` | Falta lote, data, profissional, cidade ou UF (RN-019) | Falta um campo obrigatório: lote, data, profissional, cidade ou UF. |
| `CPF_PROFISSIONAL_INVALIDO` | CPF de quem aplicou é inválido (RN-019) | CPF do profissional inválido. |
| `UF_INVALIDA` | UF sem 2 letras | UF inválida. Use 2 letras. |
| `CAMPANHA_INATIVA` | Campanha em rascunho ou encerrada | A campanha não está ativa. |
| `CAMPANHA_NAO_ENCONTRADA` | Campanha inexistente ou fora do escopo | Campanha não encontrada. |

---

# 6. Onde isso vive no código

```txt
app/helpers/mensagens_importacao.php   catálogo do backend  -> msg_importacao($codigo, $ctx)
public/assets/mensagens.js             catálogo das telas   -> MSG.texto(codigo, ctx)
```

Os dois têm a **mesma tabela** e precisam ser alterados juntos. Quem consome:

| Consumidor | Como usa |
|---|---|
| `csv_conferir()` / `CSV.conferir()` | devolvem `codigo`, `erro` (mensagem) e `detalhe` |
| `motivo_erro_importacao()` | relatório de erros dos elegíveis (CSV baixado) |
| `motivo_erro_vacinacao()` | relatório de erros dos vacinados (tela e CSV) |
| Telas de importação | exibem `erro` + `detalhe`; o semáforo é decidido pelo resultado, não pela mensagem |

## Como adicionar um código novo

```txt
1. Escolha um código em MAIÚSCULO_COM_UNDERSCORE, específico (não "ERRO_GERAL").
2. Escreva a mensagem no padrão da seção 1.
3. Adicione nos DOIS catálogos (PHP e JS) com o mesmo texto.
4. Registre na tabela deste documento.
5. Se for erro de linha, garanta que o código seja gravado na tabela de erros da
   importação — é ele que vai para o CSV que o cliente baixa.
```

---

# 7. Semáforo da tela

A cor não vem da mensagem, e sim do resultado:

| Cor | Quando | Exemplo |
|---|---|---|
| 🟢 verde | Entrou tudo, sem ressalva | "Concluído: 12 nova(s), 0 atualizada(s)." |
| 🟡 amarelo | Entrou, mas com ressalva (rejeitadas, descartadas ou aviso de cabeçalho) | "Concluído: 10 nova(s), 2 rejeitada(s)." |
| 🔴 vermelho | Nada entrou | "Nada foi importado: 0 nova(s), 0 atualizada(s)." |

Detalhe em docs/12 §2.1e.
