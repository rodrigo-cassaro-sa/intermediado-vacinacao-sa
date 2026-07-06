# Skill — Boas Práticas para Desenvolvimento de App, SaaS, Software e PWA Offline-First

## Limite desta skill

Esta skill define quando e como um sistema web deve se comportar como aplicativo/PWA, incluindo instalação, cache, offline, fila local, sincronização e recursos nativos.

Ela pode citar frontend, backend, API, segurança, notificações e performance apenas quando isso estiver relacionado à experiência PWA/offline.

Ela não deve substituir:

- `skill-responsividade.md` para adaptação visual por dispositivo;
- `skill-frontend.md` e `skill-js.md` para implementação completa da interface;
- `skill-api-rest.md` para contrato geral das APIs;
- `skill-backend.md` para regras de negócio do servidor;
- `skill-seguranca.md` e `skill-lgpd-privacidade.md` para proteção completa de dados;
- `skill-notificacoes.md` para estratégia completa de comunicação;
- `skill-performance.md` para otimização geral.

Esta skill responde "o sistema precisa se comportar como app instalável, confiável e resiliente mesmo com conexão ruim?".

---

## 1. Identidade da Skill

**Nome da skill:** `boas-praticas-pwa-offline-saas`  
**Área:** Desenvolvimento de SaaS, software web, app PWA, app instalável, modo offline e integração com recursos nativos do dispositivo.  
**Perfil esperado da IA:** Desenvolvedor sênior especialista em arquitetura web, SaaS, PWA, UX mobile, segurança, APIs, banco de dados, sincronização offline e manutenção de sistemas em produção.

Esta skill deve ser usada sempre que uma IA for criar, revisar, refatorar, planejar ou implementar um sistema web, SaaS, app mobile via PWA ou software que precise se comportar como aplicativo, inclusive com instalação, acesso a recursos do dispositivo, cache, fila offline, sincronização e boa experiência em conexão instável.

---

## 2. Objetivo Principal

Desenvolver sistemas web e SaaS com qualidade de aplicativo moderno, garantindo que o produto:

- Funcione bem em desktop, celular e tablet.
- Possa ser instalado como PWA quando fizer sentido.
- Tenha comportamento confiável com internet lenta, instável ou ausente.
- Use recursos nativos do dispositivo com permissão clara do usuário.
- Proteja dados sensíveis mesmo com armazenamento local.
- Tenha arquitetura preparada para sincronização, conflitos, reenvio e rastreabilidade.
- Seja fácil de manter, testar e evoluir.

A regra central é: **não desenvolver apenas uma página web responsiva; desenvolver uma experiência de aplicativo confiável, segura e resiliente.**

---

## 3. Princípios Obrigatórios

### 3.1. Pensar como aplicativo, não apenas como site

Antes de programar qualquer tela ou função, avaliar:

- O usuário vai acessar pelo celular?
- O sistema precisa funcionar no campo, na rua, em evento, clínica, loja, sala de aula ou local com internet ruim?
- O usuário pode iniciar uma tarefa online e terminar offline?
- O sistema precisa guardar dados temporariamente no dispositivo?
- O sistema precisa capturar foto, localização, arquivo, assinatura, áudio, notificação ou compartilhar conteúdo?
- O sistema precisa ser instalável na tela inicial?

Se a resposta for sim para qualquer item, o projeto deve ser tratado como **PWA/app-like** desde a arquitetura inicial.

### 3.2. Offline-first quando a operação não pode parar

Usar abordagem offline-first quando o sistema envolve:

- Cadastro em campo.
- Coleta de dados.
- Checklist.
- Vendas externas.
- Vacinação, agenda, rota, vistoria ou atendimento.
- Formulários longos.
- Registro de presença.
- Upload de evidência.
- Tarefas críticas que não podem ser perdidas.

A regra é: **a ação do usuário deve ser salva localmente primeiro e sincronizada com o servidor depois, com status visível.**

### 3.3. Online-first quando o dado exige validação imediata

Nem tudo deve funcionar offline. Usar online-first quando houver:

- Pagamento.
- Autorização bancária.
- Validação de estoque em tempo real.
- Dados jurídicos ou financeiros críticos.
- Operações que dependem de bloqueio imediato no servidor.
- Permissões sensíveis que podem mudar a qualquer momento.

Mesmo nesses casos, a tela deve explicar claramente que a operação exige internet.

### 3.4. Progressive enhancement

Recursos avançados devem melhorar a experiência, mas não quebrar o sistema se não existirem no navegador.

Exemplo:

- Se Background Sync estiver disponível, usar para reenviar filas em segundo plano.
- Se não estiver disponível, sincronizar quando o usuário abrir o app novamente.
- Se Push Notification não estiver disponível, usar aviso dentro do sistema, e-mail ou WhatsApp conforme regra do produto.
- Se câmera nativa não estiver disponível, permitir upload manual de arquivo.

Nunca depender cegamente de API nativa sem verificar suporte.

---

## 4. Decisão de Arquitetura: Site, SaaS, PWA ou App Nativo

### 4.1. Usar site comum quando

- O objetivo for institucional, landing page, blog, catálogo ou conteúdo público.
- Não houver necessidade real de login frequente.
- Não houver operação offline.
- Não houver necessidade de instalação.

### 4.2. Usar SaaS web responsivo quando

- O sistema for usado principalmente em navegador.
- Existirem login, permissões, dashboard, relatórios e operação administrativa.
- O foco principal for produtividade e gestão.
- O modo offline não for crítico.

### 4.3. Usar PWA quando

- O usuário acessa muito pelo celular.
- O sistema precisa parecer app.
- O sistema precisa ser instalável.
- O sistema precisa abrir rápido.
- Existe necessidade de cache, modo offline, fila local ou notificações.
- O custo e velocidade de desenvolvimento tornam melhor evitar apps nativos separados.

### 4.4. Considerar app nativo ou híbrido quando

- O produto depende fortemente de recursos nativos não suportados bem por navegador.
- Precisa rodar tarefas em background com alta confiabilidade.
- Precisa integração profunda com Bluetooth, NFC, sensores, biometria avançada, contatos, chamadas ou recursos específicos do sistema operacional.
- Precisa publicação completa e otimizada em lojas.
- A experiência PWA não atende requisitos de negócio.

---

## 5. Checklist Inicial Antes de Desenvolver

Antes de criar código, responder:

1. O sistema será usado em desktop, celular, tablet ou todos?
2. O usuário precisa instalar na tela inicial?
3. Quais telas precisam funcionar offline?
4. Quais dados podem ser salvos localmente?
5. Quais dados nunca devem ser salvos localmente?
6. O usuário pode criar, editar ou excluir dados offline?
7. Como resolver conflito se dois usuários alterarem o mesmo registro?
8. Como mostrar ao usuário o que está sincronizado, pendente ou com erro?
9. Quais recursos nativos serão usados?
10. Quais navegadores e sistemas operacionais precisam ser suportados?
11. Como será feita a autenticação quando o usuário estiver offline?
12. O logout deve apagar dados locais?
13. Existe exigência de LGPD, auditoria ou retenção de logs?
14. Como a aplicação será atualizada sem quebrar usuários com cache antigo?
15. Como testar modo avião, internet ruim e troca de versão?

Nenhum PWA sério deve começar sem essas respostas mínimas.

---

## 6. Estrutura Recomendada do Projeto

Exemplo genérico para projeto SaaS/PWA com HTML, CSS, JavaScript, PHP e MySQL:

```txt
/app
  /assets
    /css
    /js
    /icons
    /images
  /components
  /pages
  /pwa
    manifest.json
    service-worker.js
    offline.html
    cache-config.js
  /storage
    indexeddb.js
    local-cache.js
    sync-queue.js
  /api
    v1/
      auth.php
      sync.php
      users.php
      records.php
  /includes
    config.php
    database.php
    auth.php
    response.php
    validation.php
  /logs
  index.php
```

Regras:

- Separar arquivos de tela, API, cache, fila offline e regras de negócio.
- Não misturar lógica crítica dentro de HTML solto.
- Centralizar chamadas HTTP em um módulo único.
- Centralizar manipulação de IndexedDB/localStorage em camada própria.
- Criar camada de sincronização independente da tela.
- Criar componentes reutilizáveis para status offline, erro, loading, retry e pendências.

---

## 7. Manifesto PWA

Todo PWA deve ter `manifest.json` bem definido.

Exemplo base:

```json
{
  "name": "Nome Completo do Sistema",
  "short_name": "Sistema",
  "description": "Descrição curta do aplicativo.",
  "start_url": "/?source=pwa",
  "scope": "/",
  "display": "standalone",
  "orientation": "portrait-primary",
  "background_color": "#ffffff",
  "theme_color": "#111111",
  "icons": [
    {
      "src": "/assets/icons/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/assets/icons/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

Boas práticas:

- Usar ícones em 192x192 e 512x512.
- Usar `display: standalone` para experiência parecida com app.
- Configurar `theme_color` coerente com a identidade visual.
- Configurar `start_url` para abrir a área correta do sistema.
- Garantir que todas as páginas principais referenciem o manifesto.
- Testar instalação no Android, iOS/iPadOS e desktop quando estes forem públicos-alvo.
- Criar ícones adaptados para fundo claro e escuro, evitando cortes.

---

## 8. Service Worker

O `service-worker.js` é o coração do PWA.

Ele pode:

- Interceptar requisições.
- Responder com cache.
- Salvar arquivos estáticos.
- Criar fallback offline.
- Melhorar performance.
- Ajudar em sincronização e notificações, quando suportado.

### 8.1. Regras obrigatórias

- Registrar service worker somente em HTTPS ou localhost.
- Versionar cache.
- Limpar caches antigos no evento `activate`.
- Nunca cachear dados sensíveis sem necessidade.
- Nunca cachear respostas privadas de API como se fossem públicas.
- Criar fallback para navegação offline.
- Testar atualização de versão.
- Evitar service worker gigante e confuso.

### 8.2. Estratégias de cache

#### Cache First

Usar para:

- Logo.
- Ícones.
- CSS versionado.
- JS versionado.
- Fontes locais.
- Imagens estáticas.

Não usar para dados de negócio que mudam muito.

#### Network First

Usar para:

- Dashboard.
- Relatórios.
- Dados administrativos.
- Listas que precisam estar atualizadas.

Se a rede falhar, pode exibir cache antigo com aviso claro.

#### Stale While Revalidate

Usar para:

- Conteúdo que pode aparecer rápido e atualizar depois.
- Catálogos.
- Configurações não críticas.
- Dados de referência.

#### Network Only

Usar para:

- Login.
- Pagamento.
- Alteração de senha.
- Dados altamente sensíveis.
- Operações que não podem ser repetidas.

#### Cache Only

Usar raramente, apenas para assets internos versionados.

---

## 9. Página Offline

Todo PWA deve ter uma tela offline amigável.

A tela deve informar:

- Que o usuário está sem conexão.
- O que ainda pode ser feito.
- O que será sincronizado depois.
- Quantas ações estão pendentes.
- Botão para tentar novamente.
- Link para voltar à tela inicial offline, se existir.

Exemplo de mensagem:

> Você está offline. Suas ações serão salvas neste dispositivo e sincronizadas quando a conexão voltar.

Evitar mensagens genéricas como:

> Erro de rede.

---

## 10. Armazenamento Local

### 10.1. IndexedDB

Usar IndexedDB para:

- Filas de sincronização.
- Registros offline.
- Formulários salvos.
- Dados estruturados.
- Evidências pendentes.
- Tabelas auxiliares.

Boas práticas:

- Criar versão de schema.
- Criar função de migração.
- Criar índices por status, data, tipo e usuário.
- Separar tabelas locais por domínio.
- Registrar data de criação, alteração e tentativa de envio.
- Criar status: `draft`, `pending_sync`, `syncing`, `synced`, `error`, `conflict`.

### 10.2. localStorage

Usar localStorage apenas para:

- Preferências simples.
- Tema.
- Última tela aberta.
- Flags não sensíveis.

Não usar localStorage para:

- Token sensível.
- Senha.
- Dados pessoais críticos.
- Grandes volumes.
- Fila complexa.

### 10.3. sessionStorage

Usar para dados temporários da sessão atual, sem expectativa de persistência longa.

### 10.4. Cache Storage

Usar Cache Storage para arquivos e respostas HTTP cacheáveis, não como banco de dados de negócio.

---

## 11. Fila Offline

Todo sistema offline-first deve ter uma fila de operações.

### 11.1. Modelo mínimo de operação local

Cada ação offline deve gerar um item parecido com:

```json
{
  "local_id": "uuid-local",
  "operation_id": "uuid-idempotente",
  "entity": "tarefas",
  "action": "create",
  "payload": {},
  "status": "pending_sync",
  "created_at": "2026-07-03T10:00:00-03:00",
  "updated_at": "2026-07-03T10:00:00-03:00",
  "attempts": 0,
  "last_error": null
}
```

### 11.2. Regras da fila

- Toda operação deve ter `operation_id` único.
- O backend deve aceitar idempotência para evitar duplicidade.
- A fila deve manter ordem quando houver dependência entre ações.
- A fila deve permitir reenvio manual.
- A fila deve exibir erro compreensível para o usuário.
- A fila deve bloquear exclusão acidental de dados pendentes.
- A fila deve sobreviver ao fechamento do app.
- A fila deve ser apagada no logout apenas se a regra de segurança exigir.

### 11.3. Status visível para o usuário

Nunca esconder o estado da sincronização.

Mostrar etiquetas como:

- Salvo neste dispositivo.
- Pendente de sincronização.
- Sincronizando.
- Sincronizado.
- Erro ao sincronizar.
- Conflito encontrado.

---

## 12. Sincronização

### 12.1. Gatilhos de sincronização

Sincronizar quando:

- O app abrir.
- A conexão voltar.
- O usuário clicar em “sincronizar agora”.
- Um formulário for salvo.
- O service worker receber evento de sync, se suportado.
- O usuário trocar de tela crítica.

### 12.2. Ordem recomendada

1. Validar se existe conexão.
2. Verificar autenticação.
3. Buscar configurações e permissões atualizadas.
4. Enviar operações pendentes.
5. Tratar erros por item.
6. Atualizar dados locais com resposta do servidor.
7. Baixar dados atualizados necessários.
8. Atualizar tela e indicadores.

### 12.3. Conflitos

Conflitos acontecem quando:

- O usuário edita offline um dado alterado no servidor.
- Dois usuários alteram o mesmo registro.
- Um registro foi excluído no servidor antes do envio local.
- A permissão mudou antes da sincronização.

Estratégias possíveis:

- **Last write wins:** última alteração vence. Simples, mas perigoso.
- **Server wins:** servidor vence, cliente precisa revisar.
- **Client wins:** cliente vence, útil em coleta individual.
- **Merge manual:** usuário escolhe o que manter.
- **Merge por campo:** alguns campos locais são combinados com campos do servidor.

Regra: sistemas críticos devem preferir conflito visível, não sobrescrita silenciosa.

---

## 13. Backend Preparado para Offline

O backend precisa ser desenhado para receber operações atrasadas.

### 13.1. APIs idempotentes

Toda operação offline deve enviar um identificador único.

Exemplo de cabeçalho:

```http
Idempotency-Key: 550e8400-e29b-41d4-a716-446655440000
```

O servidor deve:

- Reconhecer operação repetida.
- Não duplicar registro.
- Retornar a mesma resposta quando possível.
- Salvar log de processamento.

### 13.2. Controle de versão do registro

Usar campos como:

```txt
id
uuid
created_at
updated_at
deleted_at
version
last_modified_by
sync_source
```

`version` ou `updated_at` ajudam a detectar conflito.

### 13.3. Soft delete

Em sistemas com offline, preferir `deleted_at` em vez de exclusão física imediata, porque um dispositivo offline pode tentar sincronizar algo relacionado a um registro excluído.

### 13.4. Endpoint de sincronização

Criar endpoint específico quando o sistema tiver muita operação offline.

Exemplo:

```txt
POST /api/v1/sync/push
GET  /api/v1/sync/pull?since=timestamp
POST /api/v1/sync/resolve-conflict
```

Evitar espalhar sincronização em várias APIs sem padrão.

---

## 14. Recursos Nativos do Dispositivo

Sempre usar recursos nativos com detecção de suporte, fallback e permissão clara.

### 14.1. Câmera e fotos

Usar para:

- Foto de perfil.
- Evidência.
- Comprovante.
- Documento.
- Registro visual.

Boas práticas:

- Pedir permissão apenas no momento de uso.
- Explicar por que a câmera é necessária.
- Permitir escolher arquivo como alternativa.
- Reduzir/comprimir imagem antes do upload quando possível.
- Mostrar pré-visualização.
- Permitir excluir antes de enviar.
- Salvar upload pendente na fila offline quando necessário.

### 14.2. Geolocalização

Usar para:

- Check-in.
- Rotas.
- Confirmação de presença.
- Evidência operacional.

Boas práticas:

- Solicitar permissão com contexto.
- Não capturar localização sem ação clara do usuário.
- Salvar precisão, data/hora e origem.
- Permitir operação manual se GPS falhar, quando a regra de negócio permitir.
- Informar quando a localização é obrigatória.

### 14.3. Notificações

Usar para:

- Pendências.
- Tarefas atrasadas.
- Agenda.
- Aprovação.
- Retorno do usuário.
- Aviso de sincronização com erro.

Boas práticas:

- Não pedir permissão de notificação logo no primeiro acesso sem contexto.
- Pedir quando o usuário entende o benefício.
- Permitir configurar frequência.
- Evitar excesso de notificações.
- Registrar opt-in e opt-out.
- Ter fallback por e-mail, WhatsApp ou aviso interno quando push não for suportado.

### 14.4. Compartilhamento

Usar Web Share API quando disponível para:

- Compartilhar convite.
- Enviar comprovante.
- Compartilhar link.
- Compartilhar relatório simples.

Fallback: copiar link, baixar arquivo ou abrir modal com opções.

### 14.5. Clipboard

Usar área de transferência para copiar:

- Links.
- Códigos.
- Chaves.
- Mensagens prontas.

Boas práticas:

- Informar quando copiado.
- Não copiar dados sensíveis sem ação explícita.

### 14.6. Arquivos

Permitir:

- Upload de imagem, PDF, CSV e outros formatos necessários.
- Validação de tamanho.
- Validação de extensão.
- Pré-visualização quando possível.
- Upload em fila offline quando a regra permitir.

---

## 15. Experiência do Usuário Offline

### 15.1. Indicador global de conexão

Toda aplicação offline-first deve mostrar estado de conexão.

Exemplos:

- Online.
- Offline.
- Conexão instável.
- Sincronizando.
- 3 itens pendentes.

### 15.2. Salvamento automático

Formulários longos devem ter auto-save local.

Regras:

- Salvar rascunho durante digitação.
- Mostrar “salvo neste dispositivo”.
- Evitar perda se a página fechar.
- Permitir continuar depois.
- Limpar rascunho após envio confirmado.

### 15.3. Botões conscientes de estado

Não usar apenas botão “Salvar”. Usar estados:

- Salvar.
- Salvando.
- Salvo localmente.
- Enviado.
- Erro — tentar novamente.

### 15.4. Não punir o usuário pela internet ruim

Evitar:

- Perder formulário.
- Voltar para login sem explicação.
- Mostrar tela branca.
- Duplicar envio.
- Travar botão eternamente.
- Esconder erro técnico.

---

## 16. Segurança

### 16.1. HTTPS obrigatório

PWA, service worker, permissões modernas e APIs sensíveis dependem de contexto seguro. Em produção, usar HTTPS sempre.

### 16.2. Dados sensíveis locais

Antes de salvar localmente, classificar:

- Público.
- Interno.
- Confidencial.
- Sensível.
- Crítico.

Regras:

- Não salvar senha.
- Evitar salvar token em localStorage.
- Evitar cachear páginas privadas completas.
- Apagar dados locais no logout quando exigido pela regra de negócio.
- Expirar dados locais por tempo.
- Não confiar apenas em criptografia no cliente para proteger tudo.
- Proteger no servidor com autenticação, autorização e permissões.

### 16.3. Service worker e cache privado

Cuidado com:

- Cache de resposta com dados de usuário.
- Cache compartilhado entre contas no mesmo dispositivo.
- Usuário fazendo logout e outro usuário vendo dados antigos.
- Atualizações quebradas por cache antigo.

No logout:

- Limpar IndexedDB sensível.
- Limpar caches privados.
- Cancelar filas não permitidas.
- Remover sessão.
- Redirecionar para login.

### 16.4. Permissões

Toda permissão deve seguir o princípio:

> Pedir apenas quando necessário, explicar o motivo e permitir alternativa quando possível.

Permissões comuns:

- Câmera.
- Localização.
- Notificação.
- Microfone.
- Arquivos.
- Clipboard.

Nunca pedir todas as permissões no primeiro acesso sem contexto.

---

## 17. Autenticação em PWA Offline

### 17.1. Regras gerais

- Login inicial deve ser online.
- Sessão offline deve ter prazo de validade.
- Permissões devem ser revalidadas quando voltar online.
- Ações offline devem ser bloqueadas se a permissão local estiver vencida.
- O app deve informar quando precisa reconectar.

### 17.2. Sessão expirada com fila pendente

Se o usuário tiver ações pendentes e a sessão expirar:

1. Manter ações pendentes seguras.
2. Pedir novo login.
3. Após login, validar se o usuário ainda pode enviar aquelas ações.
4. Sincronizar ou mostrar conflito/permissão negada.

Nunca apagar dados pendentes silenciosamente.

---

## 18. Performance

### 18.1. App shell

Separar a “casca” do app dos dados dinâmicos.

App shell inclui:

- HTML base.
- CSS principal.
- JS principal.
- Ícones.
- Layout.
- Componentes essenciais.

Dados dinâmicos vêm depois por API.

### 18.2. Carregamento rápido

Boas práticas:

- Minificar CSS e JS em produção.
- Dividir JS por tela quando necessário.
- Comprimir imagens.
- Usar lazy loading.
- Evitar bibliotecas pesadas sem necessidade.
- Usar cache para assets versionados.
- Priorizar renderização inicial rápida.

### 18.3. Conexão ruim

Testar:

- 3G lento.
- Offline total.
- Internet oscilando.
- Upload interrompido.
- API demorando.
- Servidor fora.

O app deve continuar compreensível mesmo quando não conseguir concluir tudo.

---

## 19. Atualização de Versão

PWAs podem sofrer com cache antigo. Criar estratégia clara.

Boas práticas:

- Versionar cache: `app-cache-v1`, `app-cache-v2`.
- Limpar cache antigo no `activate`.
- Detectar nova versão disponível.
- Mostrar aviso: “Nova versão disponível. Atualizar agora?”
- Evitar trocar versão durante preenchimento de formulário.
- Preservar fila offline em atualizações.
- Criar migrações para IndexedDB.
- Testar downgrade/upgrade quando possível.

---

## 20. Banco de Dados Local e Servidor

### 20.1. No cliente

Armazenar apenas o necessário para operação offline.

Exemplos:

- Usuário atual.
- Permissões mínimas.
- Listas auxiliares.
- Registros recentes.
- Tarefas atribuídas.
- Rascunhos.
- Fila de sincronização.

### 20.2. No servidor

O servidor é a fonte final da verdade.

Deve ter:

- Logs.
- Auditoria.
- Validação forte.
- Permissões.
- Idempotência.
- Controle de versão.
- Tratamento de conflitos.
- Respostas padronizadas.

### 20.3. Padrão de resposta de API

Exemplo:

```json
{
  "success": true,
  "data": {},
  "meta": {
    "server_time": "2026-07-03T10:00:00-03:00",
    "sync_version": 12
  },
  "errors": []
}
```

Erro:

```json
{
  "success": false,
  "data": null,
  "errors": [
    {
      "code": "VALIDATION_ERROR",
      "field": "cpf",
      "message": "CPF inválido.",
      "details": "Informe 11 dígitos válidos."
    }
  ]
}
```

---

## 21. Logs, Auditoria e Debug

Todo app/PWA deve registrar eventos importantes.

### 21.1. Logs locais

Registrar localmente:

- Falhas de rede.
- Erros de sincronização.
- Operações pendentes.
- Falhas de permissão.
- Erros de IndexedDB.
- Versão do app.

### 21.2. Logs no servidor

Registrar:

- Usuário.
- IP quando aplicável.
- Dispositivo/navegador.
- Operação.
- Payload resumido ou hash, evitando expor dados sensíveis.
- Resultado.
- Tempo de processamento.
- Idempotency key.

### 21.3. Debug visual para administrador

Criar modo debug acessível apenas a administradores.

Pode mostrar:

- Status online/offline.
- Versão do app.
- Versão do service worker.
- Quantidade de itens em cache.
- Fila pendente.
- Última sincronização.
- Últimos erros.
- Permissões disponíveis.
- Dados do ambiente.

Nunca mostrar dados sensíveis para usuário comum.

---

## 22. Acessibilidade e Usabilidade

PWA precisa funcionar como app, mas continuar respeitando boas práticas web.

Regras:

- Botões grandes o suficiente para toque.
- Feedback visual imediato.
- Contraste adequado.
- Navegação por teclado no desktop.
- Labels em campos.
- Mensagens de erro claras.
- Não depender apenas de cor para indicar status.
- Estados vazios bem explicados.
- Loading com contexto.
- Tela adaptada para uma mão no celular quando possível.

---

## 23. Responsividade App-Like

### 23.1. Mobile

- Navegação inferior quando houver 3 a 5 áreas principais.
- Botões fixos de ação quando a tarefa for operacional.
- Formulários em etapas quando forem longos.
- Inputs otimizados por tipo: telefone, número, e-mail, data.
- Evitar tabelas largas; usar cards.

### 23.2. Tablet

- Aproveitar duas colunas.
- Permitir lista + detalhe.
- Melhorar visualização de agenda, tarefas e relatórios.

### 23.3. Desktop

- Usar sidebar.
- Tabelas completas.
- Filtros avançados.
- Atalhos de teclado quando útil.
- Mais densidade de informação sem poluir.

---

## 24. Notificações e Retenção

Notificações devem gerar valor real.

Usar para:

- Lembrar tarefa necessária.
- Avisar erro de sincronização.
- Informar aprovação.
- Avisar nova mensagem relevante.
- Reativar usuário com contexto.

Não usar para:

- Spam.
- Mensagens genéricas.
- Notificação sem ação.
- Pressionar usuário sem necessidade.

Toda notificação deve responder:

- Por que estou recebendo isso?
- O que preciso fazer?
- O que acontece se eu ignorar?
- Como desativo ou ajusto?

---

## 25. Testes Obrigatórios

### 25.1. Testes de instalação

Testar:

- Android Chrome.
- Android Samsung Internet, se público relevante.
- iPhone Safari.
- iPad Safari.
- Desktop Chrome/Edge.

Verificar:

- Ícone.
- Nome curto.
- Splash.
- Tela inicial.
- Modo standalone.
- Atualização.

### 25.2. Testes offline

Testar:

- Abrir app sem internet.
- Criar registro offline.
- Editar registro offline.
- Fechar app com pendência.
- Reabrir app offline.
- Voltar internet.
- Sincronizar.
- Erro no servidor durante sync.
- Duplicidade de envio.
- Conflito de registro.
- Logout com pendência.

### 25.3. Testes de cache

Testar:

- Primeira visita.
- Segunda visita.
- Atualização de versão.
- Cache antigo.
- Limpeza de cache.
- Arquivo JS alterado.
- Página offline.

### 25.4. Testes de permissão

Testar:

- Permissão aceita.
- Permissão negada.
- Permissão bloqueada.
- Recurso indisponível.
- Fallback manual.

### 25.5. Testes de segurança

Testar:

- Logout limpa dados corretos.
- Outro usuário no mesmo dispositivo não vê dados antigos.
- Cache não expõe dados privados.
- API rejeita operação sem permissão.
- Token expirado não sincroniza indevidamente.

---

## 26. Checklist de Entrega

Antes de considerar o app pronto:

- [ ] Manifesto configurado.
- [ ] Ícones 192 e 512 criados.
- [ ] HTTPS ativo.
- [ ] Service worker registrado.
- [ ] Cache versionado.
- [ ] Cache antigo limpo.
- [ ] Página offline criada.
- [ ] Estratégias de cache documentadas.
- [ ] IndexedDB/local storage organizados.
- [ ] Fila offline implementada, se necessário.
- [ ] API idempotente para operações offline.
- [ ] Conflitos tratados.
- [ ] Indicador online/offline visível.
- [ ] Estados de sincronização visíveis.
- [ ] Logout trata dados locais.
- [ ] Recursos nativos têm fallback.
- [ ] Permissões são pedidas com contexto.
- [ ] Teste em celular real realizado.
- [ ] Teste em modo avião realizado.
- [ ] Teste de internet instável realizado.
- [ ] Teste de atualização de versão realizado.
- [ ] Logs e debug administrativo implementados.
- [ ] Documentação técnica atualizada.

---

## 27. Padrão de Comentários no Código

Todo arquivo importante deve iniciar com comentário explicando objetivo.

Exemplo:

```js
/**
 * Arquivo: sync-queue.js
 * Objetivo: controlar a fila local de operações offline do PWA.
 * Responsabilidades:
 * - salvar operações pendentes no IndexedDB;
 * - reenviar operações quando houver conexão;
 * - registrar erros de sincronização;
 * - atualizar status para a interface do usuário.
 *
 * Atenção:
 * - não colocar regra de negócio sensível apenas no cliente;
 * - toda operação enviada deve possuir operation_id idempotente;
 * - não apagar item pendente sem confirmação do servidor.
 */
```

Cada função deve explicar:

- O que faz.
- O que recebe.
- O que retorna.
- Quais efeitos colaterais possui.
- Quais erros podem acontecer.

---

## 28. Prompt Interno para IA Usar esta Skill

Sempre que for desenvolver um app, SaaS, software ou PWA, a IA deve seguir este raciocínio:

```txt
Aja como desenvolvedor sênior especialista em SaaS, software, app PWA, offline-first e recursos nativos.

Antes de implementar, avalie:
1. Esta tela/função precisa funcionar offline?
2. Precisa ser instalável como aplicativo?
3. Usa câmera, localização, notificação, arquivo, compartilhamento ou outro recurso nativo?
4. Que dados podem ser salvos localmente?
5. Que dados não podem ser salvos localmente?
6. Como a sincronização será feita?
7. Como evitar duplicidade?
8. Como resolver conflitos?
9. Como mostrar o status para o usuário?
10. Como testar em modo avião e conexão instável?

Ao criar código:
- separar tela, API, armazenamento local e sincronização;
- comentar objetivo do arquivo e das funções;
- usar service worker com cache versionado;
- usar IndexedDB para dados offline estruturados;
- usar fila de operações para ações offline;
- usar APIs idempotentes no backend;
- nunca esconder erro de sincronização;
- implementar fallback para recursos nativos;
- garantir HTTPS, segurança e limpeza no logout;
- testar instalação, cache, offline, atualização e permissões.
```

---

## 29. Antipadrões Proibidos

Evitar rigorosamente:

- Criar PWA apenas com manifesto, sem pensar em offline.
- Cachear tudo sem estratégia.
- Salvar token sensível em localStorage sem análise.
- Perder formulário quando a internet cai.
- Não mostrar fila pendente.
- Duplicar registros por reenvio.
- Não tratar conflito.
- Pedir permissão de câmera/localização/notificação sem explicar.
- Bloquear app inteiro quando apenas uma API falha.
- Não limpar cache antigo.
- Não testar em celular real.
- Não testar modo avião.
- Misturar regra de negócio, tela e sync no mesmo arquivo sem organização.
- Confiar no cliente para validação crítica.
- Esconder erro técnico sem mensagem útil.
- Atualizar service worker quebrando usuário no meio da operação.

---

## 30. Regra Final da Skill

Um SaaS/PWA profissional deve ser tratado como um produto vivo, usado em condições reais.

A IA deve sempre pensar em:

- Usuário com pressa.
- Celular com pouca bateria.
- Internet ruim.
- Permissão negada.
- App fechado no meio da tarefa.
- Cache antigo.
- Dados pendentes.
- Sessão expirada.
- Conflito entre dispositivos.
- Segurança e privacidade.
- Manutenção por outro programador.

A entrega só é boa quando o sistema continua compreensível, seguro e utilizável mesmo quando algo dá errado.

---

## 31. Referências Técnicas Recomendadas

Consultar documentação oficial e atualizada antes de implementar detalhes específicos de compatibilidade:

- MDN — Progressive Web Apps: https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps
- MDN — Service Worker API: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API
- MDN — Using Service Workers: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API/Using_Service_Workers
- MDN — Offline and background operation: https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps/Guides/Offline_and_background_operation
- MDN — Making PWAs installable: https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps/Guides/Making_PWAs_installable
- MDN — Background Synchronization API: https://developer.mozilla.org/en-US/docs/Web/API/Background_Synchronization_API
- MDN — Web Periodic Background Synchronization API: https://developer.mozilla.org/en-US/docs/Web/API/Web_Periodic_Background_Synchronization_API
- web.dev — Learn PWA: https://web.dev/learn/pwa/

