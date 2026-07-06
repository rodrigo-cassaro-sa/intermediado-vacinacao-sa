# Skill: Boas Práticas MySQL para Projetos Web

## Limite desta skill

Esta skill transforma a modelagem de dados em implementação técnica MySQL/MariaDB.

Ela deve focar em SQL, tabelas, tipos, índices, constraints, consultas, transações, performance e integração segura com PHP.

Ela pode citar modelagem, migrações, backup e segurança quando afetarem o MySQL, mas não deve substituir:

- `skill-dados.md` para definição conceitual das entidades e relacionamentos;
- `skill-migracoes-banco.md` para scripts versionados de alteração;
- `skill-backup-recuperacao.md` para política de backup e restauração;
- `skill-performance.md` para otimização completa do sistema;
- `skill-seguranca.md` para segurança transversal.

Esta skill responde "o banco MySQL está implementado corretamente, seguro e performático?".

---

## 1. Objetivo da skill

Esta skill orienta uma IA ou desenvolvedor a criar, revisar e manter bancos de dados MySQL com foco em segurança, clareza, desempenho, consistência e facilidade de manutenção.

Use esta skill em projetos com:

- PHP procedural + MySQL
- APIs REST
- sistemas administrativos
- SaaS web
- dashboards
- integrações
- relatórios
- aplicações com HTML, CSS e JavaScript consumindo backend PHP

A IA deve sempre priorizar soluções simples, seguras e fáceis de manter antes de sugerir estruturas complexas.

---

## 2. Papel da IA ao usar esta skill

Ao trabalhar com MySQL, a IA deve agir como um programador sênior de banco de dados, pensando em:

- integridade dos dados;
- segurança contra SQL Injection;
- performance de consultas;
- organização das tabelas;
- clareza dos nomes;
- facilidade de manutenção;
- rastreabilidade;
- backup e recuperação;
- crescimento futuro do sistema.

A IA nunca deve gerar SQL apenas para “funcionar”. Ela deve gerar SQL pensando em projeto real, produção, manutenção e evolução.

---

## 3. Princípios fundamentais

### 3.1 Clareza antes de complexidade

O banco deve ser fácil de entender. Tabelas, colunas, índices e relacionamentos devem ter nomes claros e previsíveis.

Evite nomes genéricos como:

```sql
Tabela1
Dados
Info
Cadastro
Campo1
Valor
Status2
```

Prefira nomes objetivos:

```sql
usuarios
clientes
pedidos
pedido_itens
vacinas
agendamentos
pagamentos
```

---

### 3.2 Consistência em todo o banco

Escolha um padrão e mantenha em todo o projeto.

Padrão recomendado:

- nomes em português ou inglês, mas não misturar sem necessidade;
- tabelas no plural;
- colunas em `snake_case`;
- chaves primárias como `id`;
- chaves estrangeiras como `cliente_id`, `usuario_id`, `pedido_id`;
- datas de auditoria como `criado_em`, `atualizado_em`, `excluido_em`.

Exemplo:

```sql
CREATE TABLE clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    telefone VARCHAR(30) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 3.3 Segurança sempre em primeiro lugar

Toda entrada externa deve ser tratada como insegura.

A IA deve sempre recomendar:

- prepared statements;
- validação de entrada;
- usuário de banco com menor privilégio possível;
- senhas fora do código-fonte;
- logs sem dados sensíveis;
- backups protegidos;
- conexão segura quando o banco estiver em outro servidor.

Nunca montar SQL concatenando valores vindos do usuário.

Errado:

```php
$sql = "SELECT * FROM usuarios WHERE email = '$email'";
```

Correto:

```php
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
```

---

## 4. Engine e configuração básica

### 4.1 Usar InnoDB por padrão

Para projetos modernos, use `InnoDB` como engine padrão.

Motivos:

- suporte a transações;
- suporte a `COMMIT` e `ROLLBACK`;
- suporte a `FOREIGN KEY`;
- melhor segurança contra corrupção em falhas;
- melhor controle de concorrência.

Exemplo:

```sql
ENGINE=InnoDB
```

Evite `MyISAM` para sistemas transacionais.

---

### 4.2 Usar `utf8mb4`

Use `utf8mb4` para suportar corretamente acentos, emojis e caracteres especiais.

Recomendado:

```sql
DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

Ou, dependendo do ambiente e versão:

```sql
DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
```

Escolha um padrão de collation para o projeto inteiro.

---

## 5. Modelagem de dados

### 5.1 Uma tabela deve representar uma entidade clara

Cada tabela deve ter uma responsabilidade principal.

Exemplo bom:

```text
clientes
usuarios
produtos
pedidos
pedido_itens
pagamentos
```

Exemplo ruim:

```text
dados_gerais
cadastros
movimentacoes
informacoes
```

---

### 5.2 Evitar tabelas gigantes com muitos significados

Não crie uma única tabela para guardar tudo.

Errado:

```text
lancamentos
- id
- tipo
- nome_cliente
- nome_produto
- valor
- data_agenda
- status_pagamento
- observacao_usuario
```

Melhor:

```text
clientes
produtos
pedidos
pedido_itens
pagamentos
agendamentos
```

Separar responsabilidades melhora:

- manutenção;
- relatórios;
- filtros;
- integridade;
- desempenho.

---

### 5.3 Normalizar até um ponto saudável

Normalize para evitar duplicação de dados importantes.

Exemplo:

Não repetir nome do cliente em todos os pedidos.

Errado:

```text
pedidos
- id
- nome_cliente
- email_cliente
- telefone_cliente
```

Correto:

```text
clientes
- id
- nome
- email
- telefone

pedidos
- id
- cliente_id
- valor_total
```

Mas evite normalização exagerada que dificulta consultas simples.

Regra prática:

- dado cadastral deve ficar em sua tabela própria;
- dado histórico importante pode ser copiado quando precisa preservar o valor da época;
- dado calculado pode ser salvo quando performance ou histórico exigir.

---

### 5.4 Preservar histórico quando necessário

Em alguns casos, não basta referenciar o cadastro atual.

Exemplo: se o cliente mudar o nome, uma nota fiscal antiga talvez precise manter o nome usado na emissão.

Nesse caso, salve um “snapshot”:

```sql
CREATE TABLE notas_fiscais (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NOT NULL,
    cliente_nome_emitido VARCHAR(150) NOT NULL,
    cliente_documento_emitido VARCHAR(30) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    emitida_em DATETIME NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 6. Tipos de dados

### 6.1 Escolher o menor tipo suficiente

Não use tipos grandes sem necessidade.

Exemplos:

```sql
TINYINT      -- valores pequenos, flags, status simples
INT          -- quantidade comum de registros
BIGINT       -- tabelas que podem crescer muito
VARCHAR      -- textos curtos variáveis
TEXT         -- textos longos
DECIMAL      -- valores monetários
DATETIME     -- data e hora de negócio
DATE         -- apenas data
BOOLEAN      -- normalmente TINYINT(1) no MySQL
```

---

### 6.2 Usar `DECIMAL` para dinheiro

Nunca use `FLOAT` ou `DOUBLE` para valores financeiros.

Errado:

```sql
valor FLOAT
```

Correto:

```sql
valor DECIMAL(10,2) NOT NULL
```

Motivo: valores monetários precisam de precisão decimal previsível.

---

### 6.3 Usar `DATE`, `DATETIME` e `TIMESTAMP` corretamente

Use:

- `DATE` para datas sem horário, como nascimento e vencimento;
- `DATETIME` para data e hora de eventos de negócio;
- `TIMESTAMP` quando quiser comportamento ligado ao fuso/configuração do servidor, com cuidado.

Para sistemas brasileiros, defina uma regra clara:

- salvar horários importantes em UTC; ou
- salvar em horário local padronizado do sistema.

O mais importante é não misturar padrões.

---

### 6.4 Evitar `VARCHAR(255)` automático em tudo

Defina tamanhos coerentes.

Exemplos:

```sql
nome VARCHAR(150)
email VARCHAR(180)
telefone VARCHAR(30)
cpf VARCHAR(14)
cnpj VARCHAR(18)
cep VARCHAR(10)
uf CHAR(2)
```

Isso melhora clareza e reduz abuso de campos genéricos.

---

## 7. Chaves primárias e relacionamentos

### 7.1 Toda tabela deve ter chave primária

Toda tabela de entidade deve ter uma `PRIMARY KEY`.

Recomendado:

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
```

Use `BIGINT` quando a tabela puder crescer bastante.

Use `INT` para tabelas menores.

---

### 7.2 Usar chaves estrangeiras quando houver relacionamento real

Use `FOREIGN KEY` para garantir integridade entre tabelas.

Exemplo:

```sql
CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pedidos_clientes
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 7.3 Definir comportamento de exclusão e atualização

Sempre pense no que deve acontecer quando o registro pai for excluído.

Opções comuns:

```sql
ON DELETE RESTRICT
ON DELETE CASCADE
ON DELETE SET NULL
```

Regras práticas:

- use `RESTRICT` quando não pode apagar dados relacionados;
- use `CASCADE` apenas quando os filhos realmente não fazem sentido sem o pai;
- use `SET NULL` quando o registro pode continuar existindo sem o vínculo.

Evite `CASCADE` sem pensar, pois pode apagar muitos dados acidentalmente.

---

## 8. Índices

### 8.1 Criar índice com base nas consultas reais

Índice não é decoração. Ele deve existir para acelerar consultas reais.

Crie índices para colunas usadas com frequência em:

- `WHERE`;
- `JOIN`;
- `ORDER BY`;
- `GROUP BY`;
- filtros de relatórios;
- buscas por status e data;
- chaves estrangeiras.

Exemplo:

```sql
CREATE INDEX idx_pedidos_cliente_id ON pedidos(cliente_id);
CREATE INDEX idx_pedidos_status_criado_em ON pedidos(status, criado_em);
```

---

### 8.2 Não criar índice em excesso

Índices aceleram leitura, mas podem deixar escrita mais lenta.

Cada `INSERT`, `UPDATE` e `DELETE` precisa atualizar os índices.

Evite:

- índice duplicado;
- índice que nunca é usado;
- índice em campo de baixa seletividade sem necessidade;
- índice criado “por garantia”.

---

### 8.3 Usar índices compostos na ordem correta

A ordem das colunas em índice composto importa.

Exemplo de consulta:

```sql
SELECT *
FROM pedidos
WHERE status = 'pago'
  AND criado_em >= '2026-01-01'
ORDER BY criado_em DESC;
```

Índice recomendado:

```sql
CREATE INDEX idx_pedidos_status_criado_em ON pedidos(status, criado_em);
```

Regra prática:

- igualdade primeiro;
- intervalo depois;
- ordenação depois, quando fizer sentido.

---

### 8.4 Usar `EXPLAIN` antes de otimizar no escuro

Antes de mexer em índice ou consulta, analise o plano de execução.

Exemplo:

```sql
EXPLAIN
SELECT *
FROM pedidos
WHERE cliente_id = 10
ORDER BY criado_em DESC;
```

Observe principalmente:

- `type`;
- `possible_keys`;
- `key`;
- `rows`;
- `Extra`.

Sinais de alerta:

```text
type = ALL
Extra = Using filesort
Extra = Using temporary
rows muito alto
```

Nem todo `Using filesort` é problema, mas precisa ser analisado quando a tabela é grande ou a consulta é frequente.

---

## 9. Consultas SQL

### 9.1 Evitar `SELECT *` em produção

Errado:

```sql
SELECT * FROM clientes;
```

Correto:

```sql
SELECT id, nome, email, telefone
FROM clientes;
```

Motivos:

- reduz tráfego;
- evita carregar colunas desnecessárias;
- deixa claro o que a aplicação usa;
- evita bugs quando a tabela muda.

---

### 9.2 Usar `LIMIT` em listagens

Toda tela de listagem deve ter paginação.

Exemplo:

```sql
SELECT id, nome, email
FROM clientes
ORDER BY id DESC
LIMIT 50 OFFSET 0;
```

Para grandes volumes, prefira paginação por cursor quando possível:

```sql
SELECT id, nome, email
FROM clientes
WHERE id < :ultimo_id
ORDER BY id DESC
LIMIT 50;
```

---

### 9.3 Evitar funções em colunas filtradas

Errado:

```sql
SELECT *
FROM pedidos
WHERE DATE(criado_em) = '2026-07-02';
```

Melhor:

```sql
SELECT id, cliente_id, valor_total, criado_em
FROM pedidos
WHERE criado_em >= '2026-07-02 00:00:00'
  AND criado_em <  '2026-07-03 00:00:00';
```

Motivo: aplicar função na coluna pode impedir uso eficiente de índice.

---

### 9.4 Evitar `LIKE '%texto%'` em tabelas grandes

Consultas assim tendem a não usar índice comum de forma eficiente:

```sql
WHERE nome LIKE '%ana%'
```

Alternativas:

- busca por início: `LIKE 'ana%'`;
- índice `FULLTEXT` quando adequado;
- mecanismo de busca externo quando o volume for grande;
- campo auxiliar normalizado para busca simples.

---

### 9.5 Evitar consultas dentro de loops

Errado:

```php
foreach ($pedidos as $pedido) {
    // faz uma consulta para cada pedido
}
```

Melhor:

- buscar tudo com `JOIN`;
- buscar por lote usando `WHERE id IN (...)`;
- montar consulta única com os dados necessários.

Problema comum: N+1 queries.

---

### 9.6 Usar `JOIN` com relacionamento claro

Exemplo:

```sql
SELECT
    p.id,
    p.valor_total,
    p.criado_em,
    c.nome AS cliente_nome
FROM pedidos p
INNER JOIN clientes c ON c.id = p.cliente_id
WHERE p.status = 'pago'
ORDER BY p.criado_em DESC
LIMIT 50;
```

Sempre use alias claros e evite joins sem condição.

---

## 10. Transações

### 10.1 Usar transação em operações com múltiplas etapas

Se uma ação grava em mais de uma tabela, use transação.

Exemplo:

```sql
START TRANSACTION;

INSERT INTO pedidos (cliente_id, valor_total, status)
VALUES (10, 250.00, 'pendente');

INSERT INTO pagamentos (pedido_id, valor, status)
VALUES (LAST_INSERT_ID(), 250.00, 'aguardando');

COMMIT;
```

Se algo der errado:

```sql
ROLLBACK;
```

---

### 10.2 Manter transações curtas

Transações longas aumentam risco de:

- locks;
- lentidão;
- deadlocks;
- bloqueios em outras operações.

Regra prática:

- abrir transação;
- executar apenas o necessário;
- confirmar ou desfazer rapidamente.

Não espere chamada de API externa dentro de transação, se puder evitar.

---

### 10.3 Tratar deadlocks

Deadlocks podem acontecer em sistemas concorrentes.

A aplicação deve:

- capturar erro;
- registrar log;
- tentar novamente quando for seguro;
- manter ordem consistente de atualização das tabelas.

Exemplo de regra:

Se dois processos atualizam `pedidos` e `pagamentos`, ambos devem atualizar sempre na mesma ordem.

---

## 11. Status, enums e regras de negócio

### 11.1 Padronizar status

Evite status escritos livremente.

Errado:

```text
Pago
pago
PAGO
finalizado
Finalizado
ok
```

Correto:

```text
pendente
pago
cancelado
estornado
falhou
```

Use valores previsíveis e documentados.

---

### 11.2 Evitar regra de negócio escondida no banco sem documentação

Triggers, procedures e constraints podem ser úteis, mas devem ser documentadas.

A IA deve evitar criar regra crítica escondida em trigger sem explicar.

Boa prática:

- regra simples de integridade pode ficar no banco;
- regra de fluxo de negócio deve estar clara no backend;
- se usar trigger, documentar motivo, evento e impacto.

---

## 12. Auditoria e rastreabilidade

### 12.1 Toda tabela importante deve ter datas de controle

Campos recomendados:

```sql
criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
```

Para exclusão lógica:

```sql
excluido_em DATETIME NULL
```

---

### 12.2 Guardar usuário responsável quando necessário

Em sistemas administrativos, registre quem criou, atualizou ou cancelou.

Exemplo:

```sql
criado_por BIGINT UNSIGNED NULL,
atualizado_por BIGINT UNSIGNED NULL,
cancelado_por BIGINT UNSIGNED NULL
```

Isso ajuda em auditoria, suporte e investigação de erros.

---

### 12.3 Usar exclusão lógica em dados sensíveis ou históricos

Em vez de apagar dados importantes:

```sql
UPDATE clientes
SET excluido_em = NOW()
WHERE id = :id;
```

Depois filtre:

```sql
SELECT id, nome, email
FROM clientes
WHERE excluido_em IS NULL;
```

Use exclusão física apenas quando a regra permitir.

---

## 13. Segurança

### 13.1 Usar prepared statements sempre

Todo valor externo deve entrar por parâmetro.

Com PDO:

```php
$stmt = $pdo->prepare('
    INSERT INTO clientes (nome, email, telefone)
    VALUES (:nome, :email, :telefone)
');

$stmt->execute([
    'nome' => $nome,
    'email' => $email,
    'telefone' => $telefone
]);
```

---

### 13.2 Não guardar senha em texto puro

Nunca salve senha diretamente.

No PHP, use:

```php
$hash = password_hash($senha, PASSWORD_DEFAULT);
```

Para validar:

```php
if (password_verify($senha_digitada, $hash_salvo)) {
    // senha correta
}
```

---

### 13.3 Criar usuários de banco com permissões limitadas

Não use `root` na aplicação.

Exemplo de usuário da aplicação:

```sql
CREATE USER 'app_user'@'%' IDENTIFIED BY 'senha_forte_aqui';

GRANT SELECT, INSERT, UPDATE, DELETE
ON nome_do_banco.*
TO 'app_user'@'%';

FLUSH PRIVILEGES;
```

Para migrações, pode existir outro usuário com permissão de `ALTER`, `CREATE` e `DROP`, usado apenas no deploy.

---

### 13.4 Proteger dados sensíveis

Dados sensíveis devem ter cuidado extra:

- CPF;
- CNPJ;
- telefone;
- e-mail;
- endereço;
- dados de saúde;
- informações financeiras;
- tokens de integração.

Boas práticas:

- não registrar dados sensíveis em logs comuns;
- mascarar dados em telas quando necessário;
- limitar acesso por perfil;
- criptografar segredos;
- separar credenciais por ambiente.

---

## 14. Migrations e versionamento

### 14.1 Nunca alterar banco manualmente sem registro

Toda alteração estrutural deve estar em arquivo versionado.

Exemplo de pasta:

```text
/database
  /migrations
    001_create_clientes.sql
    002_create_pedidos.sql
    003_add_status_to_pedidos.sql
  /seeds
    001_insert_status_padrao.sql
  /docs
    modelo_banco.md
```

---

### 14.2 Cada migration deve fazer uma mudança clara

Bom:

```text
001_create_clientes.sql
002_create_usuarios.sql
003_create_pedidos.sql
004_add_index_pedidos_status_criado_em.sql
```

Ruim:

```text
alteracoes.sql
banco_novo.sql
ajustes_finais.sql
script_rodrigo.sql
```

---

### 14.3 Testar migration antes de produção

Antes de aplicar no banco real:

- testar em cópia local;
- verificar tempo de execução;
- validar impacto em tabelas grandes;
- ter backup recente;
- planejar rollback quando possível.

---

## 15. Backup e recuperação

### 15.1 Backup precisa ser rotina, não improviso

Todo projeto deve ter política de backup.

Defina:

- frequência;
- horário;
- retenção;
- local de armazenamento;
- criptografia;
- teste de restauração;
- responsável pelo monitoramento.

---

### 15.2 Backup não testado não é backup confiável

Além de gerar backup, teste restauração periodicamente.

Checklist:

- o arquivo foi criado?
- o arquivo não está vazio?
- a restauração funciona?
- as tabelas principais voltaram?
- os dados recentes estão presentes?
- o tempo de restauração é aceitável?

---

### 15.3 Exemplo simples com `mysqldump`

```bash
mysqldump -u usuario -p --single-transaction --routines --triggers nome_do_banco > backup.sql
```

Para compactar:

```bash
mysqldump -u usuario -p --single-transaction --routines --triggers nome_do_banco | gzip > backup.sql.gz
```

Observação: para bases grandes, avalie estratégias físicas, réplicas, snapshots e backup incremental.

---

## 16. Performance

### 16.1 Medir antes de otimizar

Não otimize no chute.

Use:

- `EXPLAIN`;
- logs de queries lentas;
- métricas de CPU, RAM e disco;
- tempo médio de resposta;
- volume de linhas lidas;
- volume de linhas retornadas.

---

### 16.2 Ativar e analisar slow query log

Em produção, consultas lentas precisam ser monitoradas.

Exemplo conceitual:

```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;
```

Em servidor real, configure isso de forma persistente no arquivo de configuração do MySQL.

---

### 16.3 Evitar carregar dados demais

Nunca traga milhares de registros para filtrar no PHP se o MySQL pode filtrar.

Errado:

```php
// busca tudo e filtra no PHP
```

Correto:

```sql
SELECT id, nome, email
FROM clientes
WHERE status = 'ativo'
ORDER BY nome
LIMIT 100;
```

---

### 16.4 Cuidado com relatórios pesados

Relatórios podem derrubar sistema se forem feitos sem limite.

Boas práticas:

- filtrar por período;
- usar índices compatíveis;
- limitar exportações;
- gerar relatórios em segundo plano quando forem grandes;
- criar tabelas auxiliares ou agregadas quando necessário;
- evitar rodar relatório pesado em horário de pico.

---

## 17. Padrão recomendado para tabelas

Modelo base:

```sql
CREATE TABLE exemplo_entidades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(150) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'ativo',

    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    excluido_em DATETIME NULL,

    INDEX idx_exemplo_status (status),
    INDEX idx_exemplo_criado_em (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 18. Padrão recomendado para tabelas relacionais

Exemplo `clientes` e `pedidos`:

```sql
CREATE TABLE clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    telefone VARCHAR(30) NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'ativo',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    excluido_em DATETIME NULL,

    INDEX idx_clientes_status (status),
    INDEX idx_clientes_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pendente',
    valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    cancelado_em DATETIME NULL,

    CONSTRAINT fk_pedidos_clientes
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_pedidos_cliente_id (cliente_id),
    INDEX idx_pedidos_status_criado_em (status, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 19. Integração com PHP procedural

### 19.1 Centralizar conexão

Crie um arquivo único para conexão.

Exemplo:

```php
<?php
// config/database.php

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
```

---

### 19.2 Criar funções de repositório simples

Mesmo em PHP procedural, organize o acesso ao banco.

Exemplo:

```php
function buscarClientePorId(int $id): ?array
{
    $stmt = db()->prepare('
        SELECT id, nome, email, telefone
        FROM clientes
        WHERE id = :id
          AND excluido_em IS NULL
        LIMIT 1
    ');

    $stmt->execute(['id' => $id]);
    $cliente = $stmt->fetch();

    return $cliente ?: null;
}
```

Evite espalhar SQL por todas as telas.

---

## 20. Erros comuns que a IA deve evitar

A IA não deve gerar:

- SQL vulnerável a injection;
- tabela sem chave primária;
- banco sem charset `utf8mb4`;
- `SELECT *` em listagens de produção;
- `FLOAT` para dinheiro;
- ausência de índices em chaves estrangeiras;
- nomes genéricos de tabelas e colunas;
- senha salva em texto puro;
- exclusão física de dados históricos sem perguntar;
- migrations sem ordem e sem nome claro;
- consultas pesadas sem `LIMIT`;
- relacionamento sem regra de integridade;
- `ON DELETE CASCADE` sem justificativa;
- regra crítica escondida em trigger sem documentação.

---

## 21. Checklist antes de entregar SQL

Antes de entregar qualquer estrutura MySQL, a IA deve conferir:

- [ ] A tabela tem chave primária?
- [ ] Os nomes são claros?
- [ ] O charset está como `utf8mb4`?
- [ ] A engine está como `InnoDB`?
- [ ] Campos obrigatórios estão como `NOT NULL`?
- [ ] Valores monetários usam `DECIMAL`?
- [ ] Datas estão em tipo correto?
- [ ] Relacionamentos têm `FOREIGN KEY` quando necessário?
- [ ] Índices foram criados com base nas consultas esperadas?
- [ ] Existe controle de criação e atualização?
- [ ] Existe estratégia para exclusão lógica quando necessário?
- [ ] A consulta evita `SELECT *`?
- [ ] A consulta tem `LIMIT` em listagem?
- [ ] Entradas externas usam prepared statements?
- [ ] Existe orientação de backup?

---

## 22. Checklist de revisão de performance

Ao revisar performance, a IA deve perguntar ou analisar:

- Qual consulta está lenta?
- Quantas linhas existem na tabela?
- Qual filtro é usado?
- Existe índice compatível?
- O `EXPLAIN` usa índice?
- A consulta retorna mais dados do que precisa?
- Existe `ORDER BY` sem índice?
- Existe `LIKE '%texto%'` em tabela grande?
- Existe função aplicada na coluna do `WHERE`?
- Existe consulta dentro de loop?
- O problema é SQL, servidor, rede ou volume de dados?

---

## 23. Checklist de segurança

A IA deve revisar:

- [ ] A aplicação usa usuário próprio do banco?
- [ ] O usuário tem apenas permissões necessárias?
- [ ] Senhas estão fora do código?
- [ ] SQL usa parâmetros?
- [ ] Dados sensíveis não aparecem em logs?
- [ ] Backups estão protegidos?
- [ ] Erros internos não são exibidos ao usuário final?
- [ ] Existe controle de acesso por perfil?
- [ ] Tokens e credenciais não estão no Git?

---

## 24. Modelo de resposta que a IA deve seguir

Quando o usuário pedir criação ou revisão de banco MySQL, a IA deve responder neste formato:

```md
## Análise
Explique rapidamente a estrutura e decisões principais.

## Tabelas propostas
Liste as tabelas e responsabilidades.

## SQL
Entregue o SQL organizado.

## Índices
Explique os índices criados e por quê.

## Segurança
Explique cuidados necessários no backend.

## Observações de manutenção
Explique migrations, backup e próximos passos.
```

---

## 25. Regra final para a IA

Sempre que gerar SQL para MySQL, priorize:

1. segurança;
2. integridade;
3. clareza;
4. performance;
5. manutenção;
6. simplicidade.

A melhor estrutura não é a mais complexa. É a que resolve o problema, protege os dados e permite evolução sem virar bagunça.
