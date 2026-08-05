<?php
// ============================================================================
// scripts/testar_csv.php
// Teste do leitor de CSV do backend (app/helpers/csv.php) — evidência do BUG-001
// (primeira linha/cabeçalho sendo importada como registro).
//
// Não toca no banco. Rodar:  php scripts/testar_csv.php
// Sai com código 1 se algum caso falhar (serve para CI/pós-deploy).
// ============================================================================

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/app/helpers/csv.php';
// Os serviços abaixo só declaram funções — não abrem conexão com o banco.
require_once BASE_PATH . '/app/services/elegiveis.php';        // parsear_csv_elegiveis()
require_once BASE_PATH . '/app/services/historico_import.php'; // parsear_csv_vacinados_historico()
require_once BASE_PATH . '/app/services/importacao.php';       // importacao_contar()

$ok = 0;
$falhou = 0;

function checar(string $nome, $real, $esperado): void
{
    global $ok, $falhou;
    $a = json_encode($real, JSON_UNESCAPED_UNICODE);
    $b = json_encode($esperado, JSON_UNESCAPED_UNICODE);
    if ($a === $b) {
        $ok++;
        echo "  PASS  {$nome}\n";
    } else {
        $falhou++;
        echo "  FAIL  {$nome}\n        esperado: {$b}\n        obtido:   {$a}\n";
    }
}

function titulo(string $t): void { echo "\n== {$t} ==\n"; }

// Reproduz o parser real usado pelas importações.
function eleg(string $csv): array { return parsear_csv_elegiveis($csv); }

$BOM = "\xEF\xBB\xBF";

// ---------------------------------------------------------------------------
titulo('1. Elegíveis: cabeçalho canônico + BOM do Excel');
$r = eleg($BOM . "cpf,nome,data_nascimento,tipo_vinculo,cpf_titular,codigo_lotacao,codigo_rh,identificador\n"
    . "52998224725,Maria Silva,1990-05-10,colaborador,,LOT-01,RH-1001,\n"
    . "11144477735,Joao Souza,1985-11-02,dependente,52998224725,LOT-01,RH-1002,");
checar('2 registros (cabeçalho não virou pessoa)', count($r), 2);
checar('primeira pessoa', $r[0]['nome'], 'Maria Silva');
checar('cpf da primeira', $r[0]['cpf'], '52998224725');

titulo('2. Elegíveis: ordem trocada + nomes alternativos + delimitador ;');
$r = eleg("Nome Completo;Matrícula;CPF;Data de Nascimento;Lotação\n"
    . "Maria Silva;RH-1001;52998224725;1990-05-10;LOT-01");
checar('1 registro', count($r), 1);
checar('nome mapeado por nome', $r[0]['nome'], 'Maria Silva');
checar('cpf mapeado por nome', $r[0]['cpf'], '52998224725');
checar('nascimento mapeado', $r[0]['data_nascimento'], '1990-05-10');
checar('codigo_rh via "Matrícula"', $r[0]['codigo_rh'], 'RH-1001');
checar('codigo_lotacao via "Lotação"', $r[0]['codigo_lotacao'], 'LOT-01');

titulo('3. Elegíveis SEM cabeçalho (regressão: não pode perder a 1ª linha)');
$r = eleg("52998224725,Maria Silva,1990-05-10,colaborador,,LOT-01,RH-1001,\n"
    . "11144477735,Joao Souza,1985-11-02,dependente,52998224725,LOT-01,RH-1002,");
checar('2 registros', count($r), 2);
checar('1ª linha preservada', $r[0]['nome'], 'Maria Silva');
checar('"colaborador" não foi confundido com cabeçalho', $r[0]['tipo_vinculo'], 'colaborador');

titulo('4. Cabeçalho com coluna ausente (bug do $col[false] -> nome recebia o CPF)');
$r = eleg("cpf;data_nascimento\n52998224725;1990-05-10");
checar('nome vem vazio, não o CPF', $r[0]['nome'], '');
checar('cpf correto', $r[0]['cpf'], '52998224725');
checar('tipo_vinculo ausente = null', $r[0]['tipo_vinculo'], null);

titulo('5. Aspas com o delimitador dentro do campo');
$r = eleg("cpf,nome,data_nascimento\n52998224725,\"Silva, Maria\",1990-05-10");
checar('nome com vírgula preservado', $r[0]['nome'], 'Silva, Maria');
checar('nascimento não deslocou', $r[0]['data_nascimento'], '1990-05-10');

titulo('6. Histórico: cabeçalho fora de ordem com sinônimos');
$r = parsear_csv_vacinados_historico("Imunizante;CPF;Data da Aplicação;Dose;Lote;Nome\n"
    . "Influenza;52998224725;2024-04-10;1;L-999;Maria Silva");
checar('1 registro', count($r), 1);
checar('vacina via "Imunizante"', $r[0]['vacina'], 'Influenza');
checar('aplicado_em via "Data da Aplicação"', $r[0]['aplicado_em'], '2024-04-10');
checar('nome', $r[0]['nome'], 'Maria Silva');
checar('cidade ausente = null', $r[0]['cidade'], null);

titulo('7. Histórico SEM cabeçalho (regressão)');
$r = parsear_csv_vacinados_historico("52998224725;Maria Silva;1990-05-10;Influenza;1;L-999;2024-04-10;LOT-01;Sao Paulo;SP;");
checar('1 registro', count($r), 1);
checar('vacina pela posição', $r[0]['vacina'], 'Influenza');
checar('uf pela posição', $r[0]['uf'], 'SP');

titulo('8. Normalização de nomes de coluna');
checar('"CÓDIGO DE RH" -> codigo_rh', csv_normalizar_chave('  "CÓDIGO DE RH" '), 'codigo_rh');
checar('"Data de Nascimento" -> data_nascimento', csv_normalizar_chave('Data de Nascimento'), 'data_nascimento');
checar('BOM no 1º cabeçalho', csv_normalizar_chave($BOM . 'CPF'), 'cpf');

titulo('9. Casos de borda');
checar('vazio', count(eleg('')), 0);
checar('só cabeçalho', count(eleg('cpf,nome,data_nascimento')), 0);
checar('linhas em branco no meio', count(eleg("cpf,nome\n52998224725,Maria\n\n11144477735,Joao")), 2);
checar('TAB como delimitador', eleg("cpf\tnome\n52998224725\tMaria Silva")[0]['nome'], 'Maria Silva');
checar('detecta cabeçalho', csv_tem_cabecalho("cpf;nome\n1;a", csv_alias_elegiveis()), true);
checar('não detecta cabeçalho em dados', csv_tem_cabecalho("52998224725;Maria Silva", csv_alias_elegiveis()), false);

titulo('10. Contagem que decide inline x fila (importacao_contar)');
$comCab = "cpf;nome\n52998224725;Maria\n11144477735;Joao";
checar('desconta o cabeçalho', importacao_contar($comCab, 'csv'), 2);
checar('sem cabeçalho conta tudo', importacao_contar("52998224725;Maria\n11144477735;Joao", 'csv'), 2);
checar('contagem bate com o parser', importacao_contar($comCab, 'csv'), count(eleg($comCab)));

echo "\n----------------------------------------\n";
echo "{$ok} passaram, {$falhou} falharam\n";
exit($falhou > 0 ? 1 : 0);
