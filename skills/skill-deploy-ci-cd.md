# Skill: Deploy, Ambientes e CI/CD para Projetos Web

## Objetivo da skill

Esta skill orienta uma IA a planejar, revisar e executar **publicação de sistemas, separação de ambientes, checklist de deploy, rollback, variáveis de ambiente e fluxo CI/CD simples** para projetos PHP procedural, MySQL/MariaDB, HTML, CSS e JavaScript puro.

O foco é reduzir risco ao colocar sistemas em homologação e produção, mantendo controle, rastreabilidade e possibilidade de voltar atrás.

---

## Limite desta skill

Esta skill define publicação, ambientes, checklist de deploy, CI/CD simples, rollback, pós-deploy, variáveis de ambiente, aprovação e comunicação de versão.

Ela coordena Git, Docker, backup, migrations, QA e monitoramento no momento da publicação, mas não deve substituir:

- `skill-git.md` para versionamento, branches, commits e tags;
- `skill-dockerfile.md` para Dockerfile, Compose e containers;
- `skill-backup-recuperacao.md` para política completa de backup e restauração;
- `skill-migracoes-banco.md` para scripts versionados de banco;
- `skill-qa.md` para plano completo de testes;
- `skill-monitoramento-observabilidade.md` para saúde contínua em produção.

Esta skill responde "como publicar com segurança, validar após subir e voltar atrás se algo der errado?".

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

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior com experiência em deploy, DevOps prático, Git, Docker, servidor Linux, Apache/Nginx, banco de dados e operação de SaaS.

A IA deve pensar em:

- ambiente local;
- desenvolvimento;
- homologação;
- produção;
- variáveis de ambiente;
- branch correta;
- backup antes de mudança crítica;
- migração de banco;
- testes pós-deploy;
- rollback.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-dockerfile.md
skill-git.md
skill-backup-recuperacao.md
skill-migracoes-banco.md
skill-qa.md
skill-seguranca.md
```

---

## Princípio central

```txt
Deploy bom é aquele que publica com controle, permite testar, reduz risco e tem caminho claro para voltar se algo der errado.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Ambientes oficiais

Todo projeto deve separar ambientes.

```txt
local          = máquina do desenvolvedor
desenvolvimento = testes técnicos e novas funções
homologação    = validação antes de produção
produção       = uso real pelos usuários
```

Cada ambiente deve ter:

- banco próprio;
- arquivo `.env` próprio;
- domínio/subdomínio próprio;
- logs próprios;
- credenciais próprias;
- configurações próprias;
- política de backup apropriada.

Nunca usar banco de produção para desenvolvimento.

---

# 2. Mapeamento de branches

Fluxo recomendado:

```txt
desenvolvimento -> ambiente dev
homologacao     -> ambiente hml/staging
producao        -> ambiente prod
```

Regras:

- desenvolvimento recebe alterações em andamento;
- homologação recebe versão candidata;
- produção recebe somente versão aprovada;
- merge para produção deve ser intencional e revisado;
- hotfix deve ser documentado e depois sincronizado com as branches anteriores.

---

# 3. Variáveis de ambiente

Credenciais e configurações não devem ficar fixas no código.

Exemplos de `.env`:

```txt
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br
DB_HOST=mysql
DB_NAME=app_prod
DB_USER=app_user
DB_PASS=senha_forte
MAIL_HOST=smtp.exemplo.com
```

Regras:

- `.env` real não vai para Git;
- `.env.example` deve ir para Git;
- produção usa `APP_DEBUG=false`;
- credenciais de dev, hml e prod são diferentes;
- tokens devem ser rotacionáveis.

---

# 4. Checklist antes do deploy

Antes de publicar, a IA deve verificar:

- branch correta;
- status do Git limpo;
- testes principais executados;
- migrações revisadas;
- backup feito quando houver alteração de banco;
- `.env` correto;
- dependências instaladas;
- permissões de pastas corretas;
- debug desligado em produção;
- logs configurados;
- plano de rollback definido.

Deploy sem checklist aumenta risco de quebrar produção.

---

# 5. Ordem segura de deploy

Fluxo recomendado:

1. Fazer backup quando necessário.
2. Publicar código novo.
3. Rodar migrações compatíveis.
4. Limpar cache se existir.
5. Verificar permissões de arquivos.
6. Testar health check.
7. Testar login.
8. Testar fluxo crítico.
9. Verificar logs.
10. Comunicar conclusão.

Quando houver mudança grande de banco, planejar deploy em etapas para evitar indisponibilidade.

---

# 6. Health check

Todo sistema deve ter uma verificação simples de saúde.

Exemplo de itens:

- aplicação responde;
- banco conecta;
- storage gravável;
- versão atual;
- ambiente atual;
- horário do servidor.

A rota de health check não deve expor dados sensíveis.

Exemplo:

```json
{
  "success": true,
  "status": "ok",
  "versao": "1.4.0",
  "ambiente": "production"
}
```

---

# 7. Rollback

Todo deploy deve ter plano de rollback.

Definir:

- como voltar código anterior;
- como lidar com migração de banco;
- quem decide rollback;
- quanto tempo esperar antes de voltar;
- como comunicar usuários;
- como preservar dados criados após deploy.

Rollback de banco é mais delicado que rollback de código. Migrações devem ser planejadas para permitir compatibilidade quando possível.

---

# 8. CI/CD simples

Para projetos menores, CI/CD pode começar simples.

Etapas úteis:

- validar sintaxe PHP;
- verificar arquivos obrigatórios;
- rodar testes automatizados quando existirem;
- bloquear deploy se falhar;
- publicar em homologação;
- exigir aprovação manual para produção;
- registrar versão publicada.

Não criar pipeline complexo demais se o projeto ainda não precisa. Começar com checklist automatizado já ajuda muito.

---

# 9. Pós-deploy

Após publicar, testar imediatamente:

- login;
- tela inicial;
- fluxo principal;
- criação de registro;
- listagem;
- edição;
- permissões;
- envio de notificação se existir;
- integração externa se existir;
- logs de erro.

Registrar resultado do pós-deploy. Se falhar fluxo crítico, avaliar rollback.

---

# 10. Comunicação de deploy

Deploy profissional deve ser comunicado.

Registrar:

- versão;
- data/hora;
- ambiente;
- responsável;
- principais mudanças;
- migrações executadas;
- testes feitos;
- problemas encontrados;
- decisão de manter ou reverter.

Esse histórico ajuda manutenção e auditoria.

---

# Checklist obrigatório antes de concluir

- [ ] Versão publicada está identificável por tag, número ou commit.
- [ ] Checklist pré-produção foi validado antes de seguir.
- [ ] Observabilidade mínima está ativa no ambiente alvo.
- [ ] Campos internos de datas e logs seguem o padrão em português.

- [ ] Ambientes estão separados.
- [ ] Branch correta foi usada.
- [ ] `.env` real não está no Git.
- [ ] Backup foi feito quando necessário.
- [ ] Migrações foram revisadas.
- [ ] Debug está desligado em produção.
- [ ] Health check foi testado.
- [ ] Fluxos críticos foram testados após deploy.
- [ ] Plano de rollback existe.

---

# Modelo de entrega esperado

Ao planejar deploy, entregue:

1. Ambiente alvo.
2. Branch usada.
3. Checklist pré-deploy.
4. Passos de publicação.
5. Migrações envolvidas.
6. Testes pós-deploy.
7. Plano de rollback.
8. Registro de versão.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
