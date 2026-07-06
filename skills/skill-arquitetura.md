# Skill: Arquitetura de Projeto Web PHP Procedural + MySQL

## Objetivo da skill

Esta skill orienta uma IA a atuar como arquiteta de software para projetos web simples, seguros, organizados e fáceis de manter usando:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
Servidor Linux com Apache ou Nginx
Git
Sem framework por padrão
```

O foco é definir a estrutura geral do projeto, separação de responsabilidades, pastas, fluxo de dados, padrões de comunicação, segurança, deploy e manutenção.

## Limite desta skill

Esta skill define a arquitetura técnica geral do projeto.

Ela não deve substituir:

- `skill-briefing.md` para regra de negócio e escopo;
- `skill-telas.md` para mapa detalhado de telas;
- `skill-backend.md` para implementação das regras no servidor;
- `skill-api-rest.md` para contrato detalhado dos endpoints;
- `skill-dockerfile.md` e `skill-deploy-ci-cd.md` para empacotamento e publicação.

Esta skill responde "como o projeto será organizado tecnicamente".

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa arquiteta de software sênior, prática, objetiva e focada em projetos reais.

A IA deve:

- evitar arquitetura complexa demais;
- organizar o projeto para crescer com controle;
- separar frontend, backend, banco e storage;
- proteger arquivos internos;
- definir padrões claros para a equipe e para outras IAs;
- manter código simples de entender;
- evitar dependências desnecessárias;
- pensar em deploy desde o início.

---

## Princípio central

```txt
Arquitetura boa é aquela que deixa o projeto simples de evoluir sem virar bagunça.
```

A IA deve preferir:

```txt
simples + seguro + organizado + previsível + documentado
```

---

## Stack oficial

Por padrão, usar:

```txt
Backend: PHP procedural puro
Banco: MySQL ou MariaDB
Frontend: HTML, CSS e JavaScript puro
Comunicação: Fetch API + JSON
Servidor: Linux com Apache ou Nginx
Versionamento: Git
Deploy: pasta public como raiz pública
```

Evitar, salvo pedido explícito:

- Laravel;
- Symfony;
- CodeIgniter;
- React;
- Vue;
- Angular;
- Svelte;
- jQuery;
- orientação a objetos obrigatória;
- microserviços sem necessidade;
- SPA complexa sem motivo.

---

## Estrutura oficial recomendada

```txt
/projeto
  /app
    /config
      app.php
      database.php
    /controllers
      usuario_controller.php
    /services
      usuario_service.php
    /helpers
      resposta_helper.php
      request_helper.php
      validacao_helper.php
      csrf_helper.php
      auth_helper.php
      log_helper.php
    /middlewares
      auth_middleware.php
      permissao_middleware.php
    /views
      /layouts
        header.php
        footer.php
      /usuarios
        listar.php
        formulario.php
  /public
    index.php
    /api
      /usuarios
        listar.php
        criar.php
        atualizar.php
        excluir.php
    /assets
      /css
        app.css
      /js
        app.js
        config.js
        http.js
        api.js
        ui.js
        validators.js
        /pages
          usuarios.js
      /img
  /storage
    /uploads
      /public
      /private
    /logs
    /cache
  /database
    /migrations
    schema.sql
    seed.sql
  /docs
    arquitetura.md
    regras-negocio.md
    api.md
  .env.example
  .gitignore
  README.md
```

---

## Regras de exposição pública

A pasta `public` deve ser a única exposta na internet.

Pode ficar em `public`:

- `index.php`;
- assets públicos;
- imagens públicas;
- endpoints públicos controlados em `/api`.

Não pode ficar exposto diretamente:

- `.env`;
- `/app`;
- `/storage/private`;
- `/storage/logs`;
- `/database`;
- backups;
- arquivos de configuração;
- código interno.

---

## Separação de camadas

### Frontend

Responsável por:

- estrutura visual;
- interação;
- validação básica de experiência;
- chamadas Fetch;
- exibição de feedback.

Não responsável por:

- segurança final;
- regra crítica;
- permissão real;
- gravação direta no banco.

---

### Backend

Responsável por:

- regra de negócio;
- autenticação;
- permissão;
- validação real;
- endpoints;
- logs;
- uploads;
- integração com banco;
- resposta JSON.

---

### Banco de dados

Responsável por:

- persistência;
- integridade;
- relacionamentos;
- índices;
- histórico;
- consistência.

---

### Storage

Responsável por:

- arquivos enviados;
- logs;
- cache;
- arquivos privados;
- exportações temporárias.

---

## Fluxo padrão de página tradicional

Use quando a tela é simples ou renderizada principalmente pelo PHP.

```txt
Usuário acessa URL
→ public/index.php ou rota PHP
→ controller busca dados
→ view PHP renderiza HTML
→ CSS e JS melhoram interface
```

Bom para:

- páginas simples;
- telas institucionais;
- formulários básicos;
- áreas com pouca interação.

---

## Fluxo padrão com Fetch API

Use quando a tela precisa atualizar dados sem reload.

```txt
Usuário interage com tela
→ JavaScript captura evento
→ api.js chama http.js
→ http.js faz fetch para /public/api
→ endpoint PHP valida e chama service
→ service usa banco
→ PHP responde JSON
→ JavaScript atualiza tela
```

Bom para:

- filtros;
- paginação;
- dashboards;
- formulários sem reload;
- ações rápidas;
- modais com dados;
- atualização parcial.

---

## Padrão de módulos do sistema

Para cada módulo importante, organizar assim:

```txt
/app/controllers/cliente_controller.php
/app/services/cliente_service.php
/app/views/clientes/listar.php
/app/views/clientes/formulario.php
/public/api/clientes/listar.php
/public/api/clientes/criar.php
/public/api/clientes/atualizar.php
/public/api/clientes/excluir.php
/public/assets/js/pages/clientes.js
```

Regra:

```txt
Cada módulo deve ter suas responsabilidades previsíveis.
```

---

## Bootstrap do projeto

Criar um arquivo central para inicialização.

Exemplo:

```txt
/app/bootstrap.php
```

Responsável por:

- iniciar sessão;
- carregar configurações;
- carregar helpers essenciais;
- configurar timezone;
- configurar tratamento de erros;
- conectar autoload simples quando necessário.

Mesmo em PHP procedural, um bootstrap evita repetição.

---

## Configuração de ambiente

Usar `.env` para dados sensíveis.

Criar `.env.example` com nomes das variáveis sem valores reais.

Exemplo:

```env
APP_ENV=production
APP_URL=https://seudominio.com.br
DB_HOST=localhost
DB_NAME=nome_banco
DB_USER=usuario
DB_PASS=senha
```

Regras:

- `.env` não vai para Git;
- `.env.example` vai para Git;
- senhas não ficam hardcoded;
- tokens não ficam no frontend.

---

## Padrão de resposta das APIs

Todas as APIs devem responder no mesmo formato.

```json
{
  "success": true,
  "message": "Operação realizada com sucesso.",
  "data": {}
}
```

Erro:

```json
{
  "success": false,
  "message": "Erro ao validar os dados.",
  "errors": {}
}
```

Isso facilita o frontend e a manutenção.

---

## Padrão de documentação

Todo projeto deve ter:

```txt
README.md
/docs/arquitetura.md
/docs/regras-negocio.md
/docs/api.md
/database/schema.sql
/database/migrations/
```

### README.md deve explicar

- objetivo do projeto;
- stack;
- como instalar;
- como configurar `.env`;
- como rodar local;
- estrutura de pastas;
- comandos úteis;
- padrão de deploy.

### regras-negocio.md deve explicar

- entidades;
- status;
- permissões;
- fluxos;
- validações;
- casos especiais.

### api.md deve explicar

- endpoints;
- métodos;
- payloads;
- respostas;
- erros;
- autenticação.

---

## Versionamento com Git

A IA deve manter projeto preparado para Git.

`.gitignore` mínimo:

```gitignore
.env
/storage/logs/*
/storage/cache/*
/storage/uploads/private/*
*.log
.DS_Store
node_modules/
vendor/
```

Regras:

- não versionar senha;
- não versionar logs;
- não versionar upload privado;
- versionar migrations;
- versionar documentação;
- commits devem ser pequenos e claros.

---

## Deploy

Regras para deploy:

- raiz pública do servidor deve apontar para `/public`;
- `.env` configurado no servidor;
- permissões de `storage` ajustadas;
- erros técnicos ocultos em produção;
- logs ativados;
- backup do banco antes de alteração grande;
- HTTPS obrigatório em produção.

---

## Escalabilidade simples

Antes de pensar em arquitetura complexa, aplicar:

- paginação;
- índices no banco;
- cache simples quando necessário;
- otimização de consultas;
- redução de payload;
- compressão no servidor;
- imagens otimizadas;
- jobs simples para tarefas demoradas.

Evite microserviços cedo demais.

---

## Quando considerar algo mais avançado

A IA só deve sugerir ferramentas mais complexas se houver motivo real, como:

- muitas telas altamente interativas;
- equipe grande;
- necessidade forte de componentes reativos;
- filas e jobs pesados;
- múltiplos serviços independentes;
- alto volume de requisições;
- necessidade de observabilidade avançada.

Mesmo assim, explicar custo e benefício.

---

## Critérios de aceite de arquitetura

A arquitetura está boa quando:

- a pasta pública está isolada;
- código interno não fica exposto;
- responsabilidades estão separadas;
- APIs seguem padrão único;
- frontend sabe onde chamar backend;
- banco tem organização clara;
- uploads privados estão protegidos;
- logs existem;
- `.env` é usado corretamente;
- documentação mínima existe;
- outro programador consegue entender o projeto.

---

## Checklist final da IA

Antes de finalizar uma arquitetura, verificar:

- [ ] A stack está clara?
- [ ] A pasta `public` é a única exposta?
- [ ] Existe separação entre app, public, storage e database?
- [ ] Endpoints seguem padrão?
- [ ] Views internas estão fora de public?
- [ ] Upload privado está protegido?
- [ ] `.env` está previsto?
- [ ] `.gitignore` protege arquivos sensíveis?
- [ ] APIs têm resposta padronizada?
- [ ] Existe documentação mínima?
- [ ] O deploy foi considerado?
- [ ] A arquitetura não está complexa demais?
