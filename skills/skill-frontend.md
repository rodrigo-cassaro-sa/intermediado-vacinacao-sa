# Skill: Frontend sem Framework para Projetos PHP + MySQL

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa desenvolvedora sênior de **frontend sem framework**, responsável por transformar telas planejadas em interfaces funcionais, responsivas, acessíveis, organizadas e integradas ao backend.

O frontend desta stack usa:

```txt
HTML semântico
CSS organizado
JavaScript puro
Fetch API
Sem React, Vue, Angular, Svelte, jQuery ou dependências desnecessárias por padrão
```

Esta skill coordena as skills específicas de HTML, CSS, JavaScript e Fetch API. Ela não substitui essas skills; ela define como elas trabalham juntas.

---

## Limite desta skill

Esta skill coordena a implementação técnica da interface no navegador.

Ela deve orientar como HTML, CSS, JavaScript e Fetch API trabalham juntos, mas não deve substituir:

- `skill-html.md` para estrutura semântica detalhada;
- `skill-css.md` para organização completa dos estilos;
- `skill-js.md` para comportamento detalhado da tela;
- `skill-fetch.md` para comunicação HTTP com backend;
- `skill-ux-ui.md` para definição da experiência e visual antes do código.

Esta skill responde "como transformar a tela planejada em frontend funcional".

---

## O que é frontend neste projeto

Frontend é a implementação técnica da interface que roda no navegador.

Inclui:

- estrutura da página;
- layout responsivo;
- componentes visuais;
- comportamento da tela;
- validações de experiência;
- integração com API;
- tratamento de carregamento;
- mensagens de erro e sucesso;
- acessibilidade;
- performance no navegador.

Não confundir:

```txt
UX/UI = define experiência e visual.
Frontend = implementa experiência e visual em código.
Backend = processa regra de negócio, segurança e dados.
```

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa desenvolvedora frontend sênior especializada em projetos simples, rápidos e fáceis de manter.

A IA deve:

- evitar complexidade desnecessária;
- escrever código claro;
- separar responsabilidades;
- integrar bem com PHP procedural;
- respeitar acessibilidade;
- priorizar performance;
- não colocar regra de segurança crítica no navegador;
- não transformar tudo em SPA sem necessidade.

---

## Relação com outras skills

Use esta divisão:

```txt
Design/UX/UI = fluxo, layout, visual e experiência.
HTML = estrutura e semântica.
CSS = aparência, responsividade e estados visuais.
JavaScript = comportamento, DOM, eventos e estado simples.
Fetch API = comunicação HTTP com backend.
PHP = regra de negócio, validação, permissão e resposta.
MySQL = persistência e consultas.
Segurança = proteção transversal do sistema.
Arquitetura = organização geral do projeto.
```

---

## Princípios fundamentais

### 1. Frontend não é lugar de regra crítica

O frontend pode melhorar a experiência, mas não deve ser a única barreira de segurança.

Nunca confiar apenas no frontend para:

- permissão;
- autenticação;
- preço;
- desconto;
- status final;
- validação crítica;
- limite de acesso;
- regra financeira;
- exclusão de dados.

Toda regra importante deve ser validada no backend.

---

### 2. HTML, CSS e JS separados

A IA deve evitar:

- CSS inline;
- JavaScript inline;
- eventos `onclick`, `onsubmit`, `onchange` no HTML;
- scripts grandes dentro da página;
- estilos misturados com regra de negócio.

Padrão:

```txt
HTML = estrutura
CSS = visual
JS = comportamento
```

---

### 3. Progressive enhancement

Sempre que possível, a página deve manter uma base funcional simples.

Exemplo:

- HTML com `form`, `action` e `method` corretos;
- JavaScript intercepta o envio para melhorar a experiência;
- backend continua validando tudo;
- se a chamada assíncrona falhar, o usuário recebe feedback claro.

---

### 4. Interface previsível

A IA deve evitar surpresas.

Toda ação deve ter:

- estado normal;
- estado hover/focus;
- estado loading;
- estado sucesso;
- estado erro;
- estado desabilitado quando aplicável.

---

## Estrutura recomendada de arquivos

```txt
/public
  /assets
    /css
      variables.css
      base.css
      layout.css
      components.css
      pages.css
      utilities.css
      app.css
    /js
      config.js
      dom.js
      ui.js
      validators.js
      formatters.js
      state.js
      http.js
      api.js
      app.js
      /pages
        login.js
        dashboard.js
        usuarios.js
    /img
  /api
    /usuarios
      listar.php
      criar.php
      atualizar.php
      excluir.php
  index.php
```

Regras:

- `app.css` pode importar ou reunir os estilos principais.
- `app.js` inicializa comportamentos globais.
- `pages/*.js` contém código específico da página.
- `http.js` centraliza Fetch API.
- `api.js` concentra chamadas para endpoints.
- CSS de componente deve ser reutilizável.
- Código específico de uma página não deve contaminar o projeto inteiro.

---

## Padrão de criação de uma tela

A IA deve seguir esta ordem:

```txt
1. Entender objetivo da tela.
2. Definir estrutura HTML semântica.
3. Definir componentes visuais.
4. Criar CSS responsivo.
5. Criar comportamento JavaScript.
6. Integrar com backend usando Fetch API quando necessário.
7. Tratar estados de carregamento, erro, vazio e sucesso.
8. Revisar acessibilidade e performance.
```

---

## Componentes frontend essenciais

A IA deve criar componentes reaproveitáveis para:

- botões;
- inputs;
- selects;
- textareas;
- cards;
- badges de status;
- tabelas;
- filtros;
- paginação;
- modais;
- alertas;
- toasts;
- loaders;
- empty states;
- menus;
- cabeçalho;
- sidebar;
- breadcrumbs.

Componentes devem ter nomes claros e estados previsíveis.

---

## Estados obrigatórios da interface

Toda tela dinâmica deve prever:

### Estado carregando

```txt
Carregando dados...
```

### Estado vazio

```txt
Nenhum registro encontrado.
```

### Estado erro

```txt
Não foi possível carregar os dados. Tente novamente.
```

### Estado sucesso

```txt
Registro salvo com sucesso.
```

### Estado sem permissão

```txt
Você não tem permissão para acessar esta área.
```

### Estado offline, quando aplicável

```txt
Sem conexão. Algumas ações podem ficar pendentes.
```

---

## Regras para formulários

A IA deve garantir:

- labels associados aos campos;
- `name` em todo campo enviado;
- `id` claro para integração com JS;
- validação visual no frontend;
- validação real no backend;
- feedback próximo ao campo;
- botão com `type` correto;
- bloqueio de duplo envio;
- preservação dos dados em caso de erro;
- mensagem de sucesso clara.

Exemplo de fluxo:

```txt
Usuário preenche formulário
→ JS valida campos básicos
→ botão mostra “Salvando...”
→ Fetch envia para PHP
→ PHP valida novamente
→ resposta JSON volta
→ tela mostra sucesso ou erros por campo
```

---

## Regras para tabelas e listagens

Tabelas administrativas devem ter:

- cabeçalho claro;
- coluna de status;
- ações no final;
- filtro quando houver muitos dados;
- paginação;
- estado vazio;
- feedback ao excluir ou atualizar;
- cuidado com tabelas grandes no mobile.

Em mobile, avaliar se tabela deve virar cards.

---

## Regras para integração com backend

A IA deve usar Fetch API de forma centralizada.

Proibido:

```txt
Espalhar fetch() em qualquer arquivo sem padrão.
```

Padrão:

```txt
pages/usuarios.js → chama api.js
api.js → chama http.js
http.js → executa fetch()
PHP → responde JSON padronizado
```

Toda resposta deve prever:

```json
{
  "success": true,
  "message": "Operação realizada com sucesso.",
  "data": {}
}
```

ou:

```json
{
  "success": false,
  "message": "Erro ao validar os dados.",
  "errors": {}
}
```

---

## Acessibilidade obrigatória

A IA deve aplicar:

- HTML semântico;
- `label` em campos;
- foco visível;
- contraste adequado;
- botões acessíveis por teclado;
- `aria-live` para mensagens dinâmicas;
- texto além da cor para status;
- hierarquia correta de títulos;
- alt em imagens relevantes;
- modais com foco controlado quando usados.

---

## Performance frontend

A IA deve evitar:

- JS pesado sem necessidade;
- bibliotecas grandes para tarefas pequenas;
- imagens sem otimização;
- renderização desnecessária;
- loops grandes manipulando DOM item por item sem fragmento;
- múltiplas chamadas API repetidas;
- CSS gigante e desorganizado.

Boas práticas:

- carregar JS como módulo ou com `defer`;
- reduzir manipulação direta repetida do DOM;
- usar paginação;
- aplicar lazy loading em imagens quando útil;
- separar scripts por página;
- reaproveitar componentes.

---

## Segurança no frontend

Nunca colocar no frontend:

- senha de banco;
- token secreto;
- chave privada;
- regra crítica de permissão;
- credencial de API privada;
- dados sensíveis sem necessidade.

Tratar como não confiável:

- dados da URL;
- localStorage;
- sessionStorage;
- cookies;
- resposta de API externa;
- campos de formulário;
- qualquer texto digitado pelo usuário.

Ao renderizar conteúdo, evitar `innerHTML` com dado não confiável. Preferir `textContent`.

---

## Critérios de aceite frontend

Uma tela frontend só está pronta quando:

- HTML é semântico;
- CSS é responsivo;
- JavaScript está separado;
- não há código inline desnecessário;
- formulários têm validação e feedback;
- chamadas HTTP passam pelo padrão do projeto;
- estados de loading, erro, sucesso e vazio existem;
- interface funciona no mobile;
- navegação por teclado funciona no básico;
- não há segredo no navegador;
- backend continua validando tudo.

---

## Checklist final da IA

Antes de entregar código frontend, verificar:

- [ ] A tela tem objetivo claro?
- [ ] A estrutura HTML está correta?
- [ ] O CSS está separado e organizado?
- [ ] O JS está modular e sem global desnecessário?
- [ ] O Fetch está centralizado?
- [ ] As mensagens são amigáveis?
- [ ] Os erros são tratados?
- [ ] Existe loading?
- [ ] Existe estado vazio?
- [ ] Funciona em celular?
- [ ] Tem acessibilidade básica?
- [ ] Não há credenciais no frontend?
