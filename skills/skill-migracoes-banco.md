# Skill: Migrações e Evolução de Banco de Dados

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e executar **alterações controladas no banco de dados** em projetos MySQL/MariaDB, incluindo criação de tabelas, alteração de colunas, índices, seeds, scripts de rollback e compatibilidade com produção.

O foco é evoluir o banco com segurança, rastreabilidade e mínimo risco de perda de dados.

---

## Limite desta skill

Esta skill controla alterações no banco de dados ao longo do tempo.

Ela deve focar em scripts versionados, ordem de execução, rollback, seeds, impacto em dados existentes, compatibilidade com produção e testes pós-migração.

Ela não deve substituir:

- `skill-dados.md` para modelagem conceitual;
- `skill-mysql.md` para boas práticas gerais de SQL/MySQL;
- `skill-backup-recuperacao.md` para política completa de backup;
- `skill-deploy-ci-cd.md` para processo completo de publicação;
- `skill-qa.md` para plano completo de testes.

Esta skill responde "como alterar o banco com segurança sem perder dados e sem quebrar produção?".

---

## Stack padrão

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON como padrão de comunicação
Git
Servidor Linux com Apache ou Nginx
Sem framework por padrão
```

A IA deve manter a solução simples, segura, organizada, documentada e possível de manter por outra pessoa ou por outra IA no futuro.

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior especialista em banco relacional, MySQL/MariaDB, deploy, integridade de dados e manutenção de sistemas em produção.

A IA deve pensar em:

- versionamento do schema;
- impacto em dados existentes;
- compatibilidade com código atual;
- rollback;
- backup antes de mudança crítica;
- performance da alteração;
- integridade referencial;
- histórico da decisão.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-dados.md
skill-mysql.md
skill-backup-recuperacao.md
skill-deploy-ci-cd.md
skill-qa.md
```

---

## Princípio central

```txt
Banco de dados evolui junto com o sistema; cada alteração precisa ser planejada, versionada, testada e reversível quando possível.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. O que é migração

Migração é uma mudança controlada no banco.

Exemplos:

- criar tabela;
- adicionar coluna;
- alterar tipo de dado;
- criar índice;
- criar chave estrangeira;
- popular dados iniciais;
- corrigir dados inconsistentes;
- renomear campo;
- remover coluna;
- dividir tabela.

Toda migração deve ter motivo claro e relação com uma versão do sistema.

---

# 2. Padrão de arquivos

Usar nomes ordenáveis por data e descrição.

Exemplo:

```txt
/database/migrations
  20260702_001_create_usuarios_table.sql
  20260702_002_add_status_to_demandas.sql
  20260702_003_create_auditoria_table.sql
/database/rollbacks
  20260702_002_remove_status_from_demandas.sql
/database/seeds
  20260702_001_seed_perfis.sql
```

Regras:

- uma migração por mudança lógica;
- nome claro;
- comentário no topo explicando objetivo;
- informar se exige backup;
- informar se tem rollback seguro.

---

# 3. Tabela de controle de migrações

Projetos profissionais devem registrar migrações executadas.

Tabela sugerida:

```sql
CREATE TABLE schema_migrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  migration_name VARCHAR(255) NOT NULL UNIQUE,
  executed_at DATETIME NOT NULL,
  executed_by VARCHAR(120) NULL,
  batch INT NOT NULL DEFAULT 1
);
```

Isso evita executar a mesma migração duas vezes e permite saber o estado do banco.

---

# 4. Antes de alterar o banco

A IA deve verificar:

- qual problema a mudança resolve;
- qual tabela será impactada;
- volume de dados;
- se há dados nulos ou incompatíveis;
- se a aplicação atual depende da coluna;
- se precisa de backup;
- se precisa de janela de manutenção;
- se existe rollback;
- se o frontend/backend serão atualizados juntos.

Nunca remover ou alterar campo crítico sem entender uso atual.

---

# 5. Migrações compatíveis com produção

Quando possível, fazer mudanças em etapas.

Exemplo seguro para renomear coluna:

1. Criar nova coluna.
2. Copiar dados da antiga para nova.
3. Atualizar código para ler nova coluna.
4. Manter compatibilidade temporária.
5. Confirmar estabilidade.
6. Remover coluna antiga em outro deploy.

Evitar deploy único que muda banco e código de forma impossível de reverter.

---

# 6. Criação de tabela

## Regra de nomenclatura de datas

Por padrão, este projeto usa nomes de datas em português:

- `criado_em`
- `atualizado_em`
- `excluido_em`
- `cancelado_em`
- `pago_em`
- `usado_em`

Não misturar `created_at`, `updated_at` e `deleted_at` com `criado_em`, `atualizado_em` e `excluido_em` no mesmo projeto.

Se o projeto existente já usa padrão em inglês, manter o padrão existente para evitar inconsistência.

---

## Regra para status

Por padrão, usar `VARCHAR(30)` para status simples e validar os valores permitidos no backend.

Use tabela auxiliar quando os status forem administráveis, tiverem cor, ordem, permissões ou puderem mudar pelo painel.

Evitar `ENUM` como padrão, porque mudanças de status exigem alteração estrutural da tabela.

---

Ao criar tabela, definir:

- chave primária;
- tipos corretos;
- campos obrigatórios;
- `criado_em`;
- `atualizado_em` quando útil;
- `excluido_em` para exclusão lógica quando necessário;
- índices;
- chaves estrangeiras;
- comentários quando úteis.

Exemplo:

```sql
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  status VARCHAR(30) NOT NULL DEFAULT 'ativo',
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  excluido_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
);
```
---

# 7. Alteração de coluna

Alterar coluna pode quebrar dados.

Antes de mudar:

- consultar valores atuais;
- verificar tamanho máximo real;
- verificar nulos;
- verificar duplicidades;
- testar em homologação;
- garantir backup;
- planejar rollback.

Evitar reduzir tamanho de campo sem validar dados existentes.

---

# 8. Índices

Criar índice quando houver necessidade real de consulta.

Índices comuns:

- campos usados em busca;
- chaves estrangeiras;
- campos usados em filtros frequentes;
- campos usados em ordenação;
- combinações usadas em relatórios.

Cuidado:

- índice demais prejudica escrita;
- índice errado não ajuda consulta;
- índices em tabelas grandes devem ser planejados.

---

# 9. Seeds e dados iniciais

Seeds são dados necessários para o sistema funcionar.

Exemplos:

- perfis padrão;
- permissões;
- status iniciais;
- configurações padrão;
- templates de notificação.

Seeds devem ser idempotentes quando possível, ou seja, podem rodar mais de uma vez sem duplicar dados.

Usar `INSERT ... ON DUPLICATE KEY UPDATE` quando fizer sentido.

---

# 10. Rollback

Nem toda migração tem rollback perfeito.

A IA deve classificar:

```txt
Rollback seguro = desfaz sem perda de dados.
Rollback parcial = volta estrutura, mas pode perder alterações.
Rollback não seguro = exige backup/restauração.
```

Remover coluna com dados é rollback não seguro se não houver cópia.

Sempre informar risco.

---

# 11. Documentação da mudança

Toda migração deve registrar:

- objetivo;
- data;
- autor/responsável;
- ambiente;
- versão do sistema;
- SQL executado;
- impacto esperado;
- necessidade de backup;
- rollback;
- teste pós-migração.

Isso facilita manutenção por outra IA ou programador.

---

# Checklist obrigatório antes de concluir

- [ ] Migração tem nome claro e versionado.
- [ ] Objetivo da mudança está documentado.
- [ ] Impacto em dados existentes foi analisado.
- [ ] Backup foi previsto quando necessário.
- [ ] Rollback foi classificado.
- [ ] Migração foi testada em homologação.
- [ ] Índices foram criados com motivo.
- [ ] Seeds não duplicam dados indevidamente.
- [ ] Tabela de controle de migração foi considerada.

---

# Modelo de entrega esperado

Ao criar uma migração, entregue:

1. Nome do arquivo.
2. Objetivo.
3. SQL da migração.
4. SQL de rollback quando possível.
5. Impactos.
6. Necessidade de backup.
7. Testes pós-migração.
8. Observações para deploy.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
