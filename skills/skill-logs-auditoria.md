# Skill: Logs, Auditoria e Rastreabilidade

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e padronizar **logs técnicos, logs de negócio, auditoria de ações, histórico de alteração e rastreabilidade operacional** em sistemas web, SaaS e apps.

O foco é permitir que suporte, administrador, QA, desenvolvedor e gestor entendam o que aconteceu no sistema, quem fez, quando fez, de onde fez e qual foi o impacto, sem expor dados sensíveis.

---

## Limite desta skill

Esta skill define logs técnicos, logs de negócio, auditoria, histórico de alterações e rastreabilidade operacional.

Ela pode orientar segurança, backend, API, debug e LGPD quando o assunto for registrar eventos, mas não deve substituir:

- `skill-seguranca.md` para política de proteção;
- `skill-debug.md` para visualização administrativa em tela;
- `skill-lgpd-privacidade.md` para regra completa de privacidade;
- `skill-monitoramento-observabilidade.md` para métricas, alertas e saúde de produção;
- `skill-erros-excecoes.md` para padrão de mensagens de erro.

Esta skill responde "o sistema consegue explicar o que aconteceu, quem fez, quando fez e qual foi o impacto?".

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

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior com visão de observabilidade, auditoria, suporte técnico, segurança e operação de SaaS.

A IA deve pensar em:

- erros técnicos;
- ações de usuário;
- alterações em registros;
- acessos negados;
- integrações externas;
- eventos críticos de segurança;
- trilha de auditoria;
- consulta por administrador;
- privacidade e LGPD.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-debug.md
skill-seguranca.md
skill-backend.md
skill-api-rest.md
skill-admin-operacional.md
skill-lgpd-privacidade.md
```

---

## Princípio central

```txt
Sistema profissional não apenas executa ações: ele consegue explicar o que aconteceu, por que aconteceu e quem foi responsável.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Diferença entre log e auditoria

A IA deve separar conceitos.

```txt
Log técnico = ajuda desenvolvedor a diagnosticar erro.
Auditoria = registra ação relevante para negócio, segurança ou responsabilidade.
Histórico = mostra mudanças de um registro ao longo do tempo.
```

Exemplos:

- erro de conexão com banco: log técnico;
- usuário alterou status de uma demanda: auditoria;
- campo `valor` mudou de 100 para 120: histórico de alteração.

---

# 2. Eventos que devem ser registrados

Registrar pelo menos:

- login com sucesso;
- login falho;
- logout;
- recuperação de senha;
- criação, edição, exclusão e inativação de dados críticos;
- alteração de status;
- alteração de permissão;
- acesso negado;
- erro de validação relevante;
- falha de API externa;
- webhook recebido;
- webhook enviado;
- envio de e-mail, WhatsApp, SMS ou push;
- exportação de dados;
- importação de dados;
- ativação de debug visual;
- deploy ou migração de banco quando controlado pelo sistema.

---

# 3. Estrutura mínima de log

Um log útil deve conter contexto suficiente.

Campos recomendados:

```txt
id
criado_em
nivel
categoria
evento
mensagem
usuario_id
sessao_id
ip
user_agent
request_id
metodo
url
entidade_tipo
entidade_id
valores_anteriores
valores_novos
metadados
```

Nem todo evento precisa preencher todos os campos. A IA deve registrar o essencial para investigar sem excesso.

---

## Regra de nomenclatura

Por padrão, este projeto usa nomes de campos em português para tabelas internas.

Exemplos:

- `criado_em`
- `usuario_id`
- `entidade_tipo`
- `entidade_id`
- `valores_anteriores`
- `valores_novos`
- `metadados`

Não misturar `created_at`, `old_values` e `new_values` com `criado_em`, `valores_anteriores` e `valores_novos` no mesmo projeto.

Se o projeto existente já usa padrão em inglês, manter o padrão existente para evitar inconsistência.

---

# 4. Níveis de log

Usar níveis padronizados.

| Nível | Quando usar |
|---|---|
| `debug` | detalhes técnicos em desenvolvimento/homologação |
| `info` | evento normal relevante |
| `warning` | situação anormal, mas recuperável |
| `error` | erro que impediu uma ação |
| `critical` | falha grave de segurança, dados ou disponibilidade |

Em produção, evitar excesso de `debug` e nunca expor dados sensíveis.

---

# 5. Request ID

Cada requisição importante deve ter um identificador.

Objetivo:

- conectar frontend, backend e banco;
- facilitar suporte;
- rastrear erro em logs diferentes;
- copiar relatório técnico no debug visual.

Padrão:

```txt
request_id = string única por requisição
```

A resposta da API pode retornar `request_id` em erros para facilitar atendimento, sem expor detalhe técnico.

---

# 6. Auditoria de alterações

Para dados críticos, registrar valor antigo e novo.

Exemplo:

```json
{
  "entity_type": "demanda",
  "entity_id": 45,
  "action": "status_alterado",
  "old_values": {"status": "pendente"},
  "new_values": {"status": "concluida"}
}
```

Boas práticas:

- registrar somente campos alterados;
- mascarar dados sensíveis;
- manter usuário responsável;
- registrar data e hora;
- registrar origem da alteração: usuário, automação, webhook ou rotina.

---

# 7. Logs de erro

Erro técnico deve ir para log, não para a tela do usuário.

Usuário vê:

```txt
Não foi possível concluir a ação agora. Tente novamente ou contate o suporte.
```

Log técnico registra:

- arquivo;
- linha;
- função;
- mensagem técnica;
- payload seguro;
- usuário;
- request_id;
- stack quando permitido internamente.

Nunca exibir SQL, senha, token, caminho interno ou stack trace em produção.

---

# 8. Logs de integração

Integrações devem ser rastreáveis.

Registrar:

- sistema externo;
- endpoint chamado;
- método;
- status HTTP;
- tempo de resposta;
- tentativa atual;
- payload enviado mascarado;
- resposta recebida mascarada;
- erro;
- próximo retry quando houver;
- idempotency key quando houver.

Isso evita perda de contexto em falhas intermitentes.

---

# 9. Painel administrativo de logs

Quando o sistema tiver painel admin, criar consulta de logs com:

- filtro por período;
- filtro por usuário;
- filtro por entidade;
- filtro por nível;
- filtro por categoria;
- busca por request_id;
- visualização de detalhes;
- exportação controlada;
- permissão restrita.

O painel nunca deve mostrar dados sensíveis para perfil sem autorização.

---

# 10. Retenção e limpeza

Logs não devem crescer sem controle.

Definir:

- tempo de retenção por tipo;
- política de arquivamento;
- limpeza automática;
- backup quando necessário;
- anonimização quando aplicável;
- proteção contra alteração manual.

Logs de auditoria podem precisar ficar mais tempo que logs técnicos. A regra deve estar documentada.

---

# Checklist obrigatório antes de concluir

- [ ] Eventos críticos têm log ou auditoria.
- [ ] Logs não salvam senha, token ou segredo.
- [ ] Erros técnicos não aparecem ao usuário.
- [ ] Alterações críticas registram antes e depois.
- [ ] Existe request_id para rastreabilidade.
- [ ] Logs de integração registram tentativa e resposta.
- [ ] Painel de logs exige permissão.
- [ ] Existe política de retenção e limpeza.

---

# Modelo de entrega esperado

Ao criar logs e auditoria, entregue:

1. Lista de eventos registrados.
2. Estrutura das tabelas ou arquivos de log.
3. Níveis de log utilizados.
4. Campos sensíveis que devem ser mascarados.
5. Painel/admin necessário.
6. Política de retenção.
7. Exemplos de log para erro, ação de usuário e integração.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
