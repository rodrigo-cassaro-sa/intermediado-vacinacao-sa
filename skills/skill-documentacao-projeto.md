# Skill: Documentação de Projeto para Desenvolvimento com IA

## Objetivo da skill

Esta skill orienta uma IA a criar, organizar e manter **documentação técnica e funcional de projetos de software, SaaS e apps**, permitindo que outra pessoa ou outra IA entenda rapidamente o sistema, rode localmente, desenvolva, teste, publique e mantenha.

O foco é evitar perda de contexto, decisões esquecidas, regras soltas, código sem explicação e retrabalho.

---

## Limite desta skill

Esta skill define como documentar, organizar e manter o contexto técnico e funcional do projeto.

Ela deve focar em README, regras de negócio, mapa de telas, API, banco de dados, deploy, testes, changelog, decisões técnicas e instruções para IA de coding.

Ela pode citar briefing, arquitetura, telas, banco, API, QA e deploy para documentar o projeto, mas não deve substituir:

- `skill-briefing.md` para descoberta inicial de negócio;
- `skill-arquitetura.md` para definição técnica da arquitetura;
- `skill-telas.md` para mapa funcional detalhado;
- `skill-api-rest.md` para contrato completo de endpoints;
- `skill-dados.md` e `skill-mysql.md` para modelagem e implementação do banco;
- `skill-qa.md` para plano completo de testes;
- `skill-deploy-ci-cd.md` para publicação e automação de deploy.

Esta skill responde "o projeto está explicado o suficiente para outra pessoa ou IA entender, alterar, testar e publicar sem depender de conversa perdida?".

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


## Regra de documentação junto com mudança

Toda mudança que alterar regra, tela, endpoint, tabela, deploy, integração, permissão, fluxo crítico, performance ou comportamento esperado deve atualizar a documentação correspondente.

Código alterado sem documentação atualizada gera perda de contexto e aumenta risco de manutenção futura.

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

## Perfil que a IA deve assumir

Ao usar esta skill, aja como uma pessoa gerente técnica de projeto, arquiteta de software e documentadora sênior.

A IA deve pensar em:

- briefing;
- README;
- mapa de telas;
- regras de negócio;
- banco de dados;
- APIs;
- instalação local;
- deploy;
- testes;
- decisões técnicas;
- histórico de mudanças.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa todas as outras skills do projeto, principalmente:

```txt
skill-briefing.md
skill-arquitetura.md
skill-telas.md
skill-api-rest.md
skill-dados.md
skill-git.md
skill-deploy-ci-cd.md
skill-qa.md
```

---

## Princípio central

```txt
Projeto bem documentado é aquele que alguém consegue instalar, entender, alterar e publicar sem depender de conversa perdida.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Documentação mínima obrigatória

Todo projeto deve ter pelo menos:

```txt
README.md
/docs/briefing.md
/docs/arquitetura.md
/docs/mapa-telas.md
/docs/regras-negocio.md
/docs/api.md
/docs/banco-dados.md
/docs/deploy.md
/docs/testes.md
/docs/changelog.md
.env.example
```

Projetos menores podem juntar alguns arquivos, mas não devem deixar informação crítica apenas na memória.

---

# 2. README profissional

O README deve responder rapidamente:

- o que é o projeto;
- stack usada;
- requisitos;
- como instalar;
- como configurar `.env`;
- como rodar local;
- como acessar;
- estrutura de pastas;
- comandos úteis;
- como testar;
- como publicar;
- responsáveis.

Modelo:

```md
# Nome do Projeto

## Descrição
## Stack
## Requisitos
## Instalação
## Configuração
## Estrutura de Pastas
## Banco de Dados
## Como Rodar
## Testes
## Deploy
## Observações
```

---

# 3. Regras de negócio

Regras de negócio devem ser escritas de forma objetiva.

Para cada regra:

- código ou identificador;
- descrição;
- telas impactadas;
- tabelas impactadas;
- permissões;
- exceções;
- exemplos;
- critérios de aceite.

Exemplo:

```md
RN-001 — Apenas administradores podem excluir usuários.
Exceção: o próprio usuário não pode excluir a si mesmo.
Impacto: tela de usuários, endpoint DELETE /usuarios/{id}, tabela usuarios.
```

---

# 4. Mapa de telas

O mapa de telas deve mostrar:

- nome da tela;
- objetivo;
- quem acessa;
- ações possíveis;
- dados exibidos;
- filtros;
- botões;
- estados vazios;
- erros;
- permissões;
- endpoints usados.

Isso conecta design, frontend, backend e banco.

---

# 5. Documentação de API

A API deve ter documentação para consumo.

Para cada endpoint:

- método;
- rota;
- descrição;
- autenticação;
- permissão;
- payload;
- resposta de sucesso;
- erros possíveis;
- exemplo Fetch quando útil.

Documentação deve acompanhar mudanças de endpoint.

---

# 6. Documentação de banco

Banco de dados deve ser explicável.

Incluir:

- lista de tabelas;
- objetivo de cada tabela;
- campos principais;
- relacionamentos;
- índices importantes;
- regras de integridade;
- dados sensíveis;
- seeds;
- migrações.

Dicionário de dados evita confusão com nomes parecidos.

---

# 7. Decisões técnicas

Registrar decisões importantes.

Modelo ADR simples:

```md
# DT-001 — Usar PHP procedural puro

Data: 02/07/2026
Contexto: projeto precisa ser simples e fácil de hospedar.
Decisão: usar PHP procedural + MySQL + JS puro.
Consequências: menor complexidade, sem framework, maior disciplina de organização.
```

Registrar decisões evita rediscutir o mesmo assunto.

---

# 8. Changelog

Manter histórico de versões.

Modelo:

```md
## 1.2.0 — 02/07/2026

### Adicionado
- Tela de relatórios mensais.

### Alterado
- Melhoria na listagem de demandas.

### Corrigido
- Correção no filtro por responsável.
```

Changelog ajuda deploy, suporte e comunicação.

---

# 9. Comentários no código

Documentação não substitui comentários úteis no código.

Comentar:

- objetivo do arquivo;
- funções importantes;
- regra de negócio complexa;
- decisão não óbvia;
- integração externa;
- workaround temporário.

Evitar comentar o óbvio.

Bom comentário explica o motivo, não apenas repete o código.

---

# 10. Documentação para IA de coding

Quando o projeto será mantido por IA, criar instruções claras.

Arquivo sugerido:

```txt
CLAUDE.md / AGENTS.md / SKILLS.md
```

Conteúdo:

- stack oficial;
- o que evitar;
- padrões de pasta;
- padrão de resposta JSON;
- padrão de banco;
- regras de segurança;
- como testar;
- como fazer commit;
- não alterar arquivos fora do escopo sem avisar.

Isso reduz alterações indevidas.

---

# 11. Atualização contínua

Documentação deve acompanhar código.

Regra:

```txt
Mudou regra, tela, endpoint, tabela ou deploy? Atualize a documentação junto.
```

Documentação desatualizada pode ser pior que ausência de documentação porque induz erro.

---

# Checklist obrigatório antes de concluir

- [ ] Evidência mínima foi gerada ou checklist manual foi deixado.
- [ ] Comportamento existente foi preservado ou mudança funcional foi aprovada.
- [ ] Risco de regressão foi avaliado.

- [ ] README existe.
- [ ] `.env.example` existe.
- [ ] Regras de negócio estão documentadas.
- [ ] Mapa de telas está documentado.
- [ ] API está documentada.
- [ ] Banco tem dicionário ou explicação.
- [ ] Deploy está documentado.
- [ ] Testes estão documentados.
- [ ] Decisões técnicas importantes foram registradas.
- [ ] Documentação foi atualizada junto com a mudança.

---

# Modelo de entrega esperado

Ao documentar um projeto, entregue:

1. Estrutura de arquivos de documentação.
2. README completo.
3. Regras de negócio.
4. Mapa de telas.
5. API.
6. Banco de dados.
7. Deploy.
8. Testes.
9. Decisões técnicas.
10. Changelog.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
