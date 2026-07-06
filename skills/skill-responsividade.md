# Skill — Boas Práticas de Responsividade para Telas de App, SaaS e Software

## Papel da IA

Você é uma IA especialista em **design de interfaces, UX/UI, front-end e arquitetura de telas responsivas** para sistemas web, SaaS, aplicativos, dashboards e softwares corporativos.

Seu objetivo é criar telas que funcionem muito bem em:

- Desktop
- Notebook
- Tablet
- Celular
- Telas grandes
- Telas pequenas
- Orientação retrato e paisagem
- Usuários com diferentes níveis de habilidade

A responsabilidade principal desta skill é garantir que toda tela seja planejada para se adaptar ao dispositivo, sem apenas “espremer” os elementos. Cada dispositivo deve receber uma experiência adequada ao seu tamanho, forma de uso e prioridade de informação.

---

## Limite desta skill

Esta skill adapta telas, componentes, navegação e densidade de informação para diferentes tamanhos de tela e formas de uso.

Ela pode citar UX, CSS, acessibilidade e performance quando isso afetar a adaptação por dispositivo, mas não deve substituir:

- `skill-ux-ui.md` para definição da experiência e hierarquia visual original;
- `skill-css.md` para implementação completa dos estilos;
- `skill-acessibilidade.md` para validação profunda de acessibilidade;
- `skill-performance.md` para otimização completa de carregamento;
- `skill-pwa.md` para instalação, offline e recursos de app.

Esta skill responde "esta interface funciona bem em desktop, tablet e celular?".

---

# 1. Princípio central

Responsividade não é apenas fazer a tela caber no celular.

Responsividade é adaptar:

- Hierarquia visual
- Quantidade de informação
- Tamanho de fonte
- Espaçamento
- Quantidade de colunas
- Navegação
- Botões
- Tabelas
- Cards
- Menus
- Ícones
- Textos
- Ações principais
- Formulários
- Densidade de dados
- Comportamento da tela

Cada dispositivo deve aproveitar melhor o espaço disponível.

Desktop deve valorizar produtividade e visão ampla.

Celular deve valorizar foco, simplicidade e ação rápida.

Tablet deve equilibrar leitura, toque e uso em movimento.

---

# 2. Mentalidade mobile-first e adaptive-first

## 2.1 Mobile-first

Ao criar uma tela, comece pensando na versão celular.

Motivo:

- O celular tem menos espaço.
- Obriga a definir prioridades.
- Evita excesso de elementos.
- Melhora clareza.
- Facilita escalar para tablet e desktop.

A pergunta principal deve ser:

> Qual é a ação mais importante que o usuário precisa fazer nesta tela?

Depois disso, expanda para telas maiores.

## 2.2 Adaptive-first

Nem toda adaptação deve ser apenas proporcional.

Às vezes, a tela precisa mudar sua estrutura.

Exemplos:

- Desktop usa tabela; celular usa cards.
- Desktop mostra menu lateral; celular usa menu inferior ou drawer.
- Desktop mostra filtros abertos; celular mostra botão “Filtros”.
- Desktop mostra texto completo; celular mostra texto curto ou ícone com tooltip/label.
- Desktop mostra múltiplas colunas; celular mostra uma coluna.

Regra:

> Se apenas reduzir o tamanho prejudicar a leitura ou o toque, mude o componente.

---

# 3. Quebras de tela recomendadas

Use breakpoints como referência, não como prisão.

## 3.1 Faixas principais

| Tipo de tela | Largura aproximada | Estratégia |
|---|---:|---|
| Celular pequeno | 320px a 374px | Layout mínimo, uma coluna, textos curtos |
| Celular comum | 375px a 479px | Uma coluna, ações claras, navegação simples |
| Celular grande | 480px a 767px | Uma coluna confortável, cards melhores |
| Tablet | 768px a 1023px | Uma ou duas colunas, navegação adaptada |
| Notebook | 1024px a 1365px | Layout produtivo, tabelas e painéis compactos |
| Desktop | 1366px a 1919px | Layout completo, boa densidade de dados |
| Tela grande | 1920px+ | Conteúdo com largura máxima, não esticar demais |

## 3.2 Regras gerais

- Não dependa de apenas 3 breakpoints genéricos.
- Teste principalmente 360px, 390px, 768px, 1366px e 1920px.
- Use largura máxima para evitar que conteúdo fique espalhado demais em telas grandes.
- Evite layout quebrando entre 900px e 1200px, pois muitos notebooks ficam nessa faixa.

---

# 4. Grade, colunas e estrutura

## 4.1 Desktop

Em desktop, aproveite o espaço para produtividade.

Boas práticas:

- Usar grid de 12 colunas quando o layout for complexo.
- Usar menu lateral em sistemas administrativos.
- Mostrar mais informações por linha.
- Permitir tabelas, filtros laterais e painéis de resumo.
- Usar largura máxima para leitura, evitando linhas muito longas.

Exemplo:

- Dashboard: cards de indicadores em 4 colunas.
- Lista: tabela completa.
- Detalhe: conteúdo principal + painel lateral com ações.

## 4.2 Tablet

Tablet precisa ser confortável para toque.

Boas práticas:

- Usar 2 colunas quando fizer sentido.
- Evitar muitos painéis laterais fixos.
- Aumentar espaçamento entre elementos clicáveis.
- Usar cards mais largos.
- Manter filtros em painel expansível ou lateral temporário.

Exemplo:

- Dashboard: 2 colunas de indicadores.
- Lista: cards ou tabela simplificada.
- Detalhe: seções empilhadas com ações fixas no topo ou rodapé.

## 4.3 Celular

Celular deve ser direto, simples e focado.

Boas práticas:

- Usar sempre uma coluna principal.
- Evitar tabela tradicional.
- Priorizar cards, listas verticais e abas simples.
- Mostrar apenas informações essenciais.
- Deixar ações importantes visíveis.
- Usar botão fixo no rodapé quando a ação for frequente.

Exemplo:

- Dashboard: indicadores em lista ou cards empilhados.
- Lista: cards com status, título e ação principal.
- Detalhe: blocos verticais com progressão clara.

---

# 5. Hierarquia de informação por dispositivo

A mesma tela não precisa mostrar a mesma quantidade de informação em todos os dispositivos.

## 5.1 Desktop

Pode mostrar:

- Dados completos
- Colunas extras
- Filtros avançados
- Atalhos
- Ações secundárias
- Painéis laterais
- Comparações
- Histórico resumido

## 5.2 Tablet

Deve mostrar:

- Dados principais
- Ações mais usadas
- Filtros acessíveis, mas não necessariamente sempre abertos
- Cards mais visuais
- Conteúdo dividido em seções

## 5.3 Celular

Deve mostrar:

- Título claro
- Status
- Próxima ação
- Informação essencial
- Botão principal
- Poucas ações secundárias

No celular, esconda ou reduza o que não ajuda na decisão imediata.

---

# 6. Textos responsivos

## 6.1 Tamanho de fonte

Use fonte fluida quando possível.

Referência prática:

| Elemento | Celular | Tablet | Desktop |
|---|---:|---:|---:|
| Título principal | 22–28px | 26–34px | 32–44px |
| Título de seção | 18–22px | 20–26px | 24–32px |
| Texto comum | 14–16px | 15–17px | 16–18px |
| Texto auxiliar | 12–14px | 13–15px | 14–16px |
| Botões | 14–16px | 15–16px | 15–17px |

## 6.2 Comprimento de texto

Textos devem se adaptar ao espaço.

Exemplo:

Desktop:

> Relatório mensal de desempenho por cliente

Celular:

> Relatório mensal

Desktop:

> Exportar relatório completo em PDF

Celular:

> Exportar PDF

Desktop:

> Visualizar detalhes da demanda

Celular:

> Detalhes

## 6.3 Regras para cortar texto

Quando o espaço for pequeno:

- Reduza o texto.
- Troque por ícone + label curto.
- Use ellipsis apenas quando o corte não prejudicar entendimento.
- Nunca corte informações críticas como nome do paciente, valor, status, prazo ou alerta.
- Permita expandir quando o conteúdo for importante.

Exemplo ruim:

> Solicitação de altera...

Exemplo melhor:

> Alteração solicitada

---

# 7. Ícones, labels e ações

## 7.1 Ícone sozinho

Use ícone sozinho apenas quando:

- O significado for universal.
- A ação for comum.
- Houver tooltip no desktop.
- Houver label acessível para leitores de tela.

Exemplos aceitáveis:

- Lixeira para excluir
- Lupa para buscar
- Engrenagem para configurações
- Sino para notificações

## 7.2 Ícone com texto

Use ícone com texto quando:

- A ação for importante.
- O usuário pode ter dúvida.
- O sistema for corporativo.
- A ação tiver impacto relevante.

Exemplo:

- `+ Nova demanda`
- `Salvar alterações`
- `Enviar para aprovação`

## 7.3 Adaptação por tela

Desktop:

- Botão com ícone + texto.
- Ações secundárias visíveis.

Tablet:

- Botão com texto curto.
- Algumas ações em menu.

Celular:

- Botão principal visível.
- Ações secundárias dentro de menu “mais”.
- Evitar várias ações lado a lado.

---

# 8. Botões e área de toque

## 8.1 Tamanho mínimo

Em interfaces touch, respeite área mínima de toque.

Regras:

- Botões principais: mínimo 44px de altura.
- Ícones clicáveis: área clicável mínima de 40px a 44px.
- Espaço entre botões: mínimo 8px, ideal 12px ou mais.
- Evitar botões muito próximos em celular.

## 8.2 Botão principal

Toda tela deve ter uma ação principal clara.

Exemplos:

- Salvar
- Criar demanda
- Confirmar agendamento
- Enviar resposta
- Ver resultado
- Finalizar tarefa

No celular, a ação principal pode ficar fixa no rodapé quando for essencial.

## 8.3 Botões secundários

Ações secundárias podem ser:

- Links
- Botões menores
- Menu de três pontos
- Acordeão de opções
- Rodapé da seção

Não deixe ações perigosas próximas da ação principal.

---

# 9. Navegação responsiva

## 9.1 Desktop

Padrões indicados:

- Sidebar fixa para SaaS e sistemas administrativos.
- Topbar para busca, conta, notificações e atalhos.
- Breadcrumb em telas profundas.
- Abas para dividir seções relacionadas.

## 9.2 Tablet

Padrões indicados:

- Sidebar recolhível.
- Menu lateral temporário.
- Topbar mais compacta.
- Abas horizontais com rolagem controlada.

## 9.3 Celular

Padrões indicados:

- Bottom navigation para 3 a 5 áreas principais.
- Menu hambúrguer para áreas secundárias.
- Topbar simples.
- Botão voltar claro.
- Evitar menus muito profundos.

Regra:

> No celular, o usuário deve chegar nas funções principais com no máximo 2 toques.

---

# 10. Tabelas responsivas

Tabelas são boas no desktop, mas quase sempre ruins no celular.

## 10.1 Desktop

Pode usar tabela completa com:

- Ordenação
- Filtros
- Paginação
- Colunas configuráveis
- Ações por linha
- Status visual
- Densidade ajustável

## 10.2 Tablet

Use tabela simplificada ou cards horizontais.

Reduza:

- Colunas secundárias
- Ações repetidas
- Textos longos

## 10.3 Celular

Transforme tabela em card.

Exemplo de tabela desktop:

| Demanda | Cliente | Responsável | Status | Prazo | Ações |
|---|---|---|---|---|---|

Versão card no celular:

```text
[Status]
Título da demanda
Cliente
Responsável • Prazo
[Ver detalhes]
```

## 10.4 Regras para esconder colunas

Em telas menores, mantenha:

- Identificação principal
- Status
- Prazo
- Responsável
- Ação principal

Pode esconder:

- ID técnico
- Data de criação
- Atualização secundária
- Observações longas
- Campos administrativos raramente usados

Nunca esconda informação necessária para decidir ou agir.

---

# 11. Cards responsivos

Cards são ótimos para mobile e dashboards.

## 11.1 Estrutura ideal de card

Um card deve ter:

- Título
- Status ou categoria
- Informação principal
- Informação secundária
- Ação clara

## 11.2 Desktop

Cards podem ficar em grid:

- 3 a 4 colunas para indicadores.
- 2 a 3 colunas para conteúdo mais denso.

## 11.3 Tablet

Cards em 2 colunas ou 1 coluna larga.

## 11.4 Celular

Cards em 1 coluna.

Evite card muito cheio.

Se houver muita informação, divida em:

- Resumo
- Detalhes expansíveis
- Histórico
- Ações

---

# 12. Formulários responsivos

Formulário ruim no celular causa abandono.

## 12.1 Desktop

Pode usar:

- Duas colunas para campos relacionados.
- Agrupamento por seções.
- Ajuda lateral.
- Validação em tempo real.

## 12.2 Tablet

Use uma ou duas colunas dependendo da complexidade.

Campos importantes devem ocupar largura confortável.

## 12.3 Celular

Use uma coluna.

Boas práticas:

- Labels sempre visíveis.
- Campos grandes o suficiente para toque.
- Teclado correto para cada campo.
- Máscaras úteis.
- Erros próximos ao campo.
- Botão principal no final ou fixo.
- Evitar formulários longos sem divisão.

## 12.4 Formulários longos

Divida em etapas:

- Dados principais
- Contato
- Endereço
- Configurações
- Revisão

Mostre progresso quando fizer sentido.

Exemplo:

> Etapa 2 de 4 — Dados de contato

---

# 13. Dashboards responsivos

Dashboard não deve ser apenas um painel bonito.

Ele deve ajudar o usuário a decidir.

## 13.1 Desktop

Pode mostrar:

- Indicadores no topo
- Gráficos
- Tabelas
- Filtros avançados
- Comparativos
- Ranking
- Lista de pendências

## 13.2 Tablet

Mostre:

- Indicadores principais
- Gráficos simplificados
- Filtros recolhíveis
- Lista de prioridades

## 13.3 Celular

Mostre:

- 3 a 5 indicadores principais
- Alertas importantes
- Próximas ações
- Lista resumida
- Acesso rápido a detalhes

No celular, dashboard deve responder:

> O que preciso saber agora?

E não:

> Tudo que existe no sistema.

---

# 14. Modais, drawers e painéis

## 14.1 Desktop

Modais podem ser usados para:

- Confirmações
- Edições rápidas
- Visualização curta
- Ações pontuais

Painéis laterais são bons para detalhes sem sair da lista.

## 14.2 Celular

Evite modais pequenos difíceis de usar.

Prefira:

- Bottom sheets
- Tela dedicada
- Drawer em tela cheia
- Confirmação simples

## 14.3 Regra importante

Se o modal tiver formulário longo, transforme em página.

---

# 15. Espaçamento responsivo

Espaçamento deve mudar conforme o dispositivo.

## 15.1 Referência prática

| Elemento | Celular | Tablet | Desktop |
|---|---:|---:|---:|
| Margem lateral | 16px | 24px | 32–64px |
| Espaço entre seções | 24px | 32px | 40–64px |
| Espaço entre campos | 12–16px | 16px | 16–24px |
| Padding de card | 16px | 20px | 24px |
| Gap de grid | 12–16px | 16–24px | 24–32px |

## 15.2 Cuidados

- Não use padding exagerado no celular.
- Não use conteúdo colado nas bordas.
- Não deixe telas grandes com conteúdo esticado sem limite.
- Não reduza espaçamento a ponto de prejudicar toque.

---

# 16. Imagens, avatares e mídias

## 16.1 Imagens responsivas

Toda imagem deve:

- Ter largura fluida.
- Respeitar proporção.
- Ter corte controlado.
- Não distorcer.
- Carregar versão adequada ao dispositivo quando possível.

## 16.2 Avatares

Tamanhos sugeridos:

- Celular: 32px a 40px
- Tablet: 40px a 48px
- Desktop: 40px a 56px

## 16.3 Banners

Cuidado com banners no celular.

Em telas pequenas:

- Reduza altura.
- Reposicione texto.
- Evite texto sobre imagem poluída.
- Use gradiente ou fundo sólido para legibilidade.

---

# 17. Ocultação e simplificação de elementos

Ocultar elemento é permitido quando melhora a experiência.

Mas deve ser feito com critério.

## 17.1 Pode ocultar no celular

- Colunas secundárias
- Descrições longas
- Filtros avançados abertos
- Ações pouco usadas
- Gráficos complementares
- Dados técnicos
- Painéis laterais
- Menus extensos

## 17.2 Não deve ocultar

- Status
- Prazo
- Erro
- Alerta crítico
- Valor financeiro importante
- Nome principal do item
- Ação obrigatória
- Confirmação de impacto

## 17.3 Alternativas à ocultação

Em vez de simplesmente remover:

- Agrupe em “Ver mais”.
- Coloque em acordeão.
- Mova para aba secundária.
- Transforme em ícone com label.
- Mostre resumo e detalhe ao tocar.

---

# 18. Densidade visual

A densidade deve variar por contexto.

## 18.1 Sistemas corporativos

Podem usar mais densidade no desktop, porque o usuário precisa de produtividade.

Mas no celular, reduza a densidade.

## 18.2 Apps de uso frequente

Devem ser simples, rápidos e claros.

## 18.3 Regra geral

Desktop pode ser informativo.

Celular deve ser acionável.

---

# 19. Estados da interface

Toda tela deve prever estados diferentes.

## 19.1 Estados obrigatórios

- Carregando
- Vazio
- Com erro
- Sem conexão
- Sem permissão
- Sucesso
- Validação de campo
- Lista sem resultados
- Conteúdo parcial

## 19.2 Responsividade dos estados

No celular:

- Mensagens devem ser curtas.
- Botão de ação deve ser claro.
- Ilustrações não podem ocupar a tela inteira sem necessidade.

No desktop:

- Pode haver explicação maior.
- Pode haver links de ajuda.
- Pode haver ações secundárias.

---

# 20. Acessibilidade responsiva

Responsividade também envolve acessibilidade.

## 20.1 Regras essenciais

- Contraste adequado entre texto e fundo.
- Fonte mínima legível.
- Botões com área de toque suficiente.
- Labels visíveis em formulários.
- Navegação por teclado no desktop.
- Foco visível nos elementos interativos.
- Texto alternativo em imagens importantes.
- Não depender apenas de cor para status.

## 20.2 Status visual

Não use apenas vermelho, verde ou azul.

Combine:

- Cor
- Texto
- Ícone
- Badge

Exemplo:

- `Atrasado`
- `Concluído`
- `Pendente`
- `Em análise`

---

# 21. Performance em dispositivos móveis

Celular pode ter internet lenta e hardware limitado.

## 21.1 Boas práticas

- Carregar apenas o necessário.
- Evitar imagens pesadas.
- Usar lazy loading.
- Evitar animações exageradas.
- Reduzir scripts desnecessários.
- Paginar listas grandes.
- Usar skeleton loading com moderação.
- Evitar travamento ao rolar.

## 21.2 Listas grandes

Para listas com muitos itens:

- Use paginação.
- Use busca.
- Use filtros.
- Use carregamento incremental.
- Use virtualização quando necessário.

---

# 22. Animações responsivas

Animação deve ajudar, não atrapalhar.

## 22.1 Desktop

Pode usar:

- Hover
- Transições suaves
- Expansão de painéis
- Tooltip

## 22.2 Celular

Evite depender de hover.

Use:

- Feedback de toque
- Transição curta
- Bottom sheet animado
- Expansão de acordeão

## 22.3 Regras

- Animações devem ser rápidas.
- Não bloquear ação do usuário.
- Não exagerar em dashboards corporativos.
- Respeitar preferência de redução de movimento quando disponível.

---

# 23. Orientação retrato e paisagem

## 23.1 Celular em retrato

Priorize:

- Uma coluna
- Botões grandes
- Ação principal visível
- Leitura vertical

## 23.2 Celular em paisagem

Cuidado com altura reduzida.

Evite:

- Cabeçalhos grandes demais
- Rodapés fixos ocupando muito espaço
- Modais altos

## 23.3 Tablet em paisagem

Pode se aproximar da experiência desktop.

Use:

- Duas colunas
- Sidebar compacta
- Painel de detalhe lateral

---

# 24. Componentes por dispositivo

## 24.1 Menu

| Dispositivo | Melhor padrão |
|---|---|
| Desktop | Sidebar fixa ou topbar completa |
| Tablet | Sidebar recolhível ou drawer |
| Celular | Bottom navigation + menu secundário |

## 24.2 Lista

| Dispositivo | Melhor padrão |
|---|---|
| Desktop | Tabela ou lista densa |
| Tablet | Tabela simplificada ou cards |
| Celular | Cards empilhados |

## 24.3 Filtros

| Dispositivo | Melhor padrão |
|---|---|
| Desktop | Filtros visíveis ou lateral |
| Tablet | Filtros recolhíveis |
| Celular | Botão “Filtros” abrindo bottom sheet/tela |

## 24.4 Detalhes

| Dispositivo | Melhor padrão |
|---|---|
| Desktop | Página com painel lateral |
| Tablet | Seções empilhadas ou duas colunas |
| Celular | Página única com blocos verticais |

## 24.5 Ações

| Dispositivo | Melhor padrão |
|---|---|
| Desktop | Botões visíveis |
| Tablet | Botões principais + menu secundário |
| Celular | Ação principal fixa + menu “mais” |

---

# 25. Regras para desktop

Ao criar desktop:

- Aproveite a largura para produtividade.
- Use visão geral mais completa.
- Mantenha menu e filtros acessíveis.
- Permita comparação de dados.
- Use tabelas quando forem úteis.
- Não deixe linhas de texto muito longas.
- Não espalhe conteúdo sem necessidade.
- Use atalhos e ações rápidas.
- Preserve consistência visual.

Checklist desktop:

- [ ] A tela aproveita bem a largura?
- [ ] A hierarquia está clara?
- [ ] Filtros e ações estão fáceis de encontrar?
- [ ] Existe largura máxima para conteúdo textual?
- [ ] Tabelas não estão poluídas?
- [ ] O usuário consegue trabalhar rápido?

---

# 26. Regras para tablet

Ao criar tablet:

- Pense em toque.
- Use menos densidade que desktop.
- Evite menus pequenos demais.
- Equilibre uma e duas colunas.
- Deixe ações principais visíveis.
- Cuidado com orientação retrato e paisagem.

Checklist tablet:

- [ ] Os botões são confortáveis para toque?
- [ ] A tela funciona em retrato e paisagem?
- [ ] O layout não ficou espremido?
- [ ] Os filtros estão acessíveis?
- [ ] Tabelas foram simplificadas quando necessário?

---

# 27. Regras para celular

Ao criar celular:

- Uma coluna.
- Conteúdo essencial primeiro.
- Ação principal muito clara.
- Texto curto.
- Cards no lugar de tabelas.
- Menu simples.
- Toque confortável.
- Menos elementos por tela.
- Evitar excesso de rolagem sem organização.

Checklist celular:

- [ ] A ação principal está óbvia?
- [ ] A tela funciona com uma mão?
- [ ] O texto está legível?
- [ ] Os botões têm tamanho adequado?
- [ ] A tabela virou card quando necessário?
- [ ] Informações secundárias foram agrupadas?
- [ ] Não existe poluição visual?
- [ ] O usuário consegue agir rápido?

---

# 28. Estratégia de priorização de elementos

Para cada tela, classifique os elementos em 4 níveis.

## Nível 1 — Essencial

Deve aparecer em todos os dispositivos.

Exemplos:

- Título
- Status
- Ação principal
- Prazo
- Valor crítico
- Campo obrigatório
- Alerta

## Nível 2 — Importante

Aparece no desktop e tablet.

No celular pode aparecer resumido.

Exemplos:

- Responsável
- Categoria
- Cliente
- Data relevante
- Progresso

## Nível 3 — Complementar

Aparece no desktop.

No celular fica em “Ver mais”, aba ou detalhe.

Exemplos:

- Histórico curto
- Observações
- ID interno
- Informações administrativas

## Nível 4 — Avançado

Só aparece quando solicitado.

Exemplos:

- Logs
- Dados técnicos
- Configurações raras
- Campos de auditoria

---

# 29. Como desenhar uma tela responsiva

Antes de criar qualquer tela, responda:

1. Qual é o objetivo da tela?
2. Qual é a ação principal?
3. Quais informações são essenciais?
4. O que pode ser resumido?
5. O que pode ser escondido?
6. O que muda entre desktop e celular?
7. Existe tabela? Se sim, como vira card?
8. Existe formulário? Se sim, como fica em uma coluna?
9. O menu funciona em celular?
10. O usuário consegue usar com toque?

---

# 30. Padrão de entrega para IA

Sempre que a IA criar uma tela, ela deve entregar:

## 30.1 Visão geral

- Nome da tela
- Objetivo da tela
- Usuário principal
- Ação principal
- Ações secundárias

## 30.2 Estrutura desktop

- Layout
- Colunas
- Menu
- Componentes
- Tabelas
- Filtros
- Ações

## 30.3 Estrutura tablet

- O que muda
- Quantidade de colunas
- Adaptação de menus
- Adaptação de filtros
- Componentes simplificados

## 30.4 Estrutura celular

- Ordem dos elementos
- O que aparece primeiro
- O que vira card
- O que vira botão
- O que fica oculto em “Ver mais”
- Ação principal

## 30.5 Regras de responsividade

- Breakpoints
- Fontes
- Espaçamentos
- Ocultação
- Componentes alternativos
- Comportamento de navegação

---

# 31. Modelo de especificação de tela responsiva

Use este modelo para documentar cada tela.

```md
# Tela: [Nome da tela]

## Objetivo
[Explique o objetivo da tela]

## Usuário principal
[Administrador, analista, cliente, aluno, operador etc.]

## Ação principal
[Qual ação mais importante]

## Informações essenciais
- [Item 1]
- [Item 2]
- [Item 3]

## Desktop

### Layout
[Exemplo: sidebar fixa + conteúdo em grid de 12 colunas]

### Componentes visíveis
- [Componente 1]
- [Componente 2]

### Tabelas/cards
[Como os dados aparecem]

### Ações
- Principal: [ação]
- Secundárias: [ações]

## Tablet

### Mudanças
- [Mudança 1]
- [Mudança 2]

### Layout
[Uma ou duas colunas]

### Ações
[Como ficam os botões]

## Celular

### Ordem dos elementos
1. [Elemento mais importante]
2. [Segundo elemento]
3. [Terceiro elemento]

### Componentes adaptados
- Tabela vira: [card/lista]
- Filtros viram: [bottom sheet/tela]
- Menu vira: [bottom nav/drawer]

### Elementos ocultos ou resumidos
- [Elemento] → [onde fica]

### Ação principal
[Botão visível/fixo]

## Estados da tela
- Carregando
- Vazio
- Erro
- Sem permissão
- Sem conexão
- Sucesso

## Observações de acessibilidade
- [Contraste]
- [Área de toque]
- [Labels]
- [Foco]
```

---

# 32. Exemplo prático: lista de demandas

## Desktop

Estrutura:

- Sidebar fixa
- Topbar com busca
- Cards de resumo no topo
- Filtros laterais ou horizontais
- Tabela com colunas completas
- Ações por linha

Tabela:

- Código
- Título
- Cliente
- Responsável
- Status
- Prazo
- Última atualização
- Ações

## Tablet

Estrutura:

- Sidebar recolhível
- Cards de resumo em 2 colunas
- Filtros em botão expansível
- Tabela simplificada ou cards largos

Campos principais:

- Título
- Cliente
- Status
- Responsável
- Prazo

## Celular

Estrutura:

- Topbar simples
- Campo de busca
- Botão “Filtros”
- Cards empilhados
- Botão flutuante ou fixo para nova demanda

Card:

```text
[Status]
Título da demanda
Cliente
Responsável • Prazo
[Ver detalhes]
```

Elementos ocultos:

- Código interno vai para detalhes.
- Última atualização fica dentro de “Ver mais”.
- Ações secundárias ficam no menu de três pontos.

---

# 33. Exemplo prático: tela de detalhe

## Desktop

- Cabeçalho com título, status e ações.
- Conteúdo principal em 8 colunas.
- Painel lateral em 4 colunas com dados rápidos.
- Histórico/timeline abaixo.
- Anexos e comentários em seções.

## Tablet

- Cabeçalho compacto.
- Dados rápidos em cards de 2 colunas.
- Conteúdo principal em uma coluna.
- Ações agrupadas.

## Celular

Ordem:

1. Status
2. Título
3. Ação principal
4. Dados rápidos
5. Descrição
6. Próximas etapas
7. Comentários
8. Histórico
9. Anexos
10. Ações secundárias

Regra:

> No celular, o usuário deve entender o status e a próxima ação sem rolar muito.

---

# 34. Exemplo prático: formulário de cadastro

## Desktop

- Duas colunas para campos simples.
- Seções bem separadas.
- Botão salvar no topo e no final, se o formulário for longo.

## Tablet

- Uma ou duas colunas conforme largura.
- Campos importantes ocupam linha inteira.

## Celular

- Uma coluna.
- Campos agrupados por etapa.
- Botão grande.
- Erros abaixo de cada campo.
- Teclado correto.
- Evitar campos lado a lado.

Exemplo:

```text
Nome completo
CPF
Telefone
E-mail
[Continuar]
```

---

# 35. Erros comuns que devem ser evitados

- Apenas diminuir tudo proporcionalmente.
- Usar tabela larga no celular.
- Criar botões pequenos demais.
- Esconder ação principal.
- Usar textos longos em cards pequenos.
- Não testar telas intermediárias.
- Deixar menu desktop no celular.
- Usar hover como única forma de interação.
- Exagerar em modais no celular.
- Colocar muitos botões lado a lado.
- Usar fonte menor que 14px para texto comum.
- Cortar texto crítico.
- Usar gráfico impossível de ler no celular.
- Não prever estado vazio e erro.
- Não pensar em toque.
- Não respeitar área segura do dispositivo.

---

# 36. Checklist final de responsividade

Antes de aprovar uma tela, verifique:

## Conteúdo

- [ ] A informação essencial aparece primeiro?
- [ ] O conteúdo foi priorizado por dispositivo?
- [ ] Textos longos foram adaptados?
- [ ] Nada importante foi escondido?

## Layout

- [ ] Funciona em 320px?
- [ ] Funciona em 390px?
- [ ] Funciona em 768px?
- [ ] Funciona em 1366px?
- [ ] Funciona em 1920px?
- [ ] Existe largura máxima em telas grandes?

## Interação

- [ ] Botões têm área de toque adequada?
- [ ] Ação principal é clara?
- [ ] Ações secundárias estão organizadas?
- [ ] Não depende de hover no celular?

## Componentes

- [ ] Tabelas viram cards no celular?
- [ ] Filtros funcionam bem em mobile?
- [ ] Formulários ficam em uma coluna no celular?
- [ ] Menus foram adaptados?

## Acessibilidade

- [ ] Contraste está adequado?
- [ ] Fonte está legível?
- [ ] Foco é visível?
- [ ] Ícones têm labels ou descrição?
- [ ] Status não depende só de cor?

## Performance

- [ ] Imagens estão otimizadas?
- [ ] Listas grandes têm paginação ou virtualização?
- [ ] A tela não trava no celular?
- [ ] Carregamento é claro?

---

# 37. Regras de ouro

1. Comece pelo celular.
2. Defina a ação principal da tela.
3. Desktop mostra mais; celular mostra melhor.
4. Não esprema tabela: transforme em card.
5. Não esconda informação crítica.
6. Texto deve ser adaptado, não apenas reduzido.
7. Botões precisam ser tocáveis.
8. Menu deve mudar conforme o dispositivo.
9. Formulários longos devem ser divididos.
10. Títulos, status e ações devem aparecer cedo.
11. Telas grandes precisam de limite de largura.
12. Estados vazios e erros também precisam ser responsivos.
13. Ícone sozinho só quando for claro.
14. A experiência mobile deve ser simples e rápida.
15. A experiência desktop deve ser produtiva e completa.

---

# 38. Prompt base para usar esta skill

Use este prompt quando quiser que a IA crie uma tela responsiva:

```md
Aja como designer sênior de UX/UI, programador front-end sênior e arquiteto de telas responsivas para app, SaaS e software.

Crie a especificação da tela abaixo pensando em desktop, tablet e celular.

A tela deve ser mobile-first, mas aproveitar bem o espaço do desktop.

Para cada dispositivo, defina:

- Estrutura do layout
- Ordem dos elementos
- Componentes usados
- O que aparece
- O que é resumido
- O que é ocultado
- Como ficam fontes, espaçamentos e botões
- Como tabelas viram cards
- Como filtros, menus e ações se comportam
- Estados de carregamento, vazio, erro e sucesso
- Regras de acessibilidade e usabilidade

Tela: [nome da tela]
Objetivo: [objetivo]
Usuário: [tipo de usuário]
Ação principal: [ação]
Informações principais: [lista]
Sistema/projeto: [contexto]
```

---

# 39. Critério de qualidade

Uma tela responsiva de qualidade deve passar nestes critérios:

- O usuário entende rapidamente o que fazer.
- A experiência no celular não parece uma versão apertada do desktop.
- A experiência no desktop não parece uma versão esticada do celular.
- A informação principal está sempre visível.
- Os componentes mudam quando necessário.
- O visual mantém consistência entre dispositivos.
- A navegação é simples.
- Os botões são fáceis de tocar.
- A leitura é confortável.
- A interface suporta uso real, não apenas aparência bonita.

---

# 40. Resultado esperado da IA

Ao usar esta skill, a IA deve gerar telas com:

- Layout profissional.
- Responsividade planejada.
- Hierarquia clara.
- Uso inteligente do espaço.
- Adaptação real entre dispositivos.
- Componentes adequados para cada contexto.
- Boa usabilidade para desktop e celular.
- Design consistente e pronto para orientar programadores.

A IA nunca deve entregar apenas uma tela genérica.

Ela deve explicar como a tela se comporta em cada dispositivo e quais decisões foram tomadas para melhorar a experiência do usuário.
