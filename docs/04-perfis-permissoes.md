# Perfis e Permissões

## Objetivo

Definir quem pode acessar cada área e executar cada ação.

---

# 1. Perfis

| Perfil | Descrição | Observação |
|---|---|---|
| super_admin (interno) | Equipe da prestadora; gere tudo na plataforma | Acesso global; ações críticas auditadas |
| operador_interno | Equipe operacional da prestadora | Gere campanhas, elegíveis, execução; sem config de sistema |
| cliente_b2b | RH/gestor da empresa contratante | Vê **apenas** suas campanhas; envia elegíveis; consulta dashboards/extração |
| profissional_saude | Profissional in company (app/PWA) | Consulta elegíveis e registra aplicação **na campanha atribuída** |
| clinica_credenciada | Sistema externo da rede (via API) | Consulta elegíveis e registra vacinado **por API**, escopo restrito à campanha |
| paciente_b2c | Colaborador/pessoa | Vê **apenas os próprios** dados; auto-elegibilidade e carteira (V2) |

> Multi-tenant: `cliente_b2b`, `profissional_saude` e `clinica_credenciada` são sempre
> vinculados a um tenant/campanha. `paciente_b2c` é vinculado ao próprio CPF (premissa RN-008).

---

# 2. Matriz de permissões

| Tela/Ação | super_admin | operador_interno | cliente_b2b | profissional_saude | clinica_credenciada | paciente_b2c |
|---|---|---|---|---|---|---|
| Configurar sistema/ambiente | sim | não | não | não | não | não |
| Cadastrar cliente B2B | sim | sim | não | não | não | não |
| Criar/editar campanha | sim | sim | não | não | não | não |
| Enviar/importar elegíveis | sim | sim | sim (da própria campanha) | não | não | não |
| Consultar elegíveis | sim | sim | sim (própria campanha) | sim (campanha atribuída) | sim (via API, escopo) | próprio (V2) |
| Registrar aplicação (vacinado) | sim | sim | não | sim (campanha atribuída) | sim (via API, escopo) | não |
| Retificar aplicação | sim | sim (auditado) | não | não | não | não |
| Ver tabela verdade | sim | sim | sim (própria campanha) | parcial (própria atuação) | não | não |
| Ver dashboard/extração | sim | sim | sim (própria campanha) | não | não | não |
| Ver carteira do paciente | sim (auditado) | parcial (auditado) | não | sim (paciente em atendimento) | via API (escopo) | próprio |
| Gerir credenciais de API (rede) | sim | sim | não | não | não | não |
| Ver logs de auditoria | sim | parcial | não | não | não | não |

> Célula "parcial"/"escopo" = acesso limitado por vínculo (campanha, tenant, paciente em atendimento) e sujeito a auditoria. Detalhamento fino na Fase de segurança (doc 10).

---

# 3. Regras obrigatórias

- Permissão visual no frontend **não é** segurança real.
- Toda permissão crítica deve ser validada **no backend** (RN-006, RN-007, RN-009).
- Ações administrativas e acesso a dado de saúde devem ser registrados em **log/auditoria** (RN-004, RN-011).
- Usuário/sistema não pode acessar dados de outro tenant/campanha sem autorização (isolamento multi-tenant).
- `clinica_credenciada` e `profissional_saude` operam sempre com **escopo restrito à campanha** autorizada.
- Paciente B2C só acessa os **próprios** dados.

---

# 4. Endpoints protegidos

> Contrato detalhado na Fase de API (doc 09). Lista inicial esperada:

| Endpoint | Permissão necessária | Log? |
|---|---|---|
| POST /elegiveis/importar | cliente_b2b (própria campanha) / operador_interno | sim |
| GET /campanhas/{id}/elegiveis | escopo da campanha (cliente_b2b, profissional_saude, clinica_credenciada, operador) | sim |
| POST /aplicacoes | profissional_saude / clinica_credenciada / operador (campanha autorizada) | sim (crítico) |
| POST /aplicacoes/{id}/retificar | operador_interno / super_admin | sim (crítico) |
| GET /campanhas/{id}/tabela-verdade | cliente_b2b (própria) / operador | sim |
| GET /campanhas/{id}/dashboard | cliente_b2b (própria) / operador | sim |
| GET /pacientes/me/carteira | paciente_b2c (próprio) | sim |

---

# 5. Checklist

```md
- [ ] Perfis definidos.
- [ ] Permissões por tela definidas.
- [ ] Permissões por ação definidas.
- [ ] Backend valida permissão.
- [ ] Frontend apenas orienta visualmente.
- [ ] Logs de ações críticas definidos.
```

> Status: rascunho para aprovação de negócio/segurança. Depende das dúvidas abertas do doc 02
> (chave de identidade do paciente e fluxo de auto-elegibilidade).
