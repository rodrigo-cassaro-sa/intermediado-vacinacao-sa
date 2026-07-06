# Skill: LGPD, Privacidade e Proteção de Dados Pessoais

## Objetivo da skill

Esta skill orienta uma IA a considerar **privacidade, proteção de dados pessoais, minimização de coleta, consentimento, acesso, exclusão, retenção e segurança da informação** em projetos web, SaaS e apps no contexto brasileiro.

O foco é criar sistemas mais responsáveis no tratamento de dados. Esta skill não substitui assessoria jurídica; ela serve como guia técnico e de produto para reduzir riscos e melhorar governança de dados.

---

## Limite desta skill

Esta skill define regras técnicas e de produto para privacidade, minimização, finalidade, retenção, consentimento, acesso, exportação, exclusão e proteção de dados pessoais.

Ela não substitui assessoria jurídica.

Ela pode orientar banco, logs, permissões, backup e telas quando houver dados pessoais, mas não deve substituir:

- `skill-seguranca.md` para proteção técnica geral;
- `skill-perfi-permissoes.md` para matriz completa de autorização;
- `skill-dados.md` para modelagem completa das entidades;
- `skill-logs-auditoria.md` para trilha completa de auditoria;
- `skill-backup-recuperacao.md` para política completa de backup;
- `skill-admin-operacional.md` para gestão operacional do sistema.

Esta skill responde "estamos coletando, usando, exibindo, exportando e retendo dados pessoais do jeito mais necessário, seguro e responsável possível?".

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

Ao usar esta skill, aja como uma pessoa sênior de produto, segurança, arquitetura e governança de dados, com atenção à LGPD e à privacidade desde a concepção.

A IA deve pensar em:

- quais dados são coletados;
- por que são coletados;
- quem acessa;
- onde ficam armazenados;
- por quanto tempo ficam;
- como excluir ou anonimizar;
- como exportar;
- como proteger;
- como registrar consentimento quando necessário.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-seguranca.md
skill-permissoes.md
skill-dados.md
skill-logs-auditoria.md
skill-backup-recuperacao.md
skill-admin-operacional.md
```

---

## Princípio central

```txt
Privacidade boa começa coletando apenas o dado necessário, protegendo bem o que foi coletado e explicando ao usuário como esse dado é usado.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Mapeamento de dados

Antes de criar telas e tabelas, a IA deve mapear dados pessoais.

Para cada dado, responder:

- qual campo será coletado?
- é dado pessoal?
- é dado sensível?
- qual finalidade?
- é obrigatório ou opcional?
- quem pode ver?
- onde será salvo?
- por quanto tempo?
- pode ser excluído?
- aparece em logs, relatórios ou exportações?

Esse mapa deve acompanhar o briefing e a modelagem de banco.

---

# 2. Minimização de dados

Coletar somente o necessário.

Evitar:

- pedir CPF sem necessidade real;
- pedir data de nascimento se idade aproximada bastaria;
- pedir endereço completo quando só cidade é suficiente;
- salvar documentos sem finalidade clara;
- guardar dados indefinidamente.

Pergunta obrigatória:

> O sistema consegue funcionar sem este dado?

Se sim, tornar opcional ou remover.

---

# 3. Dados sensíveis

Dados sensíveis exigem cuidado maior.

Exemplos comuns:

- dados de saúde;
- biometria;
- informações sobre menores;
- origem racial ou étnica;
- religião;
- opiniões políticas;
- vida sexual;
- dados genéticos.

Boas práticas:

- coletar apenas quando indispensável;
- restringir acesso;
- registrar finalidade;
- proteger com controles fortes;
- evitar exposição em logs e relatórios;
- avaliar necessidade de consentimento ou base legal adequada com responsável jurídico.

---

# 4. Consentimento e transparência

Quando houver consentimento, ele deve ser claro e registrável.

Registrar:

- quem consentiu;
- data e hora;
- texto aceito;
- versão do termo;
- finalidade;
- IP quando apropriado;
- forma de revogação.

Evitar checkbox pré-marcado ou texto confuso.

Consentimento não deve ser usado como solução genérica para tudo. A base legal deve ser avaliada conforme contexto do projeto.

---

# 5. Política de privacidade e termos

O sistema deve prever links para documentos importantes quando aplicável.

Conteúdos mínimos:

- quais dados são coletados;
- finalidade;
- compartilhamento;
- retenção;
- direitos do titular;
- contato para solicitações;
- uso de cookies;
- medidas de segurança;
- atualizações da política.

A IA pode criar estrutura técnica e perguntas, mas texto jurídico final deve ser validado por responsável qualificado.

---

# 6. Controle de acesso

Privacidade depende de permissão correta.

Regras:

- usuário vê somente dados necessários para sua função;
- admin não deve ter acesso irrestrito sem motivo;
- suporte deve ter acesso limitado e auditado;
- exportações exigem permissão específica;
- logs de acesso a dados sensíveis devem ser registrados;
- telas devem ocultar campos sensíveis quando não necessários.

---

# 7. Retenção e exclusão

Dados não devem ficar para sempre sem necessidade.

Definir:

- prazo de retenção por tipo de dado;
- regra de exclusão;
- regra de anonimização;
- dados que precisam ficar por obrigação legal/operacional;
- impacto em histórico e relatórios;
- quem pode solicitar exclusão;
- como registrar a solicitação.

Exclusão deve considerar backups e logs conforme política definida.

---

# 8. Exportação de dados

Quando o sistema precisar permitir exportação, controlar bem.

Boas práticas:

- permissão específica;
- filtro por escopo;
- registro de auditoria;
- mascaramento quando possível;
- limite de volume;
- confirmação antes de exportar;
- arquivo com expiração quando gerado;
- não enviar dados sensíveis sem proteção.

Toda exportação é uma saída de dados e precisa ser tratada como risco.

---

# 9. Logs e privacidade

Logs não devem virar vazamento.

Evitar registrar:

- senha;
- token;
- documentos completos sem necessidade;
- dados de saúde;
- payload completo sensível;
- cartão ou dados financeiros críticos;
- conteúdo privado de mensagens.

Usar mascaramento:

```txt
CPF: ***.***.***-12
E-mail: r***@dominio.com
Token: abc123...mascarado
```

---

# 10. Cookies e armazenamento no navegador

Dados no navegador exigem cuidado.

Regras:

- não salvar dados sensíveis em localStorage;
- preferir cookie seguro para sessão;
- usar `HttpOnly`, `Secure` e `SameSite` quando aplicável;
- limpar dados no logout;
- explicar cookies não essenciais quando necessário;
- evitar rastreamento sem finalidade clara.

---

# 11. Privacidade por padrão

A configuração inicial deve proteger o usuário.

Exemplos:

- perfil privado por padrão quando aplicável;
- notificações configuráveis;
- menor permissão inicial;
- campos sensíveis ocultos;
- relatórios agregados quando possível;
- dados anonimizados em homologação.

A IA deve evitar soluções que exponham dados por conveniência.

---

# Checklist obrigatório antes de concluir

- [ ] Dados pessoais foram mapeados.
- [ ] Cada dado tem finalidade clara.
- [ ] Coleta desnecessária foi removida.
- [ ] Dados sensíveis têm proteção reforçada.
- [ ] Acesso é limitado por perfil/permissão.
- [ ] Logs não expõem dados sensíveis.
- [ ] Existe política de retenção/exclusão.
- [ ] Exportações têm permissão e auditoria.
- [ ] Homologação não usa dados reais sem mascaramento.
- [ ] Termos/política foram considerados.

---

# Modelo de entrega esperado

Ao aplicar esta skill, entregue:

1. Mapa de dados pessoais.
2. Finalidade de cada dado.
3. Classificação: comum, sensível ou técnico.
4. Permissões de acesso.
5. Retenção/exclusão.
6. Riscos de privacidade.
7. Ajustes técnicos recomendados.
8. Pontos que exigem validação jurídica.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
