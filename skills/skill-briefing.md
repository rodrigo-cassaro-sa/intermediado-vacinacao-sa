# Skill — Manual de Boas Práticas para Criação de Briefing de Projetos SaaS, Sistemas e Apps

## Limite desta skill

Esta skill cria a visão inicial do projeto.

Ela pode levantar telas, permissões, dados, integrações e requisitos técnicos em nível de briefing, mas não deve substituir:

- `skill-permissoes.md` para matriz detalhada de perfis e permissões;
- `skill-arquitetura.md` para estrutura técnica do projeto;
- `skill-telas.md` para mapa detalhado de telas, funções e banco;
- `skill-dados.md` e `skill-mysql.md` para modelagem e implementação do banco.

O briefing deve responder "o que e por quê".
As demais skills detalham "como".

## 1. Papel da IA nesta Skill

Você deve atuar como um **Gerente de Projeto Sênior especializado em arquitetura, desenvolvimento de sistemas, SaaS e aplicativos**, com visão de produto, regra de negócio, processos, banco de dados, UX/UI, segurança, testes, homologação e produção.

Seu objetivo é criar, revisar ou melhorar briefings de projetos digitais para que eles sirvam como **documento-guia de decisão**, evitando ambiguidades, retrabalho, funcionalidades mal definidas e desalinhamento entre negócio, design, desenvolvimento, banco de dados e testes.

---

## 2. Objetivo do Briefing de Projeto

O briefing de projeto deve ser um documento central que responde, com clareza:

- O que será construído.
- Para quem será construído.
- Qual problema resolve.
- Quais regras de negócio precisam ser respeitadas.
- Quais processos fazem parte da operação.
- Quais usuários existem e o que cada um pode fazer.
- Quais telas, funções, dados e integrações serão necessários.
- O que entra no MVP.
- O que entra nas versões futuras.
- Como o projeto será desenvolvido, testado, homologado e colocado em produção.

O briefing não deve ser apenas uma descrição genérica da ideia. Ele deve funcionar como **base para arquitetura, mapa de telas, banco de dados, backlog, testes, estimativa de custo, cronograma e tomada de decisão**.

---

## 3. Princípios de um Bom Briefing

### 3.1 Clareza antes de velocidade

Antes de iniciar desenvolvimento, o projeto precisa ter regras, processos e objetivos compreendidos. Começar rápido sem clareza gera retrabalho, telas inúteis e banco de dados mal estruturado.

### 3.2 Regra de negócio acima da tela

A tela é consequência da regra de negócio. Primeiro defina como o negócio funciona, depois desenhe telas, campos, botões, permissões e banco de dados.

### 3.3 Processo antes da funcionalidade

Não basta dizer “criar cadastro”, “criar dashboard” ou “criar relatório”. É necessário explicar o processo completo: quem inicia, o que acontece, quais validações existem, quais status são usados, quais exceções podem ocorrer e quando o processo termina.

### 3.4 MVP enxuto e funcional

O MVP deve conter apenas o mínimo necessário para validar o valor principal do sistema. Ele não deve tentar resolver todas as ideias do projeto.

### 3.5 Versões planejadas em ciclos curtos

Cada versão deve ser planejada como um ciclo de aproximadamente **30 dias de desenvolvimento**, seguido de testes, homologação e produção.

### 3.6 Decisões registradas

Toda decisão importante deve ser registrada no briefing: motivo, impacto, responsável e data. Isso evita discussões repetidas e perda de contexto.

### 3.7 Sem ambiguidade operacional

Palavras como “gerenciar”, “controlar”, “validar”, “aprovar” e “finalizar” precisam ser detalhadas. O briefing deve explicar exatamente o que essas ações significam dentro do sistema.

---

## 4. Estrutura Recomendada do Briefing

O briefing deve seguir uma estrutura organizada para facilitar leitura por gestores, designers, programadores, analistas de banco de dados, QA e IA de desenvolvimento.

---

# Modelo Oficial de Briefing

## 1. Identificação do Projeto

### Nome do projeto

Informe o nome oficial do sistema, SaaS, app ou módulo.

### Tipo de projeto

Exemplos:

- SaaS web.
- Sistema interno.
- Aplicativo mobile.
- Portal do cliente.
- Área administrativa.
- API.
- Integração entre sistemas.
- Módulo novo de um sistema existente.

### Responsáveis

Defina os principais responsáveis:

- Dono do produto.
- Gestor do projeto.
- Responsável técnico.
- Responsável pelas regras de negócio.
- Responsável pela homologação.
- Usuários-chave.

### Contexto do projeto

Explique por que o projeto existe, qual dor resolve e qual oportunidade atende.

### Objetivo principal

Descreva o objetivo central em uma frase simples.

Exemplo:

> Criar um sistema para registrar demandas internas, gerar planos de ação, acompanhar responsáveis, prazos, evidências e relatórios por cliente, equipe e status.

---

## 2. Problema, Dor e Oportunidade

### Problema atual

Descreva o que acontece hoje sem o sistema ou com o processo atual.

Perguntas importantes:

- Qual processo é manual hoje?
- Onde acontecem erros?
- Onde existe retrabalho?
- Onde há falta de controle?
- O que demora demais?
- O que depende de planilha, WhatsApp, e-mail ou conferência manual?

### Impacto do problema

Explique o impacto real no negócio:

- Perda financeira.
- Atraso operacional.
- Falta de rastreabilidade.
- Erro de comunicação.
- Insatisfação de cliente.
- Falha de faturamento.
- Falha de compliance.
- Perda de produtividade.

### Oportunidade

Descreva o ganho esperado:

- Reduzir tempo operacional.
- Melhorar controle.
- Padronizar processos.
- Automatizar etapas.
- Criar histórico confiável.
- Melhorar tomada de decisão.
- Escalar operação.

---

## 3. Objetivos do Projeto

### Objetivo geral

Declare o resultado principal esperado.

### Objetivos específicos

Liste objetivos menores, mensuráveis e práticos.

Exemplos:

- Cadastrar usuários com níveis de permissão.
- Registrar demandas com status e histórico.
- Criar plano de ação por demanda.
- Anexar evidências.
- Enviar notificações de prazo.
- Gerar relatório mensal.
- Permitir exportação de dados.

### Indicadores de sucesso

Defina como saber se o projeto deu certo.

Exemplos:

- Redução de 50% no tempo de acompanhamento manual.
- 100% das demandas registradas no sistema.
- Diminuição de atrasos em planos de ação.
- Relatório mensal gerado automaticamente.
- Menos retrabalho entre equipes.

---

## 4. Público-Alvo e Usuários

### Perfis de usuários

Liste todos os tipos de usuários que usarão o sistema.

Exemplos:

- Administrador.
- Gestor.
- Analista.
- Operador.
- Cliente.
- Supervisor.
- Financeiro.
- Suporte.
- Usuário externo.

### Necessidades por perfil

Para cada perfil, descreva:

- O que ele precisa fazer.
- Quais informações precisa ver.
- Quais ações pode executar.
- Quais limitações deve ter.
- Quais decisões pode tomar.

### Matriz resumida de permissões

| Perfil | Pode visualizar | Pode criar | Pode editar | Pode aprovar | Pode excluir | Pode exportar |
|---|---|---|---|---|---|---|
| Administrador | Tudo | Tudo | Tudo | Sim | Sim | Sim |
| Gestor | Dados da equipe | Sim | Sim | Sim | Restrito | Sim |
| Analista | Dados operacionais | Sim | Próprios registros | Não | Não | Não |
| Cliente | Dados próprios | Solicitações | Não | Não | Não | Restrito |

A matriz deve ser adaptada conforme cada projeto.

---

## 5. Escopo do Projeto

### Dentro do escopo

Liste tudo que será desenvolvido.

Exemplo:

- Login e autenticação.
- Cadastro de usuários.
- Gestão de permissões.
- Dashboard.
- Cadastro de demandas.
- Plano de ação.
- Upload de anexos.
- Notificações.
- Relatórios.
- Histórico de alterações.

### Fora do escopo

Liste o que não será feito nesta fase.

Exemplo:

- Aplicativo mobile nativo.
- Integração com sistema externo.
- Inteligência artificial.
- Pagamento online.
- Módulo financeiro completo.

### Escopo futuro

Liste ideias importantes, mas que devem ficar para versões futuras.

---

## 6. Regras de Negócio

Esta é uma das seções mais importantes do briefing.

Cada regra de negócio deve ser escrita de forma clara, numerada e testável.

### Formato recomendado

```md
RN001 — Nome da regra
Descrição: explique a regra com clareza.
Quando acontece: explique em qual momento do processo a regra se aplica.
Quem é afetado: informe quais usuários ou perfis são impactados.
Validação: explique como o sistema deve validar a regra.
Exceções: informe situações especiais.
Mensagem de erro ou alerta: descreva a mensagem esperada.
Impacto no banco de dados: informe campos, status ou registros envolvidos.
```

### Exemplo

```md
RN001 — Demanda não pode ser finalizada sem plano de ação concluído
Descrição: uma demanda só pode ser marcada como finalizada quando todas as ações vinculadas estiverem concluídas.
Quando acontece: ao tentar alterar o status da demanda para "Finalizada".
Quem é afetado: gestor, administrador e analista responsável.
Validação: o sistema deve verificar se existem ações pendentes ou atrasadas.
Exceções: administrador pode forçar encerramento com justificativa obrigatória.
Mensagem de erro: "Não é possível finalizar esta demanda porque existem ações pendentes."
Impacto no banco de dados: validar tabela de ações vinculadas à demanda antes de atualizar o status.
```

### Boas práticas para regras de negócio

- Não misture regra de negócio com opinião.
- Toda regra deve ser testável.
- Toda regra deve ter consequência no sistema.
- Regras críticas devem ter logs e auditoria.
- Regras com exceção devem exigir justificativa.
- Regras que afetam dinheiro, acesso, aprovação ou exclusão devem ser tratadas como críticas.

---

## 7. Processos Envolvidos

O briefing deve mapear os processos de ponta a ponta.

### Formato recomendado

```md
Processo: Nome do processo
Objetivo: para que esse processo existe.
Usuários envolvidos: quem participa.
Gatilho inicial: o que inicia o processo.
Etapas:
1. Primeira etapa.
2. Segunda etapa.
3. Terceira etapa.
Status possíveis: liste os status usados.
Regras relacionadas: RN001, RN002, RN003.
Exceções: descreva situações fora do fluxo comum.
Resultado final: o que caracteriza o fim do processo.
```

### Exemplo

```md
Processo: Abertura e acompanhamento de demanda
Objetivo: registrar uma demanda, definir responsáveis, acompanhar ações e concluir com evidências.
Usuários envolvidos: solicitante, analista, gestor e administrador.
Gatilho inicial: usuário cria uma nova demanda.
Etapas:
1. Solicitante cria demanda.
2. Sistema registra status "Aberta".
3. Gestor analisa e define prioridade.
4. Analista cria plano de ação.
5. Responsáveis executam ações.
6. Sistema controla prazos.
7. Gestor valida evidências.
8. Demanda é finalizada.
Status possíveis: Aberta, Em análise, Em execução, Aguardando validação, Finalizada, Cancelada.
Regras relacionadas: RN001, RN002, RN003.
Exceções: demanda pode ser cancelada com justificativa.
Resultado final: demanda finalizada com histórico, ações concluídas e evidências anexadas.
```

---

## 8. Jornada do Usuário

A jornada descreve como cada perfil usa o sistema.

### Para cada perfil, documente:

- Como entra no sistema.
- Qual tela acessa primeiro.
- Qual tarefa principal executa.
- Quais decisões precisa tomar.
- Quais alertas recebe.
- Como conclui sua atividade.

### Exemplo

```md
Jornada do Gestor
1. Acessa o sistema.
2. Visualiza dashboard com demandas abertas, atrasadas e concluídas.
3. Entra em uma demanda pendente.
4. Define prioridade e responsável.
5. Acompanha plano de ação.
6. Recebe alerta de atraso.
7. Valida evidência.
8. Finaliza demanda.
```

---

## 9. Mapa de Telas

O briefing deve servir como base para o mapa de telas.

### Para cada tela, defina:

- Nome da tela.
- Objetivo da tela.
- Quem pode acessar.
- Dados exibidos.
- Campos obrigatórios.
- Botões e ações.
- Filtros.
- Regras aplicadas.
- Estados da tela.
- Mensagens de erro e sucesso.

### Modelo

```md
Tela: Cadastro de Demanda
Objetivo: permitir abertura de uma nova demanda.
Perfis com acesso: administrador, gestor, analista e cliente.
Dados exibidos: cliente, título, descrição, prioridade, prazo, responsável e anexos.
Campos obrigatórios: título, descrição, cliente e prioridade.
Ações: salvar, cancelar, anexar arquivo.
Regras aplicadas: RN010, RN011, RN012.
Estados: vazio, preenchido, erro de validação, salvo com sucesso.
Mensagem de sucesso: "Demanda criada com sucesso."
Mensagem de erro: "Preencha todos os campos obrigatórios."
```

---

## 10. Dados e Banco de Dados

O briefing deve antecipar as principais entidades de dados para orientar a modelagem do banco.

### Para cada entidade, defina:

- Nome da entidade.
- Objetivo.
- Campos principais.
- Relacionamentos.
- Regras de validação.
- Permissões.
- Necessidade de histórico.
- Necessidade de anexos.
- Necessidade de auditoria.

### Exemplo de entidades

```md
Entidade: Usuário
Campos: id, nome, e-mail, senha_hash, perfil_id, status, criado_em, atualizado_em.
Relacionamentos: pertence a um perfil; pode criar demandas; pode ser responsável por ações.
Regras: e-mail único; senha criptografada; usuário inativo não acessa o sistema.
Auditoria: registrar criação, edição, inativação e alteração de perfil.
```

```md
Entidade: Demanda
Campos: id, título, descrição, cliente_id, prioridade, status, prazo, responsável_id, criado_por, criado_em, atualizado_em.
Relacionamentos: pertence a um cliente; possui ações; possui anexos; possui histórico.
Regras: não pode ser finalizada com ações pendentes.
Auditoria: registrar alteração de status, responsável, prioridade e prazo.
```

---

## 11. Integrações

Documente todas as integrações previstas.

### Para cada integração, informe:

- Sistema integrado.
- Objetivo.
- Tipo de integração: API, webhook, arquivo, banco, planilha, e-mail.
- Dados enviados.
- Dados recebidos.
- Frequência.
- Responsável técnico.
- Regras de autenticação.
- Tratamento de erro.
- Logs necessários.

### Exemplo

```md
Integração: Envio de notificação por WhatsApp
Objetivo: avisar responsáveis sobre ações atrasadas.
Tipo: API externa.
Gatilho: ação vencida há mais de 24 horas.
Dados enviados: nome do responsável, título da ação, prazo e link da demanda.
Tratamento de erro: registrar falha e tentar novamente em 30 minutos.
Log: salvar data, mensagem, destinatário, status e retorno da API.
```

---

## 12. Segurança e Permissões

O briefing deve definir segurança desde o início.

### Itens obrigatórios

- Perfis de acesso.
- Permissões por tela.
- Permissões por ação.
- Autenticação.
- Recuperação de senha.
- Política de senha.
- Sessão e expiração.
- Dados sensíveis.
- Logs de acesso.
- Auditoria de alterações.
- Proteção contra exclusão indevida.
- Controle de anexos públicos e privados.

### Boas práticas

- Usuário comum não deve acessar dados administrativos.
- Exclusão física deve ser evitada; prefira inativação ou exclusão lógica.
- Toda ação crítica deve registrar quem fez, quando fez e o que mudou.
- Dados sensíveis devem ser protegidos.
- Uploads devem validar tipo, tamanho e permissão.
- Acesso direto a arquivos privados deve ser bloqueado.

---

## 13. Requisitos Não Funcionais

Além das funcionalidades, o briefing deve definir critérios técnicos.

### Performance

- Tempo máximo de carregamento das telas principais.
- Quantidade esperada de usuários simultâneos.
- Volume estimado de registros.
- Volume estimado de anexos.
- Necessidade de paginação.
- Necessidade de cache.

### Disponibilidade

- Horário de uso do sistema.
- Tolerância a falhas.
- Necessidade de backup.
- Frequência de backup.

### Escalabilidade

- Crescimento esperado de usuários.
- Crescimento esperado de dados.
- Possibilidade de novos módulos.

### Usabilidade

- Sistema responsivo.
- Interface simples.
- Fluxos curtos.
- Mensagens claras.
- Evitar excesso de campos.

### Manutenção

- Código organizado por módulos.
- Padrão de nomes.
- Logs técnicos.
- Documentação mínima.
- Separação de ambiente de desenvolvimento, homologação e produção.

---

## 14. MVP — Produto Mínimo Viável

O MVP deve conter apenas as funções essenciais para validar o principal valor do sistema.

### Critérios para entrar no MVP

Uma função só entra no MVP se responder “sim” para pelo menos uma das perguntas:

- Sem essa função o sistema não entrega o valor principal?
- Essa função valida a regra de negócio central?
- Essa função é obrigatória para operação mínima?
- Essa função reduz o maior problema atual?
- Essa função é necessária para testar o fluxo principal de ponta a ponta?

### Critérios para sair do MVP

A função deve sair do MVP se:

- É apenas melhoria visual.
- É automação avançada.
- É relatório secundário.
- É integração não essencial.
- É personalização que pode esperar.
- É uma ideia interessante, mas não necessária para validar o projeto.

### Modelo de definição do MVP

```md
MVP — Objetivo
Descrever o objetivo do MVP em uma frase.

Funções incluídas:
1. Login.
2. Cadastro básico de usuários.
3. Cadastro principal do processo.
4. Listagem com filtros básicos.
5. Detalhe do registro.
6. Alteração de status.
7. Histórico básico.
8. Permissões mínimas.

Funções fora do MVP:
1. Relatórios avançados.
2. Integrações externas.
3. Notificações automáticas.
4. Aplicativo mobile nativo.
5. Inteligência artificial.
```

---

## 15. Planejamento por Versões

Cada versão deve ser pensada como um ciclo de entrega.

### Ciclo padrão recomendado

```md
Fase 1 — Desenvolvimento: 30 dias
Fase 2 — Testes internos: 3 a 7 dias
Fase 3 — Homologação com usuários-chave: 3 a 7 dias
Fase 4 — Correções finais: conforme criticidade
Fase 5 — Produção: publicação da versão aprovada
Fase 6 — Monitoramento pós-produção: acompanhar erros, dúvidas e ajustes rápidos
```

### Regra importante

Uma nova versão só deve ir para produção depois de:

- Regras principais testadas.
- Fluxos críticos validados.
- Dados conferidos.
- Permissões testadas.
- Usuário-chave aprovar a homologação.
- Backup realizado, quando aplicável.
- Plano de rollback definido.

---

# Modelo de Evolução por Versões

## MVP — Versão 0.1

### Objetivo

Validar o fluxo principal do sistema de ponta a ponta com o menor conjunto possível de funcionalidades.

### Desenvolvimento

30 dias.

### Funções recomendadas

- Login.
- Cadastro de usuários.
- Perfis básicos de permissão.
- Cadastro da entidade principal do projeto.
- Listagem da entidade principal.
- Tela de detalhe.
- Edição básica.
- Status principais.
- Histórico simples.
- Regras de negócio centrais.
- Banco de dados inicial.
- Layout responsivo básico.

### Não incluir no MVP

- Relatórios avançados.
- Automação complexa.
- Integrações externas não obrigatórias.
- IA.
- Gamificação.
- Personalização avançada.
- Aplicativo mobile nativo, salvo se o app for o produto principal.

### Critério de aceite

O usuário deve conseguir executar o processo principal sem depender de planilha paralela.

---

## Versão 1.0 — Operação Controlada

### Objetivo

Transformar o MVP em uma versão operacional mais segura, organizada e utilizável por uma equipe real.

### Desenvolvimento

30 dias.

### Funções recomendadas

- Dashboard operacional.
- Filtros mais completos.
- Permissões por perfil mais detalhadas.
- Validações adicionais de regras de negócio.
- Upload de anexos.
- Histórico detalhado.
- Logs de ações importantes.
- Melhorias de UX/UI.
- Mensagens de erro e sucesso padronizadas.
- Exportação simples.
- Ajustes vindos da homologação do MVP.

### Critério de aceite

A equipe deve conseguir usar o sistema no dia a dia com controle, rastreabilidade e menor dependência de controles externos.

---

## Versão 2.0 — Automação e Relatórios

### Objetivo

Reduzir trabalho manual e melhorar tomada de decisão.

### Desenvolvimento

30 dias.

### Funções recomendadas

- Notificações automáticas.
- Relatórios gerenciais.
- Indicadores por período.
- Indicadores por responsável.
- Indicadores por cliente, equipe ou categoria.
- Alertas de atraso.
- Regras automáticas de mudança de status.
- Exportações avançadas.
- Auditoria mais completa.
- Melhorias de performance.

### Critério de aceite

Gestores devem conseguir acompanhar indicadores e tomar decisões sem depender de levantamento manual.

---

## Versão 3.0 — Integrações e Escala

### Objetivo

Conectar o sistema a outros serviços e preparar crescimento.

### Desenvolvimento

30 dias.

### Funções recomendadas

- Integrações com APIs externas.
- Webhooks.
- Importação de dados.
- Exportação automatizada.
- Rotinas agendadas.
- Filas de processamento.
- Controle de falhas em integrações.
- Logs técnicos de integração.
- Melhorias de segurança.
- Otimização de banco de dados.

### Critério de aceite

O sistema deve trocar dados com outros sistemas de forma segura, rastreável e com tratamento de falhas.

---

## Versão 4.0 — Inteligência, Personalização e Expansão

### Objetivo

Adicionar recursos avançados depois que a base operacional estiver validada.

### Desenvolvimento

30 dias.

### Funções recomendadas

- Sugestões inteligentes.
- IA para análise ou classificação.
- Personalização de dashboards.
- Templates configuráveis.
- Workflows customizados.
- Módulos complementares.
- Portal externo mais completo.
- Recursos avançados de retenção.
- Melhorias de experiência do usuário.

### Critério de aceite

O sistema deve aumentar produtividade e inteligência operacional sem comprometer estabilidade.

---

## 16. Backlog por Prioridade

O briefing deve organizar as funcionalidades em prioridades.

### Classificação recomendada

| Prioridade | Significado | Exemplo |
|---|---|---|
| P0 | Obrigatório para o MVP funcionar | Login, cadastro principal, regra central |
| P1 | Muito importante para operação | Dashboard, filtros, permissões detalhadas |
| P2 | Melhoria relevante | Relatórios, notificações, exportações |
| P3 | Futuro ou avançado | IA, gamificação, personalizações |

### Modelo

```md
Funcionalidade: Cadastro de Demanda
Prioridade: P0
Versão prevista: MVP
Descrição: permite registrar uma nova demanda no sistema.
Regras vinculadas: RN001, RN002.
Dependências: cadastro de usuários e clientes.
Critério de aceite: usuário consegue criar demanda preenchendo campos obrigatórios e visualizar na listagem.
```

---

## 17. Critérios de Aceite

Cada função precisa ter critérios de aceite claros.

### Modelo

```md
Funcionalidade: Alterar status da demanda
Critérios de aceite:
- O usuário autorizado consegue alterar o status.
- O sistema bloqueia alteração quando regra de negócio não permite.
- O sistema registra histórico da alteração.
- O sistema mostra mensagem de sucesso.
- O sistema mostra mensagem de erro clara quando houver bloqueio.
```

### Boas práticas

- Critério de aceite deve ser objetivo.
- Deve ser possível testar manualmente.
- Deve considerar sucesso, erro e exceção.
- Deve citar regras de negócio relacionadas.

---

## 18. Testes, Homologação e Produção

## 18.1 Testes internos

Antes da homologação, a equipe técnica deve testar:

- Login e sessão.
- Permissões.
- Cadastro.
- Edição.
- Exclusão ou inativação.
- Filtros.
- Status.
- Regras de negócio.
- Mensagens de erro.
- Anexos.
- Relatórios.
- Performance básica.
- Responsividade.

## 18.2 Homologação

A homologação deve ser feita por usuários-chave do negócio.

### Objetivo da homologação

Validar se o sistema atende o processo real, não apenas se a tela funciona.

### Checklist de homologação

- O fluxo real foi executado do começo ao fim?
- As regras de negócio foram respeitadas?
- Os status fazem sentido para a operação?
- As mensagens estão claras?
- Os dados exibidos estão corretos?
- As permissões estão adequadas?
- Existe algum passo confuso?
- Existe algum campo desnecessário?
- Existe algum campo faltando?
- A versão está pronta para produção?

## 18.3 Produção

Antes de publicar em produção:

- Confirmar versão aprovada.
- Realizar backup, se houver dados existentes.
- Validar variáveis de ambiente.
- Validar conexão com banco de produção.
- Validar permissões.
- Validar uploads.
- Validar integrações.
- Definir responsável pelo acompanhamento.
- Registrar data da publicação.
- Registrar versão publicada.

## 18.4 Pós-produção

Após publicar:

- Monitorar erros.
- Coletar feedback dos usuários.
- Registrar bugs.
- Separar bug de melhoria.
- Corrigir falhas críticas rapidamente.
- Planejar melhorias para a próxima versão.

---

## 19. Ambientes do Projeto

O briefing deve prever separação de ambientes.

### Desenvolvimento

Ambiente usado pela equipe técnica para construir e alterar o sistema.

### Homologação

Ambiente usado para testes com usuários-chave antes da produção.

### Produção

Ambiente real usado pelos usuários finais.

### Boas práticas

- Não testar funcionalidades novas diretamente em produção.
- Não usar banco de produção em desenvolvimento sem proteção.
- Configurações sensíveis devem ficar em variáveis de ambiente.
- Cada ambiente deve ter banco de dados separado.
- Publicação deve seguir controle de versão.

---

## 20. Registro de Decisões

Toda decisão relevante deve ser registrada.

### Modelo

```md
Decisão: Usar exclusão lógica em vez de exclusão definitiva
Data: 2026-07-02
Responsável: Gestor do projeto
Motivo: preservar histórico e auditoria.
Impacto: registros terão campo status ou deleted_at.
Afeta: banco de dados, telas administrativas e relatórios.
```

### Decisões que devem ser registradas

- Mudança de escopo.
- Alteração de regra de negócio.
- Remoção de funcionalidade.
- Adição de funcionalidade.
- Escolha de tecnologia.
- Mudança de prazo.
- Aprovação de versão.
- Publicação em produção.

---

## 21. Riscos do Projeto

O briefing deve listar riscos e planos de mitigação.

### Tipos de risco

- Regra de negócio mal definida.
- Escopo crescendo sem controle.
- Falta de usuário-chave para homologação.
- Integração externa instável.
- Volume de dados maior que o previsto.
- Permissões mal definidas.
- Falta de testes.
- Dependência de uma única pessoa.

### Modelo

```md
Risco: regras de negócio incompletas
Impacto: alto
Probabilidade: média
Mitigação: validar regras com usuário-chave antes do desenvolvimento.
Responsável: gestor do projeto
```

---

## 22. Checklist Final do Briefing

Antes de considerar o briefing pronto, valide:

- O objetivo do projeto está claro.
- O problema está bem descrito.
- O público-alvo está definido.
- Os perfis de usuário estão definidos.
- As permissões estão documentadas.
- As regras de negócio estão numeradas.
- Os processos estão mapeados.
- Os status estão definidos.
- As telas principais estão previstas.
- As entidades do banco foram identificadas.
- As integrações foram descritas.
- Os requisitos de segurança foram definidos.
- O MVP está separado das versões futuras.
- Cada versão tem objetivo claro.
- Cada versão respeita o ciclo de 30 dias de desenvolvimento.
- Existem critérios de aceite.
- Existe plano de testes.
- Existe plano de homologação.
- Existe plano de produção.
- Existem riscos registrados.
- Existem decisões registradas.

---

# Template Pronto para Criar Briefing

Use o modelo abaixo sempre que for iniciar um novo projeto.

```md
# Briefing do Projeto — [Nome do Projeto]

## 1. Identificação

Nome do projeto:
Tipo de projeto:
Responsável pelo produto:
Responsável técnico:
Responsável pela homologação:
Data de criação:
Versão do briefing:

## 2. Contexto

Descreva o contexto do projeto.

## 3. Problema atual

Descreva o problema que será resolvido.

## 4. Objetivo principal

Descreva o objetivo principal em uma frase.

## 5. Objetivos específicos

- Objetivo 1.
- Objetivo 2.
- Objetivo 3.

## 6. Indicadores de sucesso

- Indicador 1.
- Indicador 2.
- Indicador 3.

## 7. Usuários e perfis

| Perfil | Descrição | Necessidades | Restrições |
|---|---|---|---|
| Administrador |  |  |  |
| Gestor |  |  |  |
| Analista |  |  |  |
| Cliente |  |  |  |

## 8. Matriz de permissões

| Perfil | Tela/Função | Visualizar | Criar | Editar | Aprovar | Excluir | Exportar |
|---|---|---|---|---|---|---|---|
|  |  |  |  |  |  |  |  |

## 9. Escopo

### Dentro do escopo

- Item 1.
- Item 2.
- Item 3.

### Fora do escopo

- Item 1.
- Item 2.
- Item 3.

## 10. Regras de negócio

### RN001 — [Nome da regra]

Descrição:
Quando acontece:
Quem é afetado:
Validação:
Exceções:
Mensagem de erro ou alerta:
Impacto no banco de dados:

### RN002 — [Nome da regra]

Descrição:
Quando acontece:
Quem é afetado:
Validação:
Exceções:
Mensagem de erro ou alerta:
Impacto no banco de dados:

## 11. Processos

### Processo 1 — [Nome]

Objetivo:
Usuários envolvidos:
Gatilho inicial:
Etapas:
1. 
2. 
3. 
Status possíveis:
Regras relacionadas:
Exceções:
Resultado final:

## 12. Jornada do usuário

### Jornada do [Perfil]

1. 
2. 
3. 
4. 

## 13. Mapa inicial de telas

| Tela | Objetivo | Perfis com acesso | Dados exibidos | Ações | Regras aplicadas |
|---|---|---|---|---|---|
| Login |  |  |  |  |  |
| Dashboard |  |  |  |  |  |
| Listagem |  |  |  |  |  |
| Detalhe |  |  |  |  |  |

## 14. Banco de dados inicial

### Entidade: [Nome]

Objetivo:
Campos principais:
Relacionamentos:
Regras:
Auditoria:

## 15. Integrações

### Integração: [Nome]

Objetivo:
Tipo:
Dados enviados:
Dados recebidos:
Frequência:
Autenticação:
Tratamento de erro:
Logs:

## 16. Requisitos não funcionais

Performance:
Disponibilidade:
Segurança:
Escalabilidade:
Usabilidade:
Manutenção:

## 17. MVP

### Objetivo do MVP

Descreva o objetivo.

### Funções incluídas no MVP

- Função 1.
- Função 2.
- Função 3.

### Funções fora do MVP

- Função 1.
- Função 2.
- Função 3.

### Critério de aceite do MVP

Descreva quando o MVP será considerado aprovado.

## 18. Planejamento por versões

### MVP — Versão 0.1

Tempo de desenvolvimento: 30 dias
Funções:
- 
Critério de aceite:

### Versão 1.0

Tempo de desenvolvimento: 30 dias
Objetivo:
Funções:
- 
Critério de aceite:

### Versão 2.0

Tempo de desenvolvimento: 30 dias
Objetivo:
Funções:
- 
Critério de aceite:

### Versão 3.0

Tempo de desenvolvimento: 30 dias
Objetivo:
Funções:
- 
Critério de aceite:

## 19. Testes

Testes funcionais:
Testes de regressão:
Testes de integração:
Testes de segurança:
Testes de performance:
Testes operacionais:

## 20. Homologação

Usuários responsáveis:
Fluxos que devem ser validados:
Critérios para aprovação:
Pendências encontradas:
Aprovado para produção: sim/não

## 21. Produção

Data prevista:
Responsável pela publicação:
Checklist pré-produção:
Plano de rollback:
Monitoramento pós-produção:

## 22. Registro de decisões

| Data | Decisão | Motivo | Impacto | Responsável |
|---|---|---|---|---|
|  |  |  |  |  |

## 23. Riscos

| Risco | Impacto | Probabilidade | Mitigação | Responsável |
|---|---|---|---|---|
|  |  |  |  |  |
```

---

# Prompt Base para IA Criar Briefing

Use este prompt quando quiser que uma IA crie um briefing completo de projeto:

```md
Aja como um Gerente de Projeto Sênior especializado em arquitetura e desenvolvimento de sistemas, SaaS e aplicativos.

Crie um briefing completo para o projeto abaixo, com linguagem clara, organizada e pronta para orientar arquitetura, mapa de telas, banco de dados, backlog, desenvolvimento, testes, homologação e produção.

O briefing deve conter:

1. Identificação do projeto.
2. Contexto e problema atual.
3. Objetivo principal.
4. Objetivos específicos.
5. Indicadores de sucesso.
6. Perfis de usuários.
7. Matriz de permissões.
8. Escopo dentro e fora do projeto.
9. Regras de negócio numeradas e testáveis.
10. Processos envolvidos de ponta a ponta.
11. Jornada dos usuários.
12. Mapa inicial de telas.
13. Entidades principais do banco de dados.
14. Integrações previstas.
15. Requisitos não funcionais.
16. Definição clara do MVP.
17. Planejamento por versões: MVP, V1, V2, V3 e futuras.
18. Cada versão deve considerar 30 dias de desenvolvimento, depois testes, homologação e produção.
19. Critérios de aceite por funcionalidade.
20. Plano de testes.
21. Plano de homologação.
22. Plano de produção.
23. Riscos do projeto.
24. Registro de decisões.

Dados do projeto:
[COLE AQUI AS INFORMAÇÕES DO PROJETO]

Regras de escrita:
- Não seja genérico.
- Explique regras de negócio com clareza.
- Separe o que é MVP do que é versão futura.
- Pense como produto, processo, arquitetura, banco de dados, segurança e operação.
- Sempre que uma informação estiver faltando, crie uma seção chamada "Pontos a confirmar".
- O resultado deve estar em Markdown.
```

---

# Prompt Base para IA Revisar Briefing

```md
Aja como um Gerente de Projeto Sênior especializado em arquitetura e desenvolvimento de sistemas, SaaS e aplicativos.

Revise o briefing abaixo e aponte:

1. Regras de negócio ambíguas.
2. Processos incompletos.
3. Funcionalidades sem critério de aceite.
4. Telas faltantes.
5. Perfis de usuário mal definidos.
6. Permissões perigosas ou incompletas.
7. Entidades de banco de dados que precisam existir.
8. Integrações não detalhadas.
9. Riscos de escopo.
10. O que deve entrar no MVP.
11. O que deve ficar para V1, V2, V3 ou futuro.
12. Perguntas obrigatórias antes de desenvolver.

Briefing para revisão:
[COLE AQUI O BRIEFING]
```

---

# Perguntas Essenciais para Levantar Briefing

Use estas perguntas quando o projeto ainda estiver indefinido.

## Sobre o negócio

- Qual problema o sistema resolve?
- Quem sofre com esse problema hoje?
- Como esse problema é resolvido atualmente?
- Qual o custo do problema hoje?
- O que precisa melhorar primeiro?

## Sobre usuários

- Quem vai usar o sistema?
- Existem níveis de permissão?
- Quem pode aprovar, editar ou excluir informações?
- Existe usuário externo?
- Cliente acessa o sistema?

## Sobre processos

- Qual processo começa o fluxo?
- Qual processo termina o fluxo?
- Quais status existem?
- Quem muda cada status?
- O que pode bloquear o andamento?
- Quais exceções existem?

## Sobre regras de negócio

- Quais regras não podem ser quebradas?
- O que precisa ser validado automaticamente?
- O que exige aprovação?
- O que exige justificativa?
- O que deve gerar histórico?

## Sobre dados

- Quais informações precisam ser cadastradas?
- Quais campos são obrigatórios?
- Quais dados são sensíveis?
- Quais dados precisam de histórico?
- Quais dados precisam ser exportados?

## Sobre telas

- Quais telas são obrigatórias no MVP?
- Qual tela o usuário acessa primeiro?
- Quais filtros são necessários?
- Quais ações existem em cada tela?
- Quais mensagens o sistema deve mostrar?

## Sobre integrações

- O sistema precisa enviar ou receber dados de outro sistema?
- Qual API será usada?
- Existe token, chave ou autenticação?
- Qual o formato dos dados?
- O que acontece quando a integração falha?

## Sobre versões

- O que precisa estar pronto em 30 dias?
- O que pode esperar para a próxima versão?
- O que é essencial para operar?
- O que é melhoria?
- O que é automação avançada?

---

# Regra de Ouro

Um briefing só está pronto quando qualquer pessoa da equipe consegue entender:

- O que será feito.
- Por que será feito.
- Para quem será feito.
- Como o processo funciona.
- Quais regras não podem ser quebradas.
- Quais telas precisam existir.
- Quais dados precisam ser salvos.
- O que entra no MVP.
- O que fica para versões futuras.
- Como será testado.
- Como será homologado.
- Como será publicado em produção.

Se o briefing não permite tomar decisão, estimar esforço, desenhar telas, modelar banco e testar regras, ele ainda não está pronto.
