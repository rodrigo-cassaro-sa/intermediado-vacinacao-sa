# Skill — Debug Visual Administrativo em Tela para Apps, SaaS e Sistemas

## Limite desta skill

Esta skill define debug visual administrativo dentro da interface do sistema.

Ela deve focar em painel de debug, timeline de ações, inspeção de elementos, estados da tela, chamadas de API, regras executadas e relatório técnico controlado.

Ela não deve substituir:

- `skill-logs-auditoria.md` para registro persistente e histórico oficial;
- `skill-seguranca.md` para política geral de proteção;
- `skill-erros-excecoes.md` para padrão de mensagens e códigos de erro;
- `skill-monitoramento-observabilidade.md` para métricas e alertas de produção;
- `skill-qa.md` para plano completo de testes;
- `skill-frontend.md` para implementação geral da interface.

Esta skill responde "usuários autorizados conseguem entender visualmente o que aconteceu na tela sem expor dados sensíveis?".

---

## 1. Papel da IA ao usar esta skill

Aja como um **programador sênior de app, SaaS e software**, especialista em **refatoramento, correção de bugs, rastreabilidade, observabilidade, QA e experiência de debug visual**.

Use esta skill sempre que for criar, revisar, refatorar ou corrigir telas de sistemas web, apps, painéis administrativos, CRMs, ERPs, SaaS internos ou plataformas com regras de negócio complexas.

O objetivo é implementar uma camada de **debug visual dentro da própria página**, acessível apenas para usuários autorizados, permitindo enxergar passo a passo o comportamento do sistema, identificar erros, entender estados instáveis, reproduzir bugs e acelerar a correção.

Esta skill foi inspirada em ferramentas visuais como o debug mode do Bubble, que exibe execução por etapas, ações, estados, inspeção de elementos e fluxo visual do comportamento da tela.

---

## 2. Princípio central

Toda tela importante do sistema deve ser desenvolvida pensando que, em algum momento, será necessário responder rapidamente:

- O que o usuário clicou?
- Qual ação foi disparada?
- Qual função executou?
- Qual estado mudou?
- Qual chamada de API aconteceu?
- Qual payload foi enviado?
- Qual resposta retornou?
- Qual regra de negócio bloqueou ou permitiu a ação?
- Qual componente foi atualizado?
- Qual erro ocorreu?
- Qual dado estava diferente do esperado?
- Como reproduzir o problema?

O debug visual deve transformar o sistema em algo **observável, auditável e explicável**.

---

## 3. Objetivo do debug visual

O debug visual existe para ajudar administradores, desenvolvedores, suporte técnico e QA a entenderem o comportamento real da tela sem depender apenas de console do navegador, prints soltos ou relatos incompletos do usuário.

Ele deve permitir:

1. Ver o fluxo de execução passo a passo.
2. Inspecionar componentes e elementos da tela.
3. Ver estados internos da página.
4. Ver dados de sessão, permissões e contexto.
5. Ver chamadas de API, respostas e erros.
6. Ver regras de negócio executadas.
7. Ver logs em tempo real.
8. Copiar um relatório técnico do erro.
9. Reproduzir o bug com mais facilidade.
10. Ajudar na correção sem expor dados sensíveis a usuários comuns.

---

## 4. Regra obrigatória de segurança

O debug visual **nunca deve ficar disponível para usuários comuns**.

Ele só pode ser ativado quando todas as condições forem verdadeiras:

- Usuário autenticado.
- Usuário com perfil administrador, desenvolvedor, suporte técnico ou QA autorizado.
- Ambiente permitido: desenvolvimento, homologação ou produção controlada.
- Parâmetro de ativação válido, por exemplo: `?debug_mode=true`.
- Permissão validada no backend, não apenas no frontend.
- Registro de auditoria informando quem ativou o debug, quando e em qual tela.

Nunca confiar apenas em JavaScript para liberar debug. O frontend pode esconder a interface, mas a autorização real deve vir do backend.

---

## 5. Modos recomendados de debug

### 5.1. Debug desligado

Modo padrão para usuários comuns.

Características:

- Nenhum painel de debug visível.
- Nenhum dado técnico exposto.
- Logs técnicos não aparecem na interface.
- A tela funciona normalmente.

### 5.2. Debug básico

Modo para suporte ou administrador não técnico.

Deve mostrar:

- Usuário logado.
- Perfil de acesso.
- Página atual.
- ID da sessão.
- Status geral da tela.
- Última ação executada.
- Mensagem simplificada de erro.
- Botão para copiar relatório de suporte.

Não deve mostrar payload sensível, token, senha, dados pessoais completos ou queries SQL.

### 5.3. Debug técnico

Modo para programador, QA ou administrador técnico.

Deve mostrar:

- Timeline de eventos.
- Ações executadas passo a passo.
- Estados internos.
- Requisições de API.
- Payloads mascarados.
- Respostas de API.
- Erros de validação.
- Permissões aplicadas.
- Componentes renderizados.
- Regras de negócio acionadas.
- Tempo de execução.

### 5.4. Debug passo a passo

Modo inspirado no Bubble.

Deve permitir:

- Pausar antes de executar uma ação.
- Executar próxima etapa manualmente.
- Executar lentamente.
- Continuar execução normal.
- Inspecionar estado antes e depois de cada ação.

Exemplo de botões:

- `Run`
- `Run slow`
- `Run next`
- `Pause`
- `Stop debug`
- `Copy report`

---

## 6. Componentes obrigatórios do painel de debug

### 6.1. Barra superior ou inferior de debug

Deve ser fixa na tela, sem atrapalhar totalmente o uso do sistema.

Informações recomendadas:

- `Debug mode: ON`
- Ambiente: desenvolvimento, homologação ou produção.
- Usuário logado.
- Perfil/permissão.
- Página atual.
- Trace ID da sessão.
- Botões de controle: Run, Run slow, Run next, Pause, Inspect, Exportar relatório.

### 6.2. Timeline de ações

A timeline deve mostrar o fluxo real do comportamento da tela.

Exemplo:

```text
[01] Page loaded
[02] User clicked: Criar usuário
[03] Validate form fields
[04] Check permission: usuarios.criar
[05] POST /api/usuarios
[06] API response: 201 Created
[07] Update table
[08] Show success toast
```

Cada item deve conter:

- Número da etapa.
- Horário.
- Tipo de evento.
- Nome da ação.
- Status: aguardando, executando, sucesso, alerta ou erro.
- Tempo de execução.
- Link para detalhes.

### 6.3. Inspetor de elemento

Ao clicar em um elemento da tela com o modo `Inspect` ativo, o debug deve mostrar:

- Nome técnico do componente.
- ID do elemento.
- Tipo do elemento: botão, tabela, modal, campo, card, menu.
- Estado atual: ativo, desativado, carregando, oculto.
- Permissões necessárias.
- Evento associado ao clique.
- Função chamada.
- Dados vinculados.
- Validações aplicadas.

Exemplo:

```text
Elemento: Botão Criar Usuário
ID: btn-criar-usuario
Componente: UserCreateButton
Permissão necessária: usuarios.criar
Ação no clique: openCreateUserModal()
Estado: enabled
Última execução: sucesso
```

### 6.4. Painel de estado da tela

Toda tela deve ter estados rastreáveis.

Estados mínimos:

- `idle`: tela parada.
- `loading`: carregando dados.
- `ready`: pronta para uso.
- `submitting`: enviando formulário.
- `empty`: sem dados.
- `error`: erro.
- `unauthorized`: sem permissão.
- `offline`: sem conexão.

O debug deve mostrar:

```json
{
  "screen": "controle_usuarios",
  "state": "ready",
  "selectedUserId": null,
  "filters": {
    "email": "",
    "group": null,
    "unit": null
  },
  "recordsTotal": 0,
  "lastAction": "loadUsers"
}
```

### 6.5. Painel de rede e API

Deve listar chamadas feitas pela tela.

Para cada chamada mostrar:

- Método: GET, POST, PUT, PATCH, DELETE.
- Endpoint.
- Status HTTP.
- Tempo de resposta.
- Payload enviado com dados sensíveis mascarados.
- Resposta recebida.
- Mensagem de erro, se existir.
- Tentativas de retry.
- Trace ID.

Exemplo:

```text
POST /api/usuarios
Status: 422
Tempo: 340ms
Erro: Email já cadastrado
Campo: email
Regra: unique_user_email
```

### 6.6. Painel de regras de negócio

Toda regra importante deve poder ser rastreada.

Exemplo:

```text
Regra executada: usuário precisa ter perfil administrador
Entrada: user.role = "analista"
Resultado: bloqueado
Mensagem: Usuário sem permissão para criar novos usuários
```

Use nomes técnicos claros para regras:

- `can_create_user`
- `can_delete_record`
- `validate_required_fields`
- `check_duplicate_email`
- `apply_unit_scope_filter`
- `validate_date_range`

### 6.7. Console visual de erros

O debug deve capturar erros relevantes:

- Erro JavaScript.
- Erro PHP.
- Erro SQL.
- Erro de API.
- Erro de permissão.
- Erro de validação.
- Timeout.
- Resposta inesperada.
- Estado inconsistente.

Cada erro deve ter:

- Código.
- Mensagem amigável.
- Mensagem técnica.
- Arquivo/função, se disponível.
- Stack trace em ambiente de desenvolvimento.
- Trace ID.
- Ação que causou o erro.
- Dados mínimos para reprodução.

---

## 7. Como ativar o debug visual

### 7.1. Ativação por query string

Exemplo:

```text
https://sistema.com.br/usuarios?debug_mode=true
```

A tela deve ler o parâmetro, mas só ativar se o backend confirmar permissão.

### 7.2. Ativação por atalho

Exemplo:

```text
CTRL + SHIFT + D
```

O atalho pode abrir o painel, mas deve validar permissão antes de mostrar dados.

### 7.3. Ativação por menu administrativo

Dentro do painel admin:

```text
Configurações > Ferramentas de suporte > Ativar debug visual
```

### 7.4. Ativação temporária

O debug deve expirar automaticamente.

Boas regras:

- Expirar após 30 minutos.
- Expirar ao trocar de usuário.
- Expirar ao fazer logout.
- Expirar após fechar o navegador, se aplicável.

---

## 8. Níveis de permissão para debug

Crie permissões separadas.

Exemplo:

```text
debug.visual.view
debug.visual.inspect
debug.visual.network
debug.visual.payload
debug.visual.export
debug.visual.production
debug.visual.step_mode
```

Tabela recomendada:

| Perfil | Pode ativar debug | Pode ver API | Pode ver payload | Pode exportar relatório | Pode usar em produção |
|---|---:|---:|---:|---:|---:|
| Usuário comum | Não | Não | Não | Não | Não |
| Suporte | Sim, básico | Limitado | Não | Sim | Limitado |
| QA | Sim | Sim | Mascarado | Sim | Homologação |
| Desenvolvedor | Sim | Sim | Mascarado | Sim | Com autorização |
| Administrador técnico | Sim | Sim | Mascarado | Sim | Sim, auditado |

---

## 9. Dados que nunca devem aparecer no debug

Nunca exibir em texto aberto:

- Senhas.
- Tokens de acesso.
- Cookies de sessão.
- Chaves de API.
- CPF completo, quando não for necessário.
- Dados médicos sensíveis.
- Cartão de crédito.
- Segredos de ambiente.
- Headers de autenticação completos.
- SQL com dados pessoais completos.

Use mascaramento:

```text
CPF: 123.***.***-09
Token: eyJhbGci...***
Email: ro***@dominio.com
Telefone: (14) 9****-1234
```

---

## 10. Estrutura visual recomendada

### 10.1. Layout do debug

O debug pode ser dividido em quatro áreas:

1. Barra de controle.
2. Timeline de execução.
3. Inspetor lateral.
4. Detalhes técnicos da etapa selecionada.

Exemplo:

```text
┌──────────────────────────────────────────────────────────────┐
│ DEBUG MODE | Run | Run slow | Run next | Inspect | Export    │
├──────────────────────────────────────────────────────────────┤
│ Timeline:                                                    │
│ [01] Page loaded                      sucesso       120ms    │
│ [02] Click: Criar usuário             executando      ...    │
│ [03] Validate permissions             sucesso        15ms    │
│ [04] POST /api/usuarios               erro          340ms    │
├───────────────────────────────┬──────────────────────────────┤
│ Detalhes da etapa             │ Inspetor do elemento          │
│ Endpoint, payload, resposta   │ ID, estado, permissão, ação   │
└───────────────────────────────┴──────────────────────────────┘
```

### 10.2. Cores de status

Use cores consistentes:

- Cinza: aguardando.
- Azul: executando.
- Verde: sucesso.
- Amarelo: alerta.
- Vermelho: erro.
- Roxo: informação técnica.

Não depender apenas de cor. Sempre usar texto ou ícone junto.

### 10.3. Evitar atrapalhar a operação

O debug não pode impedir o uso normal da tela.

Boas práticas:

- Painel recolhível.
- Altura ajustável.
- Modo lateral ou inferior.
- Botão para esconder.
- Não cobrir botões críticos.
- Manter responsivo no desktop e mobile.

---

## 11. Eventos que toda tela deve registrar

### 11.1. Eventos de página

- Page loaded.
- Page ready.
- Page error.
- Component mounted.
- Component updated.
- Component hidden.
- Component disabled.

### 11.2. Eventos de usuário

- Click.
- Double click.
- Submit.
- Change input.
- Select option.
- Open modal.
- Close modal.
- Apply filter.
- Clear filter.
- Pagination.
- Upload file.
- Download file.

### 11.3. Eventos de dados

- Fetch started.
- Fetch success.
- Fetch empty.
- Fetch error.
- Save started.
- Save success.
- Save error.
- Delete requested.
- Delete confirmed.
- Delete canceled.

### 11.4. Eventos de permissão

- Permission checked.
- Permission allowed.
- Permission denied.
- Scope applied.
- Role mismatch.

### 11.5. Eventos de regra de negócio

- Validation started.
- Validation passed.
- Validation failed.
- Business rule applied.
- Business rule blocked.
- Duplicate detected.
- Required field missing.

---

## 12. Padrão técnico para registrar ações

Toda ação registrada deve seguir um formato único.

Exemplo em JSON:

```json
{
  "id": "evt_0001",
  "trace_id": "trace_abc123",
  "screen": "controle_usuarios",
  "component": "btn_criar_usuario",
  "event_type": "click",
  "action": "open_create_user_modal",
  "status": "success",
  "started_at": "2026-07-02T16:30:00-03:00",
  "duration_ms": 42,
  "user_id": 15,
  "user_role": "admin",
  "metadata": {
    "button_label": "Criar usuário",
    "modal": "create_user"
  }
}
```

Campos recomendados:

- `id`
- `trace_id`
- `screen`
- `route`
- `component`
- `event_type`
- `action`
- `status`
- `started_at`
- `finished_at`
- `duration_ms`
- `user_id`
- `user_role`
- `metadata`
- `error`

---

## 13. Trace ID obrigatório

Cada carregamento de página deve gerar um `trace_id` único.

Esse ID deve acompanhar:

- Logs do frontend.
- Requisições de API.
- Logs do backend.
- Erros.
- Relatório exportado.
- Auditoria.

Exemplo:

```text
Trace ID: usr-20260702-163000-a8f91c
```

Com o `trace_id`, o programador consegue procurar o mesmo fluxo no frontend, backend, banco e logs do servidor.

---

## 14. Relatório de debug copiável

Toda tela com debug deve ter botão:

```text
Copiar relatório técnico
```

O relatório deve conter:

```text
Tela: Controle de Usuários
URL: /g_controle_usuarios?debug_mode=true
Ambiente: homologação
Usuário: admin@sistema.com.br
Perfil: administrador
Trace ID: trace_abc123
Horário: 2026-07-02 16:30:00
Navegador: Chrome
Sistema operacional: Windows
Última ação: Criar usuário
Erro: Email já cadastrado
Endpoint: POST /api/usuarios
Status HTTP: 422
Tempo de resposta: 340ms
Passos executados:
1. Page loaded
2. Click Criar usuário
3. Validate form
4. Check permission
5. POST /api/usuarios
6. API returned validation error
```

O relatório deve ser fácil de colar em tarefa, issue, chamado, WhatsApp interno ou GitHub.

---

## 15. Boas práticas para implementação em HTML, CSS, JavaScript, PHP e MySQL

### 15.1. HTML

Use atributos técnicos nos elementos importantes.

Exemplo:

```html
<button
  id="btn-criar-usuario"
  data-debug-name="Botão Criar Usuário"
  data-debug-component="UserCreateButton"
  data-debug-action="open_create_user_modal"
  data-debug-permission="usuarios.criar">
  Criar usuário
</button>
```

Boas práticas:

- Todo botão importante deve ter `id` claro.
- Todo formulário deve ter nome técnico.
- Toda tabela deve ter identificador.
- Todo modal deve ter identificador.
- Todo componente crítico deve ter `data-debug-*`.

### 15.2. CSS

O debug deve ter estilo isolado para não quebrar a tela.

Boas práticas:

- Prefixar classes com `.debug-`.
- Usar `z-index` controlado.
- Não alterar layout principal sem necessidade.
- Usar painel fixo e recolhível.
- Evitar CSS genérico que afete o sistema inteiro.

Exemplo:

```css
.debug-panel {
  position: fixed;
  left: 0;
  right: 0;
  bottom: 0;
  max-height: 40vh;
  overflow: auto;
  background: #111;
  color: #fff;
  z-index: 9999;
  font-size: 12px;
}
```

### 15.3. JavaScript

Crie uma camada única de debug.

Exemplo conceitual:

```js
window.AppDebug = {
  enabled: false,
  traceId: null,
  events: [],

  init(config) {
    this.enabled = config.enabled === true;
    this.traceId = config.traceId;
  },

  log(event) {
    if (!this.enabled) return;

    const item = {
      id: crypto.randomUUID(),
      trace_id: this.traceId,
      time: new Date().toISOString(),
      ...event
    };

    this.events.push(item);
    this.render(item);
  },

  render(item) {
    // Atualizar timeline visual do debug
  }
};
```

Boas práticas:

- Não espalhar `console.log` solto pelo sistema.
- Usar uma função central: `debugLog()` ou `AppDebug.log()`.
- Capturar erros globais com `window.onerror`.
- Capturar falhas de promises com `unhandledrejection`.
- Interceptar chamadas `fetch` quando debug estiver ativo.
- Mascarar dados sensíveis antes de renderizar.

### 15.4. PHP

O PHP deve decidir se o debug pode ser ativado.

Exemplo conceitual:

```php
$debugRequested = isset($_GET['debug_mode']) && $_GET['debug_mode'] === 'true';
$debugAllowed = $debugRequested && usuarioTemPermissao($usuarioLogado, 'debug.visual.view');

$debugConfig = [
    'enabled' => $debugAllowed,
    'trace_id' => gerarTraceId(),
    'environment' => getenv('APP_ENV'),
    'user_id' => $usuarioLogado['id'],
    'role' => $usuarioLogado['perfil'],
    'permissions' => listarPermissoesDebug($usuarioLogado)
];
```

Depois enviar para o frontend:

```html
<script>
  window.DEBUG_CONFIG = <?= json_encode($debugConfig, JSON_UNESCAPED_UNICODE); ?>;
</script>
```

Boas práticas:

- Validar permissão no backend.
- Gerar `trace_id` no backend.
- Registrar auditoria ao ativar debug.
- Nunca enviar segredos para o frontend.
- Em produção, limitar dados técnicos.

### 15.5. MySQL

Crie tabelas para auditoria e logs quando necessário.

Exemplo:

```sql
CREATE TABLE debug_audit_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  trace_id VARCHAR(100) NOT NULL,
  user_id BIGINT NOT NULL,
  screen VARCHAR(150) NOT NULL,
  url TEXT NOT NULL,
  environment VARCHAR(50) NOT NULL,
  action VARCHAR(100) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

Tabela opcional para eventos técnicos:

```sql
CREATE TABLE debug_events (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  trace_id VARCHAR(100) NOT NULL,
  user_id BIGINT NULL,
  screen VARCHAR(150) NOT NULL,
  component VARCHAR(150) NULL,
  event_type VARCHAR(80) NOT NULL,
  action VARCHAR(150) NOT NULL,
  status VARCHAR(50) NOT NULL,
  duration_ms INT NULL,
  metadata JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_trace_id (trace_id),
  INDEX idx_screen (screen),
  INDEX idx_created_at (created_at)
);
```

Cuidados:

- Não salvar dados sensíveis em `metadata`.
- Definir política de retenção.
- Apagar logs antigos automaticamente.
- Indexar `trace_id`.
- Evitar gravar evento demais em produção.

---

## 16. Interceptação de chamadas de API no frontend

Quando possível, centralize todas as chamadas de API em uma função.

Exemplo:

```js
async function apiRequest(url, options = {}) {
  const start = performance.now();

  AppDebug.log({
    event_type: 'api_start',
    action: 'api_request',
    status: 'running',
    metadata: {
      method: options.method || 'GET',
      url
    }
  });

  try {
    const response = await fetch(url, options);
    const duration = Math.round(performance.now() - start);

    AppDebug.log({
      event_type: 'api_end',
      action: 'api_request',
      status: response.ok ? 'success' : 'error',
      duration_ms: duration,
      metadata: {
        method: options.method || 'GET',
        url,
        http_status: response.status
      }
    });

    return response;
  } catch (error) {
    const duration = Math.round(performance.now() - start);

    AppDebug.log({
      event_type: 'api_error',
      action: 'api_request',
      status: 'error',
      duration_ms: duration,
      error: {
        message: error.message
      }
    });

    throw error;
  }
}
```

---

## 17. Debug de formulários

Todo formulário importante deve mostrar no debug:

- Campos preenchidos.
- Campos obrigatórios faltando.
- Validações executadas.
- Erros por campo.
- Transformações feitas antes do envio.
- Payload final mascarado.
- Resposta da API.

Exemplo de fluxo:

```text
[01] Open modal: Criar usuário
[02] Fill field: email
[03] Fill field: nome
[04] Submit form
[05] Validate required fields
[06] Validate email format
[07] Check permission usuarios.criar
[08] Send POST /api/usuarios
[09] API returned 422: email already exists
[10] Show field error: email
```

---

## 18. Debug de tabelas, filtros e paginação

Telas com lista devem registrar:

- Filtros aplicados.
- Ordenação.
- Página atual.
- Quantidade por página.
- Total de registros.
- Query enviada para API.
- Tempo de carregamento.
- Estado vazio.
- Erro de busca.

Exemplo:

```text
Tabela: usuarios_table
Filtro email: rodrigo@sistema.com.br
Página: 1
Linhas por página: 100
Total retornado: 0
Estado: empty
Mensagem: Lista vazia, nada encontrado
```

---

## 19. Debug de permissões

Toda ação sensível deve registrar a checagem de permissão.

Exemplo:

```text
Ação: criar usuário
Permissão exigida: usuarios.criar
Perfil atual: administrador
Resultado: permitido
```

Quando bloqueado:

```text
Ação: excluir usuário
Permissão exigida: usuarios.excluir
Perfil atual: analista
Resultado: negado
Motivo: perfil sem permissão
```

---

## 20. Debug de estados vazios

Quando uma tela mostra “lista vazia”, o debug precisa explicar o motivo provável.

Verificar:

- A API retornou zero registros?
- O filtro está restringindo demais?
- O usuário não tem permissão para ver registros?
- O backend retornou erro silencioso?
- A tabela falhou ao renderizar?
- O ambiente está apontando para banco vazio?

Exemplo:

```text
Estado vazio detectado
Endpoint: GET /api/usuarios
Status: 200
Total retornado: 0
Filtros ativos: email = rodrigo@sistema.com.br
Permissão aplicada: unidade_id IN (3, 5, 7)
Conclusão provável: nenhum usuário encontrado com os filtros atuais
```

---

## 21. Debug de comportamento instável

Para bugs intermitentes, registre contexto adicional:

- Latência da API.
- Estado anterior e posterior.
- Cliques repetidos.
- Duplo submit.
- Mudança rápida de filtro.
- Falha de rede.
- Sessão expirada.
- Permissão alterada.
- Dados carregados fora de ordem.

Boas práticas:

- Bloquear duplo clique em ações críticas.
- Usar `loading` explícito.
- Cancelar requisições antigas quando filtro mudar.
- Ignorar resposta antiga se uma nova busca já foi feita.
- Registrar ordem das requisições.

---

## 22. Debug de performance

Registre tempo de execução de:

- Carregamento inicial.
- Chamada de API.
- Renderização de tabela.
- Abertura de modal.
- Envio de formulário.
- Exportação de arquivo.
- Processamento em lote.

Classificação sugerida:

```text
0ms a 300ms: rápido
301ms a 1000ms: aceitável
1001ms a 3000ms: lento
Acima de 3000ms: crítico
```

---

## 23. Debug em produção

Em produção, o debug deve ser mais restrito.

Permitido:

- Trace ID.
- Timeline simplificada.
- Status de API.
- Tempo de resposta.
- Mensagens técnicas controladas.
- Relatório mascarado.

Não permitido:

- Stack trace completo para qualquer usuário.
- Tokens.
- Queries com dados pessoais.
- Dumps de objetos completos.
- Informações de infraestrutura sensível.

Ativar debug em produção deve gerar auditoria.

Exemplo:

```text
Usuário admin@sistema.com.br ativou debug técnico na tela /usuarios em produção às 16:30.
Trace ID: trace_abc123
IP: registrado no backend
```

---

## 24. Checklist para criar uma tela já preparada para debug

Antes de considerar uma tela pronta, verificar:

- [ ] A tela possui nome técnico único.
- [ ] A tela gera `trace_id`.
- [ ] O backend valida permissão de debug.
- [ ] Existe painel visual de debug.
- [ ] A tela registra carregamento inicial.
- [ ] Botões importantes possuem `data-debug-*`.
- [ ] Formulários registram validações.
- [ ] Chamadas de API são centralizadas.
- [ ] Erros são capturados e exibidos no debug.
- [ ] Dados sensíveis são mascarados.
- [ ] Estados vazios são explicados.
- [ ] Permissões são registradas.
- [ ] Há botão para copiar relatório.
- [ ] Em produção, o debug é limitado e auditado.
- [ ] O painel não quebra o layout responsivo.
- [ ] O debug pode ser desligado rapidamente.
- [ ] O debug pode ser removido da tela ou do sistema sem quebrar a regra de negócio.

---

## 25. Checklist para corrigir bug usando debug visual

Ao investigar um bug:

1. Ativar `debug_mode=true`.
2. Reproduzir o comportamento relatado.
3. Observar a timeline.
4. Identificar última ação antes do erro.
5. Abrir detalhes da etapa com erro.
6. Verificar estado anterior e posterior.
7. Verificar payload enviado.
8. Verificar resposta da API.
9. Verificar permissões aplicadas.
10. Verificar filtros ou dados ocultos.
11. Copiar relatório técnico.
12. Criar tarefa de correção com trace ID.
13. Corrigir o problema.
14. Reexecutar o mesmo fluxo.
15. Registrar resultado no chamado ou tarefa.

---

## 26. Padrão de nomenclatura

Use nomes técnicos claros e padronizados.

### 26.1. Telas

```text
controle_usuarios
cadastro_cliente
lista_agendamentos
detalhe_vacinado
relatorio_faturamento
```

### 26.2. Componentes

```text
btn_criar_usuario
modal_editar_usuario
form_cadastro_cliente
table_usuarios
filter_email
card_total_registros
```

### 26.3. Ações

```text
load_users
open_create_user_modal
submit_create_user_form
validate_user_permission
apply_user_filters
export_users_report
```

### 26.4. Eventos

```text
page_loaded
button_clicked
form_submitted
api_request_started
api_request_finished
business_rule_failed
permission_denied
empty_state_detected
```

---

## 27. Regras para refatoramento de sistemas existentes

Ao aplicar esta skill em uma tela já existente:

1. Não refatorar tudo de uma vez sem necessidade.
2. Identificar primeiro os fluxos críticos.
3. Criar camada central de debug.
4. Adicionar `trace_id`.
5. Centralizar chamadas de API.
6. Instrumentar botões, formulários e tabelas.
7. Capturar erros globais.
8. Adicionar relatório copiável.
9. Testar em homologação.
10. Só liberar em produção com permissão e auditoria.

Prioridade de instrumentação:

1. Login e autenticação.
2. Cadastro e edição de dados.
3. Telas financeiras.
4. Telas com integrações externas.
5. Telas com filtros complexos.
6. Telas com regras de permissão.
7. Relatórios e exportações.
8. Processos em lote.

---

## 28. Instalação, desinstalação e remoção global do debug visual

O debug visual deve ser planejado como uma camada técnica **instalável, removível e controlada**, para permitir uso em telas importantes durante desenvolvimento, homologação, suporte e QA, sem obrigar o sistema a carregar código de debug para sempre.

A IA deve implementar o debug de forma modular, permitindo:

- instalar debug em uma tela específica;
- instalar debug nas telas críticas do sistema;
- desativar debug por ambiente;
- remover debug de uma tela específica;
- remover debug de todas as telas antes de otimizações finais, quando o projeto exigir;
- manter logs e auditoria oficiais mesmo quando o painel visual for removido.

### 28.1. Princípio de instalação

O debug visual deve ser instalado primeiro nas telas importantes do sistema.

Telas importantes são aquelas que possuem:

- login, cadastro, recuperação de senha ou sessão;
- permissões e perfis de usuário;
- painel administrativo;
- cadastro, edição ou exclusão de dados críticos;
- upload de arquivos;
- checkout, pagamento, voucher, assinatura ou regra financeira;
- dashboards, relatórios e exportações;
- integrações externas, webhooks ou APIs críticas;
- filtros complexos, paginação, tabelas grandes ou busca;
- mudança de status, aprovação, cancelamento ou workflow;
- processos em lote, importação, sincronização ou ações demoradas;
- histórico, auditoria ou dados sensíveis.

Telas simples, institucionais, estáticas ou de baixo risco não precisam receber debug visual completo.

### 28.2. Formas de instalar em uma tela

A IA deve permitir instalação por tela usando um padrão claro.

Exemplo de configuração por tela:

```php
$debugScreenConfig = [
    'screen' => 'controle_usuarios',
    'debug_enabled_for_screen' => true,
    'debug_level' => 'technical',
    'debug_modules' => [
        'timeline',
        'api',
        'state',
        'permissions',
        'errors',
        'copy_report'
    ]
];
```

A tela só deve carregar o debug se todas as condições forem verdadeiras:

```txt
debug solicitado
+ debug permitido no ambiente
+ tela permite debug
+ usuário autenticado
+ usuário com permissão
+ backend autorizou
```

### 28.3. Arquivos de debug devem ser isolados

O código de debug não deve ficar misturado de forma irreversível na tela.

Estrutura recomendada:

```txt
/public/assets/js/debug/
  app-debug.js
  debug-panel.js
  debug-timeline.js
  debug-network.js
  debug-state.js
  debug-report.js

/public/assets/css/debug/
  debug-panel.css

/app/helpers/
  debug_helper.php
```

A tela deve apenas chamar a camada de debug quando necessário.

Exemplo:

```php
<?php if ($debugConfig['enabled'] === true): ?>
  <link rel="stylesheet" href="/assets/css/debug/debug-panel.css">
  <script type="module" src="/assets/js/debug/app-debug.js"></script>
<?php endif; ?>
```

Essa abordagem permite remover o debug sem destruir a tela principal.

### 28.4. Comentários marcadores para instalação e remoção

Todo trecho de debug inserido em uma tela deve usar comentários marcadores.

Exemplo em HTML/PHP:

```php
<!-- DEBUG_VISUAL_START -->
<?php if ($debugConfig['enabled'] === true): ?>
  <div id="debug-panel-root"></div>
  <script>
    window.DEBUG_CONFIG = <?= json_encode($debugConfig, JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <script type="module" src="/assets/js/debug/app-debug.js"></script>
<?php endif; ?>
<!-- DEBUG_VISUAL_END -->
```

Exemplo em JavaScript:

```js
// DEBUG_VISUAL_START
AppDebug.log({
  event_type: 'form_submitted',
  action: 'submit_create_user_form',
  status: 'running'
});
// DEBUG_VISUAL_END
```

Esses marcadores permitem localizar e remover rapidamente o debug de uma tela ou do sistema inteiro.

### 28.5. Manifesto de telas com debug instalado

Projetos com debug visual devem manter um manifesto simples informando onde o debug foi instalado.

Arquivo recomendado:

```txt
/docs/debug/debug-manifest.md
```

Modelo:

```md
# Manifesto de Debug Visual

| Tela | Arquivo | Nível | Módulos | Status | Observação |
|---|---|---|---|---|---|
| Login | /public/login.php | básico | erros, sessão | instalado | crítico |
| Usuários | /public/admin/usuarios.php | técnico | timeline, api, permissões | instalado | painel admin |
| Pagamentos | /public/admin/pagamentos.php | técnico | api, regras, erros | instalado | financeiro |
| Relatórios | /public/admin/relatorios.php | básico | filtros, performance | instalado | dashboard |
```

O manifesto deve ajudar a IA a saber:

- onde o debug existe;
- qual nível está ativo;
- qual arquivo precisa ser alterado;
- quais telas podem ter o debug removido depois.

### 28.6. Como desinstalar debug de uma tela específica

Quando o usuário pedir para remover debug de uma tela, a IA deve:

1. localizar a tela no manifesto;
2. remover carregamento dos arquivos de debug naquela tela;
3. remover ou desativar blocos entre `DEBUG_VISUAL_START` e `DEBUG_VISUAL_END`;
4. manter validações reais, segurança, logs e auditoria;
5. testar se a tela continua funcionando;
6. atualizar o manifesto.

A IA nunca deve remover:

- validação de permissão no backend;
- logs oficiais;
- auditoria;
- tratamento de erros;
- segurança;
- regra de negócio.

Remover debug visual não significa remover rastreabilidade oficial do sistema.

### 28.7. Como desinstalar debug de todas as telas

Quando o projeto estiver estável e o usuário quiser otimizar o sistema, a IA deve permitir remoção global do debug visual.

Processo recomendado:

1. criar backup ou branch antes da remoção;
2. consultar o manifesto de debug;
3. remover imports, scripts, CSS e containers visuais de debug;
4. remover blocos marcados com `DEBUG_VISUAL_START` e `DEBUG_VISUAL_END`;
5. remover arquivos de debug visual não usados;
6. manter logs oficiais, auditoria, erros e monitoramento;
7. rodar testes funcionais nas telas críticas;
8. validar performance antes/depois;
9. atualizar documentação;
10. registrar o motivo da remoção.

### 28.8. Modo de desativação sem remover código

Antes de remover fisicamente o debug, a IA pode implementar um modo de desativação global por configuração.

Exemplo no `.env`:

```env
DEBUG_VISUAL_ENABLED=false
DEBUG_VISUAL_ALLOW_PRODUCTION=false
```

Exemplo em PHP:

```php
function debugVisualGlobalAtivo() {
    return getenv('DEBUG_VISUAL_ENABLED') === 'true';
}
```

Esse modo é útil quando o usuário quer parar de carregar o debug, mas ainda quer manter o código disponível para manutenção futura.

### 28.9. Remoção física para otimização máxima

Quando o usuário pedir otimização máxima, a IA deve remover fisicamente:

- arquivos CSS do painel visual;
- arquivos JavaScript do painel visual;
- containers HTML do debug;
- interceptadores visuais de eventos;
- botões de debug;
- painéis de timeline;
- inspetores visuais;
- relatórios copiáveis específicos do debug.

Mas deve preservar:

- logs de erro;
- logs de auditoria;
- `request_id` ou `trace_id` quando usados por logs oficiais;
- tratamento de exceções;
- validações de backend;
- permissões;
- segurança;
- monitoramento.

### 28.10. Checklist para instalar debug em tela importante

Antes de concluir a instalação:

- [ ] A tela é realmente importante ou crítica?
- [ ] O debug está isolado em arquivos próprios?
- [ ] Existe validação de permissão no backend?
- [ ] O debug pode ser desligado por configuração?
- [ ] Os trechos têm marcadores `DEBUG_VISUAL_START` e `DEBUG_VISUAL_END`?
- [ ] A tela foi registrada no manifesto?
- [ ] Dados sensíveis estão mascarados?
- [ ] O usuário comum não consegue ver nem ativar?
- [ ] A tela funciona normalmente com debug desligado?

### 28.11. Checklist para remover debug visual

Antes de concluir a remoção:

- [ ] Foi criado backup, commit ou branch antes da remoção?
- [ ] O manifesto foi consultado?
- [ ] Scripts e CSS de debug foram removidos da tela?
- [ ] Blocos marcados foram removidos ou desativados?
- [ ] Nenhuma regra de segurança foi removida por engano?
- [ ] Logs oficiais continuam funcionando?
- [ ] Auditoria continua funcionando?
- [ ] Tratamento de erro continua funcionando?
- [ ] A tela foi testada sem debug?
- [ ] A documentação foi atualizada?
- [ ] A performance foi comparada antes e depois quando necessário?

### 28.12. Regra final sobre instalação e remoção

O debug visual deve ser fácil de instalar em telas críticas e fácil de remover quando não for mais necessário.

A IA deve tratar debug visual como uma camada auxiliar de diagnóstico, não como dependência obrigatória da regra de negócio.

Se a remoção do debug quebrar a tela, significa que o debug foi implementado errado.

---

## 29. Critérios de qualidade

Uma implementação de debug visual é boa quando:

- Ajuda a encontrar bug mais rápido.
- Explica o que aconteceu sem depender de adivinhação.
- Não expõe dados sensíveis.
- Não prejudica a experiência do usuário comum.
- Funciona em desenvolvimento e homologação.
- Pode ser usado em produção de forma controlada.
- Gera relatório técnico útil.
- Conecta frontend, backend e logs pelo mesmo `trace_id`.
- Ajuda suporte, QA e programação a falarem a mesma língua.

---

## 30. Anti-padrões que devem ser evitados

Evite:

- Deixar `console.log` espalhado no código.
- Exibir erro técnico para usuário comum.
- Ativar debug apenas pelo frontend.
- Mostrar senha, token ou dados sensíveis.
- Criar painel de debug que quebra layout.
- Registrar logs demais em produção.
- Não ter botão para copiar relatório.
- Não ter trace ID.
- Usar nomes genéricos como `button1`, `div2`, `action3`.
- Tratar lista vazia como se fosse sempre sucesso.
- Ignorar erros de API.
- Ocultar falhas silenciosamente.
- Criar mensagens como “erro desconhecido” sem contexto técnico no debug.

---

## 31. Prompt base para IA implementar debug visual em uma tela

Use este prompt quando quiser que uma IA implemente ou refatore uma tela com debug visual:

```text
Aja como um programador sênior especialista em SaaS, app, refatoramento, correção de bugs e observabilidade.

Implemente nesta tela uma camada de debug visual administrativo inspirada no debug mode do Bubble.

Regras obrigatórias:
1. O debug só pode aparecer para usuários autenticados com permissão específica validada no backend.
2. A ativação pode ocorrer por ?debug_mode=true, mas o backend deve confirmar autorização.
3. Criar trace_id único por carregamento de página.
4. Criar painel visual fixo e recolhível com controles: Run, Run slow, Run next, Inspect, Copy report e Stop debug.
5. Criar timeline de ações passo a passo.
6. Registrar cliques, submits, filtros, abertura de modal, chamadas de API, validações, permissões e erros.
7. Criar inspetor de elementos usando atributos data-debug-*.
8. Criar painel de estado da tela.
9. Interceptar chamadas de API feitas pela tela e registrar método, endpoint, status, tempo e erro.
10. Mascarar dados sensíveis como senha, token, CPF, telefone, email e headers de autenticação.
11. Criar botão para copiar relatório técnico com tela, URL, ambiente, usuário, perfil, trace_id, navegador, última ação, erro e passos executados.
12. O debug não pode quebrar o layout, nem aparecer para usuário comum.
13. Em produção, limitar detalhes e registrar auditoria de ativação.
14. Implementar o debug de forma modular, com possibilidade de instalar, desativar ou remover da tela sem quebrar a regra de negócio.
15. Usar marcadores `DEBUG_VISUAL_START` e `DEBUG_VISUAL_END` nos trechos específicos de debug quando fizer sentido.

Ao finalizar, entregue:
- Arquivos alterados.
- Explicação do fluxo de debug.
- Como ativar.
- Como testar.
- Checklist de segurança.
```

---

## 32. Modelo mínimo de painel visual

O painel mínimo deve conter:

```text
DEBUG MODE: ON | Ambiente: homologação | Trace ID: trace_abc123
[Run] [Run slow] [Run next] [Inspect] [Copy report] [Stop]

Timeline:
01 - Page loaded - success - 120ms
02 - Fetch users - success - 340ms
03 - Empty state detected - warning - 0 records

Details:
Selected event: Empty state detected
Reason: API returned zero records with active filters
```

---

## 33. Definição de pronto

Considere o debug visual pronto quando:

- Usuário comum não consegue ver nem ativar.
- Administrador autorizado consegue ativar.
- O painel mostra fluxo real de execução.
- O inspetor identifica componentes importantes.
- API, estado, permissões e erros aparecem no debug.
- Dados sensíveis são mascarados.
- Relatório técnico pode ser copiado.
- Existe trace ID conectando frontend e backend.
- O recurso foi testado em desenvolvimento e homologação.
- Produção possui modo restrito e auditado.

---

## 34. Resultado esperado

Ao seguir esta skill, qualquer tela criada ou refatorada deverá permitir que uma pessoa técnica consiga olhar o próprio sistema funcionando e entender:

- O que aconteceu.
- Onde aconteceu.
- Quando aconteceu.
- Quem executou.
- Qual dado entrou.
- Qual regra decidiu.
- Qual API respondeu.
- Qual erro ocorreu.
- Como reproduzir.
- Como corrigir.

O debug visual deve ser tratado como parte da arquitetura do sistema, não como improviso depois que o bug aparece.
