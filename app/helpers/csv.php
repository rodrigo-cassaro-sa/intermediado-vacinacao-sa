<?php
// ============================================================================
// app/helpers/csv.php
// Leitor canônico de CSV usado por TODAS as importações do backend.
//
// Correção BUG-001 (cabeçalho tratado como registro):
//  - a 1ª linha é CABEÇALHO sempre que os nomes das colunas forem reconhecidos;
//  - o mapeamento passa a ser POR NOME -> a ordem das colunas não importa;
//  - BOM do Excel, acentos, maiúsculas, aspas e conectivos ("Data de Nascimento")
//    são normalizados antes da comparação;
//  - coluna ausente no cabeçalho vira null (nunca herda o valor de outra coluna);
//  - arquivo SEM cabeçalho reconhecível continua sendo lido pela ordem padrão
//    (compatibilidade com quem já importa assim).
//
// O espelho em JavaScript é public/assets/csv.js — manter os dois em sincronia.
// ============================================================================

require_once __DIR__ . '/mensagens_importacao.php';

/** Remove o BOM UTF-8 que o Excel grava no início do arquivo. */
function csv_sem_bom(string $texto): string
{
    return (strncmp($texto, "\xEF\xBB\xBF", 3) === 0) ? substr($texto, 3) : $texto;
}

/**
 * Normaliza o nome de uma coluna para comparação:
 * "  Data de Nascimento " -> data_nascimento ; "CÓDIGO RH" -> codigo_rh.
 */
function csv_normalizar_chave(string $texto): string
{
    $t = trim(csv_sem_bom($texto), " \t\"'");

    $acentuadas = ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç','ñ',
                   'Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Õ','Ö','Ú','Ù','Û','Ü','Ç','Ñ'];
    $simples    = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n',
                   'a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','n'];
    $t = strtolower(str_replace($acentuadas, $simples, $t));
    $t = preg_replace('/[^a-z0-9]+/', '_', $t);

    // Descarta conectivos: "data_de_nascimento" -> "data_nascimento".
    $conectivos = ['de', 'do', 'da', 'dos', 'das', 'no', 'na', 'em'];
    $partes = array_filter(explode('_', $t), fn($p) => $p !== '' && !in_array($p, $conectivos, true));

    return implode('_', $partes);
}

/** Descobre o delimitador da linha (; , TAB |). Padrão: vírgula. */
function csv_delimitador(string $linha): string
{
    $contagem = [',' => substr_count($linha, ','), ';' => substr_count($linha, ';'),
                 "\t" => substr_count($linha, "\t"), '|' => substr_count($linha, '|')];
    $melhor = ',';
    foreach ($contagem as $d => $n) {
        if ($n > $contagem[$melhor]) {
            $melhor = $d;
        }
    }
    return $melhor;
}

/** Quebra o conteúdo em linhas (sem BOM, sem linhas vazias no fim). */
function csv_linhas(string $conteudo): array
{
    $linhas = preg_split('/\r\n|\r|\n/', trim(csv_sem_bom($conteudo)));
    return ($linhas === false || $linhas === [''] ) ? [] : $linhas;
}

/** Monta o índice normalizado alias -> campo canônico. */
function csv_indice_alias(array $alias): array
{
    $indice = [];
    foreach ($alias as $campo => $nomes) {
        foreach (array_merge([$campo], $nomes) as $nome) {
            $indice[csv_normalizar_chave($nome)] = $campo;
        }
    }
    return $indice;
}

/**
 * Analisa a 1ª linha e decide se ela é cabeçalho.
 * Devolve ['tem_cabecalho'=>bool, 'posicoes'=>[campo => posição]].
 *
 * Só considera cabeçalho com 2+ colunas reconhecidas (ou todas, em arquivos de
 * 1 coluna). Assim uma linha de DADOS que por acaso tenha uma célula com nome de
 * campo (ex.: tipo_vinculo = "titular") não é confundida com cabeçalho.
 */
function csv_analisar_cabecalho(string $primeiraLinha, array $alias, string $delim): array
{
    $indice   = csv_indice_alias($alias);
    $celulas  = str_getcsv($primeiraLinha, $delim);
    $posicoes = [];

    foreach ($celulas as $pos => $celula) {
        $campo = $indice[csv_normalizar_chave((string) $celula)] ?? null;
        if ($campo !== null && !isset($posicoes[$campo])) {
            $posicoes[$campo] = $pos;
        }
    }
    $reconhecidas = count($posicoes);
    $tem = $reconhecidas >= 2 || ($reconhecidas >= 1 && $reconhecidas === count($celulas));

    return ['tem_cabecalho' => $tem, 'posicoes' => $tem ? $posicoes : []];
}

/** true se a 1ª linha do conteúdo for cabeçalho (usado só para contar linhas). */
function csv_tem_cabecalho(string $conteudo, array $alias): bool
{
    $linhas = csv_linhas($conteudo);
    if (!$linhas) {
        return false;
    }
    $r = csv_analisar_cabecalho($linhas[0], $alias, csv_delimitador($linhas[0]));
    return $r['tem_cabecalho'];
}

/**
 * Converte o CSV em lista de itens associativos.
 *
 * $ordem  = campos na ordem posicional padrão (fallback p/ arquivo sem cabeçalho)
 * $alias  = [campo => [nomes aceitos no cabeçalho]]
 *
 * Todo item devolvido tem TODAS as chaves de $ordem; campo ausente = null.
 */
function csv_parsear(string $conteudo, array $ordem, array $alias): array
{
    $linhas = csv_linhas($conteudo);
    if (!$linhas) {
        return [];
    }
    $delim = csv_delimitador($linhas[0]);
    $head  = csv_analisar_cabecalho($linhas[0], $alias, $delim);

    $posicoes = $head['posicoes'];
    if (!$head['tem_cabecalho']) {
        foreach ($ordem as $pos => $campo) {
            $posicoes[$campo] = $pos;
        }
    }

    $lista = [];
    $total = count($linhas);
    for ($i = $head['tem_cabecalho'] ? 1 : 0; $i < $total; $i++) {
        if (trim($linhas[$i]) === '') {
            continue;
        }
        $col  = str_getcsv($linhas[$i], $delim);
        $item = [];
        foreach ($ordem as $campo) {
            $pos = $posicoes[$campo] ?? null;
            $val = ($pos !== null && isset($col[$pos])) ? trim((string) $col[$pos]) : '';
            $item[$campo] = ($val === '') ? null : $val;
        }
        $lista[] = $item;
    }
    return $lista;
}

/**
 * Confere o conteúdo ANTES de processar e explica o que está errado (BUG-004).
 *
 * Sem isso, um cabeçalho sem a coluna obrigatória produzia N linhas rejeitadas
 * uma a uma, com o mesmo código repetido, sem dizer ao usuário que o problema
 * estava na 1ª linha do arquivo. Espelha CSV.conferir() do public/assets/csv.js.
 *
 * $obrigatorias: colunas que precisam existir quando há cabeçalho.
 * $umaDe: grupos em que ao menos UMA das colunas precisa existir (ex.: cpf|identificador).
 *
 * Devolve ['erro'=>?string, 'aviso'=>?string, 'total'=>int, 'tem_cabecalho'=>bool,
 *          'reconhecidas'=>string[]].
 */
function csv_conferir(string $conteudo, array $ordem, array $alias,
                      array $obrigatorias = [], array $umaDe = []): array
{
    $r = ['erro' => null, 'codigo' => null, 'detalhe' => null, 'aviso' => null,
          'total' => 0, 'tem_cabecalho' => false, 'reconhecidas' => []];

    $linhas = array_values(array_filter(csv_linhas($conteudo), fn($l) => trim($l) !== ''));
    if (!$linhas) {
        $r['codigo'] = 'ARQUIVO_VAZIO';
        $r['erro'] = msg_importacao('ARQUIVO_VAZIO');
        return $r;
    }

    $head = csv_analisar_cabecalho($linhas[0], $alias, csv_delimitador($linhas[0]));
    $r['tem_cabecalho'] = $head['tem_cabecalho'];
    $r['reconhecidas'] = array_keys($head['posicoes']);
    $r['total'] = count($linhas) - ($head['tem_cabecalho'] ? 1 : 0);

    if ($head['tem_cabecalho']) {
        $faltando = array_values(array_filter($obrigatorias, fn($c) => !isset($head['posicoes'][$c])));
        foreach ($umaDe as $grupo) {
            $achou = false;
            foreach ($grupo as $c) {
                if (isset($head['posicoes'][$c])) { $achou = true; break; }
            }
            if (!$achou) { $faltando[] = implode(' ou ', $grupo); }
        }
        if ($faltando) {
            $r['codigo'] = 'COLUNA_OBRIGATORIA_AUSENTE';
            $r['erro'] = msg_importacao('COLUNA_OBRIGATORIA_AUSENTE', ['colunas' => '"' . implode('", "', $faltando) . '"']);
            // Detalhe fica separado da mensagem: a frase principal continua curta.
            $r['detalhe'] = 'Reconheci: ' . ($r['reconhecidas'] ? implode(', ', $r['reconhecidas']) : 'nenhuma coluna')
                . '. Esperado: ' . implode(';', $ordem) . '.';
            return $r;
        }
    } else {
        $r['aviso'] = msg_importacao('CABECALHO_NAO_RECONHECIDO', ['ordem' => implode(', ', $ordem)]);
    }

    if ($r['total'] === 0) {
        $r['codigo'] = 'SEM_LINHAS_DADOS';
        $r['erro'] = msg_importacao('SEM_LINHAS_DADOS');
    }
    return $r;
}

// ---------------------------------------------------------------------------
// Mapas de colunas por tipo de importação.
// Nomes já normalizados por csv_normalizar_chave() na comparação, então
// "CPF do Colaborador", "cpf_colaborador" e "CPF DO COLABORADOR" são o mesmo.
// ---------------------------------------------------------------------------

/** Ordem posicional padrão dos elegíveis (fallback sem cabeçalho). */
function csv_ordem_elegiveis(): array
{
    return ['cpf', 'nome', 'data_nascimento', 'tipo_vinculo', 'cpf_titular',
            'codigo_lotacao', 'codigo_rh', 'identificador'];
}

/** Nomes de coluna aceitos na importação de elegíveis. */
function csv_alias_elegiveis(): array
{
    return [
        'cpf'             => ['cpf_colaborador', 'cpf_funcionario', 'cpf_paciente', 'cpf_beneficiario', 'num_cpf', 'numero_cpf', 'documento'],
        'nome'            => ['nome_completo', 'nome_colaborador', 'nome_funcionario', 'nome_paciente', 'nome_beneficiario'],
        'data_nascimento' => ['nascimento', 'dt_nascimento', 'data_nasc', 'dt_nasc', 'nasc', 'dtnasc', 'data_nascto'],
        'tipo_vinculo'    => ['vinculo', 'tipo', 'parentesco', 'categoria'],
        'cpf_titular'     => ['titular', 'cpf_responsavel'],
        'codigo_lotacao'  => ['lotacao', 'cod_lotacao', 'centro_custo', 'codigo_unidade', 'unidade', 'filial', 'setor', 'departamento'],
        'codigo_rh'       => ['matricula', 'cod_rh', 'matricula_rh', 'registro', 'chapa', 'codigo_funcionario'],
        'identificador'   => ['voucher', 'passaporte', 'codigo_voucher', 'id_externo', 'documento_estrangeiro', 'rne'],
    ];
}

/** Ordem posicional padrão da importação de vacinados em campanha ativa (RN-031). */
function csv_ordem_vacinacao(): array
{
    return ['cpf', 'nome', 'vacina', 'dose', 'lote', 'aplicado_em',
            'profissional_nome', 'profissional_cpf', 'cidade', 'uf', 'unidade',
            'data_nascimento', 'tipo_vinculo', 'cpf_titular', 'codigo_lotacao',
            'codigo_rh', 'identificador'];
}

/** Nomes de coluna aceitos na importação de vacinados em campanha ativa. */
function csv_alias_vacinacao(): array
{
    $a = csv_alias_historico();
    return [
        'cpf'               => $a['cpf'],
        'nome'              => $a['nome'],
        'vacina'            => $a['vacina'],
        'dose'              => $a['dose'],
        'lote'              => $a['lote'],
        'aplicado_em'       => $a['aplicado_em'],
        'profissional_nome' => ['profissional', 'vacinador', 'aplicador', 'enfermeiro', 'responsavel_aplicacao'],
        'profissional_cpf'  => ['cpf_profissional', 'cpf_vacinador', 'cpf_aplicador'],
        'cidade'            => $a['cidade'],
        'uf'                => $a['uf'],
        'unidade'           => ['local_aplicacao', 'local', 'posto', 'sala'],
        'data_nascimento'   => $a['data_nascimento'],
        // Abaixo: só necessários quando a importação pode CRIAR o elegível que
        // ainda não está na lista (RN-016/RN-018 continuam valendo).
        'tipo_vinculo'      => ['vinculo', 'tipo', 'parentesco', 'categoria'],
        'cpf_titular'       => ['titular', 'cpf_responsavel'],
        'codigo_rh'         => ['matricula', 'cod_rh', 'matricula_rh', 'registro', 'chapa', 'codigo_funcionario'],
        // Sem o sinônimo "unidade" aqui: neste mapa 'unidade' é o LOCAL da
        // aplicação, não a lotação do colaborador — os dois coexistem.
        'codigo_lotacao'    => ['lotacao', 'cod_lotacao', 'centro_custo', 'codigo_unidade', 'filial', 'setor', 'departamento'],
        'identificador'     => $a['identificador'],
    ];
}

/** Ordem posicional padrão dos vacinados históricos (fallback sem cabeçalho). */
function csv_ordem_historico(): array
{
    return ['cpf', 'nome', 'data_nascimento', 'vacina', 'dose', 'lote', 'aplicado_em',
            'codigo_lotacao', 'cidade', 'uf', 'identificador'];
}

/** Nomes de coluna aceitos na importação de vacinados históricos. */
function csv_alias_historico(): array
{
    $a = csv_alias_elegiveis();
    return [
        'cpf'             => $a['cpf'],
        'nome'            => $a['nome'],
        'data_nascimento' => $a['data_nascimento'],
        'vacina'          => ['imunizante', 'imunobiologico', 'nome_vacina', 'produto'],
        'dose'            => ['numero_dose', 'num_dose', 'dose_numero', 'n_dose'],
        'lote'            => ['numero_lote', 'num_lote', 'lote_vacina'],
        'aplicado_em'     => ['data_aplicacao', 'dt_aplicacao', 'data_vacinacao', 'data_aplicado', 'aplicacao', 'aplicado', 'data'],
        'codigo_lotacao'  => $a['codigo_lotacao'],
        'cidade'          => ['municipio', 'localidade'],
        'uf'              => ['estado', 'sigla_uf'],
        'identificador'   => $a['identificador'],
    ];
}
