# Deploy, EasyPanel e Produção

## Objetivo

Documentar como publicar e operar o sistema no EasyPanel.

> Stack: PHP 8.3 + Apache (Dockerfile na raiz), MySQL 8 e phpMyAdmin como serviços separados.
> Document root = `public/` (docs/05). Health check em `GET /api/v1/health`.
> ⚠️ Código ainda não validado em execução — o EasyPanel será o 1º ambiente real; validar pós-deploy.

---

# 1. Repositório

```txt
GitHub: https://github.com/rodrigo-cassaro-sa/intermediado-vacinacao-sa (público)
Branch desenvolvimento: desenvolvimento
Branch homologação: homologacao
Branch produção: producao   (usar 'main' para o primeiro ambiente)
Tag/release atual: v0.1.0 (scaffold + modelo) — commit inicial 7071c6c
```

> Sugestão para o primeiro ambiente: publicar a branch `main` como **homologação** primeiro,
> validar, e só então criar `producao`. (skill-dockerfile: homologar antes de produção.)

---

# 2. EasyPanel — serviços

Criar **um projeto** no EasyPanel com 3 serviços na mesma rede interna:

| Serviço | Função | Como criar | Exposição |
|---|---|---|---|
| `imz-app` | Aplicação PHP | App a partir do GitHub, build por **Dockerfile** (raiz) | Domínio + SSL |
| `imz-mysql` | Banco MySQL 8 | Template MySQL do EasyPanel | Somente rede interna |
| `imz-phpmyadmin` | Admin visual | Template phpMyAdmin | Subdomínio protegido (opcional) |

Passo a passo:

```txt
1. Criar projeto "imunizacao".
2. Adicionar serviço MySQL (imz-mysql): definir database, usuário e senha fortes.
   - Anotar o NOME INTERNO do serviço → será o DB_HOST da app.
3. Adicionar serviço App (imz-app):
   - Fonte: repositório GitHub (conectar conta) + branch.
   - Build: Dockerfile (caminho: ./Dockerfile).
   - Variáveis de ambiente: ver seção 3.
   - Volumes: ver seção 6.
   - Domínio + SSL: ver seção 7.
4. Adicionar serviço phpMyAdmin (imz-phpmyadmin) apontando para imz-mysql (rede interna).
5. Fazer o deploy do imz-app.
6. Aplicar migrations (seção 8).
7. Rodar checklist pós-deploy (seção 10).
```

---

# 3. Variáveis de ambiente

Configurar no painel do `imz-app` (NÃO versionar `.env`). Nomes conforme `app/config/config.php`:

| Variável | Valor (exemplo) | Observação |
|---|---|---|
| APP_ENV | `homologacao` / `producao` | controla debug e cookie seguro |
| APP_DEBUG | `false` | sempre false fora de dev |
| APP_URL | `https://imz.seudominio.com.br` | domínio final com HTTPS |
| APP_TIMEZONE | `America/Sao_Paulo` |  |
| APP_CHAVE | (aleatória longa) | HMAC/criptografia de dado sensível |
| DB_HOST | `imz-mysql` | **nome interno do serviço MySQL** |
| DB_PORT | `3306` |  |
| DB_NOME | `imunizacao_hml` / `imunizacao_prod` | banco por ambiente |
| DB_USUARIO | `app_hml` / `app_prod` | usuário próprio, sem root |
| DB_SENHA | (forte, secreta) | só no painel |
| SESSAO_INATIVIDADE | `1800` |  |
| SESSAO_ABSOLUTA | `28800` |  |

> Nunca registrar senha real neste documento. Banco separado por ambiente (nunca hml=prod).

---

# 4. Banco e phpMyAdmin

```txt
Banco: MySQL 8 (serviço imz-mysql)
Host interno: imz-mysql (usado como DB_HOST)
phpMyAdmin: serviço imz-phpmyadmin, acessa imz-mysql pela rede interna
Proteção: subdomínio próprio + HTTPS + senha forte; não usar root no dia a dia; restringir por IP se possível
```

Regra:

```txt
MySQL/MariaDB interno.
phpMyAdmin protegido.
Aplicação pelo domínio com SSL.
```

---

# 5. Portas e exposição

| Serviço | Porta interna | Exposto publicamente? | Observação |
|---|---:|---|---|
| HTTP (imz-app) | 80 | sim (via proxy EasyPanel) | redireciona para HTTPS |
| HTTPS (imz-app) | 443 | sim | obrigatório |
| MySQL (imz-mysql) | 3306 | **não** | apenas rede interna |
| phpMyAdmin | 80 interno | restrito | subdomínio protegido |

App não deve depender de porta manual na URL. MySQL nunca exposto publicamente.

---

# 6. Volumes persistentes

Mapear no `imz-app` (e no MySQL) para não perder dados em redeploy:

| Caminho no container | Conteúdo | Risco se perder |
|---|---|---|
| `/var/www/html/storage/uploads` | arquivos de importação de elegíveis | perda de uploads |
| `/var/www/html/storage/logs` | logs de erro/aplicação | perda de rastreabilidade |
| `/var/lib/mysql` (imz-mysql) | banco de dados | **perda total dos dados** |

> Código vem da imagem (deploy); dados ficam em volume. Não misturar os dois.

---

# 7. Domínio e SSL

```txt
1. Apontar o DNS do domínio/subdomínio para o servidor do EasyPanel.
2. No serviço imz-app, adicionar o domínio (ex.: imz.seudominio.com.br).
3. Ativar SSL (Let's Encrypt) no EasyPanel.
4. Garantir redirect HTTP -> HTTPS.
5. APP_URL deve usar https://.
```

Produção sem SSL não é considerada pronta.

---

# 8. Migrations e primeira carga

## Migração automática no deploy (padrão)

O container tem um **entrypoint** (`docker/entrypoint.sh`) que aplica as migrations pendentes
a cada start/redeploy, de forma **incremental e idempotente** (pula as já aplicadas). Ou seja,
todo deploy já cria/atualiza o schema sozinho. Controlado pela env:

```txt
AUTO_MIGRAR=true   # dev/homologação (default)
AUTO_MIGRAR=false  # produção, se preferir aplicar manualmente com backup
```

Se o MySQL ainda estiver subindo, o entrypoint tenta por ~30s antes de seguir; o estado do
banco sempre pode ser conferido em `GET /api/v1/health`.

## Primeira carga (uma vez por ambiente)

Catálogo de vacinas e usuário admin **não** rodam no boot — faça uma vez, no terminal do `imz-app`:

```bash
php scripts/migrar.php --seeds                # cria o catálogo de vacinas (idempotente)
php scripts/criar_admin.php admin@suaempresa.com SuaSenhaForte "Administrador"
```

Alternativa manual: importar `database/migrations/*.sql` e `database/seeds/*.sql` pelo phpMyAdmin.

> Em produção, se `AUTO_MIGRAR=false`, aplicar migrations manualmente **com backup antes**.
> Ver database/README.md.

## Expiração de elegíveis (RN-015) — cron diário

Configure um **Cron Job** no serviço `imz-app` (EasyPanel → Scheduled/Cron) rodando 1x/dia:

```bash
php scripts/expirar_elegiveis.php
```

Ele expira elegíveis pendentes de campanhas vencidas e encerra campanhas com período terminado.

---

# 9. Checklist pré-deploy

```md
- [ ] Branch/tag correta selecionada no EasyPanel.
- [ ] .env NÃO versionado; variáveis configuradas no painel.
- [ ] Banco criado (imz-mysql) com usuário próprio (sem root na app).
- [ ] Backup feito quando já houver dados.
- [ ] Volumes persistentes configurados (mysql, storage/uploads, storage/logs).
- [ ] Domínio configurado e DNS propagado.
- [ ] SSL ativo.
- [ ] Rollback definido (redeploy da imagem anterior + restore de banco).
```

---

# 10. Checklist pós-deploy

```md
- [ ] App abre pelo domínio (https).
- [ ] GET /api/v1/health responde { app: ok, banco: ok }.
- [ ] Migrations aplicadas (13 tabelas + vw_tabela_verdade existem).
- [ ] Login funciona (POST /api/v1/interno/auth/login com o admin do seed).
- [ ] API responde no padrão JSON oficial.
- [ ] Banco conecta pela rede interna (DB_HOST = imz-mysql).
- [ ] Logs sendo gravados em storage/logs.
- [ ] phpMyAdmin acessa o banco correto e está protegido.
- [ ] Health check do container (Docker HEALTHCHECK) verde.
- [ ] Versão publicada registrada (tag/commit) no controle-projeto.md.
```

---

# 11. Rollback

```txt
Aplicação: redeploy da imagem/commit anterior no EasyPanel.
Banco: restaurar backup anterior (mysqldump) se houve migration destrutiva.
Uploads: restaurar volume storage/uploads se necessário.
Autorização: responsável de deploy antes de reverter em produção.
```

---

# 12. Riscos e cuidados

- Código não validado em execução → validar tudo pelo checklist pós-deploy antes de liberar acesso.
- Dado sensível de saúde → APP_DEBUG=false, HTTPS, cookies seguros, phpMyAdmin protegido.
- Primeiro deploy como **homologação**; criar produção só após validar.
- Migrations `schema_migracao` são idempotentes, mas DDL não; não reexecutar às cegas em base já criada.
