# Skill: Boas Práticas de Git para Projetos

## Objetivo da Skill

Esta skill orienta uma IA ou desenvolvedor a trabalhar com Git de forma segura, organizada e profissional em projetos de software.

O foco é manter histórico limpo, colaboração segura, rastreabilidade das alterações, proteção contra perda de código e padronização do fluxo de versionamento.

---

## Limite desta skill

Esta skill define versionamento Git, branches, commits, pull requests, tags, releases, histórico, recuperação e proteção contra perda de código.

Ela pode citar deploy, documentação, QA e code review quando isso afetar o fluxo de versionamento, mas não deve substituir:

- `skill-deploy-ci-cd.md` para processo completo de publicação;
- `skill-documentacao-projeto.md` para documentação funcional e técnica;
- `skill-qa.md` para plano completo de testes;
- `skill-refatoracao-code-review.md` para revisão profunda de código;
- `skill-seguranca.md` para proteção geral de segredos e credenciais.

Esta skill responde "o código está versionado, rastreável, revisável e seguro para ser integrado ou publicado?".

---

## Regra de versão publicada

Toda publicação em homologação ou produção deve ter uma versão identificável.

A versão pode ser:

- tag Git;
- número semântico;
- hash curto do commit;
- release documentada;
- registro no changelog.

Toda versão publicada deve informar:

```txt
versao
ambiente
branch_origem
commit_hash
data_hora_deploy
responsavel
principais_mudancas
migrations_executadas
rollback_disponivel
status_pos_deploy
```

A IA nunca deve considerar um deploy profissional quando não for possível descobrir exatamente qual código está rodando no ambiente.

Essa regra conecta Git, Docker, deploy e monitoramento, garantindo rastreabilidade da versão publicada.


---

## Regra de checklist pré-produção

Antes de qualquer deploy em produção, a IA deve validar:

- branch ou tag correta;
- Git sem alterações locais pendentes;
- `.env` correto do ambiente;
- debug desligado;
- backup realizado quando houver banco ou arquivos críticos;
- migrations revisadas e testadas;
- plano de rollback definido;
- health check disponível;
- logs ativos;
- monitoramento mínimo ativo;
- testes críticos executados;
- responsável pela validação definido.

Deploy em produção sem checklist não deve ser tratado como seguro.

Quando algum item não puder ser validado, a IA deve registrar o risco, sugerir correção e pedir aprovação explícita antes de seguir.


---

## Regra de observabilidade mínima

Nenhum sistema deve ir para produção sem visibilidade mínima.

Antes de produção, garantir:

- health check da aplicação;
- log de erro do backend;
- log de acesso ou requisições importantes;
- identificação do ambiente;
- versão publicada visível;
- status de banco;
- monitoramento de espaço em disco;
- alerta para erro crítico;
- verificação de backup;
- forma de consultar últimos erros;
- registro de deploy.

Se o sistema possui pagamentos, webhooks, integrações, filas ou jobs, monitorar também:

- último evento recebido;
- último evento processado;
- eventos com erro;
- fila acumulada;
- falhas consecutivas;
- tempo de processamento.

Produção sem observabilidade mínima vira operação às cegas.


---

## Regra de nomenclatura para produção, datas e logs

Por padrão, campos internos de logs, health check, jobs, deploy e monitoramento devem seguir a nomenclatura em português usada no projeto.

### Campos recomendados

```txt
data_hora
nivel
contexto
mensagem
usuario_id
ambiente
versao
verificado_em
iniciado_em
finalizado_em
duracao_ms
mensagem_erro
registros_processados
data_hora_deploy
responsavel
principais_mudancas
status_pos_deploy
```

### Tradução recomendada

```txt
data_hora           -> data_hora
nivel               -> nivel
contexto             -> contexto
mensagem             -> mensagem
usuario_id             -> usuario_id
verificado_em          -> verificado_em
iniciado_em          -> iniciado_em
finalizado_em         -> finalizado_em
duracao_ms         -> duracao_ms
mensagem_erro       -> mensagem_erro
registros_processados   -> registros_processados
environment         -> ambiente
version             -> versao
```

### Regra obrigatória

Não misturar nomes de campos em inglês e português no mesmo projeto quando forem campos internos do sistema.

Termos técnicos como `request_id`, `trace_id`, `status_code`, `endpoint`, `health_check`, `commit_hash`, `container_id`, `APP_ENV`, `DB_HOST` e nomes oficiais de variáveis de ambiente podem ser mantidos em inglês quando forem padrão técnico.


---


## 1. Princípio Central do Git

Git não é apenas uma ferramenta para “salvar código”. Git é um sistema de controle de versão usado para registrar decisões técnicas, acompanhar evolução do projeto e permitir colaboração segura entre pessoas e ferramentas automatizadas.

Toda alteração deve responder claramente:

- O que foi alterado?
- Por que foi alterado?
- Onde foi alterado?
- Qual impacto isso causa no projeto?

---

## 2. Sempre Verificar o Estado Antes de Agir

Antes de qualquer comando importante, sempre verificar o estado atual do repositório.

Comandos recomendados:

```bash
git status
git branch
git log --oneline --decorate --graph -10
```

### Boa prática

Nunca fazer commit, merge, rebase, pull, push ou reset sem antes saber:

- Em qual branch está.
- Se existem arquivos modificados.
- Se existem arquivos não rastreados.
- Se existem commits locais ainda não enviados.
- Se a branch local está atrasada ou à frente da branch remota.

---

## 3. Usar `.gitignore` Corretamente

O arquivo `.gitignore` deve impedir que arquivos temporários, sensíveis ou gerados automaticamente entrem no versionamento.

### Exemplos comuns

```gitignore
# Dependências
node_modules/
vendor/

# Ambiente
.env
.env.local
.env.production

# Logs
*.log
logs/

# Sistema operacional
.DS_Store
Thumbs.db

# Build / cache
dist/
build/
.cache/

# IDE
.vscode/
.idea/
```

### Boa prática

Nunca versionar:

- Senhas.
- Tokens de API.
- Chaves privadas.
- Arquivos `.env` reais.
- Backups de banco de dados com dados sensíveis.
- Arquivos temporários da IDE.
- Dependências instaladas localmente, salvo quando houver motivo técnico claro.

---

## 4. Commits Pequenos e Atômicos

Um commit deve representar uma unidade lógica de alteração.

### Correto

```bash
git commit -m "feat: adicionar validação de login"
```

### Evitar

```bash
git commit -m "alterações"
git commit -m "ajustes gerais"
git commit -m "coisas novas"
```

### Boa prática

Cada commit deve fazer uma coisa bem definida:

- Corrigir um bug.
- Criar uma funcionalidade.
- Ajustar uma regra de negócio.
- Refatorar uma parte do código.
- Atualizar documentação.

Não misturar funcionalidades diferentes no mesmo commit.

---

## 5. Padrão de Mensagem de Commit

Usar mensagens claras e padronizadas facilita leitura do histórico e automações futuras.

### Padrão recomendado

```text
tipo: descrição curta no infinitivo ou presente
```

### Tipos comuns

| Tipo | Uso |
|---|---|
| `feat` | Nova funcionalidade |
| `fix` | Correção de bug |
| `docs` | Documentação |
| `style` | Formatação sem mudar lógica |
| `refactor` | Refatoração sem alterar comportamento |
| `test` | Testes |
| `chore` | Tarefas internas, configs, dependências |
| `perf` | Melhoria de performance |
| `security` | Correção ou melhoria de segurança |
| `hotfix` | Correção urgente em produção |

### Exemplos

```bash
git commit -m "feat: criar tela de cadastro de usuário"
git commit -m "fix: corrigir cálculo de vencimento da tarefa"
git commit -m "docs: atualizar instruções de instalação"
git commit -m "refactor: simplificar consulta de clientes"
git commit -m "security: remover token exposto do código"
```

---

## 6. Separar Alterações Antes do Commit

Antes de commitar, revisar exatamente o que será enviado.

Comandos úteis:

```bash
git diff
git diff --staged
git status
```

Adicionar arquivos com cuidado:

```bash
git add arquivo.php
git add pasta/arquivo.js
```

Evitar usar `git add .` de forma automática quando não tiver certeza de todas as alterações.

### Boa prática

Usar `git add .` somente depois de revisar o estado do repositório.

---

## 7. Branches Bem Nomeadas

Branches devem indicar claramente o objetivo da alteração.

### Padrão recomendado

```text
tipo/descricao-curta
```

### Exemplos

```bash
feat/login-usuario
fix/erro-calculo-total
docs/guia-instalacao
refactor/modulo-clientes
hotfix/correcao-envio-api
```

### Boa prática

Evitar nomes genéricos:

```text
teste
ajuste
nova
branch-rodrigo
correcao-final-agora-vai
```

---

## 8. Fluxo de Branches Simples

Para projetos pequenos e médios, usar um fluxo simples:

```text
main        -> versão estável / produção
develop     -> integração de desenvolvimento, se necessário
feat/*      -> novas funcionalidades
fix/*       -> correções
hotfix/*    -> correções urgentes
```

### Boa prática

- `main` deve estar sempre estável.
- Não desenvolver diretamente na `main`.
- Criar branch para cada tarefa relevante.
- Integrar alterações por Pull Request ou Merge Request quando possível.

---

## 9. Pull Antes de Trabalhar

Antes de começar uma nova alteração, atualizar a branch local.

```bash
git pull
```

Ou, quando o time usa rebase:

```bash
git pull --rebase
```

### Boa prática

Sempre atualizar o projeto antes de iniciar trabalho para evitar conflitos grandes no final.

---

## 10. Push Frequente, Mas Organizado

Enviar alterações para o repositório remoto evita perda de trabalho.

```bash
git push origin nome-da-branch
```

### Boa prática

Fazer push ao concluir uma etapa lógica de trabalho.

Evitar passar muitos dias apenas com commits locais.

---

## 11. Pull Request / Merge Request

Todo código que entra na branch principal deve passar por revisão, mesmo em equipes pequenas.

### Um bom Pull Request deve conter

- Resumo do que foi alterado.
- Motivo da alteração.
- Como testar.
- Prints ou evidências, quando houver mudança visual.
- Riscos conhecidos.
- Checklist de validação.

### Modelo recomendado

```md
## Resumo
Descreva brevemente o que foi alterado.

## Motivo
Explique por que essa alteração foi necessária.

## Como testar
1. Acesse...
2. Execute...
3. Confira se...

## Checklist
- [ ] Código testado localmente
- [ ] Sem dados sensíveis no commit
- [ ] Sem arquivos temporários
- [ ] Sem quebra de funcionalidade existente
- [ ] Documentação atualizada, se necessário
```

---

## 12. Revisão de Código

Revisão não é para procurar culpados. Revisão é para proteger o projeto.

### O que revisar

- Clareza do código.
- Segurança.
- Performance.
- Regras de negócio.
- Possíveis efeitos colaterais.
- Nomes de variáveis, funções e arquivos.
- Se o commit está coerente.
- Se não existem dados sensíveis.

### Boa prática

Comentários de revisão devem ser objetivos, respeitosos e técnicos.

---

## 13. Cuidado com Comandos Destrutivos

Alguns comandos podem apagar alterações, reescrever histórico ou causar perda de trabalho.

### Comandos perigosos

```bash
git reset --hard
git clean -fd
git push --force
git rebase
git checkout -- arquivo
git restore arquivo
```

### Regra para IA

Nunca executar comandos destrutivos sem explicar o impacto e pedir confirmação explícita do usuário.

Antes de qualquer ação destrutiva, executar:

```bash
git status
git diff
git log --oneline -10
```

---

## 14. Preferir `--force-with-lease` em Vez de `--force`

Quando for realmente necessário reescrever histórico remoto, usar:

```bash
git push --force-with-lease
```

### Por quê?

`--force-with-lease` é mais seguro porque evita sobrescrever commits remotos que outra pessoa enviou depois da sua última atualização.

### Boa prática

Mesmo assim, só usar com confirmação clara e quando houver certeza do impacto.

---

## 15. Merge vs Rebase

### Merge

Preserva o histórico completo de integração.

```bash
git merge nome-da-branch
```

Bom quando se quer manter o registro explícito de que uma branch foi integrada.

### Rebase

Reorganiza commits para criar um histórico mais linear.

```bash
git rebase main
```

Bom para atualizar uma branch de trabalho antes de abrir Pull Request.

### Boa prática

- Não fazer rebase em branch compartilhada sem alinhamento com o time.
- Preferir merge para integração segura.
- Usar rebase com cuidado para limpar histórico local.

---

## 16. Resolver Conflitos com Atenção

Conflitos indicam que duas alterações mexeram na mesma parte do código.

### Processo recomendado

1. Ler os arquivos em conflito.
2. Entender a intenção de cada alteração.
3. Manter a lógica correta do projeto.
4. Remover marcações de conflito.
5. Testar o código.
6. Fazer commit da resolução.

### Marcações de conflito

```text
<<<<<<< HEAD
código local
=======
código vindo da outra branch
>>>>>>> nome-da-branch
```

### Boa prática

Nunca resolver conflito apagando trechos sem entender a regra de negócio.

---

## 17. Tags e Releases

Tags servem para marcar versões importantes do projeto.

### Exemplo

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

### Boa prática

Usar tags para:

- Versões de produção.
- Entregas para cliente.
- Marcos importantes.
- Versões estáveis antes de grandes mudanças.

---

## 18. Versionamento Semântico

Usar versionamento semântico quando o projeto tiver releases formais.

Formato:

```text
MAJOR.MINOR.PATCH
```

Exemplo:

```text
1.4.2
```

### Significado

- `MAJOR`: mudança grande ou incompatível.
- `MINOR`: nova funcionalidade compatível.
- `PATCH`: correção pequena ou ajuste.

---

## 19. Nunca Versionar Segredos

Dados sensíveis jamais devem entrar no Git.

### Exemplos de segredos

- Token de API.
- Senha de banco.
- Chave SSH privada.
- Certificados privados.
- Arquivos `.env` reais.
- Credenciais de produção.
- Dumps de banco com dados pessoais.

### Boa prática

Criar um arquivo de exemplo:

```bash
.env.example
```

Exemplo:

```env
DB_HOST=localhost
DB_NAME=nome_do_banco
DB_USER=usuario
DB_PASS=senha_aqui
API_TOKEN=token_aqui
```

O `.env.example` mostra quais variáveis existem, mas não contém valores reais.

---

## 20. Se Um Segredo Foi Commitado

Se uma senha ou token foi commitado, não basta apagar o arquivo em um novo commit.

### Ação correta

1. Revogar o token ou trocar a senha imediatamente.
2. Remover o segredo do histórico, se necessário.
3. Verificar logs e acessos suspeitos.
4. Criar proteção para impedir novo vazamento.

### Regra para IA

Ao encontrar segredo no código, alertar o usuário imediatamente e não repetir o valor do segredo na resposta.

---

## 21. Usar Hooks Quando Fizer Sentido

Hooks ajudam a rodar validações antes de commits ou pushes.

### Exemplos de uso

- Verificar formatação.
- Rodar testes.
- Bloquear commits com `.env`.
- Bloquear commits com palavras como `password`, `secret`, `token`.
- Validar padrão da mensagem de commit.

### Boa prática

Hooks devem ajudar o time, não atrapalhar o fluxo com regras exageradas.

---

## 22. Trabalhar com Arquivos Grandes

Git não é ideal para arquivos binários grandes.

### Evitar versionar

- Vídeos grandes.
- Imagens pesadas sem necessidade.
- Backups `.zip`.
- Dumps de banco.
- Arquivos exportados automaticamente.

### Alternativa

Usar Git LFS quando o projeto realmente precisa versionar arquivos grandes.

```bash
git lfs install
git lfs track "*.psd"
git lfs track "*.zip"
```

---

## 23. Documentar o Projeto no Repositório

Todo projeto deve ter pelo menos um `README.md` claro.

### Um bom README contém

- Nome do projeto.
- Descrição curta.
- Requisitos.
- Como instalar.
- Como configurar ambiente.
- Como rodar localmente.
- Como executar testes.
- Como fazer deploy.
- Padrão de branch e commit.
- Informações úteis para manutenção.

---

## 24. Não Usar Git Como Backup Bagunçado

Git não deve ser usado para acumular arquivos sem critério.

### Evitar

```text
backup-final.zip
backup-final-agora-vai.zip
projeto-novo-copia/
index-antigo.php
index-certo.php
index-certo-2.php
```

### Boa prática

Se precisa preservar uma versão, usar commit, branch ou tag.

---

## 25. Sincronizar Antes de Finalizar o Dia

Ao finalizar uma sessão de trabalho:

```bash
git status
git diff
git add arquivo
git commit -m "tipo: descrição"
git push origin nome-da-branch
```

### Boa prática

Não deixar alterações importantes apenas na máquina local.

---

## 26. Padrão de Trabalho Diário

### Início do trabalho

```bash
git status
git checkout main
git pull
git checkout -b feat/nome-da-tarefa
```

### Durante o trabalho

```bash
git status
git diff
git add arquivo
git commit -m "feat: descrição da alteração"
```

### Final do trabalho

```bash
git push origin feat/nome-da-tarefa
```

Depois, abrir Pull Request se o projeto usar revisão.

---

## 27. Fluxo Simples Para Projetos Individuais

Mesmo trabalhando sozinho, manter disciplina.

### Fluxo recomendado

```bash
git status
git add arquivo
git commit -m "tipo: descrição clara"
git push
```

### Boa prática

Em projeto solo, ainda vale usar branches para mudanças grandes ou arriscadas.

---

## 28. Fluxo Para Projetos com Equipe

Em equipe, evitar trabalhar direto na branch principal.

### Fluxo recomendado

1. Atualizar `main`.
2. Criar branch da tarefa.
3. Fazer commits pequenos.
4. Enviar branch para o remoto.
5. Abrir Pull Request.
6. Revisar.
7. Corrigir pontos levantados.
8. Fazer merge.
9. Apagar branch antiga, se não for mais necessária.

---

## 29. Organização de Commits Antes do Pull Request

Antes de abrir PR, revisar histórico:

```bash
git log --oneline
```

Se houver muitos commits ruins como `teste`, `ajuste`, `fix`, avaliar organizar antes do envio, desde que seja seguro.

### Boa prática

Não reescrever histórico compartilhado sem confirmação.

---

## 30. Commits de IA Devem Ser Claros

Quando uma IA alterar o projeto, os commits devem ser ainda mais explicativos.

### Exemplo

```bash
git commit -m "fix: corrigir validação de formulário de cliente"
```

### Evitar

```bash
git commit -m "mudanças feitas pela IA"
```

A mensagem deve descrever a alteração técnica, não quem fez.

---

## 31. Antes da IA Alterar Código

A IA deve sempre analisar o contexto do repositório.

### Checklist obrigatório

```bash
git status
git branch
git log --oneline -5
```

Depois deve identificar:

- Branch atual.
- Arquivos modificados.
- Arquivos não rastreados.
- Possíveis alterações do usuário ainda não commitadas.

### Regra importante

A IA nunca deve sobrescrever trabalho não commitado do usuário.

---

## 32. Antes da IA Fazer Commit

A IA deve revisar o diff.

```bash
git diff
git diff --staged
```

### Checklist

- A alteração resolve o pedido?
- O commit contém apenas arquivos relacionados?
- Não existem arquivos temporários?
- Não existem segredos?
- A mensagem do commit está clara?
- O projeto continua funcionando?

---

## 33. Antes da IA Fazer Push

A IA deve confirmar:

- Branch correta.
- Remote correto.
- Commits corretos.
- Nenhum segredo.
- Nenhum arquivo indevido.
- Se o usuário autorizou o envio.

### Regra para IA

Não fazer push sem pedido ou autorização explícita do usuário.

---

## 34. Evitar Alterar Histórico Sem Necessidade

Reescrever histórico pode atrapalhar outras pessoas.

### Evitar sem alinhamento

```bash
git rebase -i
git reset --hard
git push --force
```

### Boa prática

Preferir criar novo commit de correção quando o histórico já foi compartilhado.

---

## 35. Usar Stash Com Cuidado

`git stash` guarda alterações temporariamente.

```bash
git stash
git stash list
git stash pop
```

### Boa prática

Usar stash apenas quando precisar trocar de branch ou atualizar código sem perder alterações locais.

Sempre conferir o conteúdo depois de aplicar o stash.

---

## 36. Recuperação de Erros

Git permite recuperar muita coisa, mas é preciso agir com calma.

### Comandos úteis

```bash
git reflog
git log --oneline
git status
```

### Boa prática

Quando algo der errado, não executar vários comandos aleatórios.

Primeiro entender o estado atual, depois decidir a recuperação.

---

## 37. Boas Práticas para Deploy com Git

Quando Git é usado no processo de deploy:

- A branch de produção deve ser protegida.
- Deploy deve sair de uma branch ou tag estável.
- Commits em produção devem ser rastreáveis.
- Evitar deploy manual sem registro.
- Usar tags para marcar versões publicadas.

### Boa prática

Nunca fazer deploy de código que não está commitado e versionado.

---

## 38. Proteção de Branch Principal

Em repositórios remotos como GitHub, GitLab ou Bitbucket, proteger a branch principal.

### Regras recomendadas

- Bloquear push direto na `main`.
- Exigir Pull Request.
- Exigir revisão antes do merge.
- Exigir testes passando.
- Impedir force push.
- Impedir exclusão da branch.

---

## 39. Checklist Antes de Commitar

Antes de cada commit, verificar:

- [ ] Estou na branch correta.
- [ ] Revisei `git status`.
- [ ] Revisei `git diff`.
- [ ] O commit tem uma única intenção.
- [ ] Não há arquivos temporários.
- [ ] Não há dados sensíveis.
- [ ] A mensagem segue padrão.
- [ ] O código foi testado minimamente.

---

## 40. Checklist Antes de Fazer Merge

Antes de integrar uma branch:

- [ ] A branch está atualizada com a base.
- [ ] O código foi revisado.
- [ ] O projeto foi testado.
- [ ] Conflitos foram resolvidos corretamente.
- [ ] A documentação foi atualizada, se necessário.
- [ ] Não existem commits confusos ou desnecessários.
- [ ] Não existem segredos no histórico recente.

---

## 41. Checklist Antes de Fazer Push

Antes de enviar código:

- [ ] Estou na branch correta.
- [ ] O remoto está correto.
- [ ] Os commits estão corretos.
- [ ] Não estou enviando arquivos sensíveis.
- [ ] Não estou enviando arquivos grandes sem necessidade.
- [ ] O push foi solicitado ou autorizado.

---

## 42. Comandos Git Essenciais

### Estado do repositório

```bash
git status
```

### Histórico resumido

```bash
git log --oneline --decorate --graph
```

### Ver diferenças

```bash
git diff
git diff --staged
```

### Criar branch

```bash
git checkout -b feat/nome-da-branch
```

### Trocar de branch

```bash
git checkout nome-da-branch
```

### Adicionar arquivo

```bash
git add arquivo
```

### Criar commit

```bash
git commit -m "tipo: descrição clara"
```

### Enviar para remoto

```bash
git push origin nome-da-branch
```

### Atualizar branch local

```bash
git pull
```

### Ver remotos

```bash
git remote -v
```

---

## 43. Comandos Que Exigem Cuidado

### Reset hard

```bash
git reset --hard
```

Pode apagar alterações locais.

### Clean

```bash
git clean -fd
```

Pode apagar arquivos não rastreados.

### Force push

```bash
git push --force
```

Pode sobrescrever trabalho remoto.

### Rebase

```bash
git rebase
```

Pode reescrever histórico.

### Regra

Esses comandos só devem ser executados após análise, explicação e confirmação.

---

## 44. Regras Para IA Trabalhando com Git

A IA deve seguir estas regras:

1. Sempre verificar `git status` antes de alterar arquivos.
2. Nunca sobrescrever alterações não commitadas do usuário.
3. Nunca executar comandos destrutivos sem autorização.
4. Nunca fazer push sem autorização explícita.
5. Nunca commitar segredos ou arquivos sensíveis.
6. Sempre revisar `git diff` antes de commit.
7. Criar commits pequenos e claros.
8. Usar mensagens de commit padronizadas.
9. Explicar riscos antes de merge, rebase, reset ou force push.
10. Preferir segurança e rastreabilidade em vez de velocidade.

---

## 45. Padrão de Resposta da IA em Tarefas Git

Quando o usuário pedir ajuda com Git, a IA deve responder considerando:

- Estado atual do repositório.
- Branch atual.
- Arquivos modificados.
- Objetivo do usuário.
- Risco dos comandos sugeridos.
- Melhor sequência segura de comandos.

### Exemplo de resposta ideal

```text
Você está na branch main e possui arquivos modificados.
Antes de fazer pull ou trocar de branch, recomendo revisar o diff e commitar ou guardar essas alterações.

Sequência segura:
1. git status
2. git diff
3. git add arquivo
4. git commit -m "feat: descrição da alteração"
5. git pull --rebase
6. git push origin main
```

---

## 46. Quando Não Fazer Commit

Não fazer commit quando:

- O código está quebrado sem necessidade.
- Existem arquivos sensíveis.
- Existem alterações aleatórias misturadas.
- O usuário ainda está testando algo descartável.
- O projeto contém arquivos temporários.
- A alteração não foi minimamente validada.

---

## 47. Quando Criar Uma Nova Branch

Criar branch quando:

- A alteração é grande.
- Existe risco de quebrar o projeto.
- É uma nova funcionalidade.
- É uma correção específica.
- Outra pessoa precisa revisar.
- O projeto está em produção.

---

## 48. Quando Usar Tag

Criar tag quando:

- Uma versão foi publicada.
- Uma entrega foi enviada ao cliente.
- Um marco importante foi concluído.
- Uma versão estável precisa ser preservada.

---

## 49. Quando Usar Stash

Usar stash quando:

- Precisa trocar de branch rapidamente.
- Precisa fazer pull, mas tem alterações locais.
- Está no meio de uma tarefa e precisa resolver outra urgência.

Evitar usar stash como armazenamento permanente.

---

## 50. Mentalidade Profissional

Git deve ser usado com disciplina.

Um histórico bem cuidado ajuda a:

- Entender decisões antigas.
- Corrigir bugs com mais segurança.
- Voltar versões quando necessário.
- Trabalhar em equipe.
- Automatizar deploy.
- Proteger produção.
- Facilitar auditoria.
- Ajudar IA e humanos a entenderem o projeto.

---

## Prompt Base Para IA

Use este prompt quando quiser que uma IA trabalhe com Git em um projeto:

```text
Aja como um desenvolvedor sênior especialista em Git.

Antes de qualquer alteração, verifique o estado do repositório com git status, branch atual, arquivos modificados e histórico recente.

Siga estas regras:
- Não sobrescreva alterações locais do usuário.
- Não execute comandos destrutivos sem autorização explícita.
- Não faça push sem autorização explícita.
- Não commite arquivos sensíveis.
- Revise o diff antes de commitar.
- Crie commits pequenos, claros e atômicos.
- Use mensagens no padrão: tipo: descrição clara.
- Explique riscos antes de merge, rebase, reset, clean ou force push.
- Prefira segurança, rastreabilidade e histórico limpo.

Sempre que sugerir comandos Git, explique rapidamente o objetivo de cada etapa.
```

---

## Resumo Final

Boas práticas de Git se resumem a:

- Saber o estado do repositório antes de agir.
- Trabalhar em branches organizadas.
- Fazer commits pequenos e claros.
- Proteger a branch principal.
- Nunca versionar segredos.
- Revisar alterações antes de enviar.
- Evitar comandos destrutivos sem certeza.
- Usar Pull Request quando houver colaboração.
- Criar tags para versões importantes.
- Manter o histórico útil para humanos e IA.

Git bem usado aumenta a segurança, a qualidade e a velocidade do desenvolvimento.

---

# Checklist obrigatório das regras de produção

- [ ] Versão publicada está identificável por tag, número ou commit.
- [ ] Checklist pré-produção foi validado antes de seguir.
- [ ] Observabilidade mínima está ativa no ambiente alvo.
- [ ] Campos internos de datas e logs seguem o padrão em português.
