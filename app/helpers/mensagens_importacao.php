<?php
// ============================================================================
// app/helpers/mensagens_importacao.php
// Catálogo ÚNICO das mensagens de erro das importações.
//
// Antes cada arquivo montava a própria frase, e o mesmo problema chegava ao
// usuário escrito de três jeitos diferentes. Aqui o código é a chave estável (é
// contrato de API) e a mensagem é o texto que aparece na tela.
//
// PADRÃO DA MENSAGEM
//   "<Problema>." + opcionalmente " <O que fazer>."
//   - direta, no máximo ~120 caracteres;
//   - sem jargão interno (nada de "parser", "null", "campo obrigatório do ctx");
//   - a ação é imperativa e diz onde mexer ("Corrija a 1ª linha do arquivo.").
//
// Espelho em public/assets/mensagens.js — manter os dois iguais.
// Tabela publicada em docs/19-mensagens-erro-importacao.md.
// ============================================================================

/**
 * Mensagens por código. {chaves} são substituídas pelo contexto.
 * NÃO mude um código sem atualizar o doc 19 e o espelho em JS: o código é o que
 * a API devolve e o que o relatório de erros grava.
 */
function mensagens_importacao(): array
{
    return [
        // --- Arquivo e cabeçalho (conferidos antes de processar) ---
        'ARQUIVO_VAZIO'              => 'Nenhuma linha para importar. Cole as linhas ou escolha um arquivo.',
        'SEM_LINHAS_DADOS'           => 'O arquivo tem só o cabeçalho. Inclua ao menos uma linha de dados.',
        'COLUNA_OBRIGATORIA_AUSENTE' => 'Falta a coluna {colunas} no cabeçalho. Corrija a 1ª linha do arquivo.',
        'CABECALHO_NAO_RECONHECIDO'  => 'Nomes de coluna não reconhecidos na 1ª linha. Use: {ordem}.',
        'NENHUMA_LINHA_UTIL'         => 'Nenhuma das {total} linha(s) pôde ser usada. Confira as colunas obrigatórias.',
        'ARQUIVO_GRANDE'             => 'Arquivo muito grande. Envie até 20 MB.',
        'ARQUIVO_INVALIDO'           => 'Formato não aceito. Envie um arquivo .csv.',

        // --- Identidade da pessoa ---
        'CPF_INVALIDO'               => 'CPF inválido.',
        'SEM_IDENTIDADE'            => 'Sem CPF e sem identificador. Informe um dos dois.',
        'NOME_OBRIGATORIO'           => 'Nome não informado.',
        'DATA_NASCIMENTO_INVALIDA'   => 'Data de nascimento inválida. Use AAAA-MM-DD.',

        // --- Vínculo e códigos do cliente (lista de elegíveis) ---
        'TIPO_VINCULO_INVALIDO'      => 'Tipo de vínculo inválido. Use colaborador, dependente ou terceiro.',
        'CPF_TITULAR_INVALIDO'       => 'CPF do titular inválido.',
        'CPF_TITULAR_NAO_ELEGIVEL'   => 'O titular não é colaborador elegível nesta campanha.',
        'CODIGO_LOTACAO_OBRIGATORIO' => 'Código de lotação não informado.',
        'CODIGO_RH_OBRIGATORIO'      => 'Matrícula não informada.',

        // --- Vacinação (registro de dose) ---
        'NAO_ELEGIVEL'               => 'Não está na lista de elegíveis da campanha.',
        'ELEGIVEL_NAO_CRIADO'        => 'Não foi possível criar o elegível com os dados desta linha.',
        'VACINA_OBRIGATORIA'         => 'Vacina não informada na linha nem nos dados do lote.',
        'VACINA_FORA_DA_CAMPANHA'    => 'Vacina não prevista nesta campanha.',
        'VACINADO_DUPLICADO'         => 'Esta dose desta vacina já consta para o paciente.',
        'FORA_DO_PERIODO'            => 'Data de aplicação fora do período da campanha.',
        'DATA_INVALIDA'              => 'Data de aplicação inválida. Use AAAA-MM-DD.',
        'CAMPO_OBRIGATORIO'          => 'Falta um campo obrigatório: lote, data, profissional, cidade ou UF.',
        'CPF_PROFISSIONAL_INVALIDO'  => 'CPF do profissional inválido.',
        'UF_INVALIDA'                => 'UF inválida. Use 2 letras.',
        'CAMPANHA_INATIVA'           => 'A campanha não está ativa.',
        'CAMPANHA_NAO_ENCONTRADA'    => 'Campanha não encontrada.',
    ];
}

/**
 * Texto de um código, com o contexto interpolado.
 * Código desconhecido volta como está — melhor mostrar a sigla do que uma frase
 * inventada que esconde um caso não catalogado.
 */
function msg_importacao(string $codigo, array $ctx = []): string
{
    $texto = mensagens_importacao()[$codigo] ?? $codigo;
    foreach ($ctx as $chave => $valor) {
        $texto = str_replace('{' . $chave . '}', (string) $valor, $texto);
    }
    return $texto;
}
