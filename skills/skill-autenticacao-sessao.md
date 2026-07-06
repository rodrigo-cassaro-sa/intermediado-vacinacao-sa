# Skill: Autenticação, Sessão e Controle de Acesso

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e corrigir fluxos de **login, cadastro, recuperação de senha, sessão, logout, proteção de páginas, proteção de APIs e controle de acesso** em sistemas web, SaaS e apps.

O foco é garantir que somente usuários corretos acessem recursos corretos, com segurança, clareza, rastreabilidade e boa experiência de uso.

---

## Limite desta skill

Esta skill define autenticação, sessão, cadastro, login, logout, recuperação de senha e proteção de páginas/APIs privadas.

Ela pode citar permissões apenas para explicar que o usuário autenticado ainda precisa ser autorizado.

Ela não deve substituir:

- `skill-perfi-permissoes.md` para matriz completa de perfis, papéis e escopos;
- `skill-seguranca.md` para política geral de segurança;
- `skill-logs-auditoria.md` para auditoria completa dos eventos de acesso;
- `skill-lgpd-privacidade.md` para tratamento de dados pessoais;
- `skill-backend.md` para regra de negócio geral do servidor.

Esta skill responde "quem é o usuário e a sessão dele está segura?".

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

Ao usar esta skill, aja como uma pessoa desenvolvedora backend sênior com foco em autenticação segura, sessões PHP, permissões, proteção de rotas e experiência de login.

A IA deve pensar em:

- identidade do usuário;
- senha protegida;
- sessão segura;
- expiração;
- logout real;
- bloqueio por tentativa;
- proteção de páginas privadas;
- proteção de endpoints;
- auditoria de acesso;
- mensagens claras sem revelar informações sensíveis.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-seguranca.md
skill-permissoes.md
skill-backend.md
skill-api-rest.md
skill-logs-auditoria.md
skill-lgpd-privacidade.md
```

---

## Princípio central

```txt
Autenticação prova quem é o usuário. Autorização define o que ele pode fazer. Sessão mantém esse acesso de forma segura e controlada.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Separar autenticação de autorização

A IA deve diferenciar claramente:

```txt
Autenticação = confirmar quem é o usuário.
Autorização = confirmar se esse usuário pode acessar determinada ação ou dado.
Sessão = manter o usuário autenticado entre requisições.
```

Exemplo:

- usuário fez login corretamente: autenticado;
- usuário é administrador: autorizado para painel admin;
- usuário comum tentando acessar tela admin: autenticado, mas não autorizado.

Nunca tratar login válido como permissão total.

---

# 2. Cadastro de usuário

O cadastro deve validar dados no backend e criar usuário em estado seguro.

Verificar:

- nome obrigatório;
- e-mail válido;
- e-mail único;
- senha com política mínima;
- confirmação de senha quando houver tela;
- aceite de termos quando aplicável;
- perfil inicial seguro, normalmente usuário comum;
- status inicial: ativo, pendente ou aguardando validação conforme regra do projeto.

A senha nunca deve ser salva em texto puro. Usar `password_hash()` no PHP.

---

# 3. Login seguro

O login deve validar credenciais sem revelar se o e-mail existe.

Mensagem recomendada:

```txt
E-mail ou senha inválidos.
```

Evitar:

```txt
Este e-mail não existe.
Senha incorreta para este e-mail.
```

Boas práticas:

- buscar usuário pelo e-mail normalizado;
- verificar senha com `password_verify()`;
- bloquear usuário inativo;
- registrar tentativas falhas;
- aplicar atraso ou bloqueio após muitas tentativas;
- regenerar ID da sessão após login;
- carregar permissões a partir do banco, não do frontend.

---

# 4. Sessão PHP

A sessão deve ser protegida desde o início.

Regras recomendadas:

- usar cookie `HttpOnly`;
- usar `Secure` quando estiver em HTTPS;
- usar `SameSite=Lax` ou `Strict` conforme fluxo;
- regenerar ID da sessão no login;
- não armazenar senha, token externo ou dados sensíveis desnecessários na sessão;
- armazenar apenas identificadores e contexto mínimo;
- validar usuário ativo a cada requisição crítica;
- expirar sessão por tempo de inatividade.

Exemplo de dados mínimos:

```txt
user_id
user_name
profile_id
session_started_at
last_activity_at
```

Permissões detalhadas podem ser consultadas no backend ou cacheadas com cuidado.

---

# 5. Expiração e renovação de sessão

Toda sessão deve ter regra de expiração.

Definir:

- tempo máximo absoluto da sessão;
- tempo máximo de inatividade;
- comportamento quando expirar;
- mensagem para usuário;
- redirecionamento para login;
- limpeza de dados no frontend.

Exemplo:

```txt
Inatividade: 30 minutos
Tempo máximo: 8 horas
Ao expirar: destruir sessão e redirecionar para login
```

Para sistemas operacionais críticos, a expiração deve ser mais conservadora.

---

# 6. Logout seguro

Logout deve encerrar de verdade a sessão.

Regras:

- limpar variáveis de sessão;
- destruir sessão no servidor;
- invalidar cookie de sessão;
- remover dados sensíveis do localStorage/sessionStorage quando usados;
- redirecionar para login;
- registrar evento de logout quando útil.

Não basta esconder o menu ou redirecionar sem destruir sessão.

---

# 7. Recuperação de senha

Fluxo recomendado:

1. Usuário informa e-mail.
2. Sistema responde mensagem genérica.
3. Se o e-mail existir, gera token seguro com expiração.
4. Envia link por e-mail.
5. Usuário acessa link e define nova senha.
6. Sistema invalida o token após uso.
7. Sistema registra evento de troca de senha.

Mensagem recomendada:

```txt
Se o e-mail estiver cadastrado, enviaremos instruções de recuperação.
```

Token deve ser:

- aleatório;
- longo;
- salvo com hash quando possível;
- de uso único;
- com validade curta.

---

# 8. Proteção de páginas privadas

Toda página privada deve validar sessão no backend antes de renderizar conteúdo.

Regras:

- não depender só de JavaScript para bloquear acesso;
- verificar usuário autenticado;
- verificar perfil/permissão;
- redirecionar não autenticado para login;
- exibir erro 403 ou tela adequada para sem permissão;
- não carregar dados privados no HTML se a permissão falhou.

Cada tela deve declarar sua permissão mínima.

---

# 9. Proteção de endpoints

Toda API privada deve validar autenticação e autorização.

Verificar:

- usuário logado;
- sessão válida;
- CSRF em métodos de escrita;
- permissão da ação;
- propriedade do registro, quando aplicável;
- status do usuário;
- status da conta/empresa.

Exemplo:

```txt
Usuário pode editar demanda somente se:
- estiver autenticado;
- tiver permissão demandas.editar;
- pertencer à mesma empresa da demanda;
- demanda não estiver bloqueada/finalizada.
```

---

# 10. Auditoria de acesso

Registrar eventos importantes:

- login com sucesso;
- login falho;
- logout;
- troca de senha;
- recuperação de senha solicitada;
- bloqueio por tentativa;
- acesso negado;
- ativação/desativação de usuário;
- alteração de permissão.

Logs devem conter:

- usuário, quando conhecido;
- data e hora;
- IP;
- user agent quando útil;
- ação;
- resultado;
- motivo resumido.

Não registrar senha, token completo ou dados sensíveis desnecessários.

---

# Checklist obrigatório antes de concluir

- [ ] Senhas usam hash seguro.
- [ ] Login não revela se o e-mail existe.
- [ ] Sessão é regenerada após login.
- [ ] Cookies de sessão estão protegidos.
- [ ] Páginas privadas validam sessão no backend.
- [ ] APIs privadas validam autenticação e permissão.
- [ ] Logout destrói sessão de verdade.
- [ ] Recuperação de senha usa token seguro e expirável.
- [ ] Eventos críticos são registrados em auditoria.

---

# Modelo de entrega esperado

Ao criar autenticação, entregue:

1. Fluxo de login.
2. Fluxo de logout.
3. Regra de sessão e expiração.
4. Fluxo de recuperação de senha.
5. Proteção de páginas privadas.
6. Proteção de endpoints.
7. Tabela de permissões envolvidas.
8. Logs de auditoria necessários.
9. Mensagens para usuário final.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
