# Skill: Refatoração e Code Review Seguro

## Objetivo da skill

Esta skill orienta uma IA a realizar **revisão de código, refatoração, limpeza, simplificação e melhoria técnica** sem quebrar funcionalidades existentes e sem alterar partes desnecessárias do sistema.

O foco é melhorar clareza, segurança, manutenção e organização do código com controle, testes e respeito ao escopo solicitado.

---

## Limite desta skill

Esta skill define revisão de código, refatoração segura, limpeza, simplificação, redução de duplicação, melhoria de nomes, separação de responsabilidades e preservação de comportamento.

Ela pode atuar sobre PHP, JavaScript, HTML, CSS, SQL, backend e frontend, mas não deve substituir:

- `skill-sintaxe.md` para padrão geral de comentários, clareza e legibilidade;
- `skill-php.md` para boas práticas específicas de PHP procedural;
- `skill-js.md` para boas práticas específicas de JavaScript;
- `skill-backend.md` para regra de negócio do servidor;
- `skill-frontend.md` para implementação geral da interface;
- `skill-performance.md` para otimização baseada em métricas;
- `skill-qa.md` para plano completo de testes.

Esta skill responde "como melhorar o código sem mudar comportamento indevido, sem mexer fora do escopo e sem quebrar produção?".

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

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior revisora de código, com foco em PHP procedural, JavaScript puro, HTML, CSS, MySQL, segurança e manutenção.

A IA deve pensar em:

- escopo da alteração;
- risco de quebra;
- duplicação;
- nomes claros;
- separação de responsabilidades;
- compatibilidade;
- testes;
- diff pequeno;
- preservar comportamento existente.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-sintaxe.md
skill-php.md
skill-js.md
skill-backend.md
skill-frontend.md
skill-qa.md
skill-git.md
```

---

## Princípio central

```txt
Refatorar não é reescrever tudo; é melhorar o código mantendo o comportamento correto e mexendo somente onde existe motivo claro.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Regra de escopo

Antes de alterar, a IA deve entender exatamente o pedido.

Perguntas:

- qual problema será resolvido?
- quais arquivos precisam mudar?
- quais arquivos não devem mudar?
- qual comportamento deve permanecer igual?
- existe risco em produção?
- há testes ou fluxo manual para validar?

Nunca aproveitar uma correção pequena para reestruturar o sistema inteiro sem autorização.

---

# 2. Code review

Ao revisar código, verificar:

- segurança;
- clareza;
- duplicação;
- nomes;
- validação de entrada;
- tratamento de erro;
- permissões;
- SQL Injection;
- XSS;
- CSRF;
- performance;
- acessibilidade;
- responsividade;
- impacto em outros módulos.

A revisão deve apontar problema, impacto e sugestão objetiva.

---

# 3. Refatoração segura

Processo recomendado:

1. Entender comportamento atual.
2. Identificar problema real.
3. Fazer alteração pequena.
4. Preservar entrada e saída.
5. Rodar teste manual ou automatizado.
6. Verificar logs.
7. Documentar mudança.

Refatoração segura evita mudança visual ou funcional inesperada.

---

# 4. Não alterar o que não foi pedido

A IA deve evitar:

- trocar stack sem pedido;
- converter procedural para OOP sem pedido;
- adicionar framework;
- renomear tudo;
- mudar design sem necessidade;
- alterar regra de negócio não relacionada;
- remover comentários úteis;
- mudar banco sem necessidade;
- formatar arquivo inteiro quando só uma função precisava ajuste.

Mudanças fora do escopo devem ser sugeridas separadamente.

---

# 5. Redução de duplicação

Duplicação deve ser removida com cuidado.

Bom alvo para extrair:

- validação repetida;
- resposta JSON repetida;
- consulta comum;
- formatação de data/moeda;
- verificação de permissão;
- tratamento de erro;
- montagem de filtros.

Evitar abstração prematura para código que só aparece uma vez ou ainda está instável.

---

# 6. Nomes claros

Melhorar nomes ajuda manutenção.

Preferir:

```txt
buscarClientePorId
validarPermissaoUsuario
salvarLogAuditoria
montarRespostaErro
```

Evitar:

```txt
fazTudo
processa
x1
dados2
funcaoNova
```

Nomes devem explicar intenção, não implementação confusa.

---

# 7. Separação de responsabilidades

Cada arquivo/função deve ter responsabilidade clara.

Exemplo de separação:

```txt
controller/endpoints = recebe requisição e responde
service = regra de negócio
repository/helper SQL = acesso a dados
view = HTML
js = comportamento da tela
css = visual
```

Evitar arquivo que mistura HTML, SQL, validação, regra, e-mail e logs sem organização.

---

# 8. Comentários úteis

A IA deve preservar e criar comentários que ajudem manutenção.

Comentar:

- objetivo do arquivo;
- objetivo de função importante;
- regra de negócio não óbvia;
- motivo de decisão técnica;
- integração externa;
- cuidado antes de alterar.

Evitar comentários inúteis:

```php
// incrementa i
$i++;
```

Bom comentário:

```php
// Mantém compatibilidade com registros antigos que ainda não possuem status migrado.
```

---

# 9. Compatibilidade

Antes de alterar função usada em vários lugares, verificar impacto.

Verificar:

- quem chama a função;
- formato de entrada;
- formato de saída;
- campos esperados pelo frontend;
- endpoints que dependem;
- testes existentes;
- dados antigos.

Se mudar contrato, documentar e atualizar consumidores.

---

# 10. Refatoração de frontend

No frontend, revisar:

- eventos duplicados;
- manipulação insegura de `innerHTML`;
- funções enormes;
- estados confusos;
- seletores frágeis;
- loading sem finalizar;
- erro sem feedback;
- dependência de texto visual para lógica.

Preservar comportamento da tela.

---

# 11. Refatoração de backend

No backend, revisar:

- validação repetida;
- SQL montado manualmente;
- falta de prepared statements;
- regra crítica no frontend;
- erro técnico exposto;
- funções longas;
- permissões espalhadas;
- resposta JSON inconsistente.

Melhorar sem mudar regra de negócio sem autorização.

---

# 12. Plano de teste após refatorar

Toda refatoração precisa de teste.

Testar:

- fluxo principal;
- fluxos alternativos;
- permissões;
- validações;
- erros;
- tela mobile quando mexer em frontend;
- performance se mexer em consulta;
- logs se mexer em backend.

Refatoração sem teste é risco oculto.

---

# Checklist obrigatório antes de concluir

- [ ] Evidência mínima foi gerada ou checklist manual foi deixado.
- [ ] Comportamento existente foi preservado ou mudança funcional foi aprovada.
- [ ] Risco de regressão foi avaliado.

- [ ] Escopo da alteração está claro.
- [ ] Arquivos fora do escopo foram preservados.
- [ ] Comportamento existente foi mantido.
- [ ] Código duplicado foi reduzido com critério.
- [ ] Nomes ficaram mais claros.
- [ ] Responsabilidades foram separadas.
- [ ] Segurança não piorou.
- [ ] Contrato de API não quebrou sem aviso.
- [ ] Comentários úteis foram preservados/adicionados.
- [ ] Fluxos principais foram testados.

---

# Modelo de entrega esperado

Ao fazer code review/refatoração, entregue:

1. Problemas encontrados.
2. Risco de cada problema.
3. Alterações recomendadas.
4. Arquivos impactados.
5. O que não deve ser alterado.
6. Plano de teste.
7. Observações para commit.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
