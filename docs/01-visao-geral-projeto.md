# Visão Geral do Projeto

## Identificação

```txt
Nome do projeto: Plataforma de Gestão de Imunização Corporativa (nome provisório)
Cliente/empresa: [empresa prestadora de imunização corporativa - a preencher]
Responsável técnico: [a preencher]
Responsável de negócio: [a preencher]
Data de início: 2026-07-06
Última atualização: 2026-07-06
Status geral: planejamento
```

---

## Objetivo

```txt
Plataforma intermediadora (hub central) que orquestra campanhas de imunização
corporativa entre a empresa prestadora (nós), as empresas contratantes (clientes B2B),
os pacientes (B2C), as clínicas da rede credenciada e os profissionais de saúde in company.

O sistema resolve o problema de: receber com segurança as listas de colaboradores
elegíveis das empresas contratantes, executar a vacinação por duas modalidades
(rede credenciada e in company), registrar cada aplicação de forma rastreável e
devolver ao cliente os dados com qualidade analítica (dashboards + tabela verdade
da campanha), mantendo tudo centralizado, transparente e em conformidade com a LGPD.
```

---

## Público/usuários

| Perfil | Descrição | Objetivo no sistema |
|---|---|---|
| Operador interno / Admin (nós) | Equipe da prestadora que gere campanhas, clientes, rede e insumos | Criar/gerir campanhas, cadastrar clientes e rede, acompanhar execução e analytics |
| Cliente B2B (RH da empresa contratante) | Empresa que contrata a imunização para seus colaboradores | Enviar lista de elegíveis (interface ou API), acompanhar dashboards e extrair resultados |
| Paciente B2C (colaborador) | Pessoa elegível/vacinada | Criar conta, se auto-elegir, consultar carteira de vacinas tomadas |
| Rede credenciada (clínica conveniada) | Sistema externo de clínica parceira | Via API: consultar elegíveis e registrar vacinados |
| Profissional de saúde in company (nosso) | Profissional enviado ao local de trabalho | Via app/PWA: consultar elegíveis e cadastrar vacinados no local |

---

## Stack oficial

```txt
PHP procedural puro
MySQL/MariaDB
phpMyAdmin
HTML semântico
CSS organizado
JavaScript puro
Fetch API
JSON
Git/GitHub
Docker/Docker Compose quando aplicável
EasyPanel
Apache ou Nginx
Domínio com SSL
Sem framework por padrão
Sem orientação a objetos por padrão
```

---

## Restrições do projeto

- **Dados de saúde são dados sensíveis (LGPD):** exigem base legal, consentimento do paciente, minimização, criptografia em trânsito/repouso e auditoria de acesso.
- Toda regra crítica (elegibilidade, registro de aplicação, permissão, status de campanha) é **validada no backend**; o frontend apenas orienta a experiência.
- Modelo **multiempresa (multi-tenant)**: dados de um cliente B2B nunca podem vazar para outro.
- A plataforma é sempre o **intermediador/fonte da verdade**; os sistemas externos (rede e app profissional) apenas consomem e alimentam via API.
- Sem framework e sem OO sem autorização explícita.

---

## Sistemas externos

| Sistema | Função | Tipo de integração | Observação |
|---|---|---|---|
| Sistema do cliente B2B | Enviar lista de elegíveis / receber resultados | API REST (ingestão) + interface web (upload manual) | Alternativa manual pela interface para clientes sem TI |
| Rede credenciada (clínicas) | Consultar elegíveis e registrar vacinados | API REST (nós expomos) | Autenticação por credencial de parceiro; escopo restrito à campanha |
| App/PWA profissional in company | Consultar elegíveis e cadastrar vacinados no local | API REST (nós expomos) + PWA offline-first | Uso em campo, possivelmente sem internet estável |
| Webhooks/notificações | Avisar eventos (vacinado registrado, campanha concluída) | Webhook de saída + e-mail/WhatsApp (fase futura) | Origem de evento obrigatória (ver regras globais) |

---

## Ambientes

| Ambiente | URL | Banco | Status |
|---|---|---|---|
| desenvolvimento | [a definir] | [a definir] | pendente |
| homologação | [a definir] | [a definir] | pendente |
| produção | [a definir] | [a definir] | pendente |

---

## Regra final

Este documento é o resumo oficial do projeto. Itens marcados "[a preencher]" / "[a definir]" e as premissas do briefing devem ser confirmados antes do fim da Fase 1.
