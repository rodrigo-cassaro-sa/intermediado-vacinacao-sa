# Skill: Acessibilidade para Interfaces Web, SaaS e Apps

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e melhorar **acessibilidade em interfaces digitais**, garantindo que telas, formulários, botões, mensagens, navegação e componentes sejam mais fáceis de usar por pessoas com diferentes habilidades, dispositivos e contextos.

O foco é criar sistemas mais claros, inclusivos, profissionais e compatíveis com HTML semântico, CSS organizado e JavaScript puro.

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

## Limite desta skill

Esta skill valida se a interface é clara, navegável, compreensível e utilizável por pessoas com diferentes habilidades, dispositivos e contextos.

Ela pode orientar HTML, CSS e JavaScript quando isso afetar acessibilidade, mas não deve substituir:

- `skill-html.md` para estrutura completa da página;
- `skill-css.md` para organização visual e estilos;
- `skill-js.md` para comportamento completo da tela;
- `skill-responsividade.md` para adaptação por dispositivo;
- `skill-ux-ui.md` para desenho geral da experiência.

Esta skill responde "todos conseguem usar esta interface com segurança e clareza?".

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa especialista sênior em UX/UI, frontend acessível, HTML semântico, usabilidade e qualidade de interface.

A IA deve pensar em:

- contraste;
- tamanho de fonte;
- navegação por teclado;
- labels de formulário;
- foco visível;
- mensagens de erro;
- leitores de tela;
- toque em celular;
- linguagem clara;
- não depender apenas de cor.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-html.md
skill-css.md
skill-js.md
skill-ux-ui.md
skill-frontend.md
skill-responsividade.md
```

---

## Princípio central

```txt
Acessibilidade não é detalhe visual; é garantir que mais pessoas consigam entender, navegar e concluir tarefas com segurança.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Acessibilidade desde o início

A IA deve aplicar acessibilidade antes de finalizar o layout, não apenas como correção posterior.

Toda tela deve responder:

- o usuário entende o que fazer?
- o teclado consegue navegar?
- o foco está visível?
- os campos têm label?
- erros são claros?
- contraste permite leitura?
- o botão tem área de toque adequada?
- a tela funciona em celular?

Interface acessível costuma ser melhor para todos os usuários.

---

# 2. HTML semântico

Usar a tag correta melhora acessibilidade.

Boas práticas:

- `button` para ações;
- `a` para links;
- `label` ligado ao campo;
- `main` para conteúdo principal;
- `nav` para navegação;
- headings em ordem lógica;
- `table` para dados tabulares;
- `ul`/`ol` para listas reais.

Evitar criar botão com `div` clicável sem suporte a teclado.

---

# 3. Labels e formulários

Todo campo precisa de rótulo claro.

Exemplo correto:

```html
<label for="email">E-mail</label>
<input id="email" name="email" type="email" autocomplete="email" required>
```

Boas práticas:

- label visível sempre que possível;
- placeholder não substitui label;
- indicar campos obrigatórios;
- agrupar campos relacionados;
- explicar formato esperado;
- exibir erro próximo ao campo.

---

# 4. Mensagens de erro

Erro acessível explica o problema e como corrigir.

Evitar:

```txt
Erro inválido.
```

Preferir:

```txt
Informe um e-mail válido, como nome@exemplo.com.
```

Boas práticas:

- mensagem próxima ao campo;
- resumo de erros no topo quando formulário for grande;
- não depender só de borda vermelha;
- manter texto claro;
- preservar dados digitados após erro.

---

# 5. Contraste e cor

Texto precisa ter contraste suficiente com o fundo.

Regras práticas:

- evitar cinza claro em fundo branco;
- evitar texto pequeno com baixo contraste;
- botões importantes precisam se destacar;
- estados de erro/sucesso não devem depender só de cor;
- usar ícone, texto ou padrão visual junto da cor.

Exemplo:

```txt
Erro: campo obrigatório
```

não apenas uma borda vermelha sem explicação.

---

# 6. Foco visível e teclado

Usuário deve conseguir navegar sem mouse.

Verificar:

- tecla Tab percorre os elementos na ordem correta;
- foco visual aparece claramente;
- modais prendem foco enquanto abertos;
- ESC fecha modal quando adequado;
- Enter/Espaço acionam botões;
- menus são acessíveis por teclado;
- não existem armadilhas de foco.

Nunca remover `outline` sem criar alternativa visível.

---

# 7. Área de toque

Em celular, botões precisam ser fáceis de tocar.

Boas práticas:

- botões principais com altura confortável;
- espaçamento entre ações;
- evitar links muito pequenos;
- não colocar ações destrutivas coladas em ações comuns;
- confirmar exclusões críticas;
- manter ação principal fácil de alcançar.

---

# 8. Texto alternativo e imagens

Imagens informativas precisam de texto alternativo.

Regras:

- imagem decorativa pode ter `alt=""`;
- imagem com informação precisa de descrição;
- ícone sozinho deve ter label acessível;
- gráfico deve ter resumo textual;
- botão com ícone deve ter texto visível ou `aria-label`.

Exemplo:

```html
<button aria-label="Excluir cliente">
  <span aria-hidden="true">🗑</span>
</button>
```

---

# 9. Componentes dinâmicos

Quando JavaScript altera a tela, o usuário precisa perceber.

Boas práticas:

- mensagens de sucesso/erro em área anunciável;
- loading claro;
- foco enviado para modal aberto;
- foco retorna ao botão após fechar modal;
- campos inseridos dinamicamente continuam com label;
- tabelas/cards atualizados mantêm contexto.

Evitar atualizar conteúdo importante silenciosamente sem feedback.

---

# 10. Linguagem clara

Acessibilidade também é compreensão.

Usar:

- frases curtas;
- botões com verbo claro;
- títulos objetivos;
- instruções próximas da ação;
- mensagens sem jargão técnico;
- confirmação em ações irreversíveis.

Evitar termos genéricos como “processar”, “gerenciar” ou “efetuar” quando o usuário precisa saber exatamente o que vai acontecer.

---

# 11. Teste básico de acessibilidade

Antes de aprovar tela, testar:

- navegar usando só teclado;
- aumentar zoom do navegador;
- usar celular;
- verificar contraste visual;
- enviar formulário com erro;
- abrir/fechar modal;
- usar leitor de tela quando disponível;
- checar se todos os botões têm nome claro.

A IA deve incluir acessibilidade nos critérios de aceite da tela.

---

# Checklist obrigatório antes de concluir

- [ ] HTML usa semântica correta.
- [ ] Todo campo tem label.
- [ ] Placeholder não substitui label.
- [ ] Foco visível existe.
- [ ] Tela é navegável por teclado.
- [ ] Contraste foi verificado.
- [ ] Erros explicam como corrigir.
- [ ] Ações não dependem somente de cor.
- [ ] Botões têm área de toque adequada.
- [ ] Componentes dinâmicos dão feedback claro.

---

# Modelo de entrega esperado

Ao revisar acessibilidade, entregue:

1. Problemas encontrados.
2. Impacto para o usuário.
3. Correção recomendada.
4. Exemplo de HTML/CSS/JS quando necessário.
5. Checklist de teste manual.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
