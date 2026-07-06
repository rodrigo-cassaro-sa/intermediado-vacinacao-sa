# Skill: Motion, Feedback Visual e Performance Percebida

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e melhorar **animações, microinterações, estados visuais, carregamentos, transições, feedback imediato, onboarding e conforto de uso** em sistemas web, SaaS, apps e PWAs.

O foco é fazer o sistema parecer rápido, claro, responsivo e agradável, mesmo quando o backend, API, webhook, n8n, integração externa ou banco de dados tiverem alguma latência.

Esta skill não existe para “enfeitar” a interface.

Ela existe para:

- reduzir ansiedade do usuário;
- mostrar que uma ação foi reconhecida;
- mascarar latência real com feedback honesto;
- evitar tela travada;
- guiar o usuário em fluxos novos;
- deixar o sistema mais fluido em máquinas simples;
- melhorar a percepção de qualidade profissional.

---

## Limite desta skill

Esta skill define comportamento visual de conforto, feedback e movimento.

Ela pode citar UX/UI, frontend, CSS, JavaScript, performance, PWA, acessibilidade e onboarding quando isso afetar a sensação de uso.

Ela não deve substituir:

- `skill-ux-ui.md` para identidade visual, hierarquia e experiência geral;
- `skill-frontend.md` para implementação completa da interface;
- `skill-css.md` para arquitetura completa dos estilos;
- `skill-js.md` para lógica completa de comportamento;
- `skill-performance.md` para otimização real de backend, banco, payload e gargalos;
- `skill-pwa-offline-first.md` para cache, offline, service worker e sincronização;
- `skill-acessibilidade.md` para auditoria completa de acessibilidade.

Esta skill responde:

```txt
O usuário percebeu que o sistema respondeu rápido, entendeu o que está acontecendo e se sentiu seguro para continuar?
```

---

## Stack padrão

```txt
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON
PHP procedural puro
MySQL ou MariaDB
PWA/offline quando aplicável
Sem framework por padrão
```

---

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa designer de interação, UX engineer e frontend sênior, especialista em:

- performance percebida;
- microinterações;
- motion design funcional;
- feedback visual;
- skeleton screens;
- optimistic UI;
- estados vazios;
- onboarding progressivo;
- acessibilidade de movimento;
- interfaces leves para máquinas fracas e celulares simples.

A IA deve priorizar clareza, leveza e manutenção.

---

## Princípio central

```txt
Toda ação do usuário deve receber resposta visual imediata, preferencialmente em menos de 100ms, mesmo que o processamento real continue em segundo plano.
```

Feedback visual não significa mentir para o usuário.

Significa mostrar claramente:

- ação recebida;
- processamento em andamento;
- progresso quando possível;
- sucesso;
- falha;
- próxima ação.

---

# 1. Quando usar esta skill

Use esta skill quando houver:

- botão que envia formulário;
- tela que carrega dados;
- dashboard;
- listagem;
- busca;
- filtro;
- upload;
- checkout;
- pagamento;
- voucher;
- integração externa;
- webhook;
- processo demorado;
- onboarding;
- primeira visita;
- estado vazio;
- loading;
- atualização parcial de tela;
- PWA;
- uso em celular;
- público com computadores simples;
- sensação de sistema lento ou travado.

---

# 2. Quando não usar esta skill

Não usar para criar movimento sem propósito.

Evitar em:

- telas estáticas simples;
- conteúdo que não tem interação;
- animação decorativa pesada;
- transições longas;
- efeitos que atrapalham leitura;
- interface que já está lenta por excesso de JS/CSS;
- usuários que preferem reduzir movimento;
- fluxos críticos onde animação pode esconder erro importante.

Se a animação não melhora clareza, orientação ou percepção de resposta, ela não deve existir.

---

# 3. Relação com outras skills

Esta skill complementa:

```txt
skill-ux-ui.md
skill-frontend.md
skill-css.md
skill-js.md
skill-fetch.md
skill-performance.md
skill-pwa-offline-first.md
skill-acessibilidade.md
skill-qa.md
```

Diferença prática:

```txt
UX/UI define a experiência.
Motion/Feedback define a sensação de resposta.
Frontend implementa.
Performance otimiza o gargalo real.
PWA cuida de offline/cache.
Acessibilidade valida inclusão.
QA testa se o comportamento ficou correto.
```

---

# 4. Performance percebida

## 4.1 Regra dos 100ms

Sempre que o usuário clicar, tocar, enviar ou mudar algo, a interface deve responder visualmente em até 100ms quando possível.

Exemplos de resposta imediata:

- botão muda para estado pressionado;
- botão mostra loading;
- campo mostra validação visual;
- item aparece como “salvando”;
- linha da tabela entra em estado temporário;
- skeleton aparece no lugar da área que carregará;
- mensagem “processando” aparece perto da ação.

Evitar:

```txt
usuário clica → nada acontece → backend responde segundos depois
```

Preferir:

```txt
usuário clica → botão mostra loading imediatamente → backend processa → tela mostra sucesso ou erro
```

---

## 4.2 Skeleton screen em vez de spinner genérico

Skeleton screen deve ser usado quando a estrutura do conteúdo é previsível.

Usar skeleton em:

- cards;
- listas;
- tabelas;
- dashboards;
- perfil;
- histórico;
- blocos de formulário;
- páginas que carregam dados do backend.

Spinner pode ser usado para:

- ação pequena;
- botão;
- envio rápido;
- processo sem estrutura visual previsível.

Regra:

```txt
Skeleton mostra onde o conteúdo vai aparecer.
Spinner apenas diz que algo está carregando.
```

Em sistema profissional, skeleton costuma passar mais sensação de velocidade.

---

## 4.3 Loading por região, não tela inteira

Evitar bloquear a tela inteira quando só uma parte está carregando.

Preferir:

- loading no botão que foi clicado;
- skeleton apenas na tabela;
- shimmer apenas nos cards;
- estado de carregamento no filtro;
- loading inline no campo de busca.

Bloqueio de tela inteira só deve ser usado quando:

- ação é crítica;
- usuário não pode interagir até finalizar;
- pagamento está sendo processado;
- operação pode gerar duplicidade;
- fluxo precisa impedir clique repetido.

---

# 5. Optimistic UI

Optimistic UI é atualizar a tela imediatamente antes da confirmação final do backend ou integração, mantendo possibilidade de corrigir se der erro.

## 5.1 Quando usar

Usar em ações reversíveis ou de baixo risco:

- marcar tarefa como concluída;
- favoritar item;
- curtir;
- alterar ordem visual;
- marcar notificação como lida;
- salvar preferência local;
- adicionar item temporário em lista;
- atualizar contador não financeiro.

## 5.2 Quando não usar

Não usar optimistic UI como confirmação final em:

- pagamento;
- voucher;
- assinatura;
- liberação de acesso pago;
- exclusão definitiva;
- alteração de permissão;
- envio fiscal;
- ação irreversível;
- operação financeira;
- integração crítica sem idempotência.

Nesses casos, usar estado intermediário:

```txt
pendente
processando
aguardando confirmação
confirmado
falhou
```

## 5.3 Regra para backend lento, webhook ou n8n

Quando uma ação depende de backend, webhook, n8n ou API externa:

1. a tela deve responder imediatamente;
2. o item deve entrar em estado temporário;
3. o usuário deve saber que ainda está processando;
4. o sistema deve confirmar quando finalizar;
5. se falhar, a tela deve reverter ou mostrar ação de correção.

Exemplo:

```txt
Usuário clica em "Enviar".
Botão muda para "Enviando...".
Card entra em "Processando".
Webhook demora.
Tela continua utilizável.
Quando confirmar, status vira "Enviado".
Se falhar, status vira "Falhou — tentar novamente".
```

---

# 6. Motion design com propósito

## 6.1 Duração recomendada

Usar durações curtas:

```txt
microinteração simples: 100ms a 150ms
entrada de elemento: 150ms a 250ms
saída de elemento: 100ms a 200ms
transição de painel/modal: 200ms a 300ms
```

Evitar animações acima de 300ms em ações frequentes.

Animações longas deixam o sistema parecer lento.

---

## 6.2 Easings recomendados

Usar easing conforme intenção:

```txt
ease-out       → entrada de elemento
ease-in        → saída de elemento
ease-in-out    → transição entre estados
linear         → progresso contínuo, barra ou skeleton
```

Regra prática:

- entrada deve começar rápido e suavizar no final;
- saída deve ser rápida;
- interação de botão deve parecer imediata.

---

## 6.3 Animar apenas propriedades leves

Para rodar liso em celulares simples e máquinas fracas, animar preferencialmente:

```txt
transform
opacity
```

Evitar animar frequentemente:

```txt
width
height
top
left
margin
padding
box-shadow pesado
filter pesado
blur
```

Essas propriedades podem causar recalculo de layout e travamento.

---

## 6.4 Movimento precisa ter função

Toda animação deve ter pelo menos uma função:

- orientar atenção;
- mostrar causa e efeito;
- indicar mudança de estado;
- suavizar entrada/saída;
- reduzir sensação de espera;
- confirmar ação;
- prevenir clique repetido;
- guiar usuário iniciante.

Evitar animação apenas para “ficar bonito”.

---

# 7. Microinterações

## 7.1 Botões

Botões devem ter estados claros:

```txt
normal
hover
focus
active
loading
disabled
success
error
```

Ao clicar:

- responder imediatamente;
- impedir clique duplo quando necessário;
- mostrar loading se houver espera;
- restaurar estado se falhar;
- mostrar sucesso discreto quando concluir.

Exemplo de comportamento:

```txt
Salvar → Salvando... → Salvo
Salvar → Salvando... → Erro ao salvar
```

---

## 7.2 Formulários

Formulários devem mostrar:

- foco visível;
- validação inline;
- erro perto do campo;
- estado de salvamento;
- prevenção de envio duplicado;
- mensagem de sucesso;
- preservação dos dados se falhar.

Evitar limpar formulário após erro.

---

## 7.3 Tabelas e listas

Listagens devem ter:

- skeleton de linhas;
- estado vazio útil;
- feedback ao filtrar;
- paginação clara;
- ordenação visível;
- atualização parcial;
- destaque temporário em item criado/editado;
- mensagem quando não houver resultado.

---

## 7.4 Modais e painéis

Modais devem:

- abrir com transição curta;
- focar o primeiro elemento útil;
- permitir fechar de forma clara;
- não prender usuário sem motivo;
- respeitar `Esc` quando aplicável;
- manter foco acessível;
- evitar animações exageradas.

---

# 8. Onboarding e aprendizado progressivo

## 8.1 Empty states que ensinam

Estado vazio não deve ser apenas:

```txt
Nenhum registro encontrado.
```

Melhor:

```txt
Você ainda não cadastrou nenhum cliente.
Cadastre o primeiro cliente para começar a gerar relatórios e acompanhar o histórico.
[ Cadastrar cliente ]
```

Empty state deve responder:

- o que está vazio;
- por que está vazio;
- o que o usuário pode fazer agora;
- qual benefício existe ao agir.

---

## 8.2 Tour progressivo

Tour deve ser usado com cuidado.

Usar quando:

- sistema é novo;
- painel tem muitas áreas;
- fluxo tem etapas;
- usuário precisa aprender ordem correta.

Evitar:

- tour longo;
- bloquear tela inteira;
- mostrar tudo de uma vez;
- repetir tour toda visita;
- forçar usuário experiente.

Preferir:

```txt
dicas curtas
primeira visita
pular tour
não mostrar novamente
tour por etapa
```

---

## 8.3 Progressive disclosure

Mostrar complexidade aos poucos.

Exemplos:

- esconder configurações avançadas;
- mostrar campos extras apenas quando necessário;
- usar “ver mais filtros”;
- separar básico e avançado;
- liberar etapas conforme progresso.

O usuário iniciante não deve ser esmagado por todas as opções de uma vez.

---

# 9. Acessibilidade de movimento

## 9.1 Respeitar prefers-reduced-motion

Se o usuário prefere reduzir movimento, reduzir ou remover animações não essenciais.

Exemplo CSS:

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

## 9.2 Foco visível

Todo componente interativo deve ter foco visível.

Aplicar em:

- botões;
- links;
- inputs;
- selects;
- modais;
- tabs;
- menus;
- cards clicáveis.

## 9.3 ARIA quando necessário

Usar ARIA para componentes interativos personalizados:

- modal;
- dropdown;
- tabs;
- accordion;
- toast;
- alertas;
- loading;
- progresso.

Não usar ARIA para compensar HTML ruim.

Primeiro usar HTML semântico.

---

# 10. Loading real

Esta skill melhora a percepção, mas não substitui otimização real.

Quando houver lentidão verdadeira, verificar:

- lazy load;
- paginação;
- cache;
- payload menor;
- consulta SQL;
- índice;
- imagem pesada;
- processamento assíncrono;
- fila;
- offline-first quando aplicável.

Se a tela usa PWA, combinar com:

```txt
cache de assets
cache de dados estáveis
fila offline
sincronização posterior
estado "aguardando sincronização"
```

---

# 11. Padrões práticos de implementação

## 11.1 Classes CSS de estado

Padronizar estados visuais:

```txt
.is-loading
.is-saving
.is-success
.is-error
.is-disabled
.is-pending
.is-skeleton
.is-offline
.is-syncing
```

## 11.2 Atributos data

Usar `data-*` para conectar JS com HTML sem depender de texto visual:

```html
<button data-action="salvar-cliente" data-loading-text="Salvando...">
  Salvar
</button>
```

## 11.3 Texto de status

O usuário deve entender o que está acontecendo:

```txt
Carregando dados...
Salvando...
Processando...
Aguardando confirmação...
Sincronizando...
Salvo com sucesso.
Não foi possível salvar.
```

Evitar mensagens vagas:

```txt
Aguarde...
Erro.
Processando dados...
```

---

# 12. Exemplo CSS base

```css
.motion-fade-in {
  opacity: 0;
  transform: translateY(6px);
  animation: motionFadeIn 180ms ease-out forwards;
}

@keyframes motionFadeIn {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.button-loading {
  pointer-events: none;
  opacity: 0.75;
}

.skeleton {
  position: relative;
  overflow: hidden;
  background: #e5e7eb;
  border-radius: 8px;
}

.skeleton::after {
  content: "";
  position: absolute;
  inset: 0;
  transform: translateX(-100%);
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.55),
    transparent
  );
  animation: skeleton-loading 1.2s infinite;
}

@keyframes skeleton-loading {
  100% {
    transform: translateX(100%);
  }
}

@media (prefers-reduced-motion: reduce) {
  .motion-fade-in,
  .skeleton::after {
    animation: none;
    transform: none;
  }
}
```

---

# 13. Exemplo JavaScript base para botão com feedback

```js
function setButtonLoading(button, isLoading) {
  if (!button) return;

  const defaultText = button.dataset.defaultText || button.textContent;
  const loadingText = button.dataset.loadingText || 'Processando...';

  button.dataset.defaultText = defaultText;
  button.disabled = isLoading;
  button.classList.toggle('is-loading', isLoading);
  button.textContent = isLoading ? loadingText : defaultText;
}
```

---

# 14. Exemplo de optimistic UI segura

```js
async function marcarNotificacaoComoLida(notificacaoId, elemento) {
  const estadoAnterior = elemento.className;

  elemento.classList.add('is-pending', 'is-read');

  try {
    const response = await fetch('/api/notificacoes/marcar-lida.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: notificacaoId })
    });

    const result = await response.json();

    if (!result.success) {
      throw new Error(result.message || 'Falha ao atualizar.');
    }

    elemento.classList.remove('is-pending');
    elemento.classList.add('is-success');
  } catch (error) {
    elemento.className = estadoAnterior;
    elemento.classList.add('is-error');
  }
}
```

Regra:

```txt
Só usar optimistic UI quando a ação puder ser revertida com segurança.
```

---

# 15. Checklist obrigatório

Antes de concluir uma tela, validar:

```md
- [ ] A primeira resposta visual acontece em menos de 100ms quando possível.
- [ ] Botões importantes possuem estados de loading, sucesso e erro.
- [ ] Formulários não parecem travados durante envio.
- [ ] Listas, cards ou dashboards usam skeleton quando adequado.
- [ ] A tela evita spinner genérico quando a estrutura do conteúdo é previsível.
- [ ] Ações demoradas mostram estado claro.
- [ ] A interface impede clique duplicado em ações críticas.
- [ ] Optimistic UI só foi usada em ação reversível.
- [ ] Pagamento, voucher, permissão e ação crítica não usam confirmação otimista indevida.
- [ ] Animações usam duração curta entre 150ms e 300ms.
- [ ] Animações usam preferencialmente transform e opacity.
- [ ] Movimento tem propósito claro.
- [ ] prefers-reduced-motion foi respeitado.
- [ ] Foco visível foi preservado.
- [ ] Componentes interativos usam HTML semântico ou ARIA quando necessário.
- [ ] Empty states ensinam a próxima ação.
- [ ] Onboarding não bloqueia o usuário experiente.
- [ ] Loading visual não substitui otimização real.
- [ ] Performance real foi encaminhada para `skill-performance.md` quando necessário.
```

---

# 16. Modelo de entrega esperado

Ao revisar ou criar uma tela com esta skill, entregar:

```md
## Diagnóstico de conforto de uso

- Onde a tela parece lenta:
- Onde falta feedback:
- Onde o usuário pode ficar em dúvida:
- Onde existe risco de clique duplicado:
- Onde cabe skeleton:
- Onde cabe optimistic UI:
- Onde não pode usar optimistic UI:
- Onde precisa reduzir movimento:

## Melhorias recomendadas

1. ...
2. ...
3. ...

## Cuidados técnicos

- ...

## Checklist

- [ ] Feedback em até 100ms considerado
- [ ] Estados visuais definidos
- [ ] Acessibilidade de movimento considerada
- [ ] Performance real não foi ignorada
```

---

# 17. Regra final

A melhor animação é aquela que ajuda o usuário a entender o sistema.

```txt
Feedback visual rápido reduz ansiedade.
Skeleton reduz sensação de espera.
Microinteração confirma ação.
Transição curta orienta atenção.
Optimistic UI deve ser usada com responsabilidade.
Acessibilidade vem antes de efeito visual.
Performance percebida não substitui performance real.
```
