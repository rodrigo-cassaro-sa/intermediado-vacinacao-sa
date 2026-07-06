# Skill — Boas Práticas de Lógica, Sintaxe, Comentários e Código Legível

> Guia para orientar uma IA ou programador a desenvolver, revisar, manter e refatorar sistemas, arquivos e funções com código limpo, organizado, comentado e fácil de dar manutenção.

---

## Limite desta skill

Esta skill define padrão de legibilidade, comentários, organização e manutenção segura do código.

Ela deve ser aplicada em qualquer linguagem ou arquivo do projeto, mas não deve substituir:

- `skill-php.md` para boas práticas específicas de PHP procedural;
- `skill-js.md` para JavaScript puro;
- `skill-backend.md` para regra de negócio no servidor;
- `skill-refatoracao-code-review.md` para revisão e refatoração profunda;
- `skill-documentacao-projeto.md` para documentação externa do projeto.

Esta skill responde "o código está claro, comentado, organizado e fácil de manter?".

---

## 1. Objetivo da skill

Esta skill deve ser usada sempre que uma IA ou desenvolvedor for:

- Criar um novo arquivo de código.
- Criar uma função, rotina, endpoint, tela, componente, script ou automação.
- Refatorar código existente.
- Corrigir bugs.
- Dar manutenção em sistema, SaaS, app, painel administrativo, API ou integração.
- Melhorar legibilidade, organização, lógica, sintaxe e padronização.

O objetivo principal é garantir que qualquer programador ou IA consiga entender rapidamente:

- Para que o arquivo existe.
- O que cada função faz.
- Onde pode mexer com segurança.
- O que não deve ser alterado sem necessidade.
- Como a lógica está organizada.
- Como evitar bugs por alteração indevida.
- Como manter o código limpo, previsível e fácil de evoluir.

---

## 2. Princípio central

Todo código deve ser escrito como se outra pessoa fosse dar manutenção amanhã sem conhecer o projeto.

Código bom não é apenas código que funciona. Código bom é código que:

- Funciona.
- É fácil de ler.
- É fácil de testar.
- É fácil de alterar.
- É fácil de debugar.
- Não exige adivinhação.
- Não mistura responsabilidades.
- Não obriga o programador a mexer em áreas que não têm relação com a tarefa.

---

## 3. Regra obrigatória: comentário de cabeçalho em todo arquivo

Todo arquivo criado ou alterado deve começar com um comentário de cabeçalho explicando sua função no sistema.

Esse cabeçalho deve responder:

- Qual é o objetivo do arquivo?
- Quais responsabilidades ele possui?
- Quais responsabilidades ele não deve possuir?
- Quais funções principais existem nele?
- Quais dependências importantes ele usa?
- Quais cuidados devem ser tomados ao alterar?

### Modelo padrão de cabeçalho

```php
<?php
/**
 * Arquivo: nome-do-arquivo.php
 * Objetivo: Descrever de forma clara o que este arquivo faz no sistema.
 *
 * Responsabilidades:
 * - Responsabilidade principal 1.
 * - Responsabilidade principal 2.
 * - Responsabilidade principal 3.
 *
 * Não deve fazer:
 * - Não misturar regra de negócio que pertence a outro módulo.
 * - Não executar ações fora do objetivo deste arquivo.
 * - Não alterar dados sem validação prévia.
 *
 * Funções principais:
 * - nome_da_funcao_1(): explica resumidamente a função.
 * - nome_da_funcao_2(): explica resumidamente a função.
 *
 * Dependências:
 * - conexão com banco de dados.
 * - arquivo de configuração.
 * - sessão do usuário.
 *
 * Cuidados de manutenção:
 * - Alterar somente o bloco relacionado à demanda.
 * - Validar impacto em telas, APIs ou integrações que usam este arquivo.
 * - Manter padrão de retorno e tratamento de erro.
 */
```

Para JavaScript, HTML, CSS, SQL ou outro formato, adaptar o comentário para a sintaxe correta da linguagem.

Exemplo em JavaScript:

```js
/**
 * Arquivo: usuarios.js
 * Objetivo: Controlar ações da tela de usuários, incluindo listagem, filtro e atualização visual.
 *
 * Responsabilidades:
 * - Buscar usuários na API.
 * - Atualizar a interface com os dados recebidos.
 * - Tratar estados de carregamento, sucesso e erro.
 *
 * Não deve fazer:
 * - Não conter regra de permissão do backend.
 * - Não acessar diretamente o banco de dados.
 * - Não misturar lógica de outros módulos.
 */
```

---

## 4. Regra obrigatória: comentário antes de toda função importante

Toda função deve ter um comentário explicando:

- O que a função faz.
- Quando ela deve ser usada.
- Quais dados recebe.
- O que retorna.
- Quais efeitos colaterais pode causar.
- Quais cuidados existem na manutenção.

### Modelo de comentário de função

```php
/**
 * Busca um usuário pelo ID.
 *
 * Uso:
 * - Utilizada quando o sistema precisa carregar dados completos de um usuário específico.
 *
 * Parâmetros:
 * @param int $usuarioId ID interno do usuário.
 *
 * Retorno:
 * @return array|null Retorna os dados do usuário ou null quando não encontrado.
 *
 * Cuidados:
 * - Não remover a validação do ID.
 * - Não retornar senha ou dados sensíveis.
 * - Manter o padrão de retorno usado pelas telas que consomem esta função.
 */
function buscar_usuario_por_id($usuarioId) {
    // código aqui
}
```

### Comentário simples para funções pequenas

Quando a função for muito simples, o comentário pode ser menor, mas ainda precisa explicar o objetivo.

```js
// Formata uma data ISO para o padrão visual brasileiro usado na interface.
function formatarDataBrasil(dataIso) {
    // código aqui
}
```

---

## 5. Regra obrigatória: comentário por bloco de lógica

Blocos importantes de programação devem ter comentários explicativos.

Comente principalmente quando houver:

- Validação.
- Regra de negócio.
- Consulta ao banco.
- Chamada de API.
- Tratamento de erro.
- Controle de permissão.
- Cálculo.
- Transformação de dados.
- Atualização de interface.
- Condição complexa.
- Decisão que afeta comportamento do sistema.

### Exemplo correto

```php
// Valida se o usuário informado existe antes de tentar atualizar os dados.
// Esta validação evita atualização silenciosa em registro inexistente.
$usuario = buscar_usuario_por_id($usuarioId);

if (!$usuario) {
    return resposta_erro('Usuário não encontrado.', 404);
}

// Atualiza somente os campos permitidos para evitar alteração indevida
// de dados sensíveis como perfil, senha ou status administrativo.
$dadosPermitidos = filtrar_campos_permitidos($dadosEntrada);
```

### Evite comentário inútil

Comentário ruim:

```php
// Soma 1 ao contador
$contador++;
```

Comentário melhor:

```php
// Conta quantos registros válidos foram processados com sucesso.
$contador++;
```

O comentário deve explicar a intenção, não repetir mecanicamente o que o código já mostra.

---

## 6. Regra de manutenção segura: mexer somente no necessário

Antes de alterar qualquer código, a IA ou programador deve identificar:

- Qual é o problema real.
- Qual arquivo está relacionado ao problema.
- Qual função ou bloco precisa ser alterado.
- Quais partes devem permanecer intactas.
- Quais impactos a alteração pode causar.

Nunca alterar código que não tem relação direta com a demanda.

### Antes de modificar, perguntar mentalmente

- Este trecho está ligado ao erro ou melhoria solicitada?
- Existe risco de quebrar outra funcionalidade?
- Consigo resolver com alteração menor?
- Preciso criar função nova em vez de mexer em uma função grande?
- O comentário existente explica a intenção do código?
- Depois da alteração, o próximo programador entenderá o motivo?

---

## 7. Organização visual obrigatória do código

O código deve ser visualmente fácil de ler.

Aplicar sempre:

- Indentação consistente.
- Quebras de linha entre blocos diferentes.
- Espaçamento entre operadores.
- Nomes claros.
- Funções pequenas.
- Blocos curtos.
- Separação entre validação, processamento e retorno.
- Comentários objetivos.
- Evitar linhas muito longas.
- Evitar código amontoado.

### Padrão de indentação

Escolha um padrão e mantenha em todo o projeto:

- 4 espaços por nível de indentação, recomendado para PHP, JS, HTML e CSS.
- Tabs apenas se o projeto já usar tabs como padrão.
- Nunca misturar tabs e espaços no mesmo projeto.

### Exemplo ruim

```php
if($status=='ativo'){$resultado=processar($dados);if($resultado){echo 'ok';}else{echo 'erro';}}
```

### Exemplo correto

```php
if ($status === 'ativo') {
    // Processa os dados somente quando o registro está ativo.
    $resultado = processar_dados($dados);

    if ($resultado) {
        echo 'ok';
        return;
    }

    echo 'erro';
}
```

---

## 8. Estrutura recomendada para cada arquivo

Sempre que possível, organizar o arquivo nesta ordem:

1. Comentário de cabeçalho do arquivo.
2. Importações, includes ou requires.
3. Configurações locais e constantes.
4. Validações iniciais.
5. Funções auxiliares.
6. Função principal ou fluxo principal.
7. Tratamento de resposta.
8. Comentários finais de manutenção, se necessário.

### Exemplo de estrutura

```php
<?php
/**
 * Arquivo: salvar-usuario.php
 * Objetivo: Receber dados do formulário de usuário, validar e salvar no banco.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/respostas.php';

// Constantes locais usadas apenas neste arquivo.
define('STATUS_PADRAO_USUARIO', 'ativo');

/**
 * Valida os dados obrigatórios antes de salvar o usuário.
 */
function validar_dados_usuario($dados) {
    // código
}

/**
 * Salva o usuário no banco após validação.
 */
function salvar_usuario($conexao, $dados) {
    // código
}

// Fluxo principal do arquivo.
$dadosEntrada = $_POST;
$erros = validar_dados_usuario($dadosEntrada);

if (!empty($erros)) {
    resposta_json_erro('Dados inválidos.', $erros);
    exit;
}

$resultado = salvar_usuario($conexao, $dadosEntrada);
resposta_json_sucesso('Usuário salvo com sucesso.', $resultado);
```

---

## 9. Boas práticas de nomes

Nomes devem ser claros e descrever a intenção.

### Variáveis

Use nomes que expliquem o conteúdo.

Ruim:

```php
$x = 10;
$dt = '2026-07-02';
$u = buscar($id);
```

Melhor:

```php
$totalTentativas = 10;
$dataAgendamento = '2026-07-02';
$usuarioEncontrado = buscar_usuario_por_id($usuarioId);
```

### Funções

Função deve começar com verbo e deixar claro o que faz.

Bons exemplos:

- `buscar_usuario_por_id()`
- `validar_dados_agendamento()`
- `calcular_total_pedido()`
- `enviar_notificacao_usuario()`
- `formatar_data_brasil()`
- `montar_resposta_sucesso()`

Evitar nomes genéricos:

- `processar()`
- `dados()`
- `executar()`
- `teste()`
- `funcao1()`
- `ajuste()`

Se o nome precisar ser muito genérico, provavelmente a função está fazendo coisa demais.

---

## 10. Funções pequenas e com responsabilidade única

Cada função deve fazer apenas uma coisa principal.

Uma função ruim costuma:

- Validar dados.
- Buscar no banco.
- Calcular regra.
- Enviar e-mail.
- Salvar log.
- Montar HTML.
- Retornar resposta.

Tudo no mesmo bloco.

Uma função boa separa responsabilidades:

- Uma função valida.
- Uma função consulta.
- Uma função calcula.
- Uma função salva.
- Uma função responde.

### Exemplo de separação

```php
// Valida somente os campos obrigatórios do cadastro.
function validar_cadastro_cliente($dados) {
    // validação
}

// Consulta se já existe cliente com o mesmo CPF.
function verificar_cliente_existente($conexao, $cpf) {
    // consulta
}

// Salva o cliente após validação e checagem de duplicidade.
function salvar_cliente($conexao, $dados) {
    // insert
}
```

Benefícios:

- Facilita teste.
- Facilita manutenção.
- Evita mexer em partes desnecessárias.
- Reduz risco de bug.
- Facilita reaproveitamento.

---

## 11. Separar validação, regra de negócio e saída

Sempre que possível, organizar o fluxo assim:

1. Receber entrada.
2. Validar entrada.
3. Aplicar regra de negócio.
4. Executar ação principal.
5. Tratar erro ou sucesso.
6. Retornar resposta.

### Exemplo

```php
// 1. Recebe entrada.
$dadosEntrada = $_POST;

// 2. Valida dados obrigatórios.
$errosValidacao = validar_dados_usuario($dadosEntrada);

if (!empty($errosValidacao)) {
    resposta_json_erro('Dados inválidos.', $errosValidacao);
    exit;
}

// 3. Aplica regra de negócio antes de salvar.
$dadosNormalizados = normalizar_dados_usuario($dadosEntrada);

// 4. Executa ação principal.
$usuarioSalvo = salvar_usuario($conexao, $dadosNormalizados);

// 5 e 6. Retorna resposta padronizada.
resposta_json_sucesso('Usuário salvo com sucesso.', $usuarioSalvo);
```

---

## 12. Usar retornos antecipados para reduzir complexidade

Evite criar muitos níveis de `if` dentro de `if`.

Prefira validar problemas primeiro e sair da função cedo.

### Exemplo ruim

```php
function salvar_usuario($dados) {
    if (!empty($dados)) {
        if (!empty($dados['nome'])) {
            if (!empty($dados['email'])) {
                // salvar usuário
            }
        }
    }
}
```

### Exemplo melhor

```php
function salvar_usuario($dados) {
    // Interrompe o fluxo quando não há dados para processar.
    if (empty($dados)) {
        return resposta_erro('Dados não informados.');
    }

    // Nome é obrigatório para identificação do usuário.
    if (empty($dados['nome'])) {
        return resposta_erro('Nome é obrigatório.');
    }

    // E-mail é obrigatório para login e comunicação.
    if (empty($dados['email'])) {
        return resposta_erro('E-mail é obrigatório.');
    }

    // A partir daqui, os dados mínimos já foram validados.
    return inserir_usuario($dados);
}
```

---

## 13. Padronizar respostas de sucesso e erro

Todo sistema deve ter padrão de retorno.

Isso evita que cada função retorne de um jeito diferente.

### Exemplo de resposta de sucesso

```json
{
    "success": true,
    "message": "Usuário salvo com sucesso.",
    "data": {
        "id": 123
    }
}
```

### Exemplo de resposta de erro

```json
{
    "success": false,
    "message": "Não foi possível salvar o usuário.",
    "errors": [
        "E-mail já cadastrado."
    ]
}
```

### Regra

- Não retornar erro genérico quando for possível explicar.
- Não expor senha, token, SQL interno ou dados sensíveis.
- Manter o mesmo padrão em todo o projeto.
- Comentários devem explicar quando uma resposta foge do padrão.

---

## 14. Tratamento de erro claro e seguro

Todo erro deve ser tratado de forma previsível.

Boas práticas:

- Validar dados antes de executar ações.
- Usar mensagens claras para o usuário.
- Usar logs técnicos para o desenvolvedor.
- Não exibir erro interno diretamente na tela.
- Não esconder erro silenciosamente.
- Registrar contexto mínimo para debug.

### Exemplo

```php
try {
    // Tenta salvar o pedido no banco após validação completa.
    $pedidoId = salvar_pedido($conexao, $dadosPedido);

    resposta_json_sucesso('Pedido salvo com sucesso.', [
        'pedido_id' => $pedidoId
    ]);
} catch (Exception $erro) {
    // Registra detalhe técnico para manutenção sem expor informação interna ao usuário.
    registrar_log_erro('Erro ao salvar pedido', [
        'mensagem' => $erro->getMessage(),
        'usuario_id' => $usuarioId ?? null
    ]);

    resposta_json_erro('Não foi possível salvar o pedido. Tente novamente.');
}
```

---

## 15. Logs úteis para manutenção

Logs devem ajudar a descobrir o problema sem vazar informação sensível.

Registrar:

- Data e hora.
- Usuário relacionado, quando existir.
- Ação executada.
- Arquivo ou função de origem.
- Identificador do registro.
- Mensagem técnica resumida.
- Dados mínimos para reproduzir o erro.

Não registrar:

- Senhas.
- Tokens.
- Chaves secretas.
- Dados sensíveis sem necessidade.
- Payload completo com informação privada.

### Exemplo

```php
// Log técnico usado para investigar falhas na integração sem expor dados sensíveis.
registrar_log_erro('Falha ao enviar agenda para API externa', [
    'arquivo' => 'enviar-agenda.php',
    'funcao' => 'enviar_agenda_api_externa',
    'agenda_id' => $agendaId,
    'status_http' => $statusHttp,
    'mensagem' => $mensagemErro
]);
```

---

## 16. Evitar duplicação de código

Se a mesma lógica aparece em dois ou mais lugares, avaliar criar uma função auxiliar.

Duplicação gera risco porque:

- Uma correção pode ser feita em um lugar e esquecida em outro.
- Regras podem ficar inconsistentes.
- O sistema fica mais difícil de manter.

### Exemplo ruim

```php
$data = date('d/m/Y', strtotime($registro['created_at']));
// repetido em vários arquivos
```

### Exemplo melhor

```php
// Centraliza a formatação de data para manter o mesmo padrão visual no sistema.
function formatar_data_brasil($dataIso) {
    return date('d/m/Y', strtotime($dataIso));
}
```

---

## 17. Evitar números e textos mágicos

Não espalhar valores fixos sem explicação pelo código.

### Ruim

```php
if ($status == 3) {
    // código
}
```

### Melhor

```php
// Status 3 representa registro aprovado conforme regra de negócio do módulo.
define('STATUS_APROVADO', 3);

if ($status === STATUS_APROVADO) {
    // código
}
```

### Regra

Usar constantes para:

- Status.
- Limites.
- Tipos.
- Prazos.
- Códigos de erro.
- URLs base.
- Valores padrão.

---

## 18. Padronizar estilo de comparação

Usar comparação estrita sempre que a linguagem permitir.

### JavaScript

Preferir:

```js
if (status === 'ativo') {
    // código
}
```

Evitar:

```js
if (status == 'ativo') {
    // código
}
```

### PHP

Preferir quando possível:

```php
if ($status === 'ativo') {
    // código
}
```

Evitar quando o tipo pode gerar confusão:

```php
if ($status == 'ativo') {
    // código
}
```

---

## 19. Entrada de dados sempre deve ser validada

Nunca confiar diretamente em dados vindos de:

- Formulário.
- URL.
- API externa.
- Banco legado.
- Upload.
- LocalStorage.
- Cookies.
- Sessão.
- Integração.

Validar:

- Obrigatoriedade.
- Tipo.
- Tamanho.
- Formato.
- Permissão.
- Faixa aceita.
- Existência no banco.
- Duplicidade.

### Exemplo

```php
// Valida o ID antes da consulta para evitar erro de tipo e consulta indevida.
$usuarioId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$usuarioId) {
    resposta_json_erro('ID de usuário inválido.');
    exit;
}
```

---

## 20. Segurança básica no código

Toda função deve considerar segurança mínima.

Boas práticas:

- Validar entrada.
- Escapar saída.
- Usar prepared statements em SQL.
- Nunca concatenar dados do usuário direto no SQL.
- Verificar permissão antes de ação sensível.
- Não expor dados sensíveis em resposta.
- Não salvar senha em texto puro.
- Não deixar credenciais no código.
- Não exibir erro técnico para usuário final.

### SQL seguro com prepared statement

```php
// Consulta preparada para evitar SQL Injection com dados vindos do usuário.
$sql = 'SELECT id, nome, email FROM usuarios WHERE id = ? LIMIT 1';
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, 'i', $usuarioId);
mysqli_stmt_execute($stmt);
```

---

## 21. Separar configuração de lógica

Configurações não devem ficar espalhadas dentro das funções.

Separar em arquivo próprio quando possível:

- Dados de conexão.
- URLs base.
- Tokens.
- Chaves.
- Limites.
- Configurações de ambiente.
- Configurações de e-mail.
- Configurações de API.

### Exemplo

```php
// config/app.php
return [
    'ambiente' => 'producao',
    'limite_tentativas_login' => 5,
    'url_api_externa' => 'https://api.exemplo.com.br'
];
```

O código principal deve apenas consumir a configuração.

---

## 22. Separar arquivos por responsabilidade

Evitar arquivos gigantes que fazem tudo.

Separar por função do sistema:

```txt
/app
    /config
        database.php
        app.php
    /helpers
        respostas.php
        datas.php
        validacoes.php
    /modules
        /usuarios
            listar-usuarios.php
            salvar-usuario.php
            editar-usuario.php
        /agendamentos
            listar-agendamentos.php
            salvar-agendamento.php
    /public
        index.php
        assets/
```

Cada arquivo deve ter objetivo claro.

---

## 23. Padronização para frontend

Em HTML, CSS e JavaScript, manter separação clara:

- HTML: estrutura e semântica.
- CSS: visual, layout e responsividade.
- JavaScript: comportamento e interação.

Evitar:

- Muito CSS inline.
- JavaScript misturado sem necessidade no HTML.
- IDs e classes sem padrão.
- Manipulação visual espalhada em vários lugares.
- Funções JS gigantes.

### Comentário no HTML

```html
<!--
    Seção: Filtros da listagem de usuários
    Objetivo: Permitir busca por nome, status e perfil.
    Manutenção: novos filtros devem ser adicionados neste bloco e tratados em usuarios.js.
-->
<section class="usuarios-filtros">
    ...
</section>
```

### Comentário no CSS

```css
/*
 * Bloco: Card de resumo do dashboard
 * Objetivo: Padronizar visual dos indicadores principais.
 * Manutenção: alterações de cor devem respeitar a paleta definida no guia visual.
 */
.card-resumo {
    padding: 16px;
    border-radius: 12px;
}
```

### Comentário no JavaScript

```js
// Atualiza a tabela sem recarregar a página após aplicar filtros.
// Esta função não deve alterar as regras de busca da API, apenas refletir o resultado na interface.
function atualizarTabelaUsuarios(usuarios) {
    // código
}
```

---

## 24. Padronização para backend

No backend, separar claramente:

- Entrada da requisição.
- Validação.
- Autenticação.
- Permissão.
- Regra de negócio.
- Acesso ao banco.
- Resposta.

### Ordem recomendada em endpoints

```php
// 1. Confere se o usuário está autenticado.
$usuarioLogado = obter_usuario_logado();

if (!$usuarioLogado) {
    resposta_json_erro('Usuário não autenticado.', [], 401);
    exit;
}

// 2. Confere permissão antes de qualquer alteração.
if (!usuario_tem_permissao($usuarioLogado, 'editar_usuarios')) {
    resposta_json_erro('Usuário sem permissão para editar usuários.', [], 403);
    exit;
}

// 3. Lê e valida entrada.
$dadosEntrada = obter_json_requisicao();
$erros = validar_dados_usuario($dadosEntrada);

if (!empty($erros)) {
    resposta_json_erro('Dados inválidos.', $erros, 422);
    exit;
}

// 4. Executa regra principal.
$resultado = atualizar_usuario($conexao, $dadosEntrada);

// 5. Retorna resposta padronizada.
resposta_json_sucesso('Usuário atualizado com sucesso.', $resultado);
```

---

## 25. Boas práticas para banco de dados no código

Ao escrever código que acessa banco:

- Usar prepared statements.
- Nomear consultas de forma clara.
- Não fazer consulta sem necessidade.
- Não buscar colunas que não serão usadas.
- Usar `LIMIT` quando buscar um registro único.
- Tratar retorno vazio.
- Não misturar SQL complexo dentro de função enorme.
- Comentar consultas que representem regra de negócio importante.

### Exemplo

```php
// Busca somente usuários ativos porque usuários inativos não podem aparecer na seleção de responsáveis.
$sql = '
    SELECT id, nome, email
    FROM usuarios
    WHERE status = ?
    ORDER BY nome ASC
';
```

---

## 26. Evitar efeitos colaterais escondidos

Uma função não deve alterar dados sem deixar claro pelo nome e comentário.

### Ruim

```php
function buscar_usuario($id) {
    // além de buscar, atualiza último acesso sem avisar
}
```

### Melhor

```php
/**
 * Busca o usuário e atualiza a data do último acesso.
 *
 * Cuidado:
 * - Esta função possui efeito colateral porque grava no banco.
 * - Não usar em relatórios ou consultas somente leitura.
 */
function buscar_usuario_e_atualizar_ultimo_acesso($id) {
    // código
}
```

Função que salva, altera, envia, exclui ou dispara ação externa precisa deixar isso claro no nome.

---

## 27. Padronizar comentários de áreas sensíveis

Áreas críticas devem ser marcadas com comentário especial.

Use tags como:

- `IMPORTANTE:`
- `CUIDADO:`
- `REGRA DE NEGÓCIO:`
- `SEGURANÇA:`
- `PERFORMANCE:`
- `NÃO ALTERAR SEM VALIDAR:`

### Exemplo

```php
// REGRA DE NEGÓCIO:
// Um agendamento não pode ser criado quando já existe outro no mesmo horário
// para o mesmo profissional. Alterar esta regra pode gerar conflito de agenda.
if (existe_conflito_agenda($profissionalId, $dataHoraInicio, $dataHoraFim)) {
    resposta_json_erro('Já existe agendamento neste horário.');
    exit;
}
```

---

## 28. Comentários de manutenção futura

Use comentários de manutenção quando uma melhoria for conhecida, mas não fizer parte da tarefa atual.

Padrões úteis:

```php
// TODO: criar paginação para melhorar performance quando houver muitos registros.
```

```php
// FIXME: revisar regra de duplicidade após atualização da integração externa.
```

```php
// NOTE: esta regra existe por compatibilidade com dados antigos importados do sistema anterior.
```

Regras:

- Não usar TODO para abandonar problema importante.
- Todo TODO deve ser claro.
- Não deixar comentário vago como `// ajustar depois`.

---

## 29. Evitar refatoração desnecessária durante correção de bug

Quando a tarefa for corrigir bug:

- Corrigir apenas o ponto necessário.
- Não reescrever arquivo inteiro sem necessidade.
- Não mudar nomes públicos usados por outras partes.
- Não alterar padrão de resposta sem validar impacto.
- Não trocar biblioteca, estrutura ou arquitetura sem pedido explícito.

Após corrigir, comentar o motivo da correção quando a regra não for óbvia.

### Exemplo

```php
// Corrige comparação de data considerando timezone local do sistema.
// Sem esta normalização, registros próximos da meia-noite podem cair no dia errado.
$dataLocal = converter_data_para_timezone_local($dataOriginal);
```

---

## 30. Checklist antes de criar um arquivo novo

Antes de criar um arquivo, responder:

- Qual é o objetivo deste arquivo?
- Ele realmente precisa existir?
- Qual módulo será responsável por ele?
- O nome do arquivo é claro?
- Ele terá cabeçalho explicativo?
- Quais funções ficarão nele?
- Há alguma função que deveria ir para helper?
- Há dependências externas?
- Há regra de segurança ou permissão?
- Há padrão de resposta esperado?

---

## 31. Checklist antes de criar uma função

Antes de criar uma função, responder:

- Qual problema ela resolve?
- O nome explica bem sua finalidade?
- Ela faz apenas uma coisa?
- Os parâmetros são claros?
- O retorno é previsível?
- Ela altera dados ou apenas consulta?
- Precisa de validação interna?
- Precisa de comentário de cuidado?
- Pode ser reaproveitada?
- Existe função parecida no projeto?

---

## 32. Checklist antes de alterar uma função existente

Antes de alterar uma função, responder:

- Quem chama esta função?
- Qual retorno as outras partes esperam?
- Existe teste ou fluxo para validar?
- A alteração muda comportamento antigo?
- Existe forma de alterar com menor impacto?
- O comentário da função precisa ser atualizado?
- O nome da função continua correto após a alteração?
- Algum log ou mensagem de erro precisa melhorar?

---

## 33. Checklist de legibilidade final

Antes de entregar o código, verificar:

- O arquivo tem comentário de cabeçalho?
- Toda função importante tem comentário?
- Blocos complexos têm explicação?
- A indentação está consistente?
- Há quebras de linha entre blocos?
- Os nomes são claros?
- O código evita duplicação?
- Os erros são tratados?
- O retorno segue padrão?
- Não existem senhas, tokens ou dados sensíveis expostos?
- Não foi alterado nada fora da demanda?
- O próximo programador entenderia o código rapidamente?

---

## 34. Padrão de resposta para IA ao alterar código

Quando uma IA aplicar esta skill em uma tarefa de desenvolvimento, ela deve responder com:

1. Arquivos alterados ou criados.
2. Funções alteradas ou criadas.
3. O que foi modificado.
4. O que não foi alterado de propósito.
5. Cuidados de manutenção.
6. Como testar.

### Modelo

```md
## Alterações realizadas

### Arquivos criados/alterados
- `usuarios/salvar-usuario.php`

### Funções criadas/alteradas
- `validar_dados_usuario()`
- `salvar_usuario()`

### O que foi feito
- Adicionado cabeçalho explicativo no arquivo.
- Separada validação da lógica de salvamento.
- Criados comentários nos blocos de validação, permissão e retorno.

### O que não foi alterado
- Não alterei a estrutura da tabela `usuarios`.
- Não alterei o padrão de login.
- Não alterei permissões existentes.

### Como testar
- Tentar salvar usuário com dados válidos.
- Tentar salvar usuário sem e-mail.
- Tentar salvar e-mail duplicado.
- Confirmar resposta de sucesso e erro no padrão JSON.
```

---

## 35. Padrão mínimo para código entregue por IA

Nenhum código deve ser entregue sem:

- Cabeçalho no arquivo.
- Comentário nas funções.
- Comentário em bloco complexo.
- Indentação limpa.
- Nome claro.
- Validação de entrada.
- Tratamento de erro.
- Padrão de resposta.
- Separação mínima de responsabilidade.
- Explicação do que foi alterado.

---

## 36. Regra de ouro para comentários

Comentários devem explicar o porquê, não apenas o quê.

O código mostra o que está acontecendo.
O comentário deve explicar a intenção, a regra de negócio, o cuidado ou o motivo da decisão.

### Comentário fraco

```php
// Verifica se idade é maior que 18
if ($idade >= 18) {
    // código
}
```

### Comentário bom

```php
// REGRA DE NEGÓCIO:
// Apenas usuários maiores de idade podem aceitar este termo contratual.
if ($idade >= 18) {
    // código
}
```

---

## 37. Guia rápido para IA aplicar em qualquer linguagem

Ao receber uma tarefa de código, a IA deve seguir esta ordem:

1. Entender o objetivo da alteração.
2. Localizar o menor ponto de alteração possível.
3. Preservar comportamento existente que não faz parte da demanda.
4. Criar ou atualizar cabeçalho do arquivo.
5. Criar ou atualizar comentário das funções afetadas.
6. Inserir comentários em blocos críticos.
7. Melhorar indentação e quebras de linha sem mudar lógica desnecessariamente.
8. Validar entrada e saída.
9. Tratar erros previsíveis.
10. Informar claramente o que foi feito.

---

## 38. Exemplo completo em PHP procedural

```php
<?php
/**
 * Arquivo: usuarios/salvar-usuario.php
 * Objetivo: Receber, validar e salvar dados de cadastro de usuário.
 *
 * Responsabilidades:
 * - Ler dados enviados pelo formulário ou requisição.
 * - Validar campos obrigatórios.
 * - Verificar duplicidade de e-mail.
 * - Salvar usuário no banco.
 * - Retornar resposta JSON padronizada.
 *
 * Não deve fazer:
 * - Não autenticar login.
 * - Não alterar permissões do usuário.
 * - Não enviar notificações.
 *
 * Cuidados de manutenção:
 * - Manter prepared statements nas consultas.
 * - Não retornar senha ou dados sensíveis.
 * - Alterar somente regras relacionadas ao cadastro de usuário.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/respostas.php';

/**
 * Valida os dados obrigatórios para cadastro de usuário.
 *
 * @param array $dados Dados recebidos da requisição.
 * @return array Lista de erros encontrados. Retorna array vazio quando os dados são válidos.
 */
function validar_dados_cadastro_usuario($dados) {
    $erros = [];

    // Nome é obrigatório para identificação visual e administrativa do usuário.
    if (empty($dados['nome'])) {
        $erros[] = 'Nome é obrigatório.';
    }

    // E-mail é obrigatório porque será usado como identificador de login.
    if (empty($dados['email'])) {
        $erros[] = 'E-mail é obrigatório.';
    }

    // Valida o formato do e-mail antes de consultar duplicidade no banco.
    if (!empty($dados['email']) && !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }

    return $erros;
}

/**
 * Verifica se já existe usuário cadastrado com o mesmo e-mail.
 *
 * @param mysqli $conexao Conexão ativa com o banco.
 * @param string $email E-mail informado no cadastro.
 * @return bool Retorna true quando já existe usuário com este e-mail.
 */
function existe_usuario_com_email($conexao, $email) {
    // Consulta preparada para evitar SQL Injection com dado informado pelo usuário.
    $sql = 'SELECT id FROM usuarios WHERE email = ? LIMIT 1';
    $stmt = mysqli_prepare($conexao, $sql);

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);

    return mysqli_num_rows($resultado) > 0;
}

/**
 * Salva o usuário no banco após todas as validações.
 *
 * @param mysqli $conexao Conexão ativa com o banco.
 * @param array $dados Dados já validados e normalizados.
 * @return int ID do usuário criado.
 */
function salvar_usuario($conexao, $dados) {
    // Mantém somente campos permitidos para evitar gravação indevida de dados administrativos.
    $nome = trim($dados['nome']);
    $email = strtolower(trim($dados['email']));
    $status = 'ativo';

    $sql = '
        INSERT INTO usuarios (nome, email, status, criado_em)
        VALUES (?, ?, ?, NOW())
    ';

    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $status);
    mysqli_stmt_execute($stmt);

    return mysqli_insert_id($conexao);
}

// Fluxo principal: recebe e valida os dados antes de qualquer gravação.
$dadosEntrada = $_POST;
$erros = validar_dados_cadastro_usuario($dadosEntrada);

if (!empty($erros)) {
    resposta_json_erro('Dados inválidos.', $erros, 422);
    exit;
}

// Normaliza o e-mail antes da checagem para evitar duplicidade por diferença de maiúsculas/minúsculas.
$emailNormalizado = strtolower(trim($dadosEntrada['email']));

if (existe_usuario_com_email($conexao, $emailNormalizado)) {
    resposta_json_erro('E-mail já cadastrado.', [], 409);
    exit;
}

$usuarioId = salvar_usuario($conexao, $dadosEntrada);

resposta_json_sucesso('Usuário criado com sucesso.', [
    'usuario_id' => $usuarioId
]);
```

---

## 39. Exemplo completo em JavaScript

```js
/**
 * Arquivo: usuarios.js
 * Objetivo: Controlar a listagem de usuários na interface administrativa.
 *
 * Responsabilidades:
 * - Buscar usuários na API.
 * - Aplicar filtros de busca.
 * - Renderizar tabela de usuários.
 * - Exibir estado de carregamento, erro e vazio.
 *
 * Não deve fazer:
 * - Não validar permissão administrativa.
 * - Não acessar banco de dados diretamente.
 * - Não conter regra sensível de backend.
 */

/**
 * Busca usuários na API usando os filtros informados na tela.
 *
 * @param {Object} filtros Filtros aplicados pelo usuário.
 * @returns {Promise<Array>} Lista de usuários retornada pela API.
 */
async function buscarUsuarios(filtros) {
    // Monta os parâmetros de busca sem enviar campos vazios para a API.
    const parametros = new URLSearchParams();

    if (filtros.nome) {
        parametros.append('nome', filtros.nome);
    }

    if (filtros.status) {
        parametros.append('status', filtros.status);
    }

    const resposta = await fetch(`/api/usuarios?${parametros.toString()}`);

    // Trata erro HTTP antes de tentar converter a resposta em JSON.
    if (!resposta.ok) {
        throw new Error('Não foi possível buscar os usuários.');
    }

    const dados = await resposta.json();

    return dados.data || [];
}

/**
 * Renderiza a tabela de usuários com base na lista recebida.
 *
 * @param {Array} usuarios Lista de usuários que será exibida na tela.
 */
function renderizarTabelaUsuarios(usuarios) {
    const tabela = document.querySelector('[data-usuarios-tabela]');

    // Garante que a tabela exista antes de tentar alterar o HTML.
    if (!tabela) {
        return;
    }

    // Exibe estado vazio quando a API não retorna registros para os filtros aplicados.
    if (usuarios.length === 0) {
        tabela.innerHTML = '<tr><td colspan="4">Nenhum usuário encontrado.</td></tr>';
        return;
    }

    tabela.innerHTML = usuarios.map((usuario) => {
        return `
            <tr>
                <td>${usuario.nome}</td>
                <td>${usuario.email}</td>
                <td>${usuario.status}</td>
                <td>
                    <button data-editar-usuario="${usuario.id}">
                        Editar
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}
```

---

## 40. Definition of Done para código limpo e manutenível

Uma tarefa só deve ser considerada pronta quando:

- O código funciona conforme solicitado.
- O arquivo possui cabeçalho claro.
- As funções importantes estão comentadas.
- Blocos críticos explicam a regra ou intenção.
- A indentação está consistente.
- A lógica está separada em partes compreensíveis.
- Não há alteração fora do escopo.
- Entradas são validadas.
- Erros são tratados.
- Retornos são padronizados.
- Não há exposição de dados sensíveis.
- O código pode ser entendido por outro programador ou IA.
- Existe orientação mínima de teste.

---

## 41. Prompt interno recomendado para IA usar esta skill

Use este prompt sempre que aplicar a skill em uma tarefa real:

```md
Aja como desenvolvedor sênior especialista em SaaS, software, app, lógica de programação, sintaxe e manutenção de código.

Antes de criar ou alterar código:
1. Entenda o objetivo da tarefa.
2. Identifique o menor ponto seguro de alteração.
3. Não altere nada fora do escopo.
4. Mantenha ou crie comentário de cabeçalho em todo arquivo.
5. Comente toda função importante.
6. Comente blocos de validação, regra de negócio, erro, banco, API e permissão.
7. Use indentação, quebra de linha e nomes claros.
8. Separe validação, regra de negócio, ação principal e resposta.
9. Evite duplicação, funções gigantes e efeitos colaterais escondidos.
10. Entregue explicando arquivos alterados, funções alteradas, cuidados e como testar.

Prioridade máxima:
Código legível, seguro, comentado, fácil de manter e com alteração mínima necessária.
```

---

## 42. Regra final

Se uma alteração deixa o código funcionando, mas difícil de entender, a tarefa ainda não está pronta.

Código bem feito deve permitir que outro programador ou IA leia, entenda, altere e teste sem medo de quebrar o sistema inteiro.
