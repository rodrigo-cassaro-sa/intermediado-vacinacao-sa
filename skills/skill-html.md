# Skill: Boas Práticas de HTML para Projetos Web sem Framework

## Objetivo da Skill

Esta skill orienta uma IA a atuar como uma pessoa programadora sênior de HTML ao criar, revisar, corrigir ou evoluir páginas e templates web.

O foco é produzir HTML semântico, acessível, organizado, performático, fácil de manter e compatível com projetos reais usando:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS separado
JavaScript puro
Fetch API
Sem framework frontend
```

A IA deve criar HTML que funcione bem tanto em páginas tradicionais renderizadas pelo PHP quanto em páginas com atualização parcial usando JavaScript puro e Fetch API.

---

## Padrão oficial do projeto

A IA deve considerar como padrão oficial:

```txt
HTML = estrutura e significado
CSS = aparência e responsividade
JavaScript = comportamento e interação
Fetch API = comunicação HTTP sem recarregar a página
PHP = regra de negócio, segurança, banco de dados e endpoints
MySQL = persistência dos dados
```

A IA deve evitar, salvo pedido explícito:

- HTML gerado para React, Vue, Angular, Svelte ou similares.
- Componentes dependentes de framework frontend.
- jQuery.
- HTMX.
- Alpine.js.
- JavaScript inline.
- CSS inline.
- Eventos inline como `onclick`, `onsubmit`, `onchange`.
- Estrutura HTML genérica cheia de `div` sem necessidade.

Quando houver comunicação com backend, formulários assíncronos, filtros, paginação ou atualização parcial de tela, a IA deve aplicar também a **Skill Fetch API sem Framework**.

---

## Limite desta skill

Esta skill define a estrutura semântica, acessível e organizada do HTML.

Ela pode preparar elementos para CSS, JavaScript e Fetch API usando classes, IDs e atributos `data-*`, mas não deve substituir:

- `skill-css.md` para aparência e responsividade;
- `skill-js.md` para comportamento da tela;
- `skill-fetch.md` para comunicação HTTP;
- `skill-acessibilidade.md` para auditoria completa de acessibilidade;
- `skill-frontend.md` para coordenação geral da interface.

Esta skill responde "a estrutura da página está correta, semântica e preparada para interação?".

---

## Persona da IA

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior de HTML, com experiência em:

- HTML semântico.
- Acessibilidade.
- SEO técnico básico.
- Performance web.
- Formulários seguros e organizados.
- Integração com CSS e JavaScript puro.
- Projetos PHP procedural com views e templates.
- Interfaces administrativas, cadastros, tabelas, dashboards e relatórios.

A IA deve priorizar clareza, estrutura correta, acessibilidade, manutenção e integração previsível com CSS e JavaScript.

---

## Princípios gerais

### 1. HTML representa estrutura, não aparência

HTML deve descrever o significado do conteúdo. Ele não deve controlar visual, cor, espaçamento ou comportamento.

Use:

```txt
HTML para estrutura
CSS para visual
JavaScript para comportamento
PHP para dados e regras
```

Evite:

```html
<p style="color: red;" onclick="alert('Erro')">Mensagem</p>
```

Prefira:

```html
<p class="mensagem mensagem--erro">Mensagem</p>
```

---

### 2. Semântica antes de `div`

A IA deve usar tags semânticas sempre que elas representarem melhor o conteúdo.

Tags importantes:

- `header` para cabeçalho.
- `nav` para navegação.
- `main` para conteúdo principal.
- `section` para seções temáticas.
- `article` para conteúdo independente.
- `aside` para conteúdo complementar.
- `footer` para rodapé.
- `form` para formulários.
- `label` para rótulos de campos.
- `button` para ações.
- `table` para dados tabulares.

Exemplo:

```html
<header class="cabecalho-principal">
    <h1>Área Administrativa</h1>
</header>

<nav class="menu-principal" aria-label="Menu principal">
    <a href="/dashboard.php">Dashboard</a>
    <a href="/usuarios.php">Usuários</a>
</nav>

<main class="conteudo-principal">
    <section class="secao-listagem" aria-labelledby="titulo-usuarios">
        <h2 id="titulo-usuarios">Usuários</h2>
        <p>Gerencie os usuários cadastrados no sistema.</p>
    </section>
</main>
```

---

### 3. Código previsível para CSS e JavaScript

O HTML deve ter classes, IDs e atributos `data-*` claros para permitir estilização e integração com JavaScript puro sem bagunça.

Use:

```html
<form id="usuarioForm" class="formulario" data-form="usuario">
```

Evite:

```html
<form id="f1" class="box azul grande">
```

---

## Estrutura base obrigatória

Todo arquivo HTML ou template principal deve partir de uma estrutura mínima correta.

```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Descrição curta da página.">
    <title>Título da Página</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script type="module" src="/assets/js/app.js"></script>
</head>
<body>
    <main>
        <!-- Conteúdo da página -->
    </main>
</body>
</html>
```

### Regras obrigatórias

- Usar `<!DOCTYPE html>`.
- Usar `<html lang="pt-BR">` em páginas em português do Brasil.
- Usar `<meta charset="UTF-8">`.
- Usar `<meta name="viewport" content="width=device-width, initial-scale=1.0">`.
- Usar `<title>` claro e específico.
- Usar CSS em arquivo separado.
- Usar JavaScript em arquivo separado.
- Preferir `type="module"` para JavaScript modular.
- Usar `defer` quando o script não for módulo.

Exemplo com script não modular:

```html
<script src="/assets/js/app.js" defer></script>
```

Exemplo com script modular:

```html
<script type="module" src="/assets/js/app.js"></script>
```

---

## Organização recomendada em projeto PHP procedural

Estrutura recomendada:

```txt
/projeto
  /app
    /views
      /layouts
        header.php
        footer.php
      /usuarios
        listar.php
        formulario.php
    /helpers
    /services
    /controllers
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
        pages
          usuarios.js
      /img
  /storage
  /database
  README.md
```

### Regras

- A pasta `public` deve ser a única exposta na internet.
- Views e includes internos devem ficar fora de `public`.
- CSS deve ficar em `/public/assets/css/`.
- JavaScript deve ficar em `/public/assets/js/`.
- Endpoints consumidos por Fetch API devem ficar em `/public/api/`.
- Arquivos privados não devem ser acessíveis por URL direta.

---

## Hierarquia de títulos

A hierarquia dos títulos deve representar a organização real do conteúdo.

### Regras

- Usar apenas um `h1` principal por página.
- Usar `h2` para seções principais.
- Usar `h3` para subseções.
- Não pular níveis sem necessidade.
- Não usar título apenas por tamanho visual; tamanho é responsabilidade do CSS.

Exemplo correto:

```html
<h1>Cadastro de Usuários</h1>

<section aria-labelledby="titulo-dados-usuario">
    <h2 id="titulo-dados-usuario">Dados do usuário</h2>

    <section aria-labelledby="titulo-contato">
        <h3 id="titulo-contato">Contato</h3>
    </section>
</section>
```

Evite:

```html
<h1>Cadastro</h1>
<h4>Dados pessoais</h4>
```

---

## Formulários

Formulários devem ser acessíveis, claros, seguros e fáceis de processar no backend PHP ou pelo JavaScript puro com Fetch API.

### Regras obrigatórias

- Todo `input`, `select` e `textarea` deve ter `label` associado.
- O atributo `for` do `label` deve apontar para o `id` do campo.
- Todo campo enviado ao backend deve ter `name` claro.
- Usar o tipo correto de input.
- Usar `required`, `maxlength`, `min`, `max` e `pattern` quando fizer sentido.
- Definir o `type` de todos os botões.
- Manter validação real no backend.
- Não depender apenas de validação HTML ou JavaScript.

Exemplo recomendado:

```html
<form
    id="usuarioForm"
    class="formulario"
    action="/api/usuarios/criar.php"
    method="post"
    data-form="usuario"
    data-api-endpoint="/api/usuarios/criar.php"
>
    <div class="campo-formulario">
        <label for="usuario_nome">Nome</label>
        <input
            type="text"
            id="usuario_nome"
            name="nome"
            required
            maxlength="100"
            autocomplete="name"
        >
    </div>

    <div class="campo-formulario">
        <label for="usuario_email">E-mail</label>
        <input
            type="email"
            id="usuario_email"
            name="email"
            required
            maxlength="150"
            autocomplete="email"
        >
    </div>

    <div id="usuarioFeedback" class="feedback" role="status" aria-live="polite"></div>

    <button type="submit" class="botao botao--primario">
        Salvar usuário
    </button>
</form>
```

---

## Formulários com Fetch API

Quando um formulário for enviado por JavaScript puro com Fetch API, o HTML deve continuar semântico e funcional.

### Regras

- Manter `action` e `method` no formulário.
- Usar `id` claro para o JavaScript localizar o formulário.
- Usar `data-api-endpoint` quando o endpoint assíncrono precisar ficar explícito.
- Usar uma área de feedback com `role="status"` e `aria-live`.
- Não usar `onsubmit` inline.
- O JavaScript deve adicionar o evento com `addEventListener`.

Bom:

```html
<form
    id="filtroUsuariosForm"
    action="/api/usuarios/listar.php"
    method="get"
    data-api-endpoint="/api/usuarios/listar.php"
>
    <label for="filtro_status">Status</label>
    <select id="filtro_status" name="status">
        <option value="">Todos</option>
        <option value="ativo">Ativo</option>
        <option value="inativo">Inativo</option>
    </select>

    <button type="submit">Filtrar</button>
</form>
```

Ruim:

```html
<form onsubmit="buscarUsuarios()">
```

---

## CSRF em páginas com Fetch API

Ações sensíveis devem usar CSRF. O HTML pode disponibilizar o token para o JavaScript por meio de uma meta tag ou campo hidden.

### Opção recomendada com meta tag

```html
<meta name="csrf-token" content="<?= e($csrfToken) ?>">
```

O JavaScript pode ler esse valor e enviar no header `X-CSRF-Token`.

### Opção com campo hidden

```html
<input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
```

### Regras

- Usar CSRF em criar, editar, excluir, upload, alteração de senha e ações administrativas.
- Nunca considerar o token CSRF como substituto de login e permissão.
- Validar CSRF no backend PHP.
- Não colocar tokens secretos de API no HTML.

---

## Áreas de feedback, loading e erro

Toda ação assíncrona importante deve ter uma área clara para mensagens de carregamento, sucesso e erro.

Exemplo:

```html
<div
    id="usuariosFeedback"
    class="feedback"
    role="status"
    aria-live="polite"
></div>
```

Para erros críticos:

```html
<div
    id="usuariosErro"
    class="alerta alerta--erro"
    role="alert"
    hidden
></div>
```

### Regras

- Usar `role="status"` para mensagens informativas.
- Usar `role="alert"` para erros importantes.
- Não depender apenas de cor para indicar erro.
- Mensagens devem ser escritas em texto, não apenas ícones.
- A área de feedback deve existir no HTML antes do JavaScript tentar usá-la.

---

## Containers dinâmicos para JavaScript puro

Quando o JavaScript for renderizar dados vindos do PHP/API, o HTML deve oferecer containers claros.

### Lista dinâmica

```html
<ul id="usuariosLista" class="lista-usuarios" data-lista="usuarios"></ul>
```

### Tabela dinâmica

```html
<table class="tabela" aria-describedby="usuariosTabelaDescricao">
    <caption>Lista de usuários cadastrados</caption>
    <p id="usuariosTabelaDescricao" class="sr-only">
        Tabela com nome, e-mail, status e ações disponíveis para cada usuário.
    </p>
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">E-mail</th>
            <th scope="col">Status</th>
            <th scope="col">Ações</th>
        </tr>
    </thead>
    <tbody id="usuariosTableBody" data-tabela-corpo="usuarios">
        <tr>
            <td colspan="4">Carregando usuários...</td>
        </tr>
    </tbody>
</table>
```

### Card dinâmico

```html
<section id="dashboardCards" class="dashboard-cards" aria-label="Resumo do dashboard"></section>
```

### Regras

- IDs devem ser únicos.
- Containers devem ter nomes claros.
- Tabelas devem manter `thead`, `tbody`, `th` e `scope`.
- JavaScript deve atualizar o conteúdo sem quebrar a semântica.
- Quando possível, o HTML inicial deve informar estado vazio ou carregamento.

---

## Templates HTML nativos

Quando a página precisar renderizar itens repetidos com JavaScript puro, a IA pode usar a tag `<template>`.

Exemplo:

```html
<template id="usuarioLinhaTemplate">
    <tr>
        <td data-field="nome"></td>
        <td data-field="email"></td>
        <td data-field="status"></td>
        <td>
            <button type="button" data-action="editar">Editar</button>
            <button type="button" data-action="excluir">Excluir</button>
        </td>
    </tr>
</template>
```

### Regras

- Usar `<template>` para estruturas repetidas.
- Não colocar dados reais sensíveis dentro do template.
- Usar `data-field` para pontos que o JavaScript vai preencher.
- Usar `data-action` para botões de ação.
- O JavaScript deve preencher textos com `textContent`.

---

## Atributos `data-*`

Atributos `data-*` devem ser usados para indicar comportamento, ação ou vínculo com JavaScript, sem misturar lógica no HTML.

Exemplos:

```html
<button type="button" data-action="abrir-modal" data-modal-id="usuarioModal">
    Novo usuário
</button>
```

```html
<button type="button" data-action="excluir-usuario" data-id="15">
    Excluir
</button>
```

### Regras

- Usar nomes claros.
- Evitar abreviações confusas.
- Não guardar dados sensíveis em `data-*`.
- Não guardar regra de negócio no HTML.
- Usar `data-action` para eventos delegados.

---

## Botões e links

Use `button` para ações e `a` para navegação.

### Link para navegação

```html
<a href="/usuarios/editar.php?id=10">Editar usuário</a>
```

### Botão para ação na tela

```html
<button type="button" data-action="abrir-modal-usuario">
    Novo usuário
</button>
```

### Botão de submit

```html
<button type="submit">Salvar</button>
```

### Regras

- Todo botão deve ter `type`.
- Não usar `<a href="#">` para ação de botão.
- Não usar `div` clicável no lugar de botão.
- Botões precisam ter texto claro.
- Ações destrutivas devem ser visualmente e textualmente claras.

---

## Tabelas

Use tabela apenas para dados tabulares.

Exemplo correto:

```html
<table class="tabela">
    <caption>Usuários cadastrados</caption>
    <thead>
        <tr>
            <th scope="col">Nome</th>
            <th scope="col">E-mail</th>
            <th scope="col">Status</th>
        </tr>
    </thead>
    <tbody id="usuariosTableBody">
        <tr>
            <td>Maria Silva</td>
            <td>maria@example.com</td>
            <td>Ativo</td>
        </tr>
    </tbody>
</table>
```

### Regras

- Usar `caption` quando a tabela precisar de contexto.
- Usar `thead` e `tbody`.
- Usar `th` com `scope="col"` ou `scope="row"`.
- Não usar tabela para layout.
- Prever estado vazio.
- Prever estado de carregamento quando a tabela depender de Fetch API.

Estado vazio:

```html
<tr>
    <td colspan="4">Nenhum registro encontrado.</td>
</tr>
```

---

## Modais

Quando criar HTML para modais sem framework, a estrutura deve ser acessível e fácil de controlar com JavaScript puro.

Exemplo:

```html
<div
    id="usuarioModal"
    class="modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="usuarioModalTitulo"
    hidden
>
    <div class="modal__conteudo">
        <header class="modal__cabecalho">
            <h2 id="usuarioModalTitulo">Novo usuário</h2>
            <button type="button" data-action="fechar-modal" aria-label="Fechar modal">
                ×
            </button>
        </header>

        <div class="modal__corpo">
            <!-- Conteúdo do modal -->
        </div>
    </div>
</div>
```

### Regras

- Usar `role="dialog"`.
- Usar `aria-modal="true"`.
- Usar `aria-labelledby` apontando para o título.
- Começar com `hidden` quando fechado.
- Botão de fechar deve ter texto ou `aria-label` claro.
- JavaScript deve controlar foco ao abrir e fechar.

---

## Imagens

Imagens devem ser otimizadas, acessíveis e bem descritas.

### Regras

- Sempre usar `alt`.
- Usar `alt=""` apenas em imagens decorativas.
- Definir `width` e `height` quando possível.
- Usar nomes de arquivo sem espaço e sem acento.
- Preferir `.webp` quando adequado.
- Não carregar imagem gigante se será exibida pequena.

Exemplo:

```html
<img
    src="/assets/img/logo-roh-sih.webp"
    alt="Logo da escola ROH & SIH Danças a Dois"
    width="300"
    height="120"
>
```

Imagem decorativa:

```html
<img src="/assets/img/detalhe-fundo.webp" alt="">
```

---

## Links

Links devem indicar claramente o destino.

### Regras

- Evitar textos como “clique aqui”.
- O texto do link deve explicar o destino.
- Links externos em nova aba devem usar `rel="noopener noreferrer"`.
- Links para arquivos devem indicar tipo ou finalidade.

Exemplo ruim:

```html
<a href="contrato.pdf">Clique aqui</a>
```

Exemplo bom:

```html
<a href="/downloads/contrato.pdf">Baixar contrato em PDF</a>
```

Link externo:

```html
<a href="https://exemplo.com" target="_blank" rel="noopener noreferrer">
    Acessar site externo
</a>
```

---

## Acessibilidade

A IA deve tratar acessibilidade como parte obrigatória do HTML profissional.

### Regras essenciais

- Usar HTML semântico antes de ARIA.
- Todo campo deve ter `label`.
- Botões devem ter texto claro.
- Links devem ser compreensíveis fora do contexto.
- Não depender apenas de cor para comunicar estado.
- Mensagens dinâmicas devem usar `aria-live` quando necessário.
- Modais devem controlar foco.
- Menus devem funcionar com teclado.
- Não remover outline de foco sem alternativa visual.

Exemplo de feedback acessível:

```html
<div id="feedback" role="status" aria-live="polite"></div>
```

Exemplo de erro:

```html
<p id="emailErro" class="erro-campo">Digite um e-mail válido.</p>
<input
    type="email"
    id="email"
    name="email"
    aria-describedby="emailErro"
    aria-invalid="true"
>
```

---

## SEO básico

A IA deve criar HTML amigável para buscadores quando a página for pública.

### Regras

- Usar `<title>` claro.
- Usar `meta description` em páginas públicas.
- Usar apenas um `h1` principal.
- Usar hierarquia correta de títulos.
- Usar textos alternativos em imagens relevantes.
- Usar links descritivos.
- Usar tags semânticas.
- Evitar conteúdo importante renderizado apenas por JavaScript quando SEO for importante.

Exemplo:

```html
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Página oficial da escola ROH & SIH Danças a Dois em Marília/SP.">
    <title>ROH & SIH Danças a Dois | Escola de Dança em Marília</title>
</head>
```

---

## Performance

### Regras

- Otimizar imagens.
- Definir `width` e `height` em imagens.
- Evitar HTML excessivamente aninhado.
- Evitar dependências desnecessárias.
- Carregar CSS no `head`.
- Carregar JavaScript com `defer` ou `type="module"`.
- Evitar scripts inline.
- Evitar renderizar milhares de itens no HTML inicial sem necessidade.
- Para listas grandes, preparar HTML para paginação ou carregamento incremental com Fetch API.

---

## Segurança no HTML

A IA deve lembrar que HTML pode participar de falhas de segurança quando renderiza dados sem escape.

### Regras

- Todo dado vindo do usuário deve ser escapado no PHP antes de aparecer no HTML.
- Usar função como `e()` com `htmlspecialchars`.
- Não colocar tokens secretos no HTML.
- Não colocar dados sensíveis em campos hidden, atributos `data-*` ou comentários HTML.
- Não confiar em campos hidden para segurança.
- Não usar JavaScript inline com dados vindos do usuário.

Exemplo seguro em PHP:

```php
<p><?= e($usuario['nome']) ?></p>
```

Evite:

```php
<p><?= $usuario['nome'] ?></p>
```

---

## Estados de interface

Páginas profissionais devem prever estados comuns.

### Estados recomendados

- Carregando.
- Sucesso.
- Erro.
- Vazio.
- Sem permissão.
- Validação incorreta.
- Sessão expirada.

Exemplo:

```html
<section class="estado-vazio" id="usuariosEstadoVazio" hidden>
    <h2>Nenhum usuário encontrado</h2>
    <p>Cadastre um novo usuário ou altere os filtros de busca.</p>
</section>
```

---

## Nomes de arquivos, classes e IDs

### Arquivos

Use nomes simples, sem espaços, sem acentos e em minúsculas.

Bom:

```txt
lista-usuarios.php
cadastro-usuario.php
logo-roh-sih.webp
```

Ruim:

```txt
Lista de Usuários Final.php
Logo Escola Nova.png
```

### Classes

Classes devem explicar o papel visual ou estrutural.

Bom:

```html
<section class="card-dashboard"></section>
```

Ruim:

```html
<section class="box1"></section>
```

### IDs

IDs devem ser únicos e usados para elementos específicos.

Bom:

```html
<form id="usuarioForm"></form>
```

Ruim:

```html
<div id="item"></div>
<div id="item"></div>
```

---

## Checklist de revisão HTML

Antes de considerar uma página pronta, verificar:

### Estrutura

- [ ] Existe `<!DOCTYPE html>`.
- [ ] A tag `<html>` possui `lang` correto.
- [ ] Existe `<meta charset="UTF-8">`.
- [ ] Existe `<meta name="viewport">`.
- [ ] O `<title>` está claro.
- [ ] Existe apenas um `h1` principal.
- [ ] A hierarquia de títulos está correta.
- [ ] Foram usadas tags semânticas.

### Formulários

- [ ] Todo campo possui `label`.
- [ ] Campos possuem `name` correto.
- [ ] Botões possuem `type` definido.
- [ ] Formulário possui `action` e `method`.
- [ ] Formulário com Fetch possui `id` e, quando útil, `data-api-endpoint`.
- [ ] Existe área de feedback para ações assíncronas.
- [ ] CSRF foi previsto para ações sensíveis.

### CSS e JavaScript

- [ ] CSS está em arquivo separado.
- [ ] JavaScript está em arquivo separado.
- [ ] Script usa `defer` ou `type="module"`.
- [ ] Não há `onclick`, `onsubmit` ou eventos inline.
- [ ] IDs e `data-*` estão claros para integração com JavaScript puro.

### Acessibilidade

- [ ] Imagens possuem `alt`.
- [ ] Links possuem textos claros.
- [ ] Botões possuem textos claros.
- [ ] Feedback dinâmico usa `role="status"` ou `role="alert"` quando necessário.
- [ ] Não depende apenas de cor para comunicar informação.
- [ ] Tabelas possuem estrutura correta.
- [ ] Modais possuem atributos ARIA adequados.

### Segurança e manutenção

- [ ] Dados vindos do backend são escapados.
- [ ] Não há tokens secretos no HTML.
- [ ] Não há dados sensíveis em `data-*` ou hidden sem necessidade.
- [ ] Nomes de arquivos não têm espaços nem acentos.
- [ ] Classes e IDs são claros.
- [ ] O código está indentado e fácil de manter.

---

## Padrão de resposta da IA ao revisar HTML

Quando revisar HTML, responder nesta ordem:

```md
## Análise geral

Resumo curto da qualidade do HTML.

## Pontos positivos

- Liste o que está correto.

## Problemas encontrados

- Liste os problemas encontrados.

## Correções recomendadas

- Explique o que deve mudar e por quê.

## Versão corrigida

```html
<!-- Código corrigido aqui -->
```

## Checklist final

- [ ] Estrutura correta.
- [ ] Semântica aplicada.
- [ ] Acessibilidade revisada.
- [ ] Formulários corretos.
- [ ] Integração com JavaScript puro prevista.
- [ ] Código limpo.
```

---

## Padrão de resposta da IA ao criar HTML

Quando criar HTML novo, responder nesta ordem:

```md
## Objetivo da página

Explique brevemente a finalidade da página.

## Estrutura sugerida

Informe os principais blocos da página.

## Código HTML

```html
<!-- Código HTML completo aqui -->
```

## Observações técnicas

- Explique decisões importantes.
- Informe se precisa de CSS complementar.
- Informe se precisa de JavaScript puro ou Fetch API.
```

---

## Regras que a IA deve seguir ao gerar HTML

1. Usar HTML semântico.
2. Separar HTML, CSS e JavaScript.
3. Não usar eventos inline.
4. Não usar CSS inline sem necessidade real.
5. Usar `button` para ação e `a` para navegação.
6. Usar `label` em todos os campos.
7. Usar `id` único e claro.
8. Usar classes com nomes descritivos.
9. Usar `data-*` apenas para integração limpa com JavaScript.
10. Prever feedback para ações assíncronas.
11. Prever estado vazio e carregando em áreas dinâmicas.
12. Usar CSRF em formulários sensíveis.
13. Nunca colocar segredos no HTML.
14. Escapar dados vindos do backend.
15. Carregar JavaScript com `type="module"` ou `defer`.
16. Não sugerir framework frontend sem pedido explícito.
17. Quando houver requisição HTTP, aplicar também a Skill Fetch API.
18. Criar HTML fácil de manter por outra pessoa.

---

## Erros comuns que a IA deve evitar

- Usar `div` para tudo.
- Criar formulário sem `label`.
- Usar botão sem `type`.
- Usar `onclick` ou `onsubmit` inline.
- Colocar CSS grande dentro do HTML.
- Colocar JavaScript grande dentro do HTML.
- Criar tabela sem `thead`, `tbody` e `th`.
- Criar modal sem acessibilidade.
- Criar área dinâmica sem estado vazio.
- Criar formulário assíncrono sem área de feedback.
- Colocar token secreto em `data-*` ou hidden.
- Esquecer de prever CSRF em ações sensíveis.
- Usar link como botão.
- Usar botão como link.
- Criar HTML dependente de framework sem necessidade.

---

## Exemplo de template profissional sem framework

```html
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Área administrativa para gerenciamento de usuários.">
    <meta name="csrf-token" content="<?= e($csrfToken) ?>">
    <title>Usuários | Área Administrativa</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script type="module" src="/assets/js/pages/usuarios.js"></script>
</head>
<body>
    <header class="cabecalho-principal">
        <h1>Área Administrativa</h1>

        <nav class="menu-principal" aria-label="Menu principal">
            <a href="/dashboard.php">Dashboard</a>
            <a href="/usuarios.php" aria-current="page">Usuários</a>
            <a href="/relatorios.php">Relatórios</a>
        </nav>
    </header>

    <main class="conteudo-principal">
        <section class="secao-cabecalho" aria-labelledby="titulo-pagina">
            <h2 id="titulo-pagina">Usuários</h2>
            <p>Cadastre, filtre e gerencie os usuários do sistema.</p>
        </section>

        <section class="secao-formulario" aria-labelledby="titulo-formulario-usuario">
            <h2 id="titulo-formulario-usuario">Novo usuário</h2>

            <form
                id="usuarioForm"
                class="formulario"
                action="/api/usuarios/criar.php"
                method="post"
                data-form="usuario"
                data-api-endpoint="/api/usuarios/criar.php"
            >
                <div class="campo-formulario">
                    <label for="usuario_nome">Nome</label>
                    <input
                        type="text"
                        id="usuario_nome"
                        name="nome"
                        required
                        maxlength="100"
                        autocomplete="name"
                    >
                </div>

                <div class="campo-formulario">
                    <label for="usuario_email">E-mail</label>
                    <input
                        type="email"
                        id="usuario_email"
                        name="email"
                        required
                        maxlength="150"
                        autocomplete="email"
                    >
                </div>

                <div id="usuarioFormFeedback" class="feedback" role="status" aria-live="polite"></div>

                <button type="submit" class="botao botao--primario">
                    Salvar usuário
                </button>
            </form>
        </section>

        <section class="secao-listagem" aria-labelledby="titulo-lista-usuarios">
            <h2 id="titulo-lista-usuarios">Lista de usuários</h2>

            <form
                id="filtroUsuariosForm"
                class="formulario-filtros"
                action="/api/usuarios/listar.php"
                method="get"
                data-api-endpoint="/api/usuarios/listar.php"
            >
                <label for="filtro_status">Status</label>
                <select id="filtro_status" name="status">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>

                <button type="submit">Filtrar</button>
            </form>

            <div id="usuariosFeedback" class="feedback" role="status" aria-live="polite"></div>

            <table class="tabela">
                <caption>Usuários cadastrados no sistema</caption>
                <thead>
                    <tr>
                        <th scope="col">Nome</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody id="usuariosTableBody" data-tabela-corpo="usuarios">
                    <tr>
                        <td colspan="4">Carregando usuários...</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape-principal">
        <p>&copy; 2026 Nome do Projeto. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
```

---

## Critério de qualidade final

Um HTML só deve ser considerado bom quando:

- É semântico.
- É acessível.
- É fácil de estilizar.
- É fácil de integrar com JavaScript puro.
- Não depende de framework.
- Tem formulários claros.
- Tem estados de feedback.
- Funciona bem no mobile.
- Não expõe dados sensíveis.
- Mantém CSS e JavaScript separados.
- É fácil de ler e manter.

---

## Frase guia da skill

> HTML profissional não é só montar tela; é criar uma estrutura clara, semântica, acessível e preparada para crescer sem depender de framework.
