# Skill IA — Boas Práticas de Dockerfile, Docker Compose e Ambientes

## Limite desta skill

Esta skill define Dockerfile, Docker Compose, containers, imagens, volumes, redes, variáveis de ambiente, separação de ambientes e configuração prática para rodar o projeto.

Ela pode citar deploy, backup, migrations, segurança e monitoramento quando isso afetar containers e ambientes, mas não deve substituir:

- `skill-deploy-ci-cd.md` para fluxo completo de publicação;
- `skill-backup-recuperacao.md` para política completa de backup;
- `skill-migracoes-banco.md` para evolução versionada do banco;
- `skill-seguranca.md` para política geral de proteção;
- `skill-monitoramento-observabilidade.md` para alertas, métricas e saúde de produção.

Esta skill responde "o projeto consegue rodar de forma isolada, previsível e segura em cada ambiente?".

---

## Regra de versão publicada

Toda publicação em homologação ou produção deve ter uma versão identificável.

A versão pode ser:

- tag Git;
- número semântico;
- hash curto do commit;
- release documentada;
- registro no changelog.

Toda versão publicada deve informar:

```txt
versao
ambiente
branch_origem
commit_hash
data_hora_deploy
responsavel
principais_mudancas
migrations_executadas
rollback_disponivel
status_pos_deploy
```

A IA nunca deve considerar um deploy profissional quando não for possível descobrir exatamente qual código está rodando no ambiente.

Essa regra conecta Git, Docker, deploy e monitoramento, garantindo rastreabilidade da versão publicada.


---

## Regra de checklist pré-produção

Antes de qualquer deploy em produção, a IA deve validar:

- branch ou tag correta;
- Git sem alterações locais pendentes;
- `.env` correto do ambiente;
- debug desligado;
- backup realizado quando houver banco ou arquivos críticos;
- migrations revisadas e testadas;
- plano de rollback definido;
- health check disponível;
- logs ativos;
- monitoramento mínimo ativo;
- testes críticos executados;
- responsável pela validação definido.

Deploy em produção sem checklist não deve ser tratado como seguro.

Quando algum item não puder ser validado, a IA deve registrar o risco, sugerir correção e pedir aprovação explícita antes de seguir.


---

## Regra de observabilidade mínima

Nenhum sistema deve ir para produção sem visibilidade mínima.

Antes de produção, garantir:

- health check da aplicação;
- log de erro do backend;
- log de acesso ou requisições importantes;
- identificação do ambiente;
- versão publicada visível;
- status de banco;
- monitoramento de espaço em disco;
- alerta para erro crítico;
- verificação de backup;
- forma de consultar últimos erros;
- registro de deploy.

Se o sistema possui pagamentos, webhooks, integrações, filas ou jobs, monitorar também:

- último evento recebido;
- último evento processado;
- eventos com erro;
- fila acumulada;
- falhas consecutivas;
- tempo de processamento.

Produção sem observabilidade mínima vira operação às cegas.


---

## Regra de nomenclatura para produção, datas e logs

Por padrão, campos internos de logs, health check, jobs, deploy e monitoramento devem seguir a nomenclatura em português usada no projeto.

### Campos recomendados

```txt
data_hora
nivel
contexto
mensagem
usuario_id
ambiente
versao
verificado_em
iniciado_em
finalizado_em
duracao_ms
mensagem_erro
registros_processados
data_hora_deploy
responsavel
principais_mudancas
status_pos_deploy
```

### Tradução recomendada

```txt
data_hora           -> data_hora
nivel               -> nivel
contexto             -> contexto
mensagem             -> mensagem
usuario_id             -> usuario_id
verificado_em          -> verificado_em
iniciado_em          -> iniciado_em
finalizado_em         -> finalizado_em
duracao_ms         -> duracao_ms
mensagem_erro       -> mensagem_erro
registros_processados   -> registros_processados
environment         -> ambiente
version             -> versao
```

### Regra obrigatória

Não misturar nomes de campos em inglês e português no mesmo projeto quando forem campos internos do sistema.

Termos técnicos como `request_id`, `trace_id`, `status_code`, `endpoint`, `health_check`, `commit_hash`, `container_id`, `APP_ENV`, `DB_HOST` e nomes oficiais de variáveis de ambiente podem ser mantidos em inglês quando forem padrão técnico.


---


## 1. Papel da IA

Você deve agir como um **programador sênior especialista em Docker, Dockerfile, Docker Compose, deploy e arquitetura de ambientes**.

Seu objetivo é criar projetos preparados para rodar de forma organizada em **desenvolvimento**, **homologação** e **produção**, mantendo bancos de dados separados, configurações isoladas, segurança, facilidade de deploy e manutenção.

Sempre que gerar ou revisar um projeto, siga estas regras como padrão.

---

## 2. Princípios principais

### 2.1 Separação de ambientes

Todo projeto deve considerar três ambientes principais:

- **desenvolvimento**: ambiente local ou interno para programar, testar mudanças e depurar.
- **homologação**: ambiente de validação antes de ir para produção.
- **produção**: ambiente real utilizado pelos usuários finais.

Cada ambiente deve ter:

- banco de dados próprio;
- variáveis de ambiente próprias;
- volumes próprios;
- credenciais próprias;
- domínio ou subdomínio próprio;
- regras de segurança próprias;
- logs próprios;
- política própria de backup.

Nunca misture banco de produção com desenvolvimento ou homologação.

---

## 3. Mapeamento de branches para ambientes

### 3.1 Branch `desenvolvimento`

A branch `desenvolvimento` deve ser usada para criação de novas funcionalidades, testes técnicos e ajustes ainda não validados.

Regras recomendadas:

- usar banco de dados separado, como `app_dev`;
- permitir logs mais detalhados;
- permitir ferramentas de debug;
- permitir recarregamento mais rápido do código;
- não usar dados reais sensíveis;
- não expor publicamente sem proteção.

Exemplo:

```txt
Branch: desenvolvimento
Ambiente: development
Banco: app_dev
Domínio: dev.seudominio.com.br
Arquivo env: .env.development
```

### 3.2 Branch `homologacao`

A branch `homologacao` deve representar o ambiente de validação antes da produção.

Regras recomendadas:

- usar banco separado, como `app_hml`;
- configuração parecida com produção;
- logs moderados;
- sem ferramentas abertas de debug;
- dados fictícios ou mascarados;
- acesso restrito para equipe e cliente validar.

Exemplo:

```txt
Branch: homologacao
Ambiente: staging
Banco: app_hml
Domínio: hml.seudominio.com.br
Arquivo env: .env.staging
```

### 3.3 Branch `producao`

A branch `producao` deve conter somente código estável e aprovado.

Regras recomendadas:

- usar banco exclusivo, como `app_prod`;
- logs sem dados sensíveis;
- sem debug ativo;
- backup obrigatório;
- credenciais fortes;
- deploy controlado;
- rollback planejado;
- monitoramento ativo.

Exemplo:

```txt
Branch: producao
Ambiente: production
Banco: app_prod
Domínio: seudominio.com.br
Arquivo env: .env.production
```

---

## 4. Estrutura recomendada de projeto

Use uma estrutura simples, clara e previsível.

```txt
projeto/
├── app/
│   ├── public/
│   ├── src/
│   ├── uploads/
│   │   ├── public/
│   │   └── private/
│   └── logs/
├── docker/
│   ├── php/
│   │   ├── Dockerfile
│   │   └── php.ini
│   ├── nginx/
│   │   └── default.conf
│   └── mysql/
│       └── init/
├── database/
│   ├── migrations/
│   └── seeds/
├── scripts/
│   ├── backup-db.sh
│   ├── restore-db.sh
│   └── deploy.sh
├── .env.example
├── .env.development
├── .env.staging
├── .env.production
├── docker-compose.yml
├── docker-compose.development.yml
├── docker-compose.staging.yml
├── docker-compose.production.yml
└── README.md
```

### 4.1 Regra importante sobre arquivos `.env`

Arquivos reais de ambiente não devem ser versionados no Git.

Versione apenas:

```txt
.env.example
```

Não versionar:

```txt
.env.development
.env.staging
.env.production
```

Esses arquivos devem ficar no servidor, no painel de deploy ou em um gerenciador de secrets.

---

## 5. Boas práticas para Dockerfile

### 5.1 Use imagem base oficial e específica

Evite imagens genéricas ou sem versão.

Ruim:

```dockerfile
FROM php:latest
```

Melhor:

```dockerfile
FROM php:8.3-apache
```

A versão fixa reduz problemas inesperados em builds futuros.

---

### 5.2 Use multi-stage build quando fizer sentido

Quando o projeto tiver etapa de build, como frontend, assets, Node, Composer ou ferramentas temporárias, use multi-stage para não levar dependências desnecessárias para produção.

Exemplo conceitual:

```dockerfile
FROM node:22-alpine AS frontend-build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-apache
WORKDIR /var/www/html
COPY --from=frontend-build /app/dist ./public/assets
COPY ./app ./
```

Use multi-stage para deixar a imagem final menor, mais segura e mais rápida de publicar.

---

### 5.3 Não rode o container como root quando possível

Para produção, evite processos rodando como root.

Exemplo:

```dockerfile
RUN useradd -ms /bin/bash appuser
USER appuser
```

Em imagens Apache/PHP, ajuste permissões com cuidado para não quebrar gravação em pastas como `uploads` e `logs`.

---

### 5.4 Instale somente o necessário

Evite instalar pacotes que não serão usados.

Ruim:

```dockerfile
RUN apt-get update && apt-get install -y vim git curl zip unzip nano htop wget
```

Melhor:

```dockerfile
RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
```

Quanto menor a imagem, menor a superfície de ataque e menor o tempo de deploy.

---

### 5.5 Otimize cache de build

Copie primeiro os arquivos de dependências e depois o restante do código.

Exemplo:

```dockerfile
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader
COPY . .
```

Isso evita reinstalar dependências a cada alteração pequena no código.

---

### 5.6 Configure `php.ini` por ambiente

O PHP deve ter configurações diferentes por ambiente.

Desenvolvimento:

```ini
display_errors=On
error_reporting=E_ALL
log_errors=On
```

Produção:

```ini
display_errors=Off
log_errors=On
expose_php=Off
memory_limit=256M
upload_max_filesize=20M
post_max_size=25M
```

Nunca mostre erro detalhado para usuário final em produção.

---

### 5.7 Use `HEALTHCHECK`

Adicione verificação de saúde quando possível.

Exemplo:

```dockerfile
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost/health.php || exit 1
```

Crie uma rota simples de saúde, por exemplo:

```txt
/health.php
```

Essa rota deve verificar se a aplicação está respondendo. Em homologação e produção, também pode validar conexão básica com banco.

---

## 6. Boas práticas para Docker Compose

### 6.1 Separe compose base e compose por ambiente

Use um compose base com serviços comuns e arquivos complementares por ambiente.

Exemplo:

```txt
docker-compose.yml
docker-compose.development.yml
docker-compose.staging.yml
docker-compose.production.yml
```

Execução em desenvolvimento:

```bash
docker compose -f docker-compose.yml -f docker-compose.development.yml up -d --build
```

Execução em homologação:

```bash
docker compose -f docker-compose.yml -f docker-compose.staging.yml up -d --build
```

Execução em produção:

```bash
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d --build
```

---

### 6.2 Use nomes claros para containers, redes e volumes

Evite nomes genéricos.

Ruim:

```yaml
container_name: app
```

Melhor:

```yaml
container_name: sistema_producao_app
```

Exemplo de volumes separados:

```yaml
volumes:
  mysql_dev_data:
  mysql_hml_data:
  mysql_prod_data:
```

---

### 6.3 Use rede interna para banco de dados

O banco de dados não deve ficar exposto publicamente.

Exemplo:

```yaml
services:
  app:
    networks:
      - frontend
      - backend

  db:
    networks:
      - backend

networks:
  frontend:
  backend:
    internal: true
```

A aplicação acessa o banco pela rede interna, mas o banco não fica aberto para a internet.

---

### 6.4 Evite expor porta do banco em produção

Em desenvolvimento pode ser aceitável expor porta para ferramentas locais, como DBeaver, TablePlus ou MySQL Workbench.

Desenvolvimento:

```yaml
ports:
  - "3307:3306"
```

Produção:

```yaml
# Não expor porta do banco publicamente
```

Em produção, acesse o banco via rede interna, túnel SSH ou ferramenta segura.

---

### 6.5 Use `depends_on` com cuidado

`depends_on` controla ordem de inicialização, mas não garante que o banco já esteja pronto para receber conexão.

Use:

- healthcheck no banco;
- retry de conexão na aplicação;
- script de espera quando necessário.

Exemplo:

```yaml
services:
  db:
    image: mysql:8.4
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

  app:
    depends_on:
      db:
        condition: service_healthy
```

---

## 7. Banco de dados separado por ambiente

### 7.1 Nunca compartilhe banco entre ambientes

Cada ambiente deve ter seu próprio banco.

```txt
Desenvolvimento: app_dev
Homologação: app_hml
Produção: app_prod
```

Também use usuários diferentes:

```txt
app_dev_user
app_hml_user
app_prod_user
```

E senhas diferentes:

```txt
DB_PASSWORD_DEV
DB_PASSWORD_HML
DB_PASSWORD_PROD
```

---

### 7.2 Usuário do banco com menor privilégio possível

A aplicação não deve usar o usuário root do banco.

Ruim:

```env
DB_USER=root
```

Melhor:

```env
DB_USER=app_prod_user
```

O usuário da aplicação deve ter apenas permissões necessárias para operar.

---

### 7.3 Migrações obrigatórias

Toda alteração estrutural no banco deve ser feita por migration.

Exemplos:

```txt
001_create_users_table.sql
002_create_orders_table.sql
003_add_status_to_orders.sql
```

Evite alterar banco manualmente sem registro no projeto.

Toda migration deve ser:

- versionada;
- revisada;
- testada em desenvolvimento;
- validada em homologação;
- aplicada em produção com backup antes.

---

### 7.4 Seeds somente para desenvolvimento e homologação

Seeds são dados iniciais ou fictícios.

Use em:

- desenvolvimento;
- homologação.

Evite rodar seeds em produção, a menos que sejam dados obrigatórios e controlados, como permissões, perfis ou configurações iniciais.

---

## 8. Variáveis de ambiente

### 8.1 Padronize nomes

Use nomes simples e claros.

```env
APP_NAME=Sistema
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_HOST=db
DB_PORT=3306
DB_DATABASE=app_prod
DB_USERNAME=app_prod_user
DB_PASSWORD=senha_forte

UPLOAD_PUBLIC_PATH=/var/www/html/uploads/public
UPLOAD_PRIVATE_PATH=/var/www/html/uploads/private
```

---

### 8.2 Nunca coloque segredo dentro do Dockerfile

Ruim:

```dockerfile
ENV DB_PASSWORD=123456
```

Correto:

```dockerfile
ENV APP_ENV=production
```

Senhas, tokens e chaves devem vir de:

- `.env` no servidor;
- secrets do painel de deploy;
- variáveis protegidas do CI/CD;
- Docker secrets quando disponível.

---

### 8.3 Tenha `.env.example`

Todo projeto deve ter um `.env.example` sem senhas reais.

Exemplo:

```env
APP_NAME=
APP_ENV=
APP_DEBUG=false
APP_URL=

DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Isso ajuda novos desenvolvedores e a IA a entenderem quais configurações são necessárias.

---

## 9. Arquivos, uploads e volumes

### 9.1 Separe upload público e privado

Uploads públicos podem ser acessados por URL.

Uploads privados não devem ficar acessíveis diretamente pelo navegador.

Estrutura recomendada:

```txt
uploads/
├── public/
└── private/
```

Regras:

- arquivos públicos podem ficar em `/public/uploads`;
- arquivos privados devem ser servidos apenas por rota autenticada;
- validar extensão e MIME type;
- limitar tamanho;
- renomear arquivo ao salvar;
- nunca confiar no nome original enviado pelo usuário.

---

### 9.2 Use volumes para dados persistentes

Banco de dados, uploads e logs não devem depender apenas do filesystem interno do container.

Exemplo:

```yaml
volumes:
  mysql_prod_data:
  app_prod_uploads:
  app_prod_logs:
```

Sem volume, ao recriar container você pode perder dados importantes.

---

### 9.3 Não coloque código e dados no mesmo volume em produção

Em produção:

- código deve vir da imagem ou deploy;
- dados devem ficar em volume persistente;
- uploads devem ficar em volume próprio;
- logs devem ir para stdout/stderr ou volume/controlador de logs.

---

## 10. Logs e erros

### 10.1 Logs devem ajudar a resolver problemas

Registre:

- data e hora;
- ambiente;
- usuário ou identificador quando permitido;
- rota ou ação;
- tipo de erro;
- código do erro;
- mensagem técnica controlada.

Não registre:

- senha;
- token;
- chave de API;
- CPF completo quando não for necessário;
- dados sensíveis sem mascaramento.

---

### 10.2 Produção não deve exibir erro técnico

Usuário final deve ver mensagem simples.

Exemplo:

```txt
Não foi possível concluir a operação. Tente novamente ou entre em contato com o suporte.
```

O erro técnico deve ir para log interno.

---

## 11. Segurança

### 11.1 Não versionar arquivos sensíveis

Inclua no `.gitignore`:

```gitignore
.env
.env.*
!/.env.example
app/logs/
app/uploads/
*.sql
*.dump
*.backup
```

Atenção: se quiser versionar arquivos SQL de migration, use uma pasta específica, como `database/migrations/`, e ajuste o `.gitignore` para não bloquear migrations.

---

### 11.2 Não usar senha fraca em banco

Evite:

```txt
123456
admin
root
password
```

Use senhas longas e diferentes por ambiente.

---

### 11.3 Proteja homologação e desenvolvimento

Ambientes que não são produção também precisam de segurança.

Recomendações:

- restringir por senha;
- restringir por IP quando possível;
- não usar dados reais sem mascaramento;
- não deixar debug aberto publicamente;
- não indexar no Google.

---

### 11.4 Use HTTPS em homologação e produção

Produção e homologação devem usar HTTPS.

Desenvolvimento local pode usar HTTP, mas integração externa, login e pagamentos devem ser testados também em HTTPS quando necessário.

---

## 12. Deploy por ambiente

### 12.1 Fluxo recomendado

```txt
desenvolvimento -> homologacao -> producao
```

Fluxo:

1. Desenvolver na branch `desenvolvimento`.
2. Testar localmente.
3. Fazer merge para `homologacao`.
4. Subir ambiente de homologação.
5. Validar com equipe ou cliente.
6. Fazer merge para `producao`.
7. Fazer backup do banco de produção.
8. Rodar migrations.
9. Fazer deploy de produção.
10. Validar saúde da aplicação.

---

### 12.2 Não fazer deploy direto em produção sem homologar

Evite subir código direto da branch de desenvolvimento para produção.

A homologação existe para reduzir risco.

---

### 12.3 Tenha rollback planejado

Antes de deploy em produção, tenha resposta para:

- como voltar a versão anterior da aplicação?
- como restaurar backup do banco?
- a migration é reversível?
- quem valida depois do deploy?
- qual é a janela de menor impacto?

---

## 13. Backup e restauração

### 13.1 Backup antes de migration em produção

Antes de qualquer alteração estrutural no banco de produção, faça backup.

Exemplo:

```bash
docker exec mysql_prod mysqldump -u root -p app_prod > backup_app_prod_$(date +%Y%m%d_%H%M%S).sql
```

Guarde backups fora do container.

---

### 13.2 Teste restauração

Backup que nunca foi restaurado é apenas uma esperança.

Periodicamente, restaure backup em ambiente separado para validar se ele funciona.

---

### 13.3 Política mínima de backup

Recomendação inicial:

- backup diário do banco de produção;
- retenção mínima de 7 a 30 dias;
- backup antes de deploy crítico;
- cópia fora do servidor principal;
- teste de restauração periódico.

---

## 14. Exemplo de Dockerfile para PHP procedural + MySQL

```dockerfile
FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
        curl \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && a2enmod rewrite headers \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./app /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && mkdir -p /var/www/html/uploads/public /var/www/html/uploads/private /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html/uploads /var/www/html/logs

HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD curl -f http://localhost/health.php || exit 1

EXPOSE 80
```

---

## 15. Exemplo de docker-compose base

```yaml
services:
  app:
    build:
      contexto: .
      dockerfile: docker/php/Dockerfile
    restart: unless-stopped
    env_file:
      - .env
    depends_on:
      db:
        condition: service_healthy
    networks:
      - frontend
      - backend
    volumes:
      - app_uploads:/var/www/html/uploads
      - app_logs:/var/www/html/logs

  db:
    image: mysql:8.4
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - backend
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

networks:
  frontend:
  backend:
    internal: true

volumes:
  mysql_data:
  app_uploads:
  app_logs:
```

---

## 16. Exemplo de compose para desenvolvimento

```yaml
services:
  app:
    container_name: sistema_dev_app
    ports:
      - "8080:80"
    volumes:
      - ./app:/var/www/html
      - sistema_dev_uploads:/var/www/html/uploads
      - sistema_dev_logs:/var/www/html/logs
    environment:
      APP_ENV: development
      APP_DEBUG: "true"

  db:
    container_name: sistema_dev_db
    ports:
      - "3307:3306"
    volumes:
      - sistema_dev_mysql:/var/lib/mysql

volumes:
  sistema_dev_mysql:
  sistema_dev_uploads:
  sistema_dev_logs:
```

---

## 17. Exemplo de compose para homologação

```yaml
services:
  app:
    container_name: sistema_hml_app
    ports:
      - "8081:80"
    environment:
      APP_ENV: staging
      APP_DEBUG: "false"
    volumes:
      - sistema_hml_uploads:/var/www/html/uploads
      - sistema_hml_logs:/var/www/html/logs

  db:
    container_name: sistema_hml_db
    volumes:
      - sistema_hml_mysql:/var/lib/mysql

volumes:
  sistema_hml_mysql:
  sistema_hml_uploads:
  sistema_hml_logs:
```

---

## 18. Exemplo de compose para produção

```yaml
services:
  app:
    container_name: sistema_prod_app
    ports:
      - "80:80"
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
    volumes:
      - sistema_prod_uploads:/var/www/html/uploads
      - sistema_prod_logs:/var/www/html/logs

  db:
    container_name: sistema_prod_db
    volumes:
      - sistema_prod_mysql:/var/lib/mysql
    # Não expor porta do banco em produção

volumes:
  sistema_prod_mysql:
  sistema_prod_uploads:
  sistema_prod_logs:
```

Observação: em produção real, normalmente o HTTPS fica em um proxy reverso, como Nginx, Traefik, Caddy, EasyPanel ou outro painel de deploy.

---

## 19. Checklist obrigatório para IA antes de finalizar um projeto Docker

Antes de entregar qualquer projeto com Docker, valide:

- [ ] Existe `Dockerfile` claro e funcional.
- [ ] Existe `docker-compose.yml` base.
- [ ] Existem arquivos separados por ambiente quando necessário.
- [ ] O banco de desenvolvimento é separado do banco de homologação.
- [ ] O banco de homologação é separado do banco de produção.
- [ ] O banco de produção não está exposto publicamente.
- [ ] As senhas não estão no código.
- [ ] Existe `.env.example`.
- [ ] Arquivos `.env` reais estão no `.gitignore`.
- [ ] Uploads usam volume persistente.
- [ ] Logs estão configurados.
- [ ] Produção está com debug desligado.
- [ ] Homologação está próxima da produção.
- [ ] Existe healthcheck.
- [ ] Existe estratégia de backup.
- [ ] Existe orientação de rollback.
- [ ] Migrations são versionadas.
- [ ] Seeds não rodam automaticamente em produção sem necessidade.
- [ ] O container instala somente dependências necessárias.
- [ ] A imagem base usa versão fixa, não `latest`.
- [ ] O projeto tem README com comandos principais.

---

## 20. Padrão de resposta da IA ao criar Docker para um projeto

Quando o usuário pedir para criar Dockerfile ou ambiente Docker, responda sempre com:

1. Estrutura de pastas sugerida.
2. Dockerfile principal.
3. Docker Compose base.
4. Compose de desenvolvimento.
5. Compose de homologação.
6. Compose de produção.
7. Exemplo de `.env.example`.
8. Explicação dos bancos separados.
9. Comandos para subir cada ambiente.
10. Checklist de segurança e deploy.

---

## 21. Comandos úteis

### Subir desenvolvimento

```bash
docker compose -f docker-compose.yml -f docker-compose.development.yml --env-file .env.development up -d --build
```

### Subir homologação

```bash
docker compose -f docker-compose.yml -f docker-compose.staging.yml --env-file .env.staging up -d --build
```

### Subir produção

```bash
docker compose -f docker-compose.yml -f docker-compose.production.yml --env-file .env.production up -d --build
```

### Ver logs

```bash
docker compose logs -f app
```

### Parar ambiente

```bash
docker compose down
```

### Recriar containers

```bash
docker compose up -d --build --force-recreate
```

### Entrar no container da aplicação

```bash
docker exec -it sistema_prod_app bash
```

### Entrar no MySQL

```bash
docker exec -it sistema_prod_db mysql -u root -p
```

---

## 22. Regras de ouro

- Nunca use o mesmo banco para desenvolvimento, homologação e produção.
- Nunca coloque senha dentro do Dockerfile.
- Nunca suba `.env` real para o Git.
- Nunca deixe debug ligado em produção.
- Nunca exponha banco de produção sem necessidade.
- Nunca faça migration em produção sem backup.
- Nunca dependa de alteração manual sem documentação.
- Nunca use `latest` em imagem crítica de produção.
- Sempre use volume para dados persistentes.
- Sempre tenha `.env.example`.
- Sempre tenha healthcheck.
- Sempre tenha rollback planejado.
- Sempre valide em homologação antes da produção.

---

## 23. Objetivo final

A IA deve gerar projetos Docker que sejam:

- simples de entender;
- fáceis de subir;
- seguros para produção;
- separados por ambiente;
- preparados para banco de dados isolado;
- compatíveis com deploy em servidor próprio, VPS, EasyPanel, Portainer, Traefik, Nginx ou plataformas semelhantes;
- adequados para projetos com PHP procedural, MySQL, HTML, CSS, JavaScript e outras tecnologias web comuns.

Esta skill deve ser usada como referência sempre que a IA criar, revisar ou corrigir Dockerfile, Docker Compose, ambiente de deploy, separação de banco de dados ou organização de ambientes por branch.

---

# Checklist obrigatório das regras de produção

- [ ] Versão publicada está identificável por tag, número ou commit.
- [ ] Checklist pré-produção foi validado antes de seguir.
- [ ] Observabilidade mínima está ativa no ambiente alvo.
- [ ] Campos internos de datas e logs seguem o padrão em português.
