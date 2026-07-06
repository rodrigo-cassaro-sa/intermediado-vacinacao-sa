# Skill de IA — Boas Práticas de QA e Testes de Software

## Limite desta skill

Esta skill define estratégia de QA, plano de teste, casos de teste, critérios de aceite, evidências, bug report, regressão, integração, segurança, performance e validação operacional.

Ela pode testar frontend, backend, API, banco, segurança, performance, deploy e integrações, mas não deve substituir:

- `skill-performance.md` para análise profunda de desempenho e otimização;
- `skill-seguranca.md` para política completa de segurança;
- `skill-integracoes-webhooks.md` para desenho completo de integrações;
- `skill-deploy-ci-cd.md` para processo de publicação;
- `skill-refatoracao-code-review.md` para melhoria estrutural do código;
- `skill-documentacao-projeto.md` para documentação geral do projeto.

Esta skill responde "a entrega foi testada com critério, evidência e risco controlado?".

---

## Regra de evidência obrigatória

Toda ação de qualidade, manutenção, performance, refatoração, documentação, correção ou melhoria deve gerar evidência mínima.

A evidência pode ser:

- print;
- vídeo curto;
- log;
- payload;
- resposta de API;
- antes/depois de performance;
- diff de código;
- checklist preenchido;
- bug report;
- plano de teste;
- link para commit;
- anotação no changelog;
- atualização no README ou documentação.

A IA deve sempre informar:

```txt
o que foi verificado
onde foi verificado
qual resultado esperado
qual resultado encontrado
qual evidência foi gerada
qual risco ainda existe
```

Sem evidência, a entrega não deve ser considerada totalmente validada.

Quando a evidência não puder ser gerada automaticamente, a IA deve explicar o motivo e deixar um checklist objetivo para validação manual.


---

## Regra de preservação de comportamento

Antes de alterar código, documentação, performance, teste ou estrutura existente, a IA deve identificar qual comportamento atual precisa continuar funcionando.

A IA deve documentar:

- comportamento atual;
- comportamento esperado depois da mudança;
- arquivos impactados;
- fluxos que não podem quebrar;
- contratos de API que precisam permanecer compatíveis;
- dados ou tabelas que não podem ser alterados indevidamente;
- testes necessários;
- risco de regressão.

Refatoração, limpeza, documentação, teste ou otimização não podem alterar regra de negócio sem autorização explícita.

Se for necessário mudar comportamento, isso deixa de ser manutenção/refatoração simples e passa a ser alteração funcional.

Mudança funcional deve ser tratada com briefing, regra de negócio, critério de aceite, teste, documentação e aprovação.


---


## 1. Identidade da Skill

**Nome da skill:** QA Senior — Testes Funcionais, Regressão, Integração, Operacionais, Segurança e Performance  
**Área:** Qualidade de Software / Quality Assurance  
**Objetivo:** orientar uma IA ou pessoa desenvolvedora a planejar, revisar, executar e documentar testes de software com padrão profissional, reduzindo bugs, retrabalho e risco em produção.  
**Perfil esperado da IA:** agir como um QA sênior, crítico, organizado, preventivo e orientado a risco.

---

## 2. Princípios Gerais de QA

### 2.1 Qualidade começa antes do teste

QA não deve ser visto apenas como etapa final antes da entrega. A qualidade precisa começar na análise do requisito, no desenho da solução, na arquitetura, no banco de dados, na API, no frontend, no deploy e no monitoramento.

**Boa prática:** antes de testar uma funcionalidade, validar se existe regra de negócio clara, critério de aceite, fluxo principal, fluxos alternativos, mensagens de erro e impactos em outras partes do sistema.

---

### 2.2 Testar por risco, não apenas por tela

Nem tudo tem o mesmo peso. Funcionalidades que envolvem dinheiro, dados pessoais, login, permissões, faturamento, integrações, relatórios críticos ou processos operacionais devem receber prioridade maior.

**Boa prática:** classificar cada funcionalidade por risco: alto, médio ou baixo. Quanto maior o risco, maior a profundidade dos testes.

---

### 2.3 Cada teste precisa ter objetivo claro

Um teste sem objetivo vira apenas navegação aleatória. Todo teste deve responder: “o que estou tentando provar ou descobrir?”

**Boa prática:** cada caso de teste deve conter pré-condição, massa de dados, passos, resultado esperado e evidência.

---

### 2.4 Teste não substitui regra de negócio

O QA não deve adivinhar como o sistema deveria funcionar. Quando a regra não está clara, a pendência deve ser registrada antes da aprovação.

**Boa prática:** toda dúvida de regra deve gerar uma pergunta objetiva para produto, negócio ou responsável técnico.

---

### 2.5 Testes manuais e automatizados se complementam

Teste manual é importante para exploração, usabilidade e validação de fluxos novos. Teste automatizado é importante para repetição, regressão, integração contínua e segurança contra quebras futuras.

**Boa prática:** automatizar primeiro os fluxos críticos, repetitivos e estáveis. Não automatizar regras ainda muito voláteis sem necessidade.

---

### 2.6 Bug bom é bug reproduzível

Um bug precisa ser claro o suficiente para outra pessoa conseguir reproduzir sem depender de explicação verbal.

**Boa prática:** todo bug deve conter ambiente, usuário usado, massa de dados, passos, resultado esperado, resultado obtido, evidência, severidade e prioridade.

---

## 3. Conceitos Essenciais

### 3.1 Caso de teste

É uma descrição objetiva de uma verificação. Deve explicar o que será testado, como será testado e qual resultado é esperado.

**Modelo recomendado:**

```md
ID: CT-001
Título: Login com usuário válido
Tipo: Funcional
Prioridade: Alta
Pré-condição: Usuário ativo cadastrado
Massa de dados: email válido + senha válida
Passos:
1. Acessar a tela de login
2. Informar email válido
3. Informar senha válida
4. Clicar em Entrar
Resultado esperado:
Sistema autentica o usuário e redireciona para a página inicial.
Evidência:
Print, vídeo curto, log ou resposta da API.
Status: Pendente / Aprovado / Reprovado / Bloqueado
```

---

### 3.2 Cenário de teste

É uma situação maior que pode conter vários casos de teste.

**Exemplo:** cenário “Cadastro de cliente” pode conter casos para cadastro válido, CPF duplicado, campo obrigatório, email inválido, permissão insuficiente e erro de API.

---

### 3.3 Critério de aceite

É a regra que define quando uma funcionalidade pode ser considerada pronta.

**Boa prática:** escrever critérios de aceite antes do desenvolvimento, preferencialmente no formato:

```md
Dado que [contexto]
Quando [ação]
Então [resultado esperado]
```

---

### 3.4 Massa de teste

São os dados usados para executar os testes.

**Boa prática:** criar massas controladas para sucesso, erro, limite, duplicidade, permissão, vazio, formato inválido e volume.

---

### 3.5 Evidência de teste

É a prova de que o teste foi executado.

**Boa prática:** usar prints, vídeos curtos, logs, payloads, respostas da API, IDs de registros e data/hora da execução.

---

## 4. Testes Funcionais

## 4.1 Objetivo

Validar se o sistema faz corretamente aquilo que foi definido na regra de negócio e nos critérios de aceite.

---

## 4.2 Boas práticas para testes funcionais

### 4.2.1 Validar o fluxo principal

O primeiro teste deve provar que o caminho feliz funciona.

**Exemplo:** cadastrar um cliente com todos os dados válidos e confirmar que ele aparece na listagem, no banco e no relatório, quando aplicável.

---

### 4.2.2 Validar fluxos alternativos

Além do caminho feliz, testar situações reais que podem acontecer no uso diário.

**Exemplos:**

- Cliente já cadastrado.
- Campo obrigatório vazio.
- CPF, CNPJ, email ou telefone inválido.
- Usuário sem permissão.
- Tentativa de editar registro bloqueado.
- Registro cancelado, expirado ou inativo.

---

### 4.2.3 Validar regras de negócio

Toda regra crítica precisa ter teste específico.

**Boa prática:** transformar cada regra de negócio em pelo menos um caso de teste.

**Exemplo:** se a regra diz que uma tarefa só pode ser enviada após aprovação, deve existir teste tentando enviar antes da aprovação e outro após aprovação.

---

### 4.2.4 Validar campos obrigatórios

Campos obrigatórios precisam impedir gravação incompleta e exibir mensagem clara.

**Checklist:**

- Campo vazio.
- Campo com espaço em branco.
- Campo com formato inválido.
- Campo com tamanho menor que o mínimo.
- Campo com tamanho maior que o máximo.
- Campo com caracteres especiais.

---

### 4.2.5 Validar mensagens de erro

Mensagem de erro deve ser clara, objetiva e útil para o usuário.

**Ruim:** `Erro 500`  
**Melhor:** `Não foi possível salvar o cadastro. O CPF informado já está vinculado a outro cliente.`

---

### 4.2.6 Validar permissões por perfil

Nem todo usuário deve acessar tudo.

**Boa prática:** testar pelo menos os perfis principais:

- Administrador.
- Gestor.
- Operador.
- Usuário comum.
- Usuário não autenticado.
- Usuário inativo ou bloqueado.

---

### 4.2.7 Validar persistência dos dados

Não basta a tela mostrar sucesso. É necessário confirmar se o dado foi salvo corretamente.

**Boa prática:** quando possível, validar tela, API, banco de dados e relatório.

---

### 4.2.8 Validar edição e exclusão

Cadastro não termina no botão “Salvar”. Também é preciso testar alteração, cancelamento, exclusão lógica, exclusão física e histórico.

**Checklist:**

- Criar registro.
- Editar registro.
- Cancelar edição.
- Excluir registro.
- Restaurar, se existir.
- Verificar histórico, se existir.
- Confirmar impacto em relatórios e integrações.

---

### 4.2.9 Validar filtros, buscas e ordenações

Listagens costumam gerar muitos erros silenciosos.

**Boa prática:** testar busca por texto, filtro por status, filtro por data, paginação, ordenação crescente/decrescente e combinação de filtros.

---

### 4.2.10 Validar responsividade e experiência mínima

Mesmo sendo teste funcional, é importante confirmar se o fluxo funciona em desktop, tablet e celular.

**Boa prática:** testar pelo menos os tamanhos principais de tela e validar botões, menus, tabelas, formulários e modais.

---

## 4.3 Checklist de testes funcionais

```md
[ ] Critérios de aceite estão claros
[ ] Fluxo principal foi testado
[ ] Fluxos alternativos foram testados
[ ] Campos obrigatórios foram validados
[ ] Máscaras e formatos foram validados
[ ] Mensagens de erro são claras
[ ] Permissões foram validadas
[ ] Dados foram persistidos corretamente
[ ] Edição foi testada
[ ] Exclusão/cancelamento foi testado
[ ] Filtros, buscas e paginação foram testados
[ ] Relatórios relacionados foram conferidos
[ ] Evidências foram registradas
[ ] Bugs foram documentados com passos reproduzíveis
```

---

## 5. Testes de Regressão

## 5.1 Objetivo

Validar se uma nova alteração não quebrou funcionalidades que já estavam funcionando.

---

## 5.2 Boas práticas para testes de regressão

### 5.2.1 Criar suíte de regressão crítica

Nem todo sistema precisa ser testado por completo a cada alteração. Mas os fluxos críticos devem sempre ser protegidos.

**Boa prática:** manter uma lista fixa dos principais fluxos de negócio.

**Exemplos:**

- Login.
- Cadastro principal.
- Consulta principal.
- Edição de registro.
- Processos financeiros.
- Envio de dados para API externa.
- Relatórios essenciais.
- Permissões.
- Geração de documentos.

---

### 5.2.2 Priorizar regressão por impacto

Quando uma alteração é feita, identificar quais módulos podem ter sido afetados.

**Exemplo:** alteração no cadastro de cliente pode afetar tela de cliente, API, relatório, faturamento, busca, importação e exportação.

---

### 5.2.3 Automatizar regressão repetitiva

Testes de regressão são fortes candidatos à automação, porque precisam ser repetidos muitas vezes.

**Boa prática:** automatizar os fluxos estáveis, de alto valor e baixo custo de manutenção.

---

### 5.2.4 Executar smoke test antes da regressão completa

Smoke test é uma validação rápida para saber se o sistema está minimamente testável.

**Boa prática:** se o smoke test falhar, não iniciar regressão completa. Corrigir primeiro o bloqueio básico.

---

### 5.2.5 Manter histórico de bugs recorrentes

Bugs que já aconteceram uma vez têm chance de voltar.

**Boa prática:** todo bug crítico corrigido deve virar caso de regressão.

---

### 5.2.6 Separar regressão curta e regressão completa

Nem toda entrega permite uma regressão longa.

**Boa prática:** manter dois pacotes:

- **Regressão curta:** fluxos essenciais para deploy rápido.
- **Regressão completa:** validação mais ampla antes de releases maiores.

---

## 5.3 Checklist de regressão

```md
[ ] Mudanças da release foram identificadas
[ ] Módulos impactados foram mapeados
[ ] Smoke test foi executado
[ ] Fluxos críticos foram testados
[ ] Bugs corrigidos foram retestados
[ ] Casos históricos foram reexecutados
[ ] Permissões principais foram conferidas
[ ] Integrações afetadas foram testadas
[ ] Relatórios afetados foram conferidos
[ ] Evidências foram registradas
[ ] Resultado da regressão foi aprovado ou reprovado formalmente
```

---

## 6. Testes de Integração

## 6.1 Objetivo

Validar se diferentes partes do sistema funcionam corretamente juntas, como frontend, backend, banco de dados, APIs externas, filas, webhooks, serviços de email, serviços de pagamento e sistemas terceiros.

---

## 6.2 Boas práticas para testes de integração

### 6.2.1 Validar contrato da API

Toda integração precisa ter contrato claro.

**Contrato mínimo:**

- URL.
- Método HTTP.
- Headers.
- Autenticação.
- Payload de entrada.
- Payload de resposta.
- Códigos de status.
- Regras de negócio.
- Limites de uso.
- Exemplos de sucesso e erro.

---

### 6.2.2 Testar sucesso e erro

Integração boa não é aquela que só funciona no sucesso. Ela precisa falhar de forma controlada.

**Cenários obrigatórios:**

- Requisição válida.
- Payload inválido.
- Campo obrigatório ausente.
- Token inválido.
- Token expirado.
- Registro duplicado.
- Timeout.
- API fora do ar.
- Resposta inesperada.
- Limite de requisições atingido.

---

### 6.2.3 Validar idempotência

Quando uma integração é reenviada, ela não deve criar duplicidade indevida.

**Boa prática:** testar envio repetido com o mesmo identificador externo e confirmar se o sistema trata como atualização, duplicidade controlada ou rejeição clara.

---

### 6.2.4 Validar logs de integração

Integrações precisam ser rastreáveis.

**Boa prática:** registrar ID da requisição, payload enviado, resposta recebida, status, data/hora, usuário/processo responsável e mensagem de erro tratada.

---

### 6.2.5 Validar retry e fila

Quando uma integração falha por instabilidade temporária, o sistema deve saber tentar novamente com segurança.

**Boa prática:** testar retentativa, fila de reprocessamento, limite de tentativas e alerta após falhas repetidas.

---

### 6.2.6 Validar consistência entre sistemas

Após integrar, conferir se os dados ficaram iguais nos dois lados.

**Exemplo:** se um cadastro foi enviado para API externa, validar status local, status remoto, ID externo, data de envio e mensagem de retorno.

---

### 6.2.7 Usar ambiente separado para integração

Não se deve testar integração diretamente em produção sem controle.

**Boa prática:** usar ambientes separados:

- Desenvolvimento.
- Homologação.
- Produção.

Cada ambiente deve ter banco de dados, tokens, URLs e credenciais separados.

---

### 6.2.8 Validar versionamento de API

Mudanças em API podem quebrar clientes antigos.

**Boa prática:** testar compatibilidade entre versões e documentar mudanças incompatíveis.

---

## 6.3 Checklist de integração

```md
[ ] Contrato da API está documentado
[ ] Autenticação foi validada
[ ] Payload obrigatório foi validado
[ ] Respostas de sucesso foram conferidas
[ ] Respostas de erro foram conferidas
[ ] Timeouts foram simulados
[ ] Retentativas foram testadas
[ ] Idempotência foi validada
[ ] Logs foram conferidos
[ ] Dados foram comparados entre sistemas
[ ] Ambiente correto foi usado
[ ] Tokens e credenciais não foram expostos
[ ] Impacto em relatórios/processos foi validado
```

---

## 7. Testes Operacionais

## 7.1 Objetivo

Validar se o sistema está pronto para operar no dia a dia, incluindo deploy, suporte, logs, backup, monitoramento, alertas, permissões, recuperação de falhas e uso por equipes reais.

---

## 7.2 Boas práticas para testes operacionais

### 7.2.1 Validar instalação e deploy

Um sistema pode funcionar no computador do desenvolvedor e falhar no servidor.

**Boa prática:** testar processo de deploy do zero, incluindo variáveis de ambiente, dependências, permissões de pasta, banco de dados, migrações e serviços externos.

---

### 7.2.2 Validar configuração por ambiente

Cada ambiente precisa ter configuração própria.

**Boa prática:** nunca misturar banco de produção com homologação ou desenvolvimento.

**Checklist por ambiente:**

- URL da aplicação.
- URL da API.
- Banco de dados.
- Usuário e senha.
- Chaves de API.
- Serviço de email.
- Webhooks.
- Storage/arquivos.
- Logs.

---

### 7.2.3 Validar logs úteis

Log operacional precisa ajudar a resolver problema.

**Boa prática:** logs devem mostrar o que aconteceu, quando, onde, com qual usuário/processo e qual erro técnico ou de negócio ocorreu.

**Evitar:** logs genéricos como `erro`, `falha`, `undefined`, `null` sem contexto.

---

### 7.2.4 Validar monitoramento e alertas

Problemas importantes devem ser percebidos antes do usuário reclamar.

**Boa prática:** criar alertas para queda de serviço, erro 500, lentidão, falha de integração, fila parada, disco cheio, uso alto de CPU/RAM e falha de backup.

---

### 7.2.5 Validar backup e restauração

Backup que nunca foi restaurado é apenas uma hipótese.

**Boa prática:** testar restauração de backup periodicamente em ambiente seguro.

---

### 7.2.6 Validar rotina de suporte

QA operacional também verifica se a equipe consegue dar suporte ao sistema.

**Boa prática:** documentar mensagens de erro conhecidas, procedimentos de correção, pontos de contato e critérios de escalação.

---

### 7.2.7 Validar permissões de infraestrutura

Arquivos, uploads, logs e diretórios sensíveis não devem ficar públicos indevidamente.

**Boa prática:** separar pastas públicas e privadas. Uploads sensíveis devem ficar fora da pasta pública ou protegidos por controle de acesso.

---

### 7.2.8 Validar plano de rollback

Toda entrega importante precisa ter caminho de volta.

**Boa prática:** antes do deploy, definir como voltar a versão anterior caso a nova versão falhe.

---

### 7.2.9 Validar documentação operacional

O sistema precisa ter documentação mínima para operar.

**Documentos recomendados:**

- Como subir o projeto.
- Como configurar ambiente.
- Como rodar migrações.
- Como executar testes.
- Como fazer deploy.
- Como restaurar backup.
- Como investigar erro.
- Como acionar suporte técnico.

---

## 7.3 Checklist operacional

```md
[ ] Deploy foi testado
[ ] Variáveis de ambiente foram conferidas
[ ] Banco correto foi usado
[ ] Migrações foram executadas
[ ] Permissões de arquivos foram validadas
[ ] Pastas públicas e privadas foram separadas
[ ] Logs foram verificados
[ ] Monitoramento está ativo
[ ] Alertas foram testados
[ ] Backup foi executado
[ ] Restauração foi testada
[ ] Plano de rollback existe
[ ] Documentação operacional está atualizada
[ ] Equipe sabe como agir em caso de falha
```

---

## 8. Testes de Segurança

## 8.1 Objetivo

Identificar falhas que possam comprometer dados, usuários, permissões, autenticação, infraestrutura, APIs ou a operação do negócio.

---

## 8.2 Boas práticas para testes de segurança

### 8.2.1 Validar autenticação

O sistema deve garantir que apenas usuários válidos consigam entrar.

**Cenários:**

- Login válido.
- Senha inválida.
- Usuário inexistente.
- Usuário bloqueado.
- Usuário inativo.
- Sessão expirada.
- Token inválido.
- Token reutilizado indevidamente.

---

### 8.2.2 Validar autorização

Autenticação responde “quem é o usuário”. Autorização responde “o que ele pode fazer”.

**Boa prática:** testar acesso direto por URL e API, não apenas esconder botão na tela.

---

### 8.2.3 Validar controle de acesso por perfil

Cada perfil deve acessar somente o que foi permitido.

**Cenários:**

- Usuário comum tentando acessar área administrativa.
- Operador tentando excluir registro sensível.
- Usuário de uma empresa tentando acessar dados de outra empresa.
- Usuário inativo tentando consumir API.

---

### 8.2.4 Validar exposição de dados sensíveis

Dados pessoais, tokens, senhas, documentos, chaves e informações internas não devem aparecer sem necessidade.

**Boa prática:** verificar tela, resposta de API, logs, exportações, mensagens de erro e arquivos públicos.

---

### 8.2.5 Validar proteção contra SQL Injection

Campos de entrada não devem permitir manipulação de consulta SQL.

**Boa prática:** garantir uso de prepared statements, validação de entrada e tratamento seguro de erros.

---

### 8.2.6 Validar proteção contra XSS

O sistema não deve executar scripts inseridos por usuários.

**Boa prática:** escapar saída em HTML, validar entrada e evitar inserir conteúdo do usuário diretamente no DOM.

---

### 8.2.7 Validar proteção contra CSRF

Ações sensíveis não devem ser executadas por requisições forjadas.

**Boa prática:** usar token CSRF em formulários e validar origem quando aplicável.

---

### 8.2.8 Validar upload de arquivos

Upload é uma área crítica de segurança.

**Checklist:**

- Validar extensão.
- Validar MIME type.
- Limitar tamanho.
- Renomear arquivo.
- Bloquear execução de scripts.
- Armazenar em local seguro.
- Verificar permissão de download.

---

### 8.2.9 Validar gerenciamento de sessão

Sessões precisam expirar e ser invalidadas corretamente.

**Cenários:**

- Logout invalida sessão.
- Token expirado não funciona.
- Troca de senha invalida sessões antigas, quando aplicável.
- Sessão não fica exposta em URL.
- Cookies sensíveis usam flags de segurança quando aplicável.

---

### 8.2.10 Validar rate limit e abuso

APIs públicas ou sensíveis devem ter proteção contra abuso.

**Boa prática:** testar muitas tentativas de login, muitas chamadas por minuto e chamadas repetidas com erro.

---

### 8.2.11 Validar mensagens de erro seguras

Erro não deve revelar senha, token, stack trace, SQL, caminho interno de servidor ou estrutura do banco.

**Boa prática:** para o usuário, mensagem clara e segura. Para o log interno, detalhe técnico controlado.

---

### 8.2.12 Validar dependências

Bibliotecas antigas podem conter vulnerabilidades conhecidas.

**Boa prática:** verificar dependências, versões, pacotes abandonados e alertas de segurança.

---

## 8.3 Checklist de segurança

```md
[ ] Login foi testado
[ ] Logout foi testado
[ ] Sessão expirada foi testada
[ ] Permissões por perfil foram validadas
[ ] Acesso direto por URL foi testado
[ ] Acesso direto por API foi testado
[ ] Dados sensíveis não aparecem indevidamente
[ ] Logs não expõem senhas/tokens
[ ] SQL Injection foi considerado
[ ] XSS foi considerado
[ ] CSRF foi considerado
[ ] Upload de arquivo foi validado
[ ] Rate limit foi validado
[ ] Mensagens de erro são seguras
[ ] Dependências foram verificadas
```

---

## 9. Testes de Performance

## 9.1 Objetivo

Validar se o sistema suporta o volume esperado de usuários, dados e requisições com tempo de resposta aceitável, estabilidade e uso controlado de recursos.

---

## 9.2 Tipos de teste de performance

### 9.2.1 Teste de carga

Valida o comportamento do sistema com volume esperado de uso.

**Exemplo:** 300 usuários por dia, 100 consultas por usuário, horários de pico e processos em lote.

---

### 9.2.2 Teste de estresse

Força o sistema além do esperado para descobrir o limite.

**Objetivo:** entender quando o sistema começa a degradar, falhar ou ficar instável.

---

### 9.2.3 Teste de pico

Valida aumento brusco de acessos ou chamadas.

**Exemplo:** muitos usuários acessando no mesmo horário ou lote grande sendo processado em poucos minutos.

---

### 9.2.4 Teste de resistência

Valida estabilidade por longo período.

**Objetivo:** encontrar vazamento de memória, lentidão acumulada, filas travadas e crescimento indevido de logs.

---

## 9.3 Boas práticas para testes de performance

### 9.3.1 Definir meta antes de testar

Teste de performance sem meta não diz se está bom ou ruim.

**Métricas recomendadas:**

- Tempo médio de resposta.
- Percentil 95 ou 99 de resposta.
- Requisições por segundo.
- Taxa de erro.
- Uso de CPU.
- Uso de memória.
- Uso de disco.
- Tempo de consulta SQL.
- Tempo de resposta da API externa.

---

### 9.3.2 Testar cenários reais

Não adianta testar apenas a página inicial se o gargalo está em relatório, busca, importação ou integração.

**Boa prática:** escolher os fluxos mais usados e mais críticos.

---

### 9.3.3 Usar massa de dados parecida com produção

Banco vazio mascara problema de performance.

**Boa prática:** testar com volume realista de registros, anexos, histórico, logs e relacionamentos.

---

### 9.3.4 Medir backend, banco e frontend separadamente

Lentidão pode estar no navegador, API, SQL, rede, servidor ou integração externa.

**Boa prática:** coletar métricas por camada.

---

### 9.3.5 Validar consultas SQL

Banco de dados é uma das maiores fontes de gargalo.

**Checklist:**

- Índices corretos.
- Ausência de consultas N+1.
- Paginação em listagens.
- Filtros otimizados.
- Evitar `SELECT *` sem necessidade.
- Limitar relatórios muito grandes.
- Analisar planos de execução.

---

### 9.3.6 Validar paginação e processamento em lote

Sistemas que processam muitos registros precisam evitar chamadas unitárias quando isso gera lentidão.

**Boa prática:** quando possível, permitir envio em lote, paginação controlada, fila assíncrona e processamento incremental.

---

### 9.3.7 Validar timeout

Timeout precisa ser previsível e tratado.

**Boa prática:** definir tempo máximo de resposta para APIs, relatórios, importações e integrações externas.

---

### 9.3.8 Validar cache com cuidado

Cache pode melhorar performance, mas também pode exibir dado antigo.

**Boa prática:** testar atualização, invalidação e consistência de cache.

---

### 9.3.9 Registrar baseline

Baseline é a medição inicial usada para comparar melhorias ou pioras futuras.

**Boa prática:** guardar resultado de cada teste com data, ambiente, versão, volume, ferramenta e métricas.

---

## 9.4 Checklist de performance

```md
[ ] Metas de performance foram definidas
[ ] Cenários críticos foram escolhidos
[ ] Massa de dados é realista
[ ] Teste de carga foi executado
[ ] Teste de pico foi executado, se necessário
[ ] Teste de estresse foi executado, se necessário
[ ] Teste de resistência foi executado, se necessário
[ ] Tempo médio foi medido
[ ] Percentil 95 ou 99 foi medido
[ ] Taxa de erro foi medida
[ ] CPU e memória foram monitoradas
[ ] Consultas SQL foram analisadas
[ ] Gargalos foram registrados
[ ] Baseline foi salvo
[ ] Recomendações de melhoria foram documentadas
```

---

## 10. Estratégia de Ambientes

## 10.1 Desenvolvimento

Ambiente usado por desenvolvedores para construir e testar localmente.

**Boa prática:** pode ter dados fictícios e instáveis, mas não deve usar credenciais reais de produção.

---

## 10.2 Homologação

Ambiente usado para validação antes de produção.

**Boa prática:** deve ser o mais parecido possível com produção, mas com dados controlados e sem impactar usuários reais.

---

## 10.3 Produção

Ambiente usado pelos usuários reais.

**Boa prática:** testes em produção devem ser mínimos, controlados, rastreáveis e sem risco para dados reais.

---

## 10.4 Checklist de separação de ambientes

```md
[ ] Desenvolvimento tem banco próprio
[ ] Homologação tem banco próprio
[ ] Produção tem banco próprio
[ ] Tokens são diferentes por ambiente
[ ] URLs são diferentes por ambiente
[ ] Logs identificam o ambiente
[ ] Dados reais não são usados indevidamente em desenvolvimento
[ ] Deploy em produção exige aprovação
[ ] Existe plano de rollback
```

---

## 11. Priorização de Testes

## 11.1 Matriz de risco

Classificar testes por impacto e probabilidade.

| Impacto | Probabilidade | Prioridade |
|---|---:|---:|
| Alto | Alta | Crítica |
| Alto | Média | Alta |
| Médio | Alta | Alta |
| Médio | Média | Média |
| Baixo | Baixa | Baixa |

---

## 11.2 Severidade x Prioridade

### Severidade

Indica o tamanho do dano técnico ou de negócio.

**Exemplos:**

- Crítica: sistema fora do ar, perda de dados, falha grave de segurança.
- Alta: funcionalidade principal quebrada.
- Média: erro com contorno manual.
- Baixa: erro visual ou texto incorreto sem impacto operacional.

### Prioridade

Indica a urgência de correção.

**Boa prática:** um bug pode ter severidade alta e prioridade média, ou severidade baixa e prioridade alta, dependendo do contexto.

---

## 12. Modelo de Bug Report

```md
# Bug Report

ID: BUG-0001
Título: [Resumo claro do problema]
Ambiente: Desenvolvimento / Homologação / Produção
Versão/branch: [informar]
Data/Hora: [informar]
Usuário usado no teste: [informar]
Perfil do usuário: [informar]

## Severidade
Crítica / Alta / Média / Baixa

## Prioridade
Urgente / Alta / Média / Baixa

## Pré-condição
[O que precisa existir antes de reproduzir]

## Passos para reproduzir
1. [Passo 1]
2. [Passo 2]
3. [Passo 3]

## Resultado esperado
[O que deveria acontecer]

## Resultado obtido
[O que aconteceu de fato]

## Evidências
[Print, vídeo, payload, response, log, ID do registro]

## Impacto
[Explicar impacto para usuário, operação ou negócio]

## Observações técnicas
[Logs, endpoint, query, console, browser, device]
```

---

## 13. Modelo de Plano de Teste

```md
# Plano de Teste

## Projeto
[Nome do projeto]

## Funcionalidade
[Nome da funcionalidade]

## Objetivo
[O que será validado]

## Escopo
[O que entra no teste]

## Fora de escopo
[O que não será testado agora]

## Ambientes
- Desenvolvimento
- Homologação
- Produção, se aplicável

## Tipos de teste
- Funcional
- Regressão
- Integração
- Operacional
- Segurança
- Performance

## Critérios de entrada
[O que precisa estar pronto para começar]

## Critérios de saída
[O que precisa estar aprovado para finalizar]

## Riscos
[Principais riscos da entrega]

## Massa de dados
[Dados necessários]

## Evidências
[Como os resultados serão registrados]

## Responsáveis
[QA, desenvolvimento, produto, negócio]
```

---

## 14. Definition of Ready — Pronto para Desenvolver

Uma demanda só deve entrar em desenvolvimento quando tiver clareza mínima.

```md
[ ] Objetivo da demanda está claro
[ ] Regra de negócio foi descrita
[ ] Critérios de aceite foram definidos
[ ] Layout ou referência visual existe, se necessário
[ ] Campos obrigatórios foram definidos
[ ] Perfis de acesso foram definidos
[ ] Integrações foram mapeadas
[ ] Regras de erro foram descritas
[ ] Impacto em relatórios foi considerado
[ ] Massa de teste pode ser criada
```

---

## 15. Definition of Done — Pronto para Entrega

Uma demanda só deve ser considerada pronta quando passou por validação suficiente.

```md
[ ] Código foi finalizado
[ ] Testes unitários passaram, se existirem
[ ] Testes funcionais passaram
[ ] Testes de regressão necessários passaram
[ ] Integrações foram validadas
[ ] Permissões foram conferidas
[ ] Segurança básica foi revisada
[ ] Performance mínima foi considerada
[ ] Logs foram conferidos
[ ] Documentação foi atualizada
[ ] Bugs críticos foram corrigidos
[ ] Evidências foram anexadas
[ ] Aprovação final foi registrada
```

---

# 16. Instruções para a IA usar esta Skill

## 16.1 Papel da IA

Ao usar esta skill, a IA deve agir como um **QA sênior**. Ela deve ser crítica, objetiva, preventiva e orientada a risco.

A IA não deve apenas dizer “teste isso”. Ela deve transformar requisitos, telas, APIs, regras de negócio ou código em um plano de validação claro e executável.

---

## 16.2 Comportamento esperado da IA

A IA deve:

1. Ler o requisito ou descrição da funcionalidade.
2. Identificar regras de negócio explícitas.
3. Apontar dúvidas e regras implícitas.
4. Mapear riscos.
5. Criar cenários de teste.
6. Criar casos de teste objetivos.
7. Separar testes por tipo: funcional, regressão, integração, operacional, segurança e performance.
8. Sugerir massa de dados.
9. Sugerir evidências.
10. Definir critérios de aprovação.
11. Gerar checklist final para homologação.

---

## 16.3 A IA deve sempre perguntar ou sinalizar

Quando faltar informação, a IA deve sinalizar claramente:

```md
Ponto pendente de regra de negócio:
[descrever a dúvida]

Impacto da dúvida:
[explicar o risco se isso não for definido]

Sugestão de decisão:
[sugerir uma regra objetiva]
```

---

## 16.4 A IA deve evitar

A IA não deve:

- Aprovar funcionalidade sem critério claro.
- Criar teste genérico demais.
- Ignorar permissões.
- Ignorar erro de API.
- Ignorar impacto em relatórios.
- Ignorar logs.
- Ignorar segurança básica.
- Sugerir teste em produção sem controle.
- Usar dados reais sensíveis sem necessidade.
- Confundir teste funcional com teste visual superficial.

---

## 16.5 Formato de resposta recomendado da IA

Sempre que receber uma funcionalidade para testar, responder neste formato:

```md
# Plano de QA — [Nome da funcionalidade]

## 1. Resumo da funcionalidade
[explicar em linguagem simples]

## 2. Regras de negócio identificadas
- [regra 1]
- [regra 2]

## 3. Dúvidas ou pontos pendentes
- [dúvida 1]
- [dúvida 2]

## 4. Riscos principais
- [risco 1]
- [risco 2]

## 5. Testes funcionais
| ID | Cenário | Passos | Resultado esperado | Prioridade |
|---|---|---|---|---|
| CT-001 |  |  |  |  |

## 6. Testes de regressão
- [fluxo crítico 1]
- [fluxo crítico 2]

## 7. Testes de integração
- [API, webhook, banco, serviço externo]

## 8. Testes operacionais
- [deploy, logs, backup, permissões, monitoramento]

## 9. Testes de segurança
- [autenticação, autorização, dados sensíveis, upload, sessão]

## 10. Testes de performance
- [volume, tempo de resposta, consulta, lote, pico]

## 11. Massa de dados necessária
- [massa 1]
- [massa 2]

## 12. Evidências esperadas
- [prints, vídeos, payloads, logs, IDs]

## 13. Critério de aprovação
[descrever quando pode aprovar]

## 14. Checklist final
[ ] Fluxo principal aprovado
[ ] Fluxos alternativos aprovados
[ ] Integrações aprovadas
[ ] Regressão aprovada
[ ] Segurança básica aprovada
[ ] Evidências anexadas
```

---

# 17. Checklist Geral de QA para Qualquer Entrega

```md
[ ] O requisito está claro
[ ] As regras de negócio foram entendidas
[ ] Os critérios de aceite foram definidos
[ ] Os riscos foram classificados
[ ] Os casos de teste foram escritos
[ ] A massa de teste foi preparada
[ ] O ambiente correto foi usado
[ ] O fluxo principal foi aprovado
[ ] Os fluxos alternativos foram aprovados
[ ] Os erros foram tratados corretamente
[ ] As permissões foram validadas
[ ] As integrações foram testadas
[ ] A regressão foi executada
[ ] Segurança básica foi validada
[ ] Performance mínima foi validada
[ ] Logs foram conferidos
[ ] Evidências foram registradas
[ ] Bugs foram documentados
[ ] Retestes foram feitos
[ ] Aprovação final foi registrada
```

---

# 18. Comandos úteis para a IA

## 18.1 Criar plano de teste

```md
Aja como QA sênior. Crie um plano de teste completo para a funcionalidade abaixo, separando testes funcionais, regressão, integração, operacionais, segurança e performance. Gere cenários, casos de teste, massa de dados, riscos, evidências e critérios de aprovação.

Funcionalidade:
[descrever]
```

---

## 18.2 Revisar requisito como QA

```md
Aja como QA sênior. Revise este requisito antes do desenvolvimento. Aponte regras de negócio faltantes, ambiguidades, riscos, impactos em integração, segurança, performance e critérios de aceite necessários.

Requisito:
[colar requisito]
```

---

## 18.3 Criar checklist de homologação

```md
Aja como QA sênior. Crie um checklist de homologação objetivo para esta entrega, priorizando riscos, fluxos críticos e validações obrigatórias antes de produção.

Entrega:
[descrever entrega]
```

---

## 18.4 Criar casos de teste em tabela

```md
Aja como QA sênior. Transforme esta funcionalidade em casos de teste em tabela com ID, tipo de teste, cenário, pré-condição, passos, resultado esperado, prioridade e evidência esperada.

Funcionalidade:
[descrever]
```

---

## 18.5 Revisar bug report

```md
Aja como QA sênior. Revise este bug report e diga se ele está reproduzível. Se faltar informação, reescreva o bug com os campos corretos.

Bug:
[colar bug]
```

---

# 19. Padrão mínimo de qualidade para aprovação

Uma entrega não deve ser aprovada se existir qualquer uma das situações abaixo:

- Fluxo principal quebrado.
- Bug crítico sem correção.
- Falha de permissão grave.
- Exposição de dados sensíveis.
- Integração crítica sem teste.
- Erro sem mensagem clara para o usuário.
- Falta de evidência dos testes.
- Falta de critério de aceite.
- Deploy sem plano de rollback.
- Performance muito abaixo do mínimo esperado.

---

# 20. Resumo Executivo

QA profissional não é apenas procurar erro na tela. QA é proteger o negócio, o usuário, os dados e a operação.

Uma boa prática de QA precisa cobrir:

- O que o sistema deve fazer.
- O que o sistema não pode permitir.
- Como o sistema deve falhar.
- Como o sistema deve se recuperar.
- Como provar que tudo foi validado.

Esta skill deve ser usada sempre que uma IA precisar avaliar requisitos, criar testes, revisar entregas, preparar homologação ou aumentar a confiabilidade de um projeto de software.


---

# Checklist obrigatório das regras de manutenção

- [ ] Evidência mínima foi gerada ou checklist manual foi deixado.
- [ ] Comportamento existente foi preservado ou mudança funcional foi aprovada.
- [ ] Risco de regressão foi avaliado.
