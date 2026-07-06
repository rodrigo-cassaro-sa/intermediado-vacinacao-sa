# Skill: EasyPanel, MySQL e phpMyAdmin para Deploy de Projetos PHP

## Objetivo da skill

Esta skill orienta uma IA a preparar, configurar, revisar e publicar projetos PHP usando **EasyPanel** como painel de deploy, **MySQL/MariaDB** como banco de dados e **phpMyAdmin** como ferramenta visual de administração do banco.

Ela padroniza a infraestrutura real de publicação dos projetos, evitando improviso com banco, domínio, SSL, variáveis de ambiente, volumes, backup e segurança.

---

## Stack padrão considerada

```txt
EasyPanel
GitHub como origem do código
PHP procedural puro
MySQL ou MariaDB
phpMyAdmin
Docker/Docker Compose quando aplicável
Apache ou Nginx
HTML
CSS
JavaScript puro
Fetch API
JSON
Domínio final
SSL/HTTPS
Volumes persistentes para banco, uploads e logs
```

---

## Limite desta skill

Esta skill define como o projeto deve ser preparado para rodar no EasyPanel com MySQL/phpMyAdmin.

Ela não substitui:

- `skill-git.md` para versionamento, branch, commit, tag e release;
- `skill-dockerfile.md` para Dockerfile, Compose, volumes, redes e containers;
- `skill-deploy-ci-cd.md` para deploy, checklist, rollback e pós-deploy;
- `skill-monitoramento-observabilidade.md` para monitoramento, health check e incidentes;
- `skill-backup-recuperacao.md` para política completa de backup e restauração;
- `skill-seguranca.md` para proteção geral;
- `skill-mysql.md` para implementação SQL;
- `skill-migracoes-banco.md` para evolução controlada do schema.

Esta skill responde:

```txt
Como publicar este projeto no EasyPanel usando MySQL/phpMyAdmin de forma segura, persistente e pronta para produção?
```

---

## Quando usar esta skill

Use quando o projeto for:

- publicado no EasyPanel;
- conectado a GitHub;
- rodar em container;
- usar PHP;
- usar MySQL/MariaDB;
- usar phpMyAdmin;
- precisar de domínio final;
- precisar de SSL;
- precisar de `.env` por ambiente;
- precisar de banco separado por ambiente;
- precisar de volume persistente;
- precisar de uploads persistentes;
- precisar de logs persistentes;
- precisar de backup e restore;
- precisar de homologação e produção.

---

## Quando não usar esta skill

Não usar quando:

- o projeto não será publicado no EasyPanel;
- o projeto usa hospedagem compartilhada simples sem container;
- o projeto não usa MySQL/MariaDB;
- a tarefa é apenas modelagem de banco;
- a tarefa é apenas código PHP local;
- a tarefa é apenas Git;
- a tarefa é apenas design/frontend;
- o projeto ainda está em fase de briefing sem decisão de infraestrutura.

---

# 1. Ordem recomendada no Grupo 10

Quando esta skill existir na biblioteca, a ordem recomendada do Grupo 10 passa a ser:

```txt
1. skill-git.md
2. skill-dockerfile.md
3. skill-easypanel-mysql-phpmyadmin.md
4. skill-deploy-ci-cd.md
5. skill-monitoramento-observabilidade.md
```

Motivo:

```txt
Git define a versão.
Docker define como a aplicação roda.
EasyPanel define como isso será publicado e configurado no painel.
Deploy define checklist, rollback e publicação.
Monitoramento valida produção.
```

---

# 2. Responsabilidades principais

A IA deve garantir que o projeto tenha:

```txt
repositório GitHub preparado
Dockerfile ou configuração compatível com EasyPanel
variáveis de ambiente separadas por ambiente
banco MySQL/MariaDB configurado
phpMyAdmin configurado com segurança
volumes persistentes
uploads persistentes
logs persistentes
domínio configurado
SSL ativo
health check
backup do banco
backup dos uploads
plano de rollback
documentação de deploy
```

---

# 3. Ambientes obrigatórios

Sempre que possível, separar:

```txt
development
staging/homologacao
production
```

Cada ambiente deve ter:

```txt
APP_ENV próprio
APP_URL próprio
DB_DATABASE próprio
DB_USERNAME próprio
DB_PASSWORD próprio
uploads próprios
logs próprios
configuração própria no EasyPanel
```

Nunca usar o mesmo banco para homologação e produção.

---

# 4. Variáveis de ambiente obrigatórias

O `.env.example` deve conter, no mínimo:

```env
APP_NAME=
APP_ENV=
APP_DEBUG=
APP_URL=

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_NAME=
SESSION_SECURE_COOKIE=
SESSION_LIFETIME=

CSRF_SECRET=

UPLOAD_PUBLIC_PATH=
UPLOAD_PRIVATE_PATH=
LOG_PATH=

PHP_MEMORY_LIMIT=
PHP_UPLOAD_MAX_FILESIZE=
PHP_POST_MAX_SIZE=
PHP_MAX_EXECUTION_TIME=
```

Regras:

- não versionar `.env`;
- não colocar senha real em `.env.example`;
- configurar variáveis reais dentro do EasyPanel;
- usar senha forte;
- trocar senha padrão;
- separar senha por ambiente.

---

# 5. Banco MySQL/MariaDB

## 5.1 Usuário do banco

A aplicação deve usar um usuário próprio do banco.

Evitar usar:

```txt
root
admin global
usuário do phpMyAdmin
usuário com permissões excessivas
```

O usuário da aplicação deve ter apenas as permissões necessárias.

Exemplo:

```txt
SELECT
INSERT
UPDATE
DELETE
```

Permissões como `DROP`, `ALTER` e `CREATE` em produção devem ser usadas com cuidado e, preferencialmente, apenas durante migrations controladas.

---

## 5.2 Banco separado por ambiente

Usar nomes claros:

```txt
projeto_dev
projeto_hml
projeto_prod
```

Nunca rodar teste ou homologação no banco de produção.

---

## 5.3 Persistência

O banco deve usar volume persistente.

A IA deve verificar:

```txt
volume do banco existe
volume não será apagado em redeploy
container pode ser recriado sem perder dados
backup existe antes de migration crítica
```

---

# 6. phpMyAdmin

## 6.1 Função do phpMyAdmin

phpMyAdmin é ferramenta de administração visual do banco.

Ele serve para:

- consultar tabelas;
- conferir registros;
- importar/exportar manualmente;
- validar estrutura;
- ajudar em suporte técnico;
- inspecionar banco em homologação.

Ele não deve ser tratado como:

- estratégia principal de backup;
- camada de segurança;
- substituto de migrations;
- substituto de scripts de restore;
- ferramenta de operação diária por usuário comum.

Regra:

```txt
phpMyAdmin administra.
Migration altera estrutura.
Script de backup protege.
Aplicação opera.
```

---

## 6.2 Segurança do phpMyAdmin

Nunca deixar phpMyAdmin exposto sem proteção.

A IA deve recomendar:

- senha forte;
- acesso restrito;
- subdomínio protegido;
- autenticação do próprio phpMyAdmin;
- bloqueio por IP quando possível;
- não usar credencial root no dia a dia;
- remover/desativar se não for necessário em produção;
- não compartilhar acesso com usuário final.

Se o phpMyAdmin ficar público em domínio, deve usar HTTPS.

---

# 7. Volumes persistentes

O EasyPanel deve preservar:

```txt
banco de dados
uploads públicos
uploads privados
logs
backups locais temporários
arquivos gerados importantes
```

A IA deve mapear:

```txt
caminho no container
caminho persistente/volume
tipo de dado
risco se perder
backup necessário
```

Exemplo:

```txt
/storage/uploads/public
/storage/uploads/private
/storage/logs
/var/lib/mysql
/backups
```

---

# 8. Uploads e arquivos

Uploads não devem ficar em pasta que será apagada no redeploy.

A IA deve garantir:

- volume persistente;
- limite de tamanho;
- validação de extensão;
- validação de MIME;
- separação público/privado;
- nome de arquivo seguro;
- backup dos uploads;
- permissão correta de escrita.

Nunca salvar upload crítico apenas dentro do container sem volume.

---

# 9. Logs

Logs precisam ser persistentes e consultáveis.

Separar:

```txt
log de erro PHP
log da aplicação
log de auditoria
log de integração
log de deploy quando aplicável
```

Não registrar:

- senha;
- token;
- segredo;
- cookie de sessão;
- dados sensíveis sem mascaramento.

---

# 10. Organização de portas, rede e exposição dos serviços

A IA deve definir claramente quais serviços ficam públicos, quais ficam internos e quais portas serão usadas.

O objetivo é garantir que a aplicação fique online sem expor banco, phpMyAdmin ou serviços internos de forma indevida.

## 10.1 Regra principal de exposição

Por padrão:

```txt
Internet pública → apenas HTTP/HTTPS
Aplicação PHP → exposta pelo domínio via EasyPanel/proxy
MySQL/MariaDB → somente rede interna
phpMyAdmin → acesso restrito, preferencialmente por subdomínio protegido
```

Nunca expor o MySQL diretamente para a internet sem necessidade real e sem proteção avançada.

## 10.2 Portas recomendadas

| Serviço | Porta interna comum | Exposição pública | Observação |
|---|---:|---|---|
| HTTP | 80 | Sim | Normalmente gerenciado pelo proxy/EasyPanel |
| HTTPS | 443 | Sim | Obrigatório em produção |
| App PHP/Apache/Nginx | 80, 8080 ou 9000 | Não diretamente | Deve passar pelo proxy do EasyPanel |
| MySQL/MariaDB | 3306 | Não | Apenas rede interna |
| phpMyAdmin | 80 interno | Restrito | Expor por subdomínio protegido se necessário |
| Health check | rota HTTP | Sim, sem dados sensíveis | Exemplo: `/health` ou `/api/health.php` |

## 10.3 Regra para MySQL

O banco deve ficar acessível apenas pela rede interna do EasyPanel/Docker.

A aplicação deve acessar usando:

```env
DB_HOST=nome_interno_do_servico_mysql
DB_PORT=3306
```

Evitar:

```txt
abrir porta 3306 publicamente
usar IP público do servidor para conectar a aplicação ao banco
usar root na aplicação
usar o mesmo usuário do phpMyAdmin na aplicação
```

## 10.4 Regra para phpMyAdmin

phpMyAdmin pode ser publicado, mas deve ser tratado como área sensível.

Preferir:

```txt
phpmyadmin.dominio.com.br
admin-db.dominio.com.br
acesso com HTTPS
senha forte
usuário sem privilégios excessivos
restrição de IP quando possível
desativar quando não estiver em uso, se fizer sentido
```

Evitar:

```txt
phpMyAdmin aberto sem SSL
phpMyAdmin com usuário root
phpMyAdmin com senha fraca
phpMyAdmin no mesmo domínio público da aplicação sem proteção
```

## 10.5 Regra para aplicação

A aplicação deve ser acessada pelo domínio final.

Exemplo:

```txt
https://app.dominio.com.br
https://sistema.dominio.com.br
https://dominio.com.br
```

A aplicação não deve depender de porta manual na URL em produção.

Evitar:

```txt
https://dominio.com.br:8080
http://ip-do-servidor:porta
```

Em produção, o usuário deve acessar pelo domínio com HTTPS.

## 10.6 Rede interna dos serviços

Quando houver múltiplos serviços, eles devem compartilhar uma rede interna.

Exemplo lógico:

```txt
app_php       → acessa mysql pela rede interna
phpmyadmin    → acessa mysql pela rede interna
mysql         → não exposto publicamente
proxy/easypanel → expõe app e, se necessário, phpMyAdmin
```

A IA deve documentar:

```txt
nome do serviço
porta interna
porta pública, se existir
domínio/subdomínio
tipo de exposição
risco
```

## 10.7 Conflito de portas

Antes de publicar, verificar se alguma porta já está em uso no servidor.

Portas sensíveis:

```txt
80
443
3306
8080
9000
```

Se houver conflito:

- não trocar porta aleatoriamente sem documentar;
- preferir usar proxy do EasyPanel;
- manter app atrás do domínio;
- manter banco interno;
- registrar a porta final usada.

## 10.8 Checklist de portas e serviços

Antes de considerar o app online, validar:

```md
- [ ] Domínio aponta para o servidor.
- [ ] Porta 80 responde ou redireciona.
- [ ] Porta 443 responde com SSL.
- [ ] Aplicação abre sem precisar digitar porta.
- [ ] MySQL não está exposto publicamente.
- [ ] Aplicação conecta no MySQL pela rede interna.
- [ ] phpMyAdmin está protegido.
- [ ] phpMyAdmin acessa o banco correto.
- [ ] Health check responde pelo domínio.
- [ ] Não existe serviço sensível aberto sem necessidade.
- [ ] Portas e domínios foram documentados.
```

---

# 11. Domínio e SSL

Antes de considerar produção pronta, validar:

```txt
domínio apontando para servidor
DNS propagado
domínio configurado no EasyPanel
SSL ativo
HTTP redireciona para HTTPS
APP_URL usa https
cookies seguros
health check acessível pelo domínio
login funcionando pelo domínio
APIs funcionando pelo domínio
uploads funcionando pelo domínio
```

Produção sem SSL não deve ser considerada pronta.

---

# 12. Health check

A aplicação deve ter um endpoint simples de saúde.

Exemplo:

```txt
/health
/api/health.php
```

O health check deve validar:

```txt
aplicação responde
ambiente identificado
conexão com banco funciona
versão publicada disponível
status geral
```

Não deve expor:

- senha;
- DSN completo;
- tokens;
- caminhos internos sensíveis;
- stack trace.

---

# 13. Deploy via EasyPanel

Antes do deploy:

```md
- [ ] GitHub conectado.
- [ ] Branch correta selecionada.
- [ ] Dockerfile/configuração validada.
- [ ] Variáveis de ambiente configuradas.
- [ ] Banco criado.
- [ ] Usuário do banco criado.
- [ ] phpMyAdmin protegido.
- [ ] Volumes persistentes configurados.
- [ ] Domínio configurado.
- [ ] SSL configurado.
- [ ] Backup realizado quando necessário.
- [ ] Migrations revisadas.
- [ ] Rollback definido.
```

Depois do deploy:

```md
- [ ] Aplicação abre pelo domínio.
- [ ] SSL funciona.
- [ ] Login funciona.
- [ ] API principal responde.
- [ ] Banco conecta.
- [ ] Upload funciona quando existir.
- [ ] Logs estão sendo gravados.
- [ ] Health check funciona.
- [ ] phpMyAdmin acessa o banco correto.
- [ ] Versão publicada foi registrada.
```

---

# 14. Backup e restauração

Não depender apenas do phpMyAdmin para backup.

A IA deve recomendar:

```txt
script de backup do banco
script de restore
backup de uploads
backup antes de deploy crítico
backup antes de migration
retenção mínima
local seguro
teste de restauração
```

Gerar, quando aplicável:

```txt
scripts/backup-db.sh
scripts/restore-db.sh
docs/producao/backup-recuperacao.md
```

Backup que nunca foi testado não deve ser considerado confiável.

---

# 15. Rollback

O plano de rollback deve explicar:

```txt
qual versão anterior voltar
qual branch/tag usar
como restaurar container
como restaurar banco se migration falhar
como restaurar uploads se necessário
quanto tempo estimado
quem autoriza
```

Se houve migration destrutiva, rollback precisa considerar banco.

---

# 16. Segurança mínima para produção

Antes de produção:

```md
- [ ] APP_ENV=production.
- [ ] APP_DEBUG=false.
- [ ] Senhas reais fora do Git.
- [ ] Banco com usuário próprio.
- [ ] phpMyAdmin protegido.
- [ ] SSL ativo.
- [ ] Cookies seguros.
- [ ] Uploads validados.
- [ ] Logs sem dados sensíveis.
- [ ] Debug visual desligado ou restrito.
- [ ] Health check sem vazamento.
- [ ] Backup inicial feito.
```

---

# 17. Estrutura recomendada de documentação

Criar ou atualizar:

```txt
docs/deploy/easypanel.md
docs/deploy/variaveis-ambiente.md
docs/deploy/dominio-ssl.md
docs/producao/backup-recuperacao.md
docs/producao/rollback.md
docs/producao/monitoramento.md
```

---

# 18. Modelo de entrega esperado

Ao aplicar esta skill, a IA deve entregar:

```md
# Plano EasyPanel + MySQL + phpMyAdmin

## 1. Resumo da infraestrutura

## 2. Serviços necessários

| Serviço | Função | Exposição | Volume | Observação |
|---|---|---|---|---|

## 3. Variáveis de ambiente

## 4. Banco de dados

## 5. phpMyAdmin

## 6. Volumes persistentes

## 7. Domínio e SSL

## 8. Health check

## 9. Backup e restore

## 11. Checklist antes do deploy

## 12. Checklist pós-deploy

## 13. Riscos e cuidados
```

---

# 19. Regras anti-erro

A IA nunca deve:

- versionar `.env` real;
- usar root do banco na aplicação;
- deixar phpMyAdmin sem proteção;
- salvar upload crítico sem volume;
- apagar volume de banco sem confirmação;
- rodar migration em produção sem backup;
- considerar deploy pronto sem SSL;
- considerar produção pronta sem health check;
- tratar phpMyAdmin como backup oficial;
- expor erro técnico em produção;
- deixar `APP_DEBUG=true` em produção.

---

# 20. Regra final

EasyPanel facilita deploy, mas não substitui disciplina de produção.

```txt
Git rastreia o código.
Docker define o ambiente.
EasyPanel gerencia publicação.
MySQL guarda os dados.
phpMyAdmin administra visualmente.
Backup protege contra perda.
Deploy controla mudança.
Monitoramento valida produção.
```

A aplicação só está pronta quando domínio, SSL, banco, volumes, logs, backup, rollback e health check estiverem validados.
