# Skill — Guia Sênior de Gamificação para Produto Software, App e SaaS

## Limite desta skill

Esta skill define mecanismos de gamificação responsável, como pontos, níveis, missões, selos, conquistas, progresso, feedback visual, recompensas e ranking saudável.

Ela pode citar retenção e notificações quando isso reforçar comportamento útil, mas não deve substituir:

- `skill-retencao.md` para estratégia de retorno, ativação e reativação;
- `skill-notificacoes.md` para canais, templates, frequência e envio;
- `skill-relatorios-bi-dashboard.md` para métricas e análise de resultado;
- `skill-dados.md` para modelagem completa das tabelas;
- `skill-ux-ui.md` para design visual completo.

Esta skill responde "como o sistema mostra progresso, reconhece ações úteis e incentiva continuidade sem manipulação?".

---

## Regra de origem do evento

Todo evento de engajamento, retenção, gamificação, notificação ou integração deve ter origem clara.

A IA nunca deve criar notificação, ponto, missão, webhook, retenção, automação ou integração sem saber qual evento real disparou a ação.

### Origens possíveis

```txt
usuario
sistema
backend
webhook
cron
admin
suporte
pagamento
tarefa
relatorio
integracao
gamificacao
retencao
notificacao
```

### Dados mínimos do evento

Todo evento importante deve registrar ou transportar, quando aplicável:

```txt
evento
origem
usuario_id
tenant_id
workspace_id
entidade_tipo
entidade_id
data_hora
status
request_id
metadata
```

### Regra obrigatória

O evento deve representar algo real que aconteceu no sistema.

Exemplos:

- tarefa atribuída;
- prazo próximo;
- pagamento confirmado;
- missão concluída;
- integração falhou;
- relatório gerado;
- chamado atualizado;
- usuário ficou inativo;
- webhook recebido.

Se não existe evento real, a IA não deve criar comunicação, pontuação, reativação ou integração artificial.

Essa regra evita gamificação solta, notificação sem motivo, retenção agressiva e webhooks sem rastreabilidade.


---

## Regra de nomenclatura de campos de data

Por padrão, este projeto usa nomes de campos em português para datas, auditoria, histórico, eventos e filas.

### Campos padrão

```txt
criado_em
atualizado_em
excluido_em
enviado_em
entregue_em
lido_em
clicado_em
dispensado_em
ocorreu_em
processado_em
proxima_tentativa_em
ultimo_login_em
ultima_notificacao_enviada_em
ultimo_evento_de_valor_em
concluido_em
expira_em
criado_por
atualizado_por
excluido_por
```

### Regra obrigatória

Não misturar nomes de campos em inglês com nomes em português no mesmo projeto.

Quando o projeto usar português, todos os exemplos de tabelas, eventos, filas, logs, notificações, webhooks, payloads internos e relatórios devem seguir o padrão em português.

### Tradução recomendada

```txt
criado_em                  -> criado_em
atualizado_em                  -> atualizado_em
excluido_em                  -> excluido_em
enviado_em                     -> enviado_em
entregue_em                -> entregue_em
lido_em                     -> lido_em
clicado_em                  -> clicado_em
dispensado_em                -> dispensado_em
ocorreu_em                 -> ocorreu_em
processado_em                -> processado_em
proxima_tentativa_em             -> proxima_tentativa_em
ultimo_login_em               -> ultimo_login_em
ultima_notificacao_enviada_em   -> ultima_notificacao_enviada_em
ultimo_evento_de_valor_em         -> ultimo_evento_de_valor_em
concluido_em                -> concluido_em
expira_em                  -> expira_em
criado_por                  -> criado_por
atualizado_por                  -> atualizado_por
excluido_por                  -> excluido_por
```

Exceção: termos técnicos como `tenant_id`, `workspace_id`, `request_id`, `trace_id`, `external_id`, `idempotency_key`, `metadata`, `payload`, `endpoint`, `webhook` e `status` podem ser mantidos quando esse for o padrão técnico do projeto.


---


## 1. Identidade da Skill

**Nome da skill:** Gamificação Sênior para Produto Digital  
**Área:** Produto, SaaS, App, UX, Regras de Negócio, Engajamento e Retenção  
**Objetivo:** orientar uma IA, gerente de produto, designer ou desenvolvedor a criar uma documentação profissional de gamificação para sistemas digitais, com mecanismos que aumentem o engajamento, incentivem o usuário a cumprir tarefas e mantenham uma experiência saudável, clara e alinhada ao valor real do produto.

> Esta skill não deve criar mecanismos abusivos, manipulativos, compulsivos ou que explorem vulnerabilidades do usuário. O foco correto é criar **engajamento responsável**, senso de progresso, clareza, motivação e recompensa proporcional ao valor entregue.

---

## 2. Papel que a IA deve assumir

Ao usar esta skill, a IA deve agir como um **engenheiro de produto sênior especialista em gamificação para software, app e SaaS**, com visão de:

- Estratégia de produto.
- UX/UI e comportamento do usuário.
- Regras de negócio.
- Retenção e ativação.
- Jornada do usuário.
- Métricas de engajamento.
- Sistemas de recompensa.
- Ética em design de produto.
- Documentação clara para desenvolvimento.

A IA deve criar gamificações que ajudem o usuário a:

- Entender o que precisa fazer.
- Começar com pouco esforço.
- Sentir progresso real.
- Receber feedback imediato.
- Voltar ao sistema com propósito.
- Cumprir tarefas importantes.
- Evoluir dentro do produto.
- Criar rotina de uso saudável.

---

## 3. Princípio central da gamificação

Gamificação profissional não é apenas colocar pontos, medalhas e ranking.

Gamificação profissional é desenhar um sistema de comportamento onde:

1. O usuário sabe qual ação deve realizar.
2. A ação tem valor real para ele ou para o negócio.
3. O sistema reduz atrito para essa ação acontecer.
4. O usuário recebe feedback claro após agir.
5. O progresso fica visível.
6. Existe recompensa proporcional.
7. O usuário sente que está avançando.
8. O produto cria hábito sem forçar dependência.

---

## 4. Regras éticas obrigatórias

A IA deve seguir estas regras antes de propor qualquer mecânica:

### 4.1. Não criar dependência artificial

Evitar mecanismos feitos apenas para prender o usuário sem entregar valor real.

**Evitar:**

- Recompensas aleatórias agressivas.
- Notificações excessivas.
- Penalidades emocionais exageradas.
- Medo de perder tudo caso o usuário pare.
- Rankings humilhantes.
- Contadores de urgência falsos.
- Mensagens de culpa.

### 4.2. Priorizar utilidade real

Toda mecânica deve responder:

- Qual comportamento útil ela incentiva?
- Qual valor o usuário recebe?
- Qual valor o negócio recebe?
- Como medir se está funcionando?

### 4.3. Dar controle ao usuário

Sempre que possível, permitir:

- Pausar notificações.
- Ocultar ranking.
- Desativar lembretes não essenciais.
- Recuperar sequência perdida.
- Escolher metas pessoais.
- Entender por que recebeu pontos ou perdeu progresso.

### 4.4. Evitar exploração de ansiedade

A gamificação deve motivar, não pressionar de forma tóxica.

**Preferir:**

- “Você está perto de concluir sua meta.”
- “Faltam 2 tarefas para fechar seu ciclo.”
- “Seu progresso desta semana está melhor que o anterior.”

**Evitar:**

- “Você está ficando para trás.”
- “Todo mundo está melhor que você.”
- “Você perdeu tudo porque não entrou hoje.”

---

## 5. Estrutura do documento de gamificação do projeto

Sempre que a IA for criar um documento de gamificação, deve seguir esta estrutura:

1. Visão geral da gamificação.
2. Objetivos de negócio.
3. Objetivos do usuário.
4. Perfil dos usuários.
5. Comportamentos que o sistema precisa incentivar.
6. Ações pontuáveis.
7. Sistema de pontos.
8. Níveis de evolução.
9. Medalhas, conquistas ou selos.
10. Missões e desafios.
11. Sequência de uso ou streak.
12. Recompensas.
13. Feedback visual e mensagens.
14. Notificações e lembretes.
15. Ranking, se fizer sentido.
16. Regras antifraude.
17. Regras de perda, pausa e recuperação.
18. Métricas de sucesso.
19. Tabelas para desenvolvimento.
20. Roadmap por versão.

---

## 6. Objetivos de negócio

A gamificação deve estar ligada a objetivos reais do produto.

Exemplos de objetivos:

- Aumentar ativação de novos usuários.
- Aumentar conclusão de tarefas.
- Reduzir abandono no onboarding.
- Aumentar uso recorrente semanal.
- Melhorar qualidade dos dados cadastrados.
- Estimular colaboração entre equipes.
- Melhorar cumprimento de prazos.
- Reduzir tarefas atrasadas.
- Aumentar registro de evidências.
- Incentivar aprendizado dentro do sistema.

A IA deve transformar esses objetivos em regras práticas.

**Exemplo:**

Objetivo: reduzir tarefas atrasadas.  
Mecânica: pontos extras para tarefas concluídas antes do prazo, selo de consistência semanal e alerta preventivo antes do vencimento.

---

## 7. Objetivos do usuário

A gamificação também deve beneficiar o usuário, não apenas a empresa.

Exemplos:

- Sentir clareza sobre o que fazer.
- Ter reconhecimento pelo esforço.
- Visualizar progresso.
- Ganhar autonomia.
- Melhorar produtividade.
- Reduzir esquecimento.
- Priorizar tarefas certas.
- Receber feedback justo.
- Evoluir por mérito.

A IA deve sempre equilibrar:

- Interesse do negócio.
- Interesse do usuário.
- Saúde da experiência.

---

## 8. Mapa de comportamentos desejados

Antes de criar pontos, a IA deve mapear os comportamentos que precisam acontecer.

### 8.1. Modelo de tabela

| Comportamento desejado | Por que é importante | Frequência esperada | Valor para o usuário | Valor para o negócio |
|---|---|---:|---|---|
| Completar perfil | Personaliza a experiência | 1 vez | Recebe recomendações melhores | Dados mais completos |
| Criar primeira tarefa | Ativa o uso do produto | 1 vez | Começa a organizar trabalho | Aumenta ativação |
| Concluir tarefa no prazo | Gera produtividade | Recorrente | Sente progresso | Reduz atraso |
| Atualizar status | Melhora acompanhamento | Diário/semanal | Evita cobranças manuais | Melhora gestão |
| Anexar evidência | Dá rastreabilidade | Por tarefa | Comprova entrega | Melhora auditoria |

---

## 9. Classificação das ações do sistema

A IA deve separar as ações em categorias.

### 9.1. Ações de ativação

São ações que fazem o usuário entrar no fluxo principal do produto.

Exemplos:

- Completar cadastro.
- Fazer primeiro login.
- Criar primeira demanda.
- Concluir onboarding.
- Convidar equipe.
- Configurar preferências.

### 9.2. Ações de rotina

São ações que mantêm o produto vivo.

Exemplos:

- Atualizar status.
- Concluir tarefa.
- Responder comentário.
- Registrar evidência.
- Revisar pendências.
- Validar uma etapa.

### 9.3. Ações de qualidade

São ações que melhoram o valor dos dados.

Exemplos:

- Preencher descrição completa.
- Adicionar prazo realista.
- Vincular responsável.
- Adicionar anexo correto.
- Marcar prioridade.
- Encerrar tarefa com justificativa.

### 9.4. Ações de colaboração

São ações que melhoram o trabalho em equipe.

Exemplos:

- Comentar com solução.
- Ajudar outro usuário.
- Reatribuir corretamente.
- Aprovar entrega.
- Dar feedback útil.

### 9.5. Ações estratégicas

São ações que indicam maturidade de uso.

Exemplos:

- Criar relatório.
- Analisar indicadores.
- Melhorar processo.
- Criar template.
- Padronizar operação.

---

## 10. Sistema de pontos

O sistema de pontos deve ser simples, previsível e justo.

### 10.1. Regras gerais

- Pontos devem premiar ações úteis.
- Ações fáceis não devem gerar pontos altos.
- Ações repetitivas devem ter limite diário.
- Ações críticas devem ter maior peso.
- Pontos devem ser explicados ao usuário.
- O sistema deve impedir fraude.
- Pontos não devem substituir salário, avaliação humana ou gestão formal.

### 10.2. Exemplo de tabela de pontos

| Ação | Pontos | Limite | Observação |
|---|---:|---|---|
| Completar perfil | 50 | 1 vez | Ação de ativação |
| Criar primeira tarefa | 30 | 1 vez | Ativa uso inicial |
| Concluir tarefa no prazo | 20 | Sem limite artificial | Só conta se tarefa for válida |
| Concluir tarefa antes do prazo | 30 | Sem limite artificial | Bônus de produtividade |
| Atualizar status da tarefa | 5 | 5 vezes/dia | Evita spam |
| Anexar evidência válida | 15 | 1 por tarefa | Precisa estar vinculada à entrega |
| Ajudar outro usuário | 10 | 3 vezes/dia | Requer confirmação ou interação real |
| Reabrir tarefa por erro | -10 | Por ocorrência | Usar com cuidado e transparência |

### 10.3. Pontos por qualidade

Nem toda conclusão deve valer igual.

A IA pode propor multiplicadores:

| Critério | Multiplicador |
|---|---:|
| Entrega no prazo | 1.0x |
| Entrega antes do prazo | 1.2x |
| Entrega com evidência | 1.3x |
| Entrega aprovada sem retrabalho | 1.5x |
| Entrega reaberta | 0.5x |

---

## 11. Sistema de níveis

Níveis devem representar evolução real, não apenas acúmulo vazio de pontos.

### 11.1. Regras para níveis

- Níveis devem ter nomes claros.
- Cada nível deve indicar maturidade de uso.
- O avanço deve parecer possível.
- Os primeiros níveis devem ser rápidos.
- Os níveis avançados devem exigir consistência.
- Subir de nível deve desbloquear algo útil ou simbólico.

### 11.2. Exemplo de níveis

| Nível | Nome | Pontos necessários | Significado |
|---:|---|---:|---|
| 1 | Iniciante | 0 | Começou a usar o sistema |
| 2 | Organizado | 150 | Já entende o fluxo básico |
| 3 | Produtivo | 400 | Cumpre tarefas com frequência |
| 4 | Consistente | 900 | Mantém rotina e qualidade |
| 5 | Referência | 1.800 | Ajuda outros e entrega com excelência |
| 6 | Mestre do Processo | 3.500 | Usa o sistema de forma estratégica |

### 11.3. Benefícios por nível

Os benefícios podem ser:

- Personalização visual.
- Selos no perfil.
- Acesso a relatórios pessoais.
- Templates avançados.
- Destaque em painel de reconhecimento.
- Certificado interno.
- Liberação de recursos não críticos.

Evitar liberar recursos essenciais apenas para usuários avançados, pois isso pode prejudicar usuários novos.

---

## 12. Medalhas, conquistas e selos

Medalhas devem reconhecer marcos importantes.

### 12.1. Tipos de medalhas

#### Medalhas de ativação

- Primeiro Login.
- Perfil Completo.
- Primeira Tarefa Criada.
- Primeira Entrega.

#### Medalhas de consistência

- 5 dias atualizando tarefas.
- 4 semanas sem atraso.
- 10 entregas no prazo.
- 30 tarefas finalizadas.

#### Medalhas de qualidade

- Entrega aprovada sem retrabalho.
- Evidência exemplar.
- Descrição completa.
- Processo bem documentado.

#### Medalhas de colaboração

- Ajudou um colega.
- Comentário útil.
- Participante ativo.
- Mentor interno.

#### Medalhas estratégicas

- Criador de template.
- Analista de indicadores.
- Melhorador de processo.
- Gestor de fluxo.

### 12.2. Modelo de tabela de medalhas

| Medalha | Critério | Tipo | Recompensa | Visibilidade |
|---|---|---|---|---|
| Primeiro Passo | Completar o primeiro login | Ativação | 20 pontos | Perfil |
| Missão Cumprida | Concluir primeira tarefa | Ativação | 30 pontos | Perfil e dashboard |
| Semana Produtiva | Concluir 5 tarefas na semana | Consistência | 80 pontos | Perfil |
| Sem Retrabalho | 10 tarefas aprovadas sem reabertura | Qualidade | Selo especial | Perfil |
| Parceiro de Equipe | Ajudar 5 colegas | Colaboração | 100 pontos | Ranking colaborativo |

---

## 13. Missões e desafios

Missões ajudam o usuário a saber o que fazer agora.

### 13.1. Regras para missões

- Cada missão deve ter objetivo claro.
- Deve ser possível concluir em tempo razoável.
- Deve estar ligada a uma ação útil.
- Deve mostrar progresso parcial.
- Deve ter recompensa clara.
- Deve evitar tarefas artificiais.

### 13.2. Tipos de missão

#### Missão diária

Pequena, rápida e ligada à rotina.

Exemplos:

- Atualizar 3 tarefas pendentes.
- Revisar suas prioridades do dia.
- Concluir uma tarefa simples.

#### Missão semanal

Focada em consistência.

Exemplos:

- Concluir 80% das tarefas da semana.
- Não deixar tarefas sem responsável.
- Encerrar pendências antigas.

#### Missão de onboarding

Focada em ativação.

Exemplos:

- Complete seu perfil.
- Crie sua primeira tarefa.
- Convide um membro da equipe.
- Entenda o painel principal.

#### Missão de qualidade

Focada em melhoria de processo.

Exemplos:

- Adicione evidência em todas as tarefas finalizadas.
- Padronize a descrição de 5 demandas.
- Revise tarefas sem prazo.

### 13.3. Modelo de missão

| Campo | Descrição |
|---|---|
| Nome da missão | Título curto e claro |
| Objetivo | O que o usuário deve fazer |
| Critério de conclusão | Regra exata para considerar concluída |
| Prazo | Diário, semanal, mensal ou personalizado |
| Recompensa | Pontos, selo, progresso, benefício |
| Mensagem de incentivo | Texto exibido ao usuário |
| Mensagem de conclusão | Texto após completar |

---

## 14. Streaks ou sequências de uso

Streaks são sequências de uso contínuo. Devem ser usadas com cuidado.

### 14.1. Quando usar

Usar quando a rotina for realmente importante, como:

- Revisar tarefas diariamente.
- Registrar progresso diário.
- Estudar um conteúdo.
- Fazer check-in operacional.
- Atualizar dados críticos.

### 14.2. Quando não usar

Evitar quando:

- O usuário não precisa entrar todos os dias.
- O produto é usado por demanda.
- A frequência depende de terceiros.
- O streak pode gerar ansiedade.

### 14.3. Regras saudáveis para streak

- Permitir dias de pausa.
- Permitir recuperação limitada.
- Valorizar consistência semanal, não apenas uso diário.
- Não zerar todo o histórico por uma falha.
- Mostrar progresso acumulado além da sequência atual.

### 14.4. Exemplo de regra

| Regra | Descrição |
|---|---|
| Sequência ativa | Usuário revisou suas tarefas no dia |
| Proteção semanal | 1 dia de pausa permitido por semana |
| Recuperação | Pode recuperar 1 vez por mês |
| Recompensa | Bônus de pontos a cada 5 dias úteis |
| Limite | Não usar notificações agressivas |

---

## 15. Recompensas

Recompensas devem reforçar comportamentos corretos.

### 15.1. Tipos de recompensa

#### Recompensa simbólica

- Pontos.
- Selos.
- Medalhas.
- Níveis.
- Destaque no perfil.

#### Recompensa funcional

- Templates extras.
- Relatórios pessoais.
- Personalização de tela.
- Atalhos.
- Automações simples.

#### Recompensa social

- Reconhecimento no dashboard.
- Destaque semanal.
- Agradecimento público.
- Selo de colaboração.

#### Recompensa de progresso

- Barra de evolução.
- Histórico de entregas.
- Comparativo com a própria semana anterior.
- Mapa de conquistas.

### 15.2. Regras para boas recompensas

- A recompensa deve ser proporcional ao esforço.
- O usuário deve entender por que recebeu.
- O benefício não deve prejudicar outros usuários.
- Evitar recompensas financeiras automáticas sem regra administrativa clara.
- Evitar competição tóxica.

---

## 16. Feedback visual

Feedback visual é uma das partes mais importantes da gamificação.

### 16.1. Tipos de feedback

- Barra de progresso.
- Checklist concluído.
- Animação discreta de sucesso.
- Mensagem de parabéns.
- Pontos somados em tempo real.
- Selo desbloqueado.
- Indicador de próxima meta.
- Painel de evolução.

### 16.2. Regras de design

- O feedback deve ser imediato.
- A mensagem deve ser curta.
- A animação deve ser rápida.
- Evitar excesso de confete ou distração.
- Mostrar sempre o próximo passo.
- Usar cores com consistência.
- Feedback negativo deve ser orientativo, não punitivo.

### 16.3. Exemplos de mensagens

#### Sucesso

- “Tarefa concluída. Você avançou mais um passo.”
- “Boa entrega! Seu progresso semanal aumentou.”
- “Missão finalizada. Pontos adicionados.”

#### Perto da conclusão

- “Falta apenas 1 tarefa para fechar sua meta da semana.”
- “Você está a 80% da missão semanal.”
- “Complete mais uma ação para desbloquear o próximo selo.”

#### Atraso ou pendência

- “Esta tarefa precisa de atenção.”
- “O prazo está próximo. Revise antes do vencimento.”
- “Você pode reorganizar esta tarefa ou pedir apoio.”

---

## 17. Notificações e lembretes

Notificações devem ajudar o usuário, não incomodar.

### 17.1. Regras obrigatórias

- Notificar apenas quando houver motivo real.
- Evitar repetição excessiva.
- Dar opção de configurar frequência.
- Diferenciar lembrete, alerta e reconhecimento.
- Não usar culpa ou medo.
- Não enviar notificação sem ação possível.

### 17.2. Tipos de notificação

| Tipo | Quando usar | Exemplo |
|---|---|---|
| Lembrete preventivo | Antes do prazo | “Você tem 2 tarefas vencendo amanhã.” |
| Reconhecimento | Após ação positiva | “Você concluiu sua meta semanal.” |
| Próximo passo | Após concluir etapa | “Agora você pode revisar a entrega.” |
| Risco operacional | Quando algo pode atrasar | “Existe tarefa crítica sem responsável.” |
| Resumo | Diário ou semanal | “Resumo da sua semana: 8 tarefas concluídas.” |

### 17.3. Frequência recomendada

| Canal | Frequência saudável |
|---|---|
| Notificação interna | Conforme evento relevante |
| E-mail | Resumo diário ou semanal |
| Push | Apenas lembretes importantes |
| WhatsApp/SMS | Somente eventos críticos ou autorizados |

---

## 18. Ranking

Ranking pode funcionar, mas deve ser usado com muito cuidado.

### 18.1. Quando usar ranking

Usar quando:

- O ambiente aceita competição saudável.
- As ações são comparáveis.
- Todos têm oportunidades semelhantes.
- O ranking não expõe negativamente usuários.
- Existe regra clara contra manipulação.

### 18.2. Quando evitar ranking

Evitar quando:

- Os usuários têm funções muito diferentes.
- A comparação pode gerar injustiça.
- O volume de tarefas depende de cargo ou setor.
- O ambiente é sensível.
- O ranking pode virar pressão pública.

### 18.3. Alternativas melhores

- Ranking por equipe.
- Ranking por evolução pessoal.
- Ranking por colaboração.
- Destaques semanais rotativos.
- Metas coletivas.
- Comparação do usuário com ele mesmo.

### 18.4. Exemplo de ranking saudável

| Tipo | Critério | Observação |
|---|---|---|
| Evolução pessoal | Melhorou em relação à semana anterior | Evita comparação injusta |
| Equipe | Percentual de tarefas no prazo | Incentiva colaboração |
| Qualidade | Entregas aprovadas sem retrabalho | Valoriza qualidade |
| Colaboração | Apoios reconhecidos por colegas | Evita foco apenas em volume |

---

## 19. Progressão e curva de dificuldade

A evolução deve ser bem calibrada.

### 19.1. Regra dos primeiros minutos

Nos primeiros minutos, o usuário precisa sentir avanço rápido.

Exemplo:

- Completar perfil: recompensa imediata.
- Primeira ação: selo simples.
- Primeiro painel: checklist visível.
- Primeira tarefa concluída: feedback forte.

### 19.2. Regra dos primeiros dias

Nos primeiros dias, o usuário precisa entender o ciclo principal.

Exemplo:

- Dia 1: configurar perfil e criar primeira tarefa.
- Dia 2: atualizar status.
- Dia 3: concluir tarefa.
- Dia 4: revisar painel.
- Dia 5: receber resumo da evolução.

### 19.3. Regra da maturidade

Com o tempo, a gamificação deve migrar de recompensa simples para domínio real.

Exemplo:

- Início: pontos e onboarding.
- Meio: missões, metas e selos.
- Avançado: templates, relatórios, liderança e melhoria de processo.

---

## 20. Regras antifraude

Toda gamificação precisa prever abuso.

### 20.1. Fraudes comuns

- Criar tarefas falsas para ganhar pontos.
- Fechar e reabrir tarefas repetidamente.
- Atualizar status sem mudança real.
- Marcar evidência inválida.
- Comentar mensagens vazias.
- Dividir uma tarefa real em várias tarefas artificiais.

### 20.2. Regras de proteção

| Risco | Regra antifraude |
|---|---|
| Tarefa falsa | Pontos só contam após validação ou tempo mínimo |
| Spam de status | Limite diário de pontuação por atualização |
| Evidência inválida | Exigir anexo, comentário ou aprovação |
| Comentário vazio | Comentário precisa ter tamanho mínimo e contexto |
| Reabrir tarefa | Reduzir pontos se houver retrabalho |
| Volume artificial | Usar peso por complexidade, não só quantidade |

### 20.3. Auditoria

Registrar no banco:

- Usuário.
- Ação.
- Data e hora.
- Origem da pontuação.
- Entidade relacionada.
- Pontos gerados.
- Regra aplicada.
- Status de validação.
- Motivo de bloqueio, se houver.

---

## 21. Banco de dados sugerido

A IA deve sugerir uma modelagem mínima para suportar a gamificação.

### 21.1. Tabelas principais

#### usuarios_gamificacao

| Campo | Tipo sugerido | Descrição |
|---|---|---|
| id | int/uuid | Identificador |
| usuario_id | int/uuid | Usuário relacionado |
| pontos_total | int | Pontos acumulados |
| nivel_atual | int | Nível atual |
| streak_atual | int | Sequência atual |
| maior_streak | int | Maior sequência histórica |
| ultima_atividade | datetime | Última ação válida |
| criado_em | datetime | Data de criação |
| atualizado_em | datetime | Data de atualização |

#### gamificacao_eventos

| Campo | Tipo sugerido | Descrição |
|---|---|---|
| id | int/uuid | Identificador |
| usuario_id | int/uuid | Usuário |
| tipo_evento | varchar | Tipo da ação |
| entidade_tipo | varchar | Tarefa, demanda, comentário etc. |
| entidade_id | int/uuid | Registro relacionado |
| pontos | int | Pontos gerados |
| multiplicador | decimal | Multiplicador aplicado |
| status | varchar | válido, pendente, bloqueado, cancelado |
| motivo | text | Explicação da regra |
| criado_em | datetime | Data do evento |

#### gamificacao_medalhas

| Campo | Tipo sugerido | Descrição |
|---|---|---|
| id | int/uuid | Identificador |
| nome | varchar | Nome da medalha |
| descricao | text | Descrição |
| criterio | text/json | Regra de conquista |
| pontos_bonus | int | Pontos extras |
| ativo | boolean | Se está ativa |

#### usuarios_medalhas

| Campo | Tipo sugerido | Descrição |
|---|---|---|
| id | int/uuid | Identificador |
| usuario_id | int/uuid | Usuário |
| medalha_id | int/uuid | Medalha |
| conquistado_em | datetime | Data da conquista |
| origem_evento_id | int/uuid | Evento que gerou a medalha |

#### gamificacao_missoes

| Campo | Tipo sugerido | Descrição |
|---|---|---|
| id | int/uuid | Identificador |
| nome | varchar | Nome da missão |
| descricao | text | Descrição |
| tipo | varchar | diária, semanal, onboarding, qualidade |
| criterio | json/text | Regra de conclusão |
| recompensa_pontos | int | Pontos ao concluir |
| ativo | boolean | Status |
| criado_em | datetime | Data de criação |

#### usuarios_missoes

| Campo | Tipo sugerido | Descrição |
|---|---|---|
| id | int/uuid | Identificador |
| usuario_id | int/uuid | Usuário |
| missao_id | int/uuid | Missão |
| progresso | int | Progresso atual |
| meta | int | Total necessário |
| status | varchar | em_andamento, concluida, expirada |
| iniciado_em | datetime | Início |
| concluido_em | datetime | Conclusão |

---

## 22. Regras de negócio para tarefas

Para sistemas de tarefas, plano de ação, demandas ou operação, a IA pode usar este modelo.

### 22.1. Eventos pontuáveis

| Evento | Regra |
|---|---|
| Tarefa criada | Pontuar apenas se tiver título, responsável e prazo |
| Tarefa aceita | Pontuar o responsável ao assumir |
| Tarefa iniciada | Pontuar quando status mudar para em andamento |
| Tarefa concluída | Pontuar quando marcada como concluída |
| Tarefa aprovada | Pontuar bônus se validada por gestor |
| Tarefa reaberta | Reduzir ou bloquear bônus de qualidade |
| Tarefa vencida | Não precisa punir sempre; preferir alerta e plano de recuperação |
| Evidência anexada | Pontuar quando vinculada à conclusão |
| Comentário útil | Pontuar com limite e critério mínimo |

### 22.2. Status recomendados

- Pendente.
- Em andamento.
- Aguardando validação.
- Concluída.
- Reaberta.
- Cancelada.
- Vencida.

### 22.3. Relação status x gamificação

| Status | Ação de gamificação |
|---|---|
| Pendente | Sugerir próxima ação |
| Em andamento | Mostrar progresso |
| Aguardando validação | Não liberar pontos totais ainda |
| Concluída | Pontuar parcialmente |
| Aprovada | Liberar bônus de qualidade |
| Reaberta | Reduzir multiplicador |
| Vencida | Gerar missão de recuperação |

---

## 23. Gamificação por perfil de usuário

A IA deve considerar que cada perfil pode ter incentivos diferentes.

### 23.1. Usuário comum

Foco:

- Concluir tarefas.
- Atualizar status.
- Cumprir prazos.
- Registrar evidências.
- Evoluir individualmente.

Mecânicas indicadas:

- Missões diárias.
- Barra de progresso.
- Pontos por tarefa.
- Selos pessoais.
- Sequência semanal.

### 23.2. Analista ou operador

Foco:

- Volume com qualidade.
- Padronização.
- Resolução de pendências.
- Organização de dados.

Mecânicas indicadas:

- Metas por fila.
- Bônus por qualidade.
- Selos de consistência.
- Missões de limpeza de pendências.

### 23.3. Gestor

Foco:

- Validar entregas.
- Destravar gargalos.
- Acompanhar equipe.
- Melhorar processo.

Mecânicas indicadas:

- Painel de saúde da equipe.
- Metas coletivas.
- Reconhecimento por redução de atraso.
- Missões de aprovação rápida.

### 23.4. Administrador

Foco:

- Configurar regras.
- Monitorar abusos.
- Ajustar metas.
- Revisar indicadores.

Mecânicas indicadas:

- Dashboard de regras.
- Logs de pontuação.
- Simulador de impacto.
- Controle de ativar/desativar mecânicas.

---

## 24. Jornada gamificada do usuário

A IA deve montar a gamificação por jornada.

### 24.1. Etapa 1 — Entrada

Objetivo: reduzir atrito e gerar primeira vitória.

Mecânicas:

- Checklist de onboarding.
- Barra de progresso de cadastro.
- Primeira medalha simples.
- Tutorial curto.

### 24.2. Etapa 2 — Ativação

Objetivo: fazer o usuário executar a primeira ação de valor.

Mecânicas:

- Missão “Primeira tarefa”.
- Pontos de ativação.
- Feedback visual forte.
- Próximo passo sugerido.

### 24.3. Etapa 3 — Rotina

Objetivo: criar hábito de uso.

Mecânicas:

- Missões diárias ou semanais.
- Lembretes preventivos.
- Resumo de progresso.
- Streak saudável.

### 24.4. Etapa 4 — Evolução

Objetivo: aprofundar uso e qualidade.

Mecânicas:

- Níveis.
- Selos de qualidade.
- Desafios avançados.
- Relatórios pessoais.

### 24.5. Etapa 5 — Advocacia

Objetivo: transformar usuário avançado em referência.

Mecânicas:

- Selo de mentor.
- Destaque colaborativo.
- Criação de templates.
- Reconhecimento por ajudar equipe.

---

## 25. Métricas de sucesso

A gamificação deve ser medida.

### 25.1. Métricas de ativação

- Percentual de usuários que completam onboarding.
- Tempo até primeira ação de valor.
- Percentual de perfis completos.
- Primeira tarefa criada.

### 25.2. Métricas de engajamento

- Usuários ativos diários.
- Usuários ativos semanais.
- Frequência de login.
- Tarefas atualizadas por semana.
- Missões concluídas.

### 25.3. Métricas de produtividade

- Tarefas concluídas no prazo.
- Tarefas atrasadas.
- Tempo médio de conclusão.
- Taxa de retrabalho.
- Demandas sem responsável.

### 25.4. Métricas de qualidade

- Evidências anexadas.
- Tarefas aprovadas sem reabertura.
- Dados completos.
- Comentários úteis.
- Templates usados.

### 25.5. Métricas de risco

- Usuários incomodados com notificações.
- Desativação de alertas.
- Comportamento de spam.
- Pontuação artificial.
- Queda de qualidade por busca de pontos.

---

## 26. Roadmap de implantação

A IA deve propor gamificação por versões, evitando criar tudo no MVP.

### 26.1. MVP — 30 dias de desenvolvimento

Foco: ativação, clareza e progresso básico.

Entregas:

- Pontuação simples.
- Checklist de onboarding.
- Barra de progresso.
- Pontos por ações principais.
- 5 a 10 medalhas básicas.
- Log de eventos de pontuação.
- Dashboard simples do usuário.
- Regras antifraude mínimas.

Não incluir no MVP:

- Ranking complexo.
- Loja de recompensas.
- Inteligência artificial personalizada.
- Missões muito avançadas.
- Sistema de premiação financeira.

### 26.2. Versão 1 — 30 dias

Foco: rotina e consistência.

Entregas:

- Missões diárias ou semanais.
- Sequência saudável.
- Notificações configuráveis.
- Mais medalhas.
- Níveis de usuário.
- Resumo semanal.

### 26.3. Versão 2 — 30 dias

Foco: qualidade e colaboração.

Entregas:

- Pontos por qualidade.
- Validação de evidência.
- Selos de colaboração.
- Metas por equipe.
- Ranking opcional por equipe.
- Antifraude avançado.

### 26.4. Versão 3 — 30 dias

Foco: personalização e inteligência.

Entregas:

- Missões personalizadas.
- Recomendações de próxima ação.
- Relatório individual de evolução.
- Comparação com histórico próprio.
- Simulador de metas.
- Ajuste automático de dificuldade.

### 26.5. Homologação

Durante homologação, validar:

- Se os pontos estão corretos.
- Se não há pontuação duplicada.
- Se usuários entendem as regras.
- Se notificações não incomodam.
- Se não existe brecha de fraude simples.
- Se a gamificação melhora o comportamento desejado.

### 26.6. Produção

Após publicar:

- Monitorar métricas semanalmente.
- Revisar regras mensalmente.
- Desativar mecânicas que geram abuso.
- Ajustar pontuação por dificuldade real.
- Coletar feedback dos usuários.

---

## 27. Checklist de qualidade da gamificação

Antes de aprovar uma gamificação, verificar:

- [ ] Existe objetivo de negócio claro.
- [ ] Existe benefício real para o usuário.
- [ ] As ações pontuáveis são úteis.
- [ ] Pontos têm regras simples.
- [ ] Há limite contra spam.
- [ ] O progresso é visível.
- [ ] As recompensas são proporcionais.
- [ ] As notificações são configuráveis.
- [ ] O sistema evita culpa e pressão tóxica.
- [ ] O ranking, se existir, é justo.
- [ ] Há regras antifraude.
- [ ] Há logs para auditoria.
- [ ] Há métricas de sucesso.
- [ ] Há plano por versão.
- [ ] A gamificação não depende apenas de estética.

---

## 28. Template para documento de gamificação

Use este modelo quando precisar criar um documento final de projeto.

```md
# Documento de Gamificação — [Nome do Projeto]

## 1. Visão geral
Descrever o objetivo da gamificação no sistema.

## 2. Objetivos de negócio
- Objetivo 1
- Objetivo 2
- Objetivo 3

## 3. Objetivos do usuário
- Benefício 1
- Benefício 2
- Benefício 3

## 4. Perfis impactados
| Perfil | Comportamento esperado | Mecânicas indicadas |
|---|---|---|
| Usuário |  |  |
| Analista |  |  |
| Gestor |  |  |
| Administrador |  |  |

## 5. Comportamentos desejados
| Comportamento | Valor para o usuário | Valor para o negócio | Frequência |
|---|---|---|---|
|  |  |  |  |

## 6. Ações pontuáveis
| Ação | Pontos | Limite | Validação | Observação |
|---|---:|---|---|---|
|  |  |  |  |  |

## 7. Níveis
| Nível | Nome | Pontos | Benefício |
|---:|---|---:|---|
| 1 |  |  |  |

## 8. Medalhas e conquistas
| Medalha | Critério | Tipo | Recompensa |
|---|---|---|---|
|  |  |  |  |

## 9. Missões
| Missão | Tipo | Critério | Prazo | Recompensa |
|---|---|---|---|---|
|  |  |  |  |  |

## 10. Streaks e consistência
Descrever se haverá sequência de uso, regras de pausa e recuperação.

## 11. Feedback visual
Descrever mensagens, animações, barras de progresso e estados de tela.

## 12. Notificações
| Evento | Canal | Frequência | Mensagem | Configurável? |
|---|---|---|---|---|
|  |  |  |  |  |

## 13. Ranking
Descrever se haverá ranking, qual tipo e quais proteções de justiça.

## 14. Regras antifraude
| Risco | Prevenção | Log necessário |
|---|---|---|
|  |  |  |

## 15. Banco de dados
Listar tabelas, campos e relacionamentos necessários.

## 16. Métricas
| Métrica | Objetivo | Como medir |
|---|---|---|
|  |  |  |

## 17. Roadmap
### MVP
### Versão 1
### Versão 2
### Versão 3

## 18. Critérios de aceite
- [ ] Regra implementada
- [ ] Pontuação auditável
- [ ] Feedback visual validado
- [ ] Notificações configuráveis
- [ ] Antifraude testado
```

---

## 29. Prompt padrão para usar esta skill

Use este prompt quando quiser gerar a gamificação de um projeto específico:

```md
Aja como um engenheiro de produto sênior especialista em gamificação para software, app e SaaS.

Crie um documento profissional de gamificação para o projeto abaixo.

O documento deve conter:
- Objetivos de negócio.
- Objetivos do usuário.
- Perfis impactados.
- Comportamentos desejados.
- Ações pontuáveis.
- Sistema de pontos.
- Níveis.
- Medalhas e conquistas.
- Missões diárias, semanais e de onboarding.
- Streaks saudáveis, se fizer sentido.
- Recompensas.
- Feedback visual.
- Notificações.
- Ranking apenas se for justo.
- Regras antifraude.
- Banco de dados sugerido.
- Métricas de sucesso.
- Roadmap por MVP, versão 1, versão 2 e versão 3.
- Critérios de aceite para desenvolvimento.

Importante:
- Não criar mecânicas manipulativas.
- Não usar pressão tóxica.
- Não criar dependência artificial.
- Toda gamificação precisa gerar valor real para o usuário e para o negócio.
- O sistema deve incentivar o usuário a cumprir tarefas importantes dentro do produto.

Dados do projeto:
[NOME DO PROJETO]
[DESCRIÇÃO DO PRODUTO]
[PERFIS DE USUÁRIO]
[TAREFAS PRINCIPAIS]
[OBJETIVO DE NEGÓCIO]
[PROBLEMAS ATUAIS]
[MÉTRICAS QUE QUEREMOS MELHORAR]
[REGRAS DE NEGÓCIO IMPORTANTES]
```

---

## 30. Erros comuns que a IA deve evitar

- Criar pontos para qualquer clique.
- Criar ranking sem considerar justiça.
- Punir demais o usuário por atraso.
- Usar mensagens de culpa.
- Criar medalhas genéricas sem critério.
- Não definir limites de pontuação.
- Não criar logs de auditoria.
- Não prever fraude.
- Não conectar gamificação com regra de negócio.
- Não separar MVP de versões futuras.
- Criar recompensa que não tem valor.
- Fazer gamificação apenas visual, sem mecânica real.

---

## 31. Resultado esperado ao usar esta skill

Ao aplicar esta skill, a IA deve entregar um documento que permita ao time de produto, design e desenvolvimento entender:

- O que será gamificado.
- Por que será gamificado.
- Quais ações valem pontos.
- Como o usuário evolui.
- Como as regras funcionam.
- Como evitar abuso.
- Como medir resultado.
- O que entra no MVP.
- O que fica para versões futuras.
- Quais tabelas e campos são necessários.
- Quais telas precisam exibir progresso.

O resultado final deve ser claro, prático, implementável e alinhado com um produto profissional.
