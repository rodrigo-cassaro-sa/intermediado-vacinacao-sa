# Skill: Boas Práticas de CSS para Projetos Web

## Objetivo da skill

Esta skill orienta uma IA a agir como um **programador sênior de CSS**, criando estilos organizados, reutilizáveis, responsivos, acessíveis e fáceis de manter em projetos web.

A IA deve priorizar:

- Clareza no código.
- Manutenção simples.
- Responsividade.
- Acessibilidade.
- Performance.
- Reutilização de componentes.
- Baixa complexidade visual e técnica.

---

## Limite desta skill

Esta skill implementa a aparência da interface com CSS organizado, reutilizável, responsivo e acessível.

Ela pode aplicar regras de responsividade, acessibilidade e identidade visual, mas não deve substituir:

- `skill-ux-ui.md` para definição da experiência e direção visual;
- `skill-responsividade.md` para estratégia completa por dispositivo;
- `skill-acessibilidade.md` para validação completa de inclusão;
- `skill-html.md` para estrutura semântica;
- `skill-js.md` para comportamento.

Esta skill responde "os estilos estão organizados, consistentes e fáceis de manter?".

---

# 1. Mentalidade de CSS profissional

## 1.1 CSS deve ser previsível

O CSS de um projeto deve ser fácil de entender mesmo depois de meses. Evite estilos soltos, seletores confusos e regras que dependem demais da ordem do arquivo.

**Boa prática:**

```css
.card {
  padding: 1rem;
  border-radius: 0.75rem;
  background: var(--color-surface);
}
```

**Evite:**

```css
div div .box span:first-child {
  color: red;
}
```

Seletores muito específicos são difíceis de sobrescrever e aumentam a chance de conflito.

---

## 1.2 CSS deve seguir um padrão de projeto

Antes de escrever estilos, defina uma organização clara para nomes, arquivos, variáveis e componentes.

Um projeto pequeno pode ter poucos arquivos. Um projeto grande deve separar base, layout, componentes e páginas.

Exemplo simples:

```txt
/css
  base.css
  variables.css
  layout.css
  components.css
  pages.css
  utilities.css
```

---

# 2. Organização dos arquivos CSS

## 2.1 Separe responsabilidades

Cada arquivo deve ter uma função clara.

| Arquivo | Responsabilidade |
|---|---|
| `variables.css` | Cores, fontes, espaçamentos, sombras e tokens globais |
| `base.css` | Reset, estilos globais, body, links, imagens e tipografia base |
| `layout.css` | Containers, grids, seções e estrutura geral |
| `components.css` | Botões, cards, modais, inputs, menus e componentes reutilizáveis |
| `pages.css` | Ajustes específicos de páginas |
| `utilities.css` | Classes auxiliares pequenas e reutilizáveis |

---

## 2.2 Evite um único arquivo gigante

Um arquivo CSS enorme dificulta manutenção, revisão e reaproveitamento.

Para projetos simples, tudo bem começar com um arquivo principal. Porém, conforme o projeto cresce, separe o CSS por responsabilidade.

---

# 3. Variáveis CSS

## 3.1 Use variáveis para valores repetidos

Cores, espaçamentos, fontes, sombras, bordas e tamanhos devem ficar em variáveis globais.

```css
:root {
  --color-primary: #0f4cff;
  --color-danger: #d92d20;
  --color-text: #1f2937;
  --color-muted: #6b7280;
  --color-background: #f9fafb;
  --color-surface: #ffffff;

  --font-base: Arial, sans-serif;

  --space-xs: 0.25rem;
  --space-sm: 0.5rem;
  --space-md: 1rem;
  --space-lg: 1.5rem;
  --space-xl: 2rem;

  --radius-sm: 0.25rem;
  --radius-md: 0.5rem;
  --radius-lg: 1rem;

  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.08);
  --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.12);
}
```

---

## 3.2 Não espalhe cores fixas pelo projeto

**Evite:**

```css
.button {
  background: #0f4cff;
}

.alert {
  border-color: #0f4cff;
}
```

**Prefira:**

```css
.button {
  background: var(--color-primary);
}

.alert {
  border-color: var(--color-primary);
}
```

Isso facilita alterar a identidade visual sem reescrever o projeto inteiro.

---

# 4. Reset e estilos base

## 4.1 Use um reset simples

Um reset reduz diferenças entre navegadores e cria uma base consistente.

```css
* {
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

body {
  margin: 0;
  font-family: var(--font-base);
  color: var(--color-text);
  background: var(--color-background);
}

img,
picture,
video,
canvas,
svg {
  display: block;
  max-width: 100%;
}

button,
input,
textarea,
select {
  font: inherit;
}

a {
  color: inherit;
  text-decoration: none;
}
```

---

## 4.2 Defina uma tipografia base

O projeto deve ter padrão de títulos, textos, links e espaçamentos.

```css
h1,
h2,
h3,
p {
  margin-top: 0;
}

p {
  line-height: 1.6;
}
```

Evite definir estilos de texto diferentes em cada página sem necessidade.

---

# 5. Nomeação de classes

## 5.1 Use nomes claros e semânticos

O nome da classe deve explicar a função do elemento, não apenas sua aparência.

**Bom:**

```css
.card-product {}
.button-primary {}
.form-group {}
.sidebar-menu {}
```

**Ruim:**

```css
.azul {}
.grande {}
.caixa1 {}
.textoBonito {}
```

Nomes baseados apenas em aparência dificultam manutenção quando o design muda.

---

## 5.2 Use um padrão de nomeação

Um padrão simples inspirado em BEM ajuda a evitar conflitos.

```css
.card {}
.card__title {}
.card__description {}
.card__footer {}
.card--featured {}
```

Exemplo:

```html
<article class="card card--featured">
  <h2 class="card__title">Título</h2>
  <p class="card__description">Descrição do conteúdo.</p>
</article>
```

---

# 6. Controle de especificidade

## 6.1 Evite seletores muito profundos

**Evite:**

```css
.page .content .section .card .header h2 span {
  color: var(--color-primary);
}
```

**Prefira:**

```css
.card__title-highlight {
  color: var(--color-primary);
}
```

Quanto mais profundo o seletor, mais difícil será alterar ou reutilizar o componente.

---

## 6.2 Evite `!important`

O `!important` deve ser exceção, não regra.

**Evite:**

```css
.title {
  color: red !important;
}
```

Antes de usar `!important`, revise:

- A ordem dos arquivos.
- A especificidade do seletor.
- A estrutura das classes.
- Se existe conflito entre componentes.

---

# 7. Layout moderno

## 7.1 Use Flexbox para alinhamentos simples

Flexbox é ideal para alinhar itens em linha ou coluna.

```css
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-md);
}
```

Use Flexbox para:

- Menus.
- Botões lado a lado.
- Cards em linha simples.
- Alinhamento vertical e horizontal.

---

## 7.2 Use Grid para estruturas maiores

CSS Grid é ideal para layouts de página e grades mais complexas.

```css
.dashboard {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: var(--space-lg);
}
```

Use Grid para:

- Dashboards.
- Galerias.
- Layouts com colunas.
- Áreas principais de página.

---

## 7.3 Evite usar `float` para layout

`float` deve ser usado apenas em casos específicos, como texto contornando imagem. Para layout moderno, prefira Flexbox e Grid.

---

# 8. Responsividade

## 8.1 Comece pelo mobile first

Escreva primeiro o CSS para telas pequenas. Depois adicione melhorias para telas maiores.

```css
.cards {
  display: grid;
  gap: var(--space-md);
}

@media (min-width: 768px) {
  .cards {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .cards {
    grid-template-columns: repeat(3, 1fr);
  }
}
```

---

## 8.2 Use unidades flexíveis

Prefira `rem`, `%`, `vw`, `vh`, `fr`, `minmax()`, `clamp()` e `auto` quando fizer sentido.

```css
.title {
  font-size: clamp(1.5rem, 4vw, 3rem);
}
```

Evite depender demais de `px` em fontes, larguras e espaçamentos globais.

---

## 8.3 Evite largura fixa sem necessidade

**Evite:**

```css
.container {
  width: 1200px;
}
```

**Prefira:**

```css
.container {
  width: min(100% - 2rem, 1200px);
  margin-inline: auto;
}
```

Assim o layout funciona bem em telas pequenas e grandes.

---

# 9. Componentização

## 9.1 Crie componentes reutilizáveis

Componentes devem funcionar em várias páginas sem depender de contexto específico.

Exemplo:

```css
.button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  min-height: 2.75rem;
  padding: 0.75rem 1rem;
  border: 0;
  border-radius: var(--radius-md);
  font-weight: 600;
  cursor: pointer;
}

.button--primary {
  color: #ffffff;
  background: var(--color-primary);
}

.button--danger {
  color: #ffffff;
  background: var(--color-danger);
}
```

---

## 9.2 Evite estilos presos a uma página

**Evite:**

```css
.home .button {
  background: blue;
}
```

**Prefira:**

```css
.button--primary {
  background: var(--color-primary);
}
```

Componentes genéricos devem ser independentes da página.

---

# 10. Estados visuais

## 10.1 Sempre estilize estados importantes

Elementos interativos devem ter estados claros.

```css
.button:hover {
  filter: brightness(0.95);
}

.button:active {
  transform: translateY(1px);
}

.button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.input:focus-visible,
.button:focus-visible {
  outline: 3px solid rgba(15, 76, 255, 0.35);
  outline-offset: 2px;
}
```

Estados importantes:

- `hover`
- `active`
- `focus-visible`
- `disabled`
- `checked`
- `invalid`
- `loading`
- `error`
- `success`

---

# 11. Acessibilidade

## 11.1 Nunca remova foco sem substituir

**Evite:**

```css
button:focus {
  outline: none;
}
```

**Prefira:**

```css
button:focus-visible {
  outline: 3px solid var(--color-primary);
  outline-offset: 2px;
}
```

Usuários que navegam pelo teclado precisam enxergar onde estão.

---

## 11.2 Garanta contraste suficiente

Texto precisa ter contraste adequado com o fundo.

**Evite:**

```css
.card__text {
  color: #cccccc;
  background: #ffffff;
}
```

Use cores legíveis, principalmente em:

- Botões.
- Links.
- Alertas.
- Mensagens de erro.
- Textos pequenos.
- Menus.

---

## 11.3 Respeite preferência de movimento reduzido

Animações devem respeitar usuários sensíveis a movimento.

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    scroll-behavior: auto !important;
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

# 12. Performance

## 12.1 Evite CSS desnecessário

CSS não usado aumenta o carregamento e dificulta manutenção.

Revise periodicamente:

- Classes antigas.
- Componentes removidos.
- Estilos duplicados.
- Media queries sem uso.
- Animações pesadas.

---

## 12.2 Evite animações de propriedades caras

Prefira animar `opacity` e `transform`.

**Bom:**

```css
.modal {
  opacity: 0;
  transform: translateY(1rem);
  transition: opacity 200ms ease, transform 200ms ease;
}
```

**Evite animar com frequência:**

```css
width
height
top
left
margin
padding
box-shadow muito pesado
```

---

## 12.3 Cuidado com sombras e filtros pesados

Sombras grandes, blur e filtros podem prejudicar o desempenho em telas mais fracas.

Use com moderação:

```css
.card {
  box-shadow: var(--shadow-sm);
}
```

---

# 13. Boas práticas para formulários

## 13.1 Padronize inputs, labels e mensagens

```css
.form-group {
  display: grid;
  gap: var(--space-sm);
}

.form-label {
  font-weight: 600;
}

.form-control {
  width: 100%;
  min-height: 2.75rem;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: var(--radius-md);
  background: #ffffff;
}

.form-control:focus-visible {
  outline: 3px solid rgba(15, 76, 255, 0.25);
  border-color: var(--color-primary);
}

.form-error {
  color: var(--color-danger);
  font-size: 0.875rem;
}
```

---

## 13.2 Diferencie erro, sucesso e aviso

```css
.alert {
  padding: var(--space-md);
  border-radius: var(--radius-md);
  border: 1px solid transparent;
}

.alert--success {
  color: #065f46;
  background: #ecfdf5;
  border-color: #a7f3d0;
}

.alert--error {
  color: #991b1b;
  background: #fef2f2;
  border-color: #fecaca;
}

.alert--warning {
  color: #92400e;
  background: #fffbeb;
  border-color: #fde68a;
}
```

---

# 14. Utilitários CSS

## 14.1 Use utilitários pequenos com cuidado

Classes utilitárias ajudam em ajustes simples, mas não devem substituir componentes bem feitos.

```css
.text-center {
  text-align: center;
}

.mt-md {
  margin-top: var(--space-md);
}

.hidden {
  display: none !important;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

---

## 14.2 Não crie utilitário para tudo

Evite transformar o CSS em centenas de classes pequenas sem padrão.

Para projetos com CSS puro, o ideal é equilibrar:

- Componentes reutilizáveis.
- Variáveis globais.
- Poucas classes utilitárias realmente úteis.

---

# 15. Temas e identidade visual

## 15.1 Centralize a identidade visual

A identidade visual deve estar concentrada em variáveis.

```css
:root {
  --brand-blue: #004aad;
  --brand-red: #e30613;
  --brand-black: #111111;
  --brand-white: #ffffff;
}
```

Assim é possível criar temas e alterar o visual com segurança.

---

## 15.2 Crie suporte a tema escuro quando necessário

```css
[data-theme="dark"] {
  --color-background: #111827;
  --color-surface: #1f2937;
  --color-text: #f9fafb;
  --color-muted: #d1d5db;
}
```

Evite duplicar o CSS inteiro para tema escuro. Altere apenas variáveis.

---

# 16. Media queries

## 16.1 Defina breakpoints padrão

Use poucos breakpoints e mantenha consistência.

Sugestão:

```css
/* Tablet */
@media (min-width: 768px) {}

/* Desktop */
@media (min-width: 1024px) {}

/* Desktop grande */
@media (min-width: 1280px) {}
```

---

## 16.2 Não crie media query para cada elemento

Agrupe regras responsivas por componente ou seção quando fizer sentido.

CSS responsivo deve ser organizado, não espalhado aleatoriamente.

---

# 17. Compatibilidade

## 17.1 Use recursos modernos com consciência

Recursos modernos como `grid`, `clamp`, `aspect-ratio`, `:is()`, `:where()` e variáveis CSS são úteis, mas devem ser usados com clareza.

Exemplo:

```css
.avatar {
  aspect-ratio: 1;
  width: 4rem;
  border-radius: 50%;
  object-fit: cover;
}
```

---

## 17.2 Evite hacks antigos

Evite gambiarras de CSS para navegadores antigos, a não ser que o projeto realmente exija suporte legado.

Prefira soluções modernas, simples e documentadas.

---

# 18. Comentários no CSS

## 18.1 Comente decisões importantes

Comentários devem explicar decisões, não repetir o óbvio.

**Bom:**

```css
/* Mantém o menu acima do overlay do modal. */
.header {
  z-index: 20;
}
```

**Ruim:**

```css
/* cor azul */
.button {
  color: blue;
}
```

---

# 19. Padrão recomendado para componentes

Ao criar um componente CSS, siga esta ordem:

1. Estrutura e display.
2. Box model: width, height, margin, padding.
3. Tipografia.
4. Cores e fundo.
5. Bordas, sombras e radius.
6. Estados: hover, active, focus, disabled.
7. Variações.
8. Responsividade.

Exemplo:

```css
.card {
  display: grid;
  gap: var(--space-md);
  padding: var(--space-lg);
  color: var(--color-text);
  background: var(--color-surface);
  border: 1px solid #e5e7eb;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}

.card__title {
  margin: 0;
  font-size: 1.25rem;
  line-height: 1.2;
}

.card__description {
  margin: 0;
  color: var(--color-muted);
}

.card--highlight {
  border-color: var(--color-primary);
}
```

---

# 20. Boas práticas para projetos com HTML, CSS, JavaScript e PHP

## 20.1 CSS não deve depender de lógica do backend

O PHP deve gerar classes claras no HTML, mas o CSS não deve depender de estruturas imprevisíveis.

**Bom:**

```html
<div class="status status--success">Pago</div>
<div class="status status--pending">Pendente</div>
<div class="status status--error">Erro</div>
```

```css
.status {
  display: inline-flex;
  padding: 0.25rem 0.5rem;
  border-radius: 999px;
  font-size: 0.875rem;
  font-weight: 600;
}

.status--success {
  color: #065f46;
  background: #d1fae5;
}

.status--pending {
  color: #92400e;
  background: #fef3c7;
}

.status--error {
  color: #991b1b;
  background: #fee2e2;
}
```

---

## 20.2 JavaScript deve alterar classes, não estilos inline

**Evite:**

```js
modal.style.display = 'block';
modal.style.opacity = '1';
```

**Prefira:**

```js
modal.classList.add('modal--open');
```

```css
.modal {
  display: none;
}

.modal--open {
  display: block;
}
```

O CSS continua responsável pelo visual e o JavaScript apenas controla o estado.

---

# 21. Erros comuns que devem ser evitados

## 21.1 Usar IDs para estilização

Evite usar `#id` para CSS de componentes.

**Evite:**

```css
#botaoSalvar {}
```

**Prefira:**

```css
.button-save {}
```

IDs têm especificidade alta e dificultam reaproveitamento.

---

## 21.2 Misturar estilos globais com componentes

Evite alterar todos os elementos de uma vez sem necessidade.

**Cuidado com:**

```css
span {
  color: red;
}
```

Prefira classes específicas:

```css
.text-danger {
  color: var(--color-danger);
}
```

---

## 21.3 Duplicar código

Se vários componentes usam os mesmos valores, transforme em variável, classe base ou componente reutilizável.

---

## 21.4 Criar CSS sem pensar no HTML

CSS bom depende de HTML bem estruturado.

Antes de estilizar, confira se o HTML está semântico, organizado e com classes adequadas.

---

# 22. Checklist de revisão CSS

Antes de finalizar uma entrega, revise:

- [ ] O CSS está separado por responsabilidade?
- [ ] Existem variáveis para cores, fontes e espaçamentos?
- [ ] O layout funciona no mobile?
- [ ] O layout funciona no tablet e desktop?
- [ ] Os nomes das classes são claros?
- [ ] Existem seletores muito profundos?
- [ ] Existe uso desnecessário de `!important`?
- [ ] Botões, links e inputs têm `focus-visible`?
- [ ] O contraste dos textos está adequado?
- [ ] As animações respeitam `prefers-reduced-motion`?
- [ ] O CSS tem código morto ou duplicado?
- [ ] Os componentes podem ser reutilizados?
- [ ] O JavaScript altera classes em vez de estilos inline?
- [ ] O CSS está fácil de entender por outro desenvolvedor?

---

# 23. Padrão de resposta esperado da IA

Quando a IA receber uma tarefa de CSS, ela deve:

1. Analisar o objetivo visual e funcional da tela.
2. Verificar se o HTML está adequado para estilização.
3. Definir ou reaproveitar variáveis CSS.
4. Criar estilos mobile first.
5. Usar Flexbox ou Grid conforme o caso.
6. Criar componentes reutilizáveis.
7. Evitar seletores profundos e `!important`.
8. Incluir estados de interação.
9. Garantir acessibilidade básica.
10. Entregar código limpo, comentado apenas quando necessário.

---

# 24. Prompt base para usar esta skill

Use este comportamento ao responder tarefas de CSS:

```md
Aja como um programador sênior de CSS.
Crie estilos limpos, organizados, responsivos e acessíveis.
Use CSS puro sempre que possível.
Priorize mobile first, variáveis CSS, componentes reutilizáveis e nomes de classes claros.
Evite `!important`, seletores profundos, estilos inline e duplicação de código.
Sempre que criar componentes, inclua estados como hover, active, focus-visible e disabled quando fizer sentido.
Explique rapidamente as decisões importantes e entregue o código pronto para uso.
```

---

# 25. Modelo base de CSS recomendado

```css
/* ==============================
   Variables
============================== */
:root {
  --color-primary: #0f4cff;
  --color-danger: #d92d20;
  --color-success: #039855;
  --color-warning: #f79009;

  --color-text: #1f2937;
  --color-muted: #6b7280;
  --color-background: #f9fafb;
  --color-surface: #ffffff;
  --color-border: #e5e7eb;

  --font-base: Arial, sans-serif;

  --space-xs: 0.25rem;
  --space-sm: 0.5rem;
  --space-md: 1rem;
  --space-lg: 1.5rem;
  --space-xl: 2rem;

  --radius-sm: 0.25rem;
  --radius-md: 0.5rem;
  --radius-lg: 1rem;

  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.08);
  --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.12);
}

/* ==============================
   Reset / Base
============================== */
* {
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

body {
  margin: 0;
  font-family: var(--font-base);
  color: var(--color-text);
  background: var(--color-background);
}

img,
svg,
video {
  display: block;
  max-width: 100%;
}

button,
input,
textarea,
select {
  font: inherit;
}

a {
  color: inherit;
  text-decoration: none;
}

/* ==============================
   Layout
============================== */
.container {
  width: min(100% - 2rem, 1200px);
  margin-inline: auto;
}

.section {
  padding-block: var(--space-xl);
}

/* ==============================
   Components
============================== */
.button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-sm);
  min-height: 2.75rem;
  padding: 0.75rem 1rem;
  border: 0;
  border-radius: var(--radius-md);
  font-weight: 600;
  cursor: pointer;
  transition: transform 150ms ease, filter 150ms ease;
}

.button--primary {
  color: #ffffff;
  background: var(--color-primary);
}

.button:hover {
  filter: brightness(0.95);
}

.button:active {
  transform: translateY(1px);
}

.button:focus-visible {
  outline: 3px solid rgba(15, 76, 255, 0.35);
  outline-offset: 2px;
}

.button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.card {
  display: grid;
  gap: var(--space-md);
  padding: var(--space-lg);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}

/* ==============================
   Responsive
============================== */
@media (min-width: 768px) {
  .section {
    padding-block: 4rem;
  }
}

/* ==============================
   Reduced Motion
============================== */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    scroll-behavior: auto !important;
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

# 26. Regra final da skill

A IA deve sempre escrever CSS pensando em manutenção, clareza e reutilização.

O objetivo não é apenas “fazer funcionar”, mas criar um CSS que outro desenvolvedor consiga entender, alterar e evoluir sem medo.
