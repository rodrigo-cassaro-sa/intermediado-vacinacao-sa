# Skill: Banco de Dados Relacional para Projetos Web

## Objetivo da skill

Esta skill orienta uma IA a projetar, revisar e evoluir a camada de **banco de dados relacional** de sistemas web, com foco em clareza, integridade, segurança, desempenho e manutenção.

Esta skill complementa a skill específica de MySQL. O foco aqui é pensar o banco como parte do projeto: entidades, relacionamentos, regras, histórico, consultas, documentação e evolução.

---

## Limite desta skill

Esta skill define a modelagem conceitual e lógica dos dados do sistema.

Ela deve focar em entidades, relacionamentos, regras de integridade, histórico, auditoria, dados sensíveis e documentação do modelo.

Ela pode citar índices, tipos SQL, migrações e backup em nível de orientação, mas não deve substituir:

- `skill-mysql.md` para implementação técnica em SQL/MySQL;
- `skill-migracoes-banco.md` para alteração versionada do banco;
- `skill-backup-recuperacao.md` para política de backup e restauração;
- `skill-lgpd-privacidade.md` para regras completas de dados pessoais;
- `skill-relatorios-bi-dashboard.md` para indicadores e consultas analíticas.

Esta skill responde "quais dados existem, como se relacionam e quais regras precisam ser preservadas?".

---

## Stack padrão

```txt
MySQL ou MariaDB
PHP procedural puro
PDO
APIs JSON
HTML/CSS/JavaScript puro no frontend
```

---

## Papel da IA

Ao usar esta skill, a IA deve agir como uma pessoa sênior em modelagem de banco de dados relacional.

A IA deve pensar em:

- entidades reais do sistema;
- relacionamentos;
- integridade dos dados;
- histórico;
- auditoria;
- performance;
- segurança;
- relatórios;
- manutenção futura;
- crescimento do volume de dados.

---

## Banco de dados não é só tabela

A IA deve considerar que banco de dados envolve:

- modelagem;
- nomes;
- tipos de dados;
- constraints;
- índices;
- relacionamentos;
- transações;
- histórico;
- auditoria;
- backup;
- migrações;
- permissões;
- documentação.

---

## Processo recomendado de modelagem

### 1. Entender o domínio

Antes de criar tabela, a IA deve entender:

- quais entidades existem;
- quais ações o sistema executa;
- quais dados são obrigatórios;
- quais dados mudam com o tempo;
- quais dados precisam de histórico;
- quais relatórios serão necessários;
- quais volumes são esperados.

---

### 2. Listar entidades

Exemplo para sistema de demandas:

```txt
usuarios
clientes
demandas
demanda_participantes
planos_acao
anexos
comentarios
status_historico
```

Cada tabela deve representar uma coisa clara.

---

### 3. Definir relacionamentos

Perguntas que a IA deve fazer internamente:

```txt
Um cliente tem muitas demandas?
Uma demanda tem vários responsáveis?
Um plano de ação pertence a uma demanda?
Um anexo pertence a qual registro?
Um status precisa de histórico?
```

---

### 4. Definir regras de integridade

A IA deve aplicar:

- chave primária;
- chave estrangeira;
- campos obrigatórios;
- unicidade;
- valores padrão;
- status controlados;
- datas de auditoria.

---

## Padrões de nomeação

Recomendado:

```txt
Tabelas no plural.
Colunas em snake_case.
Chave primária como id.
Chave estrangeira como entidade_id.
Datas com sufixo _em ou _data.
Booleanos com prefixo is_, tem_ ou permite_ quando fizer sentido.
```

Exemplos:

```txt
usuarios
clientes
demandas
plano_acoes
criado_em
atualizado_em
excluido_em
usuario_id
cliente_id
ativo
```

Evite:

```txt
Tabela1
cadastro
info
dados
data1
status2
cod
```

---

## Estrutura mínima de tabela

Tabela principal recomendada:

```sql
CREATE TABLE clientes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(180) NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    excluido_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Campos de auditoria

Sempre avaliar uso de:

```txt
criado_em
criado_por
atualizado_em
atualizado_por
excluido_em
excluido_por
```

Use quando o sistema precisar rastrear alterações.

---

## Soft delete

Use `excluido_em` quando for necessário preservar histórico.

Exemplo:

```sql
UPDATE clientes
SET excluido_em = NOW()
WHERE id = :id;
```

A IA deve lembrar que consultas precisam filtrar:

```sql
WHERE excluido_em IS NULL
```

Não usar soft delete automaticamente para tudo. Use quando houver motivo real.

---

## Histórico de status

Quando o status de um registro for importante, crie histórico.

Exemplo:

```txt
demandas
- id
- titulo
- status_atual

status_historico
- id
- demanda_id
- status_anterior
- status_novo
- alterado_por
- alterado_em
- observacao
```

Isso permite responder:

- quem mudou;
- quando mudou;
- de qual status para qual status;
- por quê.

---

## Tabelas de relacionamento

Use tabela intermediária quando houver muitos-para-muitos.

Exemplo:

```txt
usuarios
demandas
demanda_participantes
```

```sql
CREATE TABLE demanda_participantes (
    demanda_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NOT NULL,
    papel VARCHAR(50) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (demanda_id, usuario_id),
    FOREIGN KEY (demanda_id) REFERENCES demandas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Tipos de dados

A IA deve escolher tipos com intenção.

Regras:

- dinheiro usa `DECIMAL`, nunca `FLOAT`;
- data simples usa `DATE`;
- data e hora de evento usa `DATETIME`;
- texto curto usa `VARCHAR`;
- texto longo usa `TEXT`;
- status simples pode usar `VARCHAR` ou tabela auxiliar;
- chave grande usa `BIGINT UNSIGNED`;
- booleano usa `TINYINT(1)`.

---

## Status no banco

Para status simples e estáveis, pode usar `VARCHAR` com validação no backend.

Exemplo:

```txt
pendente
em_andamento
concluido
cancelado
```

Para status administráveis, use tabela própria.

Exemplo:

```txt
status_demandas
- id
- nome
- chave
- cor
- ordem
- ativo
```

---

## Índices

A IA deve criar índices baseados nas consultas reais.

Adicionar índice para:

- chaves estrangeiras;
- campos usados em filtros frequentes;
- campos usados em busca;
- datas usadas em relatórios;
- combinações frequentes de filtros.

Exemplo:

```sql
CREATE INDEX idx_demandas_cliente_status ON demandas (cliente_id, status);
CREATE INDEX idx_demandas_criado_em ON demandas (criado_em);
```

Evite índice em tudo. Índice ajuda leitura, mas pesa escrita e armazenamento.

---

## Consultas

A IA deve evitar:

- `SELECT *` em listagens grandes;
- consultas sem `LIMIT` em telas administrativas;
- filtros sem índice em tabelas grandes;
- subconsultas confusas quando um `JOIN` claro resolve;
- repetir consulta dentro de loop.

Prefira:

```sql
SELECT id, nome, email, ativo
FROM usuarios
WHERE excluido_em IS NULL
ORDER BY nome ASC
LIMIT 50 OFFSET 0;
```

---

## Paginação

Listagens devem ter paginação quando houver chance de muitos registros.

Padrão:

```txt
page
per_page
search
status
data_inicio
data_fim
```

Limitar `per_page` no backend para evitar abuso.

---

## Integridade e constraints

A IA deve usar constraints para proteger consistência.

Exemplos:

- `NOT NULL` em campos obrigatórios;
- `UNIQUE` em e-mail único;
- `FOREIGN KEY` em relações reais;
- `DEFAULT` para valores padrão;
- `CHECK` se o ambiente suportar e fizer sentido.

---

## Dados sensíveis

A IA deve reduzir exposição de dados sensíveis.

Regras:

- salvar apenas o necessário;
- proteger backups;
- não registrar dados sensíveis em logs;
- limitar acesso do usuário do banco;
- não retornar dados sensíveis em consultas comuns;
- mascarar dados quando possível.

---

## Migrações e evolução

Mesmo sem framework, mudanças no banco devem ser versionadas.

Estrutura recomendada:

```txt
/database
  /migrations
    20260702_001_criar_usuarios.sql
    20260702_002_criar_clientes.sql
  schema.sql
  seed.sql
```

Regras:

- nunca alterar produção manualmente sem registro;
- salvar scripts de alteração;
- testar alteração antes;
- prever rollback quando possível;
- documentar impacto.

---

## Backup

A IA deve lembrar que banco real precisa de backup.

Recomendações:

- backup automático;
- teste de restauração;
- armazenamento seguro;
- retenção definida;
- proteção contra acesso indevido;
- backup antes de migrações grandes.

---

## Documentação mínima

Todo projeto deve documentar:

- objetivo das tabelas principais;
- relacionamentos importantes;
- campos críticos;
- status possíveis;
- regras de exclusão;
- índices relevantes;
- scripts de criação;
- dados iniciais.

---

## Critérios de aceite

Um banco de dados está bem projetado quando:

- tabelas representam entidades claras;
- nomes são consistentes;
- chaves primárias existem;
- relacionamentos importantes têm FK;
- campos obrigatórios são `NOT NULL`;
- dados financeiros usam `DECIMAL`;
- existe estratégia de histórico quando necessário;
- consultas principais têm índices;
- listagens têm paginação;
- scripts estão versionados;
- dados sensíveis são protegidos.

---

## Checklist final da IA

Antes de entregar modelagem ou SQL, verificar:

- [ ] A entidade da tabela está clara?
- [ ] Os nomes seguem padrão?
- [ ] A chave primária está definida?
- [ ] As chaves estrangeiras estão corretas?
- [ ] Os tipos de dados fazem sentido?
- [ ] Existe auditoria quando necessário?
- [ ] Precisa preservar histórico?
- [ ] Existem índices para consultas frequentes?
- [ ] Existe paginação para listagens?
- [ ] Há proteção para dados sensíveis?
- [ ] O SQL usa InnoDB e utf8mb4?
- [ ] A mudança foi pensada como migração?
