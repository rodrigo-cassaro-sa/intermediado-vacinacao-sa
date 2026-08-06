/* ============================================================================
   public/assets/csv.js
   Leitor canônico de CSV usado por TODAS as telas de importação do admin e do
   portal (elegíveis, unidades, clientes, grupos, vacinados históricos).

   Correção BUG-001 (cabeçalho tratado como registro):
     - a 1ª linha é CABEÇALHO sempre que os nomes das colunas forem reconhecidos;
     - o mapeamento é POR NOME -> a ordem das colunas não importa;
     - BOM do Excel, acentos, maiúsculas, aspas e conectivos ("Data de
       Nascimento") são normalizados antes da comparação;
     - coluna ausente no cabeçalho vira null (nunca herda o valor de outra);
     - arquivo SEM cabeçalho reconhecível continua sendo lido pela ordem padrão.

   Espelho do backend em app/helpers/csv.php — manter os dois em sincronia.
   Uso: CSV.parsear(texto, 'elegiveis') -> [{cpf, nome, ...}, ...]
   ============================================================================ */
(function (global) {
  'use strict';

  var CONECTIVOS = ['de', 'do', 'da', 'dos', 'das', 'no', 'na', 'em'];
  var ACENTOS = { 'á':'a','à':'a','â':'a','ã':'a','ä':'a','é':'e','è':'e','ê':'e','ë':'e',
                  'í':'i','ì':'i','î':'i','ï':'i','ó':'o','ò':'o','ô':'o','õ':'o','ö':'o',
                  'ú':'u','ù':'u','û':'u','ü':'u','ç':'c','ñ':'n' };

  /** Remove o BOM UTF-8 que o Excel grava no início do arquivo. */
  function semBom(texto) {
    return String(texto == null ? '' : texto).replace(/^﻿/, '');
  }

  /** "  Data de Nascimento " -> data_nascimento ; "CÓDIGO RH" -> codigo_rh */
  function normalizarChave(texto) {
    var t = semBom(texto).trim().replace(/^["']+|["']+$/g, '').toLowerCase();
    t = t.replace(/[áàâãäéèêëíìîïóòôõöúùûüçñ]/g, function (c) { return ACENTOS[c] || c; });
    t = t.replace(/[^a-z0-9]+/g, '_');
    return t.split('_').filter(function (p) {
      return p !== '' && CONECTIVOS.indexOf(p) === -1;
    }).join('_');
  }

  /** Descobre o delimitador da linha (; , TAB |). Padrão: vírgula. */
  function delimitador(linha) {
    var cont = { ',': 0, ';': 0, '\t': 0, '|': 0 };
    for (var i = 0; i < linha.length; i++) {
      if (cont[linha[i]] !== undefined) cont[linha[i]]++;
    }
    var melhor = ',';
    [',', ';', '\t', '|'].forEach(function (d) { if (cont[d] > cont[melhor]) melhor = d; });
    return melhor;
  }

  /** Divide uma linha respeitando aspas ("Silva, Maria" continua um campo só). */
  function dividir(linha, delim) {
    var out = [], campo = '', aspas = false;
    for (var i = 0; i < linha.length; i++) {
      var c = linha[i];
      if (aspas) {
        if (c === '"') {
          if (linha[i + 1] === '"') { campo += '"'; i++; } else { aspas = false; }
        } else { campo += c; }
      } else if (c === '"') {
        aspas = true;
      } else if (c === delim) {
        out.push(campo); campo = '';
      } else {
        campo += c;
      }
    }
    out.push(campo);
    return out.map(function (s) { return s.trim(); });
  }

  /** Índice normalizado alias -> campo canônico. */
  function indiceAlias(alias) {
    var idx = {};
    Object.keys(alias).forEach(function (campo) {
      [campo].concat(alias[campo]).forEach(function (nome) { idx[normalizarChave(nome)] = campo; });
    });
    return idx;
  }

  /**
   * Decide se a 1ª linha é cabeçalho. Exige 2+ colunas reconhecidas (ou todas,
   * em arquivos de 1 coluna) para não confundir uma linha de dados que por acaso
   * tenha uma célula com nome de campo.
   */
  function analisarCabecalho(primeiraLinha, alias, delim) {
    var idx = indiceAlias(alias);
    var celulas = dividir(primeiraLinha, delim);
    var posicoes = {}, reconhecidas = 0;

    celulas.forEach(function (celula, pos) {
      var campo = idx[normalizarChave(celula)];
      if (campo && posicoes[campo] === undefined) { posicoes[campo] = pos; reconhecidas++; }
    });
    var tem = reconhecidas >= 2 || (reconhecidas >= 1 && reconhecidas === celulas.length);
    return { temCabecalho: tem, posicoes: tem ? posicoes : {} };
  }

  /**
   * Converte o CSV em lista de objetos.
   * @param {string} texto  conteúdo colado ou lido do arquivo
   * @param {string|object} tipo  nome do mapa ('elegiveis', 'unidades', ...) ou
   *                              {ordem: [...], alias: {...}}
   */
  function parsear(texto, tipo) {
    var def = (typeof tipo === 'string') ? MAPAS[tipo] : tipo;
    if (!def) throw new Error('CSV: mapa desconhecido "' + tipo + '"');

    var linhas = semBom(texto).replace(/^\s+|\s+$/g, '').split(/\r\n|\r|\n/);
    if (!linhas.length || linhas[0] === '') return [];

    var delim = delimitador(linhas[0]);
    var head = analisarCabecalho(linhas[0], def.alias, delim);
    var posicoes = head.posicoes;

    if (!head.temCabecalho) {
      def.ordem.forEach(function (campo, pos) { posicoes[campo] = pos; });
    }

    var lista = [];
    for (var i = head.temCabecalho ? 1 : 0; i < linhas.length; i++) {
      if (linhas[i].trim() === '') continue;
      var col = dividir(linhas[i], delim);
      var item = {};
      def.ordem.forEach(function (campo) {
        var pos = posicoes[campo];
        var val = (pos !== undefined && col[pos] !== undefined) ? String(col[pos]).trim() : '';
        item[campo] = (val === '') ? null : val;
      });
      lista.push(item);
    }
    return lista;
  }

  /**
   * Confere o conteúdo ANTES de enviar e explica o que está errado.
   *
   * Existe porque a tela dizia "Cole ao menos uma linha" quando o usuário tinha
   * colado dezenas — só que o cabeçalho não trazia a coluna obrigatória, todas as
   * linhas caíam no filtro e sumiam sem explicação. Também avisa quando não houve
   * cabeçalho reconhecido e o arquivo foi lido pela ordem padrão, caso em que a
   * própria linha de cabeçalho vira registro.
   *
   * Devolve { vazio, total, temCabecalho, reconhecidas, faltando, erro, aviso, amostra }.
   * `erro` preenchido = não envie. `aviso` = envie, mas mostre ao usuário.
   */
  function conferir(texto, tipo) {
    var def = (typeof tipo === 'string') ? MAPAS[tipo] : tipo;
    if (!def) throw new Error('CSV: mapa desconhecido "' + tipo + '"');

    var limpo = semBom(texto).replace(/^\s+|\s+$/g, '');
    var r = { vazio: limpo === '', total: 0, temCabecalho: false,
              reconhecidas: [], faltando: [], erro: null, aviso: null, amostra: null };
    if (r.vazio) {
      r.erro = 'Cole ao menos uma linha ou escolha um arquivo.';
      return r;
    }

    var linhas = limpo.split(/\r\n|\r|\n/).filter(function (l) { return l.trim() !== ''; });
    var head = analisarCabecalho(linhas[0], def.alias, delimitador(linhas[0]));
    r.temCabecalho = head.temCabecalho;
    r.reconhecidas = Object.keys(head.posicoes);
    r.total = linhas.length - (head.temCabecalho ? 1 : 0);

    var obrig = def.obrigatorias || [];
    if (head.temCabecalho) {
      r.faltando = obrig.filter(function (c) { return head.posicoes[c] === undefined; });
      if (r.faltando.length) {
        r.erro = 'O cabeçalho não traz ' + (r.faltando.length > 1 ? 'as colunas' : 'a coluna')
          + ' ' + r.faltando.map(function (c) { return '"' + c + '"'; }).join(', ')
          + ', então nenhuma linha pôde ser lida. Reconheci: '
          + (r.reconhecidas.length ? r.reconhecidas.join(', ') : 'nenhuma coluna')
          + '. Corrija a 1ª linha do arquivo (ex.: ' + def.ordem.join(';') + ').';
        return r;
      }
    } else {
      // Sem cabeçalho reconhecido o arquivo é lido pela ORDEM padrão — inclusive a
      // 1ª linha. Se ela era um cabeçalho fora do padrão, vira registro.
      r.aviso = 'Não reconheci um cabeçalho: li o arquivo pela ordem padrão '
        + def.ordem.join(', ') + '. Se a 1ª linha for um cabeçalho, ela vai entrar como registro.';
    }

    if (r.total === 0) {
      r.erro = 'O arquivo tem só o cabeçalho, sem nenhuma linha de dados.';
      return r;
    }

    // Como a 1ª linha de dados foi interpretada — a conferência que pega coluna
    // trocada, que nenhuma regra automática detecta.
    var itens = parsear(limpo, def);
    r.amostra = itens.length ? itens[0] : null;
    return r;
  }

  // -------------------------------------------------------------------------
  // Mapas de colunas por tipo de importação.
  // ordem = posição padrão quando o arquivo NÃO tem cabeçalho reconhecível.
  // alias = outros nomes aceitos no cabeçalho (o nome canônico já vale sempre).
  // -------------------------------------------------------------------------
  var ALIAS_PESSOA = {
    cpf:             ['cpf_colaborador', 'cpf_funcionario', 'cpf_paciente', 'cpf_beneficiario', 'num_cpf', 'numero_cpf', 'documento'],
    nome:            ['nome_completo', 'nome_colaborador', 'nome_funcionario', 'nome_paciente', 'nome_beneficiario'],
    data_nascimento: ['nascimento', 'dt_nascimento', 'data_nasc', 'dt_nasc', 'nasc', 'dtnasc', 'data_nascto'],
    codigo_lotacao:  ['lotacao', 'cod_lotacao', 'centro_custo', 'codigo_unidade', 'unidade', 'filial', 'setor', 'departamento'],
    identificador:   ['voucher', 'passaporte', 'codigo_voucher', 'id_externo', 'documento_estrangeiro', 'rne']
  };

  var MAPAS = {
    elegiveis: {
      obrigatorias: ['nome'],
      ordem: ['cpf', 'nome', 'data_nascimento', 'tipo_vinculo', 'cpf_titular', 'codigo_lotacao', 'codigo_rh', 'identificador'],
      alias: {
        cpf:             ALIAS_PESSOA.cpf,
        nome:            ALIAS_PESSOA.nome,
        data_nascimento: ALIAS_PESSOA.data_nascimento,
        tipo_vinculo:    ['vinculo', 'tipo', 'parentesco', 'categoria'],
        cpf_titular:     ['titular', 'cpf_responsavel'],
        codigo_lotacao:  ALIAS_PESSOA.codigo_lotacao,
        codigo_rh:       ['matricula', 'cod_rh', 'matricula_rh', 'registro', 'chapa', 'codigo_funcionario'],
        identificador:   ALIAS_PESSOA.identificador
      }
    },
    historico: {
      obrigatorias: ['vacina', 'aplicado_em'],
      ordem: ['cpf', 'nome', 'data_nascimento', 'vacina', 'dose', 'lote', 'aplicado_em', 'codigo_lotacao', 'cidade', 'uf', 'identificador'],
      alias: {
        cpf:             ALIAS_PESSOA.cpf,
        nome:            ALIAS_PESSOA.nome,
        data_nascimento: ALIAS_PESSOA.data_nascimento,
        vacina:          ['imunizante', 'imunobiologico', 'nome_vacina', 'produto'],
        dose:            ['numero_dose', 'num_dose', 'dose_numero', 'n_dose'],
        lote:            ['numero_lote', 'num_lote', 'lote_vacina'],
        aplicado_em:     ['data_aplicacao', 'dt_aplicacao', 'data_vacinacao', 'data_aplicado', 'aplicacao', 'aplicado', 'data'],
        codigo_lotacao:  ALIAS_PESSOA.codigo_lotacao,
        cidade:          ['municipio', 'localidade'],
        uf:              ['estado', 'sigla_uf'],
        identificador:   ALIAS_PESSOA.identificador
      }
    },
    unidades: {
      obrigatorias: ['nome'],
      ordem: ['nome', 'codigo_lotacao', 'cidade', 'uf'],
      alias: {
        nome:           ['nome_unidade', 'unidade', 'descricao', 'local'],
        codigo_lotacao: ['lotacao', 'cod_lotacao', 'codigo', 'cod', 'centro_custo', 'codigo_unidade'],
        cidade:         ['municipio', 'localidade'],
        uf:             ['estado', 'sigla_uf']
      }
    },
    clientes: {
      obrigatorias: ['razao_social'],
      ordem: ['razao_social', 'cnpj', 'sigla', 'grupo_sigla'],
      alias: {
        razao_social: ['razao', 'nome', 'empresa', 'cliente', 'nome_empresa'],
        cnpj:         ['documento', 'cnpj_empresa', 'num_cnpj'],
        sigla:        ['apelido', 'nome_fantasia', 'fantasia', 'codigo', 'abreviacao'],
        grupo_sigla:  ['grupo', 'sigla_grupo', 'grupo_empresarial', 'codigo_grupo']
      }
    },
    grupos: {
      obrigatorias: ['nome'],
      ordem: ['nome', 'sigla'],
      alias: {
        nome:  ['grupo', 'nome_grupo', 'descricao', 'grupo_empresarial'],
        sigla: ['codigo', 'apelido', 'abreviacao', 'sigla_grupo']
      }
    }
  };

  global.CSV = {
    parsear: parsear,
    conferir: conferir,
    normalizarChave: normalizarChave,
    delimitador: delimitador,
    dividir: dividir,
    MAPAS: MAPAS
  };
})(typeof window !== 'undefined' ? window : this);
