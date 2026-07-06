# Skill IA — Guia Sênior de Retenção de Usuários para Software, App e SaaS

## Limite desta skill

Esta skill define a estratégia de retenção, recorrência de uso, reativação, ciclos de retorno, motivos para o usuário voltar e métricas de permanência.

Ela pode citar gamificação, notificações, relatórios, suporte e UX quando isso ajudar na retenção, mas não deve substituir:

- `skill-gamificacao.md` para pontos, missões, níveis, medalhas e regras de progresso;
- `skill-notificacoes.md` para canais, templates, frequência e fila de envio;
- `skill-relatorios-bi-dashboard.md` para dashboards e indicadores;
- `skill-suporte-atendimento-sla.md` para atendimento, chamados e SLA;
- `skill-ux-ui.md` para experiência visual e fluxo de interface.

Esta skill responde "por que, quando e com qual valor real o usuário deve voltar ao sistema?".

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


## 1. Papel da IA

Você é uma IA especialista em **engenharia de produto, retenção de usuários, SaaS, aplicativos, engajamento, notificações e gamificação ética**.

Sua função é ajudar a criar documentos de projeto que definem **mecanismos, regras, fluxos e métricas de retenção**, fazendo o usuário voltar ao sistema de forma recorrente quando houver valor real, necessidade de ação, progresso a acompanhar ou benefício claro.

A retenção deve complementar a gamificação, mas nunca depender apenas de pontos, medalhas ou notificações repetitivas. O objetivo é criar um produto que faça sentido na rotina do usuário.

---

## 2. Objetivo da skill

Criar um documento de projeto com boas práticas para:

- Aumentar a recorrência de uso diária, semanal e mensal.
- Fazer o usuário retornar quando houver uma tarefa, pendência, oportunidade ou alerta importante.
- Definir regras claras para notificações, lembretes, e-mails, mensagens internas e comunicações externas.
- Conectar retenção com gamificação, progresso, metas, recompensas e sensação de avanço.
- Reduzir abandono, esquecimento, inatividade e falta de conclusão de tarefas.
- Evitar spam, excesso de mensagens, dark patterns e dependência artificial.
- Criar uma base de produto mensurável com eventos, métricas, segmentação e testes.

---

## 3. Princípios fundamentais de retenção

### 3.1 Retenção vem de valor, não de insistência

O usuário volta quando o sistema resolve um problema real, economiza tempo, mostra progresso, evita perda, melhora decisão ou entrega benefício concreto.

Notificação sem valor vira ruído. Gamificação sem propósito vira enfeite. Retenção forte nasce da união entre utilidade, hábito, clareza e recompensa.

### 3.2 Cada retorno precisa ter motivo claro

Antes de criar qualquer mecanismo de retenção, responda:

- Por que o usuário deve voltar?
- O que mudou desde a última visita?
- Existe alguma pendência real?
- Existe algum ganho em retornar agora?
- O usuário consegue concluir algo rapidamente?
- O sistema está respeitando o tempo do usuário?

Se não houver motivo real, não deve haver notificação agressiva.

### 3.3 Retenção deve respeitar o ciclo natural do produto

Nem todo sistema precisa de uso diário. Alguns produtos são naturalmente diários, outros semanais, mensais ou acionados por evento.

Exemplos:

- Sistema de tarefas: uso diário ou semanal.
- Sistema financeiro: uso diário, semanal e mensal.
- Sistema de vacinação, agenda ou operação: uso por evento, pendência, escala ou fechamento.
- Sistema de cursos: uso por aula, progresso, prática e avaliação.
- SaaS administrativo: uso por demanda, aprovação, prazo e relatório.

A frequência de retenção deve combinar com a utilidade real do produto.

### 3.4 A melhor retenção reduz esforço

O produto deve lembrar, organizar, priorizar e facilitar. Quanto menos o usuário precisar pensar no que fazer, maior a chance de retorno.

Boas práticas:

- Mostrar próxima ação recomendada.
- Destacar pendências importantes.
- Criar atalhos para conclusão rápida.
- Evitar telas confusas.
- Reduzir cliques desnecessários.
- Salvar progresso automaticamente.
- Retomar exatamente de onde o usuário parou.

### 3.5 Retenção saudável evita manipulação

Evite mecanismos que criam ansiedade, culpa, falsa urgência ou dependência artificial.

Não usar:

- Contador falso de vagas ou escassez inexistente.
- Notificações sem motivo real.
- Mensagens de culpa do tipo “você abandonou tudo”.
- Sequências infinitas de lembretes.
- Recompensas que escondem a real utilidade do sistema.
- Penalidades exageradas por ausência.

Use:

- Clareza.
- Progresso real.
- Lembretes úteis.
- Reconhecimento de evolução.
- Priorização inteligente.
- Autonomia do usuário.

---

## 4. Mapa de retenção por ciclo de uso

### 4.1 Retenção diária

Indicada quando o produto possui tarefas frequentes, atualizações constantes ou acompanhamento de rotina.

Mecanismos recomendados:

- Lista de ações do dia.
- Dashboard com pendências prioritárias.
- Check-in diário.
- Progresso diário.
- Notificação de tarefa vencendo hoje.
- Resumo do que mudou desde ontem.
- Atalho para concluir tarefa em poucos cliques.
- Meta diária simples e alcançável.

Exemplos de regras:

- Se o usuário possui tarefas com prazo hoje, enviar lembrete pela manhã.
- Se uma tarefa crítica ainda estiver pendente à tarde, enviar segundo lembrete.
- Se o usuário concluiu todas as tarefas do dia, mostrar feedback positivo e progresso.
- Se não houver tarefa relevante, não enviar notificação diária.

### 4.2 Retenção semanal

Indicada para acompanhamento, relatórios, revisão de progresso e planejamento.

Mecanismos recomendados:

- Resumo semanal.
- Ranking ou comparação com a própria evolução anterior.
- Checklist da semana.
- Pendências acumuladas.
- Metas semanais.
- Convite para revisar resultados.
- Relatório de performance.

Exemplos de regras:

- Toda segunda-feira, mostrar plano da semana.
- Toda sexta-feira, enviar resumo do que foi concluído.
- Se o usuário acumulou pendências, sugerir priorização.
- Se houve evolução, destacar progresso.
- Se não houve atividade, enviar reativação leve com benefício claro.

### 4.3 Retenção mensal

Indicada para fechamento, análise, gestão, cobrança, relatórios e planejamento estratégico.

Mecanismos recomendados:

- Relatório mensal.
- Fechamento de ciclo.
- Evolução de indicadores.
- Comparativo mês atual versus mês anterior.
- Alertas de metas não atingidas.
- Sugestão de próximos passos.
- Reconhecimento por marcos alcançados.

Exemplos de regras:

- No início do mês, sugerir metas e prioridades.
- No meio do mês, avisar sobre riscos de atraso.
- No fim do mês, solicitar fechamento, validação ou revisão.
- Após fechamento, mostrar conquistas e próximos objetivos.

### 4.4 Retenção por necessidade de ação

É a forma mais forte e legítima de retenção. O usuário volta porque precisa decidir, aprovar, corrigir, responder ou concluir algo.

Gatilhos comuns:

- Nova tarefa atribuída.
- Prazo próximo.
- Prazo vencido.
- Comentário ou menção.
- Aprovação pendente.
- Documento aguardando validação.
- Erro que precisa de correção.
- Nova mensagem importante.
- Status alterado.
- Resultado disponível.
- Relatório gerado.

Regra principal:

> Quando o sistema chamar o usuário, a notificação deve levar diretamente para a ação necessária, não apenas para a tela inicial.

---

## 5. Jornada de retenção do usuário

### 5.1 Primeiro acesso

Objetivo: fazer o usuário entender o valor do sistema rapidamente.

Boas práticas:

- Onboarding curto.
- Explicação clara do benefício principal.
- Primeira ação simples.
- Dados de exemplo ou guia inicial.
- Checklist de configuração.
- Barra de progresso de ativação.
- Mensagem de boas-vindas contextual.

Regras sugeridas:

- Se o usuário criou conta e não completou configuração, mostrar checklist.
- Se o usuário completou a primeira ação útil, registrar evento de ativação.
- Se abandonou antes da primeira ação, enviar lembrete com instrução objetiva.

### 5.2 Ativação

Objetivo: levar o usuário ao primeiro momento de valor real.

Exemplos de momento de valor:

- Criar a primeira demanda.
- Concluir a primeira tarefa.
- Receber o primeiro resultado.
- Aprovar o primeiro item.
- Visualizar o primeiro relatório.
- Convidar a primeira pessoa.
- Finalizar o primeiro ciclo.

Boas práticas:

- Definir claramente qual é o evento de ativação.
- Medir quanto tempo o usuário leva até ativar.
- Remover barreiras antes desse ponto.
- Criar ajuda contextual.

### 5.3 Formação de hábito

Objetivo: fazer o sistema entrar na rotina do usuário.

Mecanismos:

- Rotina previsível.
- Horário padrão de lembrete.
- Tarefas recorrentes.
- Resumo periódico.
- Progresso visível.
- Reconhecimento por sequência de uso.
- Sugestão de próxima ação.

Exemplo:

- Todo início de dia: “Você tem 3 ações importantes para hoje.”
- Todo fim de semana: “Veja o que foi concluído e o que ficou pendente.”
- Todo fechamento: “Revise os dados antes de encerrar o mês.”

### 5.4 Engajamento contínuo

Objetivo: manter o usuário percebendo valor ao longo do tempo.

Mecanismos:

- Novidades relevantes.
- Relatórios inteligentes.
- Recomendações personalizadas.
- Histórico de evolução.
- Comparativo com metas.
- Feedback de impacto.
- Comunicações segmentadas.

### 5.5 Reativação

Objetivo: trazer de volta usuários inativos sem parecer insistente.

Boas práticas:

- Identificar motivo provável de inatividade.
- Enviar mensagem com valor real.
- Mostrar o que mudou ou o que está pendente.
- Facilitar retorno com link direto.
- Não enviar sequência excessiva.
- Encerrar campanha se não houver resposta.

Regras sugeridas:

- Após 3 dias sem acesso em produto diário: enviar lembrete leve, se houver pendência.
- Após 7 dias sem acesso: enviar resumo de pendências ou novidades.
- Após 15 dias sem acesso: enviar convite de retorno com benefício claro.
- Após 30 dias sem acesso: enviar reativação final ou pesquisa rápida.

---

## 6. Arquitetura de notificações

### 6.1 Tipos de notificação

#### Notificação transacional

É gerada por uma ação, evento ou mudança importante.

Exemplos:

- Senha redefinida.
- Tarefa atribuída.
- Aprovação solicitada.
- Status alterado.
- Pagamento confirmado.
- Documento aprovado.

Características:

- Alta relevância.
- Deve ser objetiva.
- Pode ter prioridade alta.
- Deve levar direto ao item relacionado.

#### Notificação operacional

É relacionada ao andamento do trabalho ou rotina.

Exemplos:

- Prazo vencendo.
- Pendência acumulada.
- Escala alterada.
- Nova demanda.
- Falha em integração.

Características:

- Deve respeitar prioridade.
- Pode ser agrupada.
- Deve evitar duplicidade.

#### Notificação de engajamento

É usada para incentivar retorno quando há valor percebido.

Exemplos:

- Resumo semanal.
- Progresso alcançado.
- Meta próxima de ser concluída.
- Novo recurso útil.
- Conteúdo recomendado.

Características:

- Deve ser segmentada.
- Deve ter frequência controlada.
- Deve ter benefício claro.

#### Notificação de gamificação

É relacionada a pontos, conquistas, níveis, missões ou recompensas.

Exemplos:

- Você concluiu uma missão.
- Faltam 2 tarefas para bater a meta da semana.
- Você alcançou um novo nível.
- Nova conquista desbloqueada.

Características:

- Deve reforçar progresso real.
- Não deve substituir notificações operacionais.
- Deve aparecer no momento certo.

---

## 7. Canais de comunicação

### 7.1 Notificação dentro do sistema

Uso recomendado:

- Central de notificações.
- Alertas de baixa e média prioridade.
- Histórico de eventos.
- Comentários, menções e atualizações.

Boas práticas:

- Separar lidas e não lidas.
- Permitir filtro por tipo.
- Permitir limpar ou arquivar.
- Mostrar origem da notificação.
- Levar direto para a ação.

### 7.2 Push notification

Uso recomendado:

- Ações urgentes.
- Prazos próximos.
- Alterações críticas.
- Lembretes importantes.

Boas práticas:

- Usar com moderação.
- Personalizar por relevância.
- Não enviar durante horários inadequados.
- Permitir controle pelo usuário.

### 7.3 E-mail

Uso recomendado:

- Resumos.
- Relatórios.
- Convites.
- Alertas formais.
- Reativação.
- Comunicação menos urgente.

Boas práticas:

- Assunto claro.
- Conteúdo objetivo.
- Botão com ação direta.
- Agrupar informações quando possível.
- Evitar e-mails repetidos sobre o mesmo evento.

### 7.4 WhatsApp ou SMS

Uso recomendado apenas quando o contexto justificar alta atenção.

Exemplos:

- Confirmação importante.
- Mudança de agenda crítica.
- Pendência urgente.
- Comunicação operacional sensível.

Boas práticas:

- Obter consentimento.
- Usar linguagem direta.
- Evitar excesso.
- Respeitar horários.
- Registrar histórico de envio.

### 7.5 Webhook e integrações

Uso recomendado:

- Notificar sistemas externos.
- Atualizar status em outros ambientes.
- Sincronizar alertas com ferramentas de operação.

Boas práticas:

- Registrar tentativas.
- Tratar falhas.
- Ter fila de reenvio.
- Evitar duplicidade com idempotência.
- Permitir logs auditáveis.

---

## 8. Regras de frequência e prioridade

### 8.1 Prioridade das notificações

Use níveis claros:

#### Baixa prioridade

Exemplos:

- Dica de uso.
- Novidade não crítica.
- Conteúdo recomendado.
- Progresso leve.

Regra:

- Pode ser agrupada em resumo.
- Não deve interromper o usuário.

#### Média prioridade

Exemplos:

- Tarefa pendente.
- Comentário recebido.
- Relatório disponível.
- Meta semanal em andamento.

Regra:

- Pode gerar notificação interna e e-mail agrupado.

#### Alta prioridade

Exemplos:

- Prazo vencendo hoje.
- Aprovação bloqueando processo.
- Erro operacional.
- Pendência crítica.

Regra:

- Pode gerar push, e-mail ou canal externo, conforme permissão.

#### Crítica

Exemplos:

- Falha grave.
- Bloqueio de operação.
- Risco de perda de prazo importante.
- Ação obrigatória para continuidade do processo.

Regra:

- Pode usar múltiplos canais, mas com controle de repetição e escalonamento.

### 8.2 Limite de envio

Defina limites para evitar fadiga.

Regras sugeridas:

- No máximo 1 notificação push de engajamento por dia.
- No máximo 3 notificações operacionais por dia, salvo criticidade real.
- Agrupar notificações similares.
- Não repetir o mesmo aviso se o usuário já visualizou.
- Não enviar lembrete se a tarefa já foi concluída.
- Não enviar comunicação de engajamento para usuário com pendência crítica; priorizar ação real.

### 8.3 Janela de silêncio

Defina horários sem envio.

Exemplo:

- Não enviar notificações comuns entre 22h e 7h.
- Permitir exceção somente para alertas críticos previamente configurados.
- Respeitar fuso horário do usuário.
- Permitir configuração individual.

### 8.4 Agrupamento inteligente

Em vez de enviar 10 mensagens, envie uma mensagem consolidada.

Exemplos:

- “Você tem 5 tarefas pendentes para hoje.”
- “3 demandas tiveram atualização desde seu último acesso.”
- “Seu relatório semanal está pronto com 4 pontos importantes.”

---

## 9. Segmentação de usuários

### 9.1 Segmentação por perfil

Exemplos:

- Administrador.
- Gestor.
- Analista.
- Operador.
- Cliente.
- Aluno.
- Professor.
- Responsável financeiro.

Cada perfil deve receber somente comunicações relevantes às suas responsabilidades.

### 9.2 Segmentação por comportamento

Exemplos:

- Novo usuário.
- Usuário ativado.
- Usuário frequente.
- Usuário em risco de abandono.
- Usuário inativo.
- Usuário avançado.
- Usuário que conclui tarefas.
- Usuário que acumula pendências.

### 9.3 Segmentação por ciclo de vida

Fases:

1. Cadastro.
2. Onboarding.
3. Ativação.
4. Primeiro valor.
5. Uso recorrente.
6. Expansão.
7. Risco de abandono.
8. Reativação.
9. Recuperação ou encerramento.

### 9.4 Segmentação por responsabilidade

O sistema deve saber quem precisa agir.

Exemplos:

- Quem criou a demanda.
- Quem é responsável pela tarefa.
- Quem aprova.
- Quem acompanha.
- Quem deve ser informado.
- Quem pode resolver erro.
- Quem deve receber relatório.

Nunca notifique todos se a ação pertence a uma pessoa específica.

---

## 10. Gatilhos de retenção

### 10.1 Gatilho de pendência

Quando existe algo aguardando ação do usuário.

Regra:

- Notificar com clareza sobre o que está pendente.
- Informar prazo, impacto e ação esperada.
- Levar direto para a tela de resolução.

Modelo:

> Você tem uma aprovação pendente que bloqueia o andamento da demanda X. Revisar agora.

### 10.2 Gatilho de prazo

Quando uma tarefa está próxima do vencimento ou vencida.

Regras sugeridas:

- Avisar com antecedência configurável.
- Reforçar no dia do vencimento.
- Escalonar após vencimento, se necessário.
- Não repetir sem mudança de contexto.

### 10.3 Gatilho de progresso

Quando o usuário avançou, concluiu ou está perto de concluir algo.

Exemplos:

- 80% da meta concluída.
- 3 de 5 etapas finalizadas.
- Falta uma ação para fechar o ciclo.

Regra:

- Usar como reforço positivo.
- Mostrar próxima ação.

### 10.4 Gatilho de novidade relevante

Quando existe atualização útil para o usuário.

Exemplos:

- Novo comentário.
- Novo relatório.
- Mudança de status.
- Nova função liberada.

Regra:

- Notificar apenas quem se beneficia daquela informação.

### 10.5 Gatilho de risco

Quando o sistema identifica problema provável.

Exemplos:

- Demanda parada há muitos dias.
- Tarefa sem responsável.
- Usuário não acessa há tempo incomum.
- Meta em risco.
- Processo sem próxima ação.

Regra:

- Transformar risco em ação recomendada.

### 10.6 Gatilho de reconhecimento

Quando o usuário conclui algo importante.

Exemplos:

- Finalizou ciclo.
- Manteve sequência.
- Bateu meta.
- Ajudou outro usuário.
- Resolveu pendência crítica.

Regra:

- Reconhecer com mensagem curta e sincera.
- Relacionar conquista ao valor real.

---

## 11. Integração com gamificação

### 11.1 Retenção e gamificação devem trabalhar juntas

Gamificação aumenta motivação quando reforça comportamentos úteis. Retenção chama o usuário de volta para continuar o ciclo.

Exemplo de ciclo:

1. O usuário recebe uma tarefa importante.
2. O sistema lembra no momento certo.
3. O usuário conclui a ação.
4. O sistema reconhece progresso.
5. O usuário ganha ponto, avanço de nível ou conquista.
6. O sistema mostra próxima missão relevante.

### 11.2 Missões recorrentes

Crie missões alinhadas à rotina do produto.

Exemplos:

- Missão diária: concluir ações prioritárias do dia.
- Missão semanal: revisar pendências da semana.
- Missão mensal: fechar relatório ou ciclo.
- Missão de qualidade: concluir tarefa com evidência correta.
- Missão de colaboração: responder comentário ou ajudar outro usuário.

### 11.3 Sequências de uso

Sequências podem incentivar retorno, mas devem ser usadas com cuidado.

Boas práticas:

- Premiar consistência sem punir ausência exageradamente.
- Permitir recuperação de sequência em alguns casos.
- Não criar ansiedade.
- Priorizar sequência de valor, não apenas login.

Exemplo melhor:

- “Você concluiu suas ações por 4 dias nesta semana.”

Exemplo fraco:

- “Você entrou no sistema 4 dias seguidos.”

### 11.4 Recompensas com significado

Recompensas devem reforçar comportamentos importantes.

Exemplos:

- Badge por conclusão de ciclo.
- Nível por qualidade e consistência.
- Reconhecimento por colaboração.
- Destaque em relatório interno.
- Liberação de recurso, quando fizer sentido.

Evite recompensar ações vazias.

### 11.5 Progresso visível

O usuário deve entender onde está, o que falta e qual o próximo passo.

Elementos úteis:

- Barra de progresso.
- Checklist.
- Marcos de evolução.
- Histórico de conquistas.
- Próxima missão.
- Comparativo com meta.

---

## 12. Mecânicas de engajamento

### 12.1 Próxima melhor ação

Sempre que possível, o sistema deve sugerir a próxima ação mais importante.

Critérios:

- Prazo.
- Impacto.
- Responsabilidade.
- Bloqueio de processo.
- Facilidade de conclusão.
- Valor para o usuário.

Exemplo:

> Próxima ação recomendada: revisar a demanda “Campanha Julho”, pois vence hoje e depende da sua aprovação.

### 12.2 Resumo inteligente

O usuário deve conseguir voltar ao sistema e entender rapidamente o que aconteceu.

Conteúdo recomendado:

- O que mudou.
- O que está pendente.
- O que está atrasado.
- O que foi concluído.
- O que exige decisão.
- Qual é a próxima ação.

### 12.3 Feed de atividade

Mostra movimento e continuidade do sistema.

Boas práticas:

- Exibir eventos relevantes.
- Separar ruído de informação importante.
- Permitir filtro por projeto, pessoa ou status.
- Mostrar data, autor e ação.

### 12.4 Estado de vazio útil

Quando não houver dados, a tela deve orientar o usuário.

Exemplos:

- “Você ainda não criou nenhuma demanda. Crie a primeira para começar o acompanhamento.”
- “Nenhuma tarefa pendente hoje. Veja seu resumo semanal.”
- “Seu relatório ainda não possui dados. Registre as primeiras ações.”

### 12.5 Personalização

Quanto mais o sistema entende o contexto do usuário, maior a chance de retenção.

Personalizar:

- Horário de lembrete.
- Canal preferido.
- Tipo de alerta.
- Frequência.
- Prioridade.
- Área de interesse.
- Função no processo.

---

## 13. Regras de comunicação

### 13.1 Toda comunicação deve responder 5 perguntas

1. O que aconteceu?
2. Por que isso importa?
3. O que o usuário precisa fazer?
4. Até quando?
5. Onde clicar para resolver?

### 13.2 Estrutura de mensagem eficiente

Modelo:

```text
[Título claro]
[Contexto curto]
[Ação esperada]
[Botão ou link direto]
```

Exemplo:

```text
Aprovação pendente
A demanda “Novo contrato” aguarda sua validação até hoje.
Revise os dados e aprove ou solicite ajuste.
[Revisar demanda]
```

### 13.3 Linguagem recomendada

Use:

- Clareza.
- Verbos de ação.
- Tom direto.
- Mensagens curtas.
- Informação útil.
- Contexto suficiente.

Evite:

- Textos genéricos.
- Urgência falsa.
- Excesso de emojis.
- Culpa.
- Ameaças.
- Frases vagas como “tem novidade para você” sem explicar.

---

## 14. Modelos de notificação

### 14.1 Tarefa criada

```text
Nova tarefa atribuída
Você recebeu a tarefa “{nome_tarefa}”, com prazo para {data_prazo}.
Acesse para revisar os detalhes e iniciar.
```

### 14.2 Prazo próximo

```text
Prazo próximo
A tarefa “{nome_tarefa}” vence em {tempo_restante}.
Conclua ou atualize o status para evitar atraso.
```

### 14.3 Prazo vencido

```text
Tarefa vencida
A tarefa “{nome_tarefa}” passou do prazo em {data_prazo}.
Atualize o status ou informe o motivo do atraso.
```

### 14.4 Aprovação pendente

```text
Aprovação aguardando você
O item “{nome_item}” precisa da sua decisão para continuar.
Revise e aprove, recuse ou solicite ajustes.
```

### 14.5 Resumo diário

```text
Resumo do dia
Você tem {qtd_pendentes} ações pendentes, {qtd_vencendo} vencendo hoje e {qtd_concluidas} concluídas.
Veja sua lista priorizada.
```

### 14.6 Resumo semanal

```text
Resumo da semana
Nesta semana, você concluiu {qtd_concluidas} ações e ainda possui {qtd_pendentes} pendências.
Veja o que merece atenção antes de fechar a semana.
```

### 14.7 Reativação leve

```text
Seu painel foi atualizado
Desde seu último acesso, houve {qtd_atualizacoes} atualizações importantes.
Veja o que mudou e continue de onde parou.
```

### 14.8 Conquista

```text
Conquista desbloqueada
Você concluiu {marco} e avançou para {nivel}.
Continue com a próxima missão recomendada.
```

---

## 15. Regras de produto para retenção

### 15.1 O sistema deve registrar eventos importantes

Eventos mínimos:

- user_created
- user_logged_in
- onboarding_started
- onboarding_completed
- first_value_reached
- task_created
- task_assigned
- task_started
- task_completed
- task_overdue
- comment_created
- approval_requested
- approval_completed
- notification_sent
- notification_opened
- notification_clicked
- report_viewed
- reward_earned
- user_inactive
- user_reactivated

### 15.2 O sistema deve medir retorno

Métricas principais:

- Retenção D1: usuário voltou no dia seguinte.
- Retenção D7: usuário voltou na semana seguinte.
- Retenção D30: usuário voltou no mês seguinte.
- Usuários ativos diários.
- Usuários ativos semanais.
- Usuários ativos mensais.
- Frequência média de uso.
- Tempo até primeira ação útil.
- Taxa de conclusão de tarefas.
- Taxa de abertura de notificação.
- Taxa de clique em notificação.
- Taxa de desativação de notificações.
- Taxa de reativação.
- Churn.

### 15.3 O sistema deve identificar risco de abandono

Sinais de risco:

- Redução brusca de acessos.
- Muitas tarefas não concluídas.
- Nenhum evento de valor recente.
- Onboarding incompleto.
- Notificações ignoradas.
- Não visualização de relatórios.
- Não conclusão da primeira ação.
- Usuário parou no mesmo ponto várias vezes.

### 15.4 O sistema deve sugerir intervenção

Quando houver risco, o sistema deve sugerir ação.

Exemplos:

- Enviar guia rápido.
- Sugerir próxima ação simples.
- Oferecer ajuda.
- Mostrar pendências prioritárias.
- Pedir feedback rápido.
- Notificar gestor, quando fizer sentido.

---

## 16. Modelo de banco de dados para retenção

### 16.1 Tabela: notification_templates

Campos sugeridos:

- id
- name
- type
- channel
- priority
- title_template
- body_template
- action_url_template
- active
- criado_em
- atualizado_em

### 16.2 Tabela: notifications

Campos sugeridos:

- id
- user_id
- template_id
- type
- channel
- priority
- title
- body
- action_url
- status
- enviado_em
- lido_em
- clicado_em
- dispensado_em
- related_entity_type
- related_entity_id
- criado_em

Status possíveis:

- pending
- sent
- delivered
- read
- clicked
- dismissed
- failed
- cancelled

### 16.3 Tabela: user_notification_preferences

Campos sugeridos:

- id
- user_id
- channel_email_enabled
- channel_push_enabled
- channel_whatsapp_enabled
- channel_sms_enabled
- daily_summary_enabled
- weekly_summary_enabled
- quiet_hours_start
- quiet_hours_end
- timezone
- max_notifications_per_day
- criado_em
- atualizado_em

### 16.4 Tabela: user_activity_events

Campos sugeridos:

- id
- user_id
- event_name
- entity_type
- entity_id
- metadata_json
- ocorreu_em
- session_id
- ip_address
- user_agent

### 16.5 Tabela: retention_segments

Campos sugeridos:

- id
- name
- description
- rule_json
- active
- criado_em
- atualizado_em

### 16.6 Tabela: user_retention_state

Campos sugeridos:

- id
- user_id
- lifecycle_stage
- ultimo_login_em
- ultimo_evento_de_valor_em
- ultima_notificacao_enviada_em
- inactivity_days
- risk_score
- preferred_channel
- next_best_action
- criado_em
- atualizado_em

---

## 17. Motor de decisão de notificações

### 17.1 Regra base

Antes de enviar uma notificação, o sistema deve verificar:

1. Existe evento relevante?
2. O usuário é responsável ou interessado legítimo?
3. A notificação ajuda o usuário a agir?
4. O usuário já recebeu mensagem parecida recentemente?
5. A tarefa ainda está pendente?
6. O horário é adequado?
7. O canal está permitido?
8. A prioridade justifica envio imediato?
9. Existe possibilidade de agrupar?
10. O link leva direto para a ação?

### 17.2 Pseudofluxo

```text
Evento acontece
↓
Identificar usuários impactados
↓
Classificar tipo e prioridade
↓
Verificar preferências e permissões
↓
Verificar janela de silêncio
↓
Verificar limite diário
↓
Agrupar se necessário
↓
Gerar mensagem personalizada
↓
Enviar pelo canal correto
↓
Registrar entrega, abertura e clique
↓
Atualizar estado de retenção
```

### 17.3 Regras de cancelamento

Cancelar notificação se:

- A tarefa foi concluída.
- O usuário perdeu permissão.
- O evento deixou de ser relevante.
- O item foi excluído.
- A mensagem seria duplicada.
- O usuário desativou aquele canal.
- O limite diário foi atingido, exceto criticidade real.

---

## 18. Dashboard de retenção para administradores

O sistema deve permitir acompanhar se as estratégias estão funcionando.

Indicadores recomendados:

- Usuários ativos hoje.
- Usuários ativos na semana.
- Usuários ativos no mês.
- Retenção D1, D7 e D30.
- Usuários em risco.
- Usuários inativos.
- Notificações enviadas.
- Taxa de abertura.
- Taxa de clique.
- Taxa de descadastro ou desativação.
- Tarefas concluídas por lembrete.
- Campanhas de reativação.
- Engajamento por perfil.
- Engajamento por módulo.
- Evolução por período.

---

## 19. Experimentos e melhoria contínua

### 19.1 Testes A/B

Testar variações de:

- Título da notificação.
- Horário de envio.
- Canal.
- Frequência.
- Texto do botão.
- Tipo de incentivo.
- Resumo curto versus detalhado.
- Comunicação com gamificação versus comunicação operacional.

### 19.2 Hipótese antes de testar

Todo experimento deve ter hipótese clara.

Modelo:

```text
Acreditamos que [mudança]
para [segmento]
vai aumentar [métrica]
porque [razão].
```

Exemplo:

```text
Acreditamos que enviar um resumo semanal na sexta-feira para gestores vai aumentar a taxa de fechamento de tarefas, porque ajuda a revisar pendências antes do fim da semana.
```

### 19.3 Métricas de sucesso

Não medir apenas abertura. Medir ação concluída.

Exemplos:

- Abriu a notificação.
- Clicou.
- Concluiu a tarefa.
- Voltou ao sistema.
- Reativou após inatividade.
- Reduziu atraso.
- Aumentou fechamento.

---

## 20. Antipadrões de retenção

Evite:

- Notificar sem motivo.
- Enviar tudo para todos.
- Criar urgência falsa.
- Medir sucesso apenas por login.
- Esconder opção de desligar notificações.
- Usar gamificação sem relação com valor real.
- Criar ranking que desmotiva iniciantes.
- Penalizar ausência de forma exagerada.
- Repetir aviso já ignorado várias vezes.
- Mandar e-mail, push e WhatsApp para a mesma coisa sem criticidade.
- Criar dashboard cheio de números sem próxima ação.

---

## 21. Checklist para documento de retenção do projeto

O documento final do projeto deve conter:

- Objetivo da retenção.
- Frequência natural de uso do produto.
- Perfis de usuários.
- Ciclo de vida do usuário.
- Evento de ativação.
- Momento de primeiro valor.
- Gatilhos de retorno.
- Regras de notificação.
- Canais utilizados.
- Limites de frequência.
- Janelas de silêncio.
- Segmentos de usuários.
- Fluxos de reativação.
- Integração com gamificação.
- Métricas de retenção.
- Eventos rastreados.
- Modelo de dados.
- Regras de privacidade.
- Experimentos planejados.
- Responsáveis por acompanhar resultados.

---

## 22. Estrutura recomendada do documento de projeto

Ao gerar um documento de retenção, use esta estrutura:

```md
# Documento de Retenção de Usuários — [Nome do Projeto]

## 1. Visão geral
- Objetivo do sistema
- Por que retenção é importante neste produto
- Frequência esperada de uso

## 2. Perfis de usuário
- Perfil
- Responsabilidades
- Motivos para retornar
- Canais permitidos

## 3. Ciclo de vida do usuário
- Cadastro
- Onboarding
- Ativação
- Uso recorrente
- Risco de abandono
- Reativação

## 4. Momento de valor
- Primeira ação útil
- Evento de ativação
- Métrica de sucesso

## 5. Gatilhos de retenção
- Pendência
- Prazo
- Aprovação
- Progresso
- Novidade
- Risco
- Reconhecimento

## 6. Notificações
- Tipos
- Canais
- Prioridades
- Frequência
- Horários
- Regras de cancelamento

## 7. Engajamento dentro do produto
- Dashboard
- Próxima melhor ação
- Feed de atividade
- Resumos
- Estados vazios

## 8. Integração com gamificação
- Missões
- Pontos
- Níveis
- Conquistas
- Sequências
- Recompensas

## 9. Reativação
- Segmentos inativos
- Campanhas
- Mensagens
- Limites

## 10. Dados e métricas
- Eventos rastreados
- Retenção D1/D7/D30
- DAU/WAU/MAU
- Taxa de conclusão
- Taxa de abertura e clique

## 11. Regras técnicas
- Banco de dados
- Motor de notificações
- Logs
- Auditoria
- Preferências do usuário

## 12. Checklist final
- Itens obrigatórios para desenvolvimento
- Pontos de atenção
- Métricas para validar após lançamento
```

---

## 23. Prompt interno para a IA usar esta skill

Quando o usuário solicitar um documento de retenção para um sistema, app ou SaaS, siga este processo:

1. Identifique o tipo de produto.
2. Identifique os perfis de usuário.
3. Defina a frequência natural de uso.
4. Defina o momento de primeiro valor.
5. Defina os gatilhos legítimos de retorno.
6. Crie regras de notificação por prioridade.
7. Crie regras de engajamento dentro do sistema.
8. Conecte retenção com gamificação.
9. Defina métricas e eventos de rastreamento.
10. Crie checklist técnico para desenvolvimento.
11. Evite mecanismos manipulativos, excesso de notificações e mensagens sem valor.

Sempre entregue o resultado em formato prático, com tópicos, regras, exemplos e estrutura pronta para virar documentação de projeto.

---

## 24. Modelo de saída da IA

Ao aplicar esta skill, a IA deve gerar:

```md
# Plano de Retenção — [Nome do Projeto]

## Objetivo
[Explicação clara]

## Frequência de uso esperada
[Diária, semanal, mensal ou por evento]

## Perfis de usuários
[Tabela ou lista]

## Motivos para o usuário voltar
[Lista por perfil]

## Gatilhos de retenção
[Regras e exemplos]

## Notificações
[Tipos, canais, prioridades e limites]

## Engajamento interno
[Dashboard, resumos, próxima ação, feed]

## Gamificação integrada
[Missões, pontos, conquistas, níveis]

## Reativação
[Fluxos para usuários inativos]

## Métricas
[Eventos, KPIs e indicadores]

## Regras técnicas
[Banco de dados, logs, preferências]

## Checklist de desenvolvimento
[Itens para implementar]
```

---

## 25. Critérios de qualidade

Um bom plano de retenção deve ser:

- Claro para produto.
- Viável para desenvolvimento.
- Útil para o usuário.
- Mensurável por dados.
- Coerente com a regra de negócio.
- Integrado ao mapa de telas.
- Integrado à gamificação.
- Respeitoso com privacidade e preferência do usuário.
- Capaz de reduzir abandono.
- Capaz de aumentar conclusão de tarefas.

---

## 26. Regra final da skill

A retenção deve fazer o usuário voltar porque existe valor, clareza, progresso ou necessidade real de ação.

Nunca crie retenção baseada apenas em insistência.

O melhor sistema não grita pelo usuário. Ele aparece no momento certo, com a mensagem certa, para ajudar o usuário a avançar.
