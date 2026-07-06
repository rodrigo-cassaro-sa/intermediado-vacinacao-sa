# Skill: Vendas, Pedidos, Checkout e Pagamentos para SaaS, App, E-commerce e Software

## Objetivo da skill

Esta skill orienta uma IA a atuar como uma pessoa especialista em **produto, backend, financeiro, comercial, regras de negócio, checkout, pedidos, pagamentos e operação de vendas**.

O foco é criar, revisar e evoluir sistemas que vendem qualquer tipo de oferta:

- produtos físicos;
- produtos digitais;
- vouchers;
- ingressos;
- eventos;
- pacotes;
- créditos;
- planos;
- assinaturas;
- serviços;
- aulas;
- consultorias;
- orçamentos;
- propostas comerciais;
- vendas B2B;
- vendas B2C;
- vendas presenciais;
- vendas online;
- pré-vendas;
- reservas;
- marketplaces;
- comissões;
- repasses;
- pagamento único;
- pagamento recorrente;
- pagamento parcial.

A skill deve garantir fluxos de venda seguros, rastreáveis, auditáveis, claros para o usuário e preparados para operação real.

Stack padrão:

```txt
PHP procedural puro
MySQL ou MariaDB
HTML semântico
CSS organizado
JavaScript puro
Fetch API
APIs JSON
Integração com gateway de pagamento
Servidor Linux com Apache ou Nginx
```

Esta skill deve ser usada junto com:

```txt
skill-seguranca.md
skill-api-rest.md
skill-backend.md
skill-autenticacao-sessao.md
skill-permissoes.md
skill-logs-auditoria.md
skill-lgpd-privacidade.md
skill-erros-excecoes.md
skill-qa.md
skill-deploy-ci-cd.md
skill-documentacao-projeto.md
skill-relatorios-bi-dashboard.md
skill-integracoes-webhooks.md
skill-notificacoes.md
```

---

## Limite desta skill

Esta skill define fluxos comerciais, pedidos, checkout, cobranças, pagamentos, vouchers, assinaturas, créditos, entregas, reembolsos, conciliação e operação financeira.

Ela pode citar relatórios, suporte, notificações, multitenant, integrações e admin quando isso afetar venda ou pagamento, mas não deve substituir:

- `skill-relatorios-bi-dashboard.md` para dashboards e indicadores;
- `skill-suporte-atendimento-sla.md` para chamados, SLA e atendimento;
- `skill-multitenant-workspaces.md` para isolamento por cliente/tenant;
- `skill-admin-operacional.md` para operação geral do painel administrativo;
- `skill-integracoes-webhooks.md` para webhooks externos em profundidade;
- `skill-notificacoes.md` para estratégia completa de comunicação.

Esta skill responde "como o sistema vende, cobra, confirma, entrega, cancela, reembolsa e concilia?".

---

## Regra de nomenclatura de campos

Por padrão, este projeto usa nomes de campos em português para datas, auditoria e histórico.

### Campos padrão

```txt
criado_em
atualizado_em
excluido_em
cancelado_em
confirmado_em
pago_em
expira_em
disponivel_em
emitido_em
processado_em
usado_em
aceito_em
primeira_resposta_em
resolvido_em
fechado_em
sla_primeira_resposta_em
sla_resolucao_em
criado_por
atualizado_por
excluido_por
```

### Regra obrigatória

Não misturar nomes de campos em inglês com nomes em português no mesmo projeto.

Quando o projeto usar português, todos os exemplos de tabelas, campos, históricos, filtros, relatórios e payloads internos devem seguir o padrão em português.

Exceção: termos técnicos de SaaS como `tenant_id`, `workspace_id`, `request_id`, `trace_id`, `uuid_publico`, `metadata`, `related_type` e `related_id` podem ser mantidos quando esse for o padrão técnico escolhido no projeto.

---

## Papel da IA

Ao usar esta skill, a IA deve agir como uma pessoa sênior em arquitetura de sistemas de venda, pedido, pagamento e operação comercial.

A IA deve pensar em:

- catálogo;
- produto ou serviço;
- preço;
- desconto;
- cupom;
- carrinho;
- checkout;
- pedido;
- cobrança;
- pagamento;
- confirmação;
- entrega;
- liberação de acesso;
- emissão de voucher;
- emissão de ingresso;
- agendamento;
- estoque;
- reserva;
- cancelamento;
- devolução;
- reembolso;
- chargeback;
- assinatura;
- recorrência;
- inadimplência;
- nota fiscal;
- comissão;
- repasse;
- conciliação;
- auditoria;
- suporte;
- segurança;
- LGPD.

A IA não deve tratar venda como apenas um botão de compra. Venda é um fluxo completo que envolve intenção comercial, dados do cliente, regras do produto, cálculo de valores, pagamento, confirmação, entrega, suporte e histórico.

---

## Princípio central

```txt
Nunca considerar uma venda como concluída apenas porque o frontend informou sucesso.
```

A confirmação real deve vir do backend.

Quando houver gateway de pagamento, a confirmação deve vir preferencialmente por:

- webhook validado;
- assinatura do gateway;
- consulta segura ao provedor;
- conciliação posterior.

O frontend pode mostrar tela de sucesso, aguardando pagamento ou pagamento iniciado, mas a liberação de produto, serviço, voucher, ingresso, crédito, plano ou acesso deve depender de regra validada no backend.

---

## Conceitos fundamentais

```txt
Oferta = aquilo que pode ser comprado.
Produto = item físico ou digital.
Serviço = algo executado por uma pessoa, agenda ou equipe.
Plano = pacote de benefícios recorrentes ou não.
Assinatura = vínculo recorrente entre cliente e plano.
Pedido = intenção de compra registrada.
Item do pedido = cada produto, serviço, voucher, plano ou crédito vendido.
Cobrança = tentativa ou obrigação de pagamento.
Pagamento = confirmação financeira.
Entrega = disponibilização do que foi comprado.
Voucher = direito de uso representado por código.
Ingresso = direito de entrada em evento.
Crédito = saldo que pode ser consumido depois.
Reembolso = devolução total ou parcial do valor.
Conciliação = conferência entre sistema interno e provedor financeiro.
```

A IA deve separar esses conceitos para evitar sistemas confusos.

---

## Quando usar esta skill

Use esta skill quando o projeto envolver:

- venda online;
- venda presencial;
- PDV;
- checkout;
- carrinho;
- pedido;
- orçamento;
- proposta;
- voucher;
- ingresso;
- evento;
- aula;
- curso;
- pacote de serviço;
- crédito pré-pago;
- assinatura;
- plano recorrente;
- mensalidade;
- upgrade;
- downgrade;
- pagamento por Pix;
- pagamento por cartão;
- pagamento por boleto;
- pagamento manual;
- pagamento em dinheiro;
- pagamento por link;
- split de pagamento;
- comissão;
- repasse;
- cupom;
- desconto;
- entrega;
- liberação de acesso;
- cancelamento;
- reembolso;
- painel financeiro;
- conciliação.

---

## Quando não usar esta skill

Não use esta skill para:

- telas sem cobrança;
- cadastro simples sem transação comercial;
- relatórios que não envolvem venda, pedido ou pagamento;
- notificações sem relação com compra, cobrança, entrega ou suporte financeiro.

Nesses casos, use as skills específicas de frontend, backend, dados, relatórios, notificações ou API.

---

# 1. Tipos de venda suportados

## 1.1 Venda de produto físico

Exemplos:

- camiseta;
- livro;
- equipamento;
- alimento;
- produto de loja;
- material didático;
- mercadoria com entrega.

Precisa considerar:

- estoque;
- variação;
- frete;
- endereço;
- separação;
- envio;
- retirada;
- entrega;
- troca;
- devolução;
- nota fiscal;
- prazo.

Fluxo recomendado:

```txt
Produto → Carrinho → Identificação → Endereço → Frete → Pagamento → Separação → Envio/Retirada → Entrega → Pós-venda
```

---

## 1.2 Venda de produto digital

Exemplos:

- PDF;
- curso gravado;
- arquivo;
- licença;
- template;
- acesso a conteúdo;
- software.

Precisa considerar:

- liberação automática;
- controle de acesso;
- download seguro;
- limite de download, se houver;
- expiração;
- licença;
- termos de uso;
- bloqueio em caso de reembolso.

Fluxo recomendado:

```txt
Produto → Pedido → Pagamento confirmado → Liberação digital → Acesso/Download → Suporte
```

---

## 1.3 Venda de serviço

Exemplos:

- aula particular;
- consultoria;
- atendimento;
- manutenção;
- mentoria;
- sessão;
- serviço técnico.

Precisa considerar:

- agenda;
- disponibilidade;
- profissional responsável;
- duração;
- local;
- remarcação;
- falta;
- cancelamento;
- confirmação;
- execução do serviço.

Fluxo recomendado:

```txt
Serviço → Escolha de data/horário → Pedido → Pagamento/Sinal → Confirmação → Execução → Finalização
```

---

## 1.4 Venda de voucher

Exemplos:

- vale-aula;
- vale-presente;
- voucher de evento;
- crédito promocional;
- pacote com código de uso.

Precisa considerar:

- código seguro;
- validade;
- uso único ou múltiplo;
- transferência;
- bloqueio;
- expiração;
- reemissão;
- consulta;
- baixa de uso.

Fluxo recomendado:

```txt
Oferta → Pedido → Pagamento confirmado → Geração do voucher → Envio → Uso → Baixa
```

---

## 1.5 Venda de ingresso

Exemplos:

- festa;
- baile;
- show;
- evento;
- workshop;
- palestra.

Precisa considerar:

- lote;
- capacidade;
- check-in;
- QR Code;
- lista de presença;
- meia entrada, se existir;
- transferência;
- cancelamento;
- reembolso;
- controle de duplicidade.

Fluxo recomendado:

```txt
Evento → Lote → Ingresso → Pedido → Pagamento → Emissão → Check-in → Encerramento
```

---

## 1.6 Venda de pacote

Exemplos:

- pacote de aulas;
- pacote de consultas;
- pacote de créditos;
- plano de sessões;
- combo de serviços.

Precisa considerar:

- quantidade comprada;
- quantidade consumida;
- saldo;
- validade;
- renovação;
- uso parcial;
- transferência;
- cancelamento;
- consumo por agendamento ou check-in.

Fluxo recomendado:

```txt
Pacote → Pedido → Pagamento → Crédito/Sessões liberadas → Consumo gradual → Expiração ou renovação
```

---

## 1.7 Venda por assinatura

Exemplos:

- plano mensal;
- SaaS;
- clube;
- área de membros;
- mensalidade;
- recorrência.

Precisa considerar:

- ciclo de cobrança;
- renovação;
- inadimplência;
- trial;
- upgrade;
- downgrade;
- cancelamento;
- pausa;
- limite de uso;
- bloqueio;
- reativação.

Fluxo recomendado:

```txt
Plano → Assinatura → Cobrança recorrente → Pagamento → Liberação de acesso → Renovação/Cancelamento
```

---

## 1.8 Venda por orçamento ou proposta

Exemplos:

- serviço personalizado;
- venda B2B;
- projeto sob medida;
- pacote negociado;
- venda consultiva.

Precisa considerar:

- proposta;
- validade;
- aceite;
- aprovação;
- negociação;
- desconto;
- contrato;
- faturamento;
- pagamento parcial;
- etapas de entrega.

Fluxo recomendado:

```txt
Lead → Orçamento → Proposta → Aceite → Pedido → Cobrança → Execução → Entrega
```

---

## 1.9 Venda B2B faturada

Exemplos:

- venda para empresa;
- pagamento por boleto;
- pagamento posterior;
- faturamento mensal;
- contrato corporativo.

Precisa considerar:

- CNPJ;
- dados fiscais;
- condição de pagamento;
- centro de custo;
- pedido de compra;
- nota fiscal;
- prazo;
- limite de crédito;
- aprovação interna.

Fluxo recomendado:

```txt
Cliente empresa → Proposta/Pedido → Aprovação → Faturamento → Nota/Cobrança → Pagamento → Conciliação
```

---

## 1.10 Venda presencial / PDV

Exemplos:

- balcão;
- escola;
- recepção;
- evento presencial;
- venda por atendente.

Precisa considerar:

- operador;
- caixa;
- forma de pagamento;
- dinheiro;
- maquininha;
- comprovante;
- abertura e fechamento de caixa;
- cancelamento;
- sangria;
- auditoria.

Fluxo recomendado:

```txt
Atendente → Produto/Serviço → Pagamento presencial → Comprovante → Entrega imediata ou agendamento
```

---

## 1.11 Pré-venda e reserva

Exemplos:

- lote antecipado;
- reserva de vaga;
- sinal;
- matrícula;
- encomenda;
- produto futuro.

Precisa considerar:

- data de liberação;
- quantidade reservada;
- pagamento total ou parcial;
- expiração da reserva;
- conversão em pedido final;
- cancelamento;
- fila de espera.

Fluxo recomendado:

```txt
Reserva → Pagamento/Sinal → Confirmação → Liberação futura → Consumo/Entrega
```

---

## 1.12 Marketplace, comissão e repasse

Exemplos:

- plataforma com vendedores;
- professores recebendo comissão;
- parceiros;
- afiliados;
- split de pagamento.

Precisa considerar:

- vendedor;
- comissão;
- taxa da plataforma;
- repasse;
- split;
- conciliação;
- estorno;
- responsabilidade fiscal;
- disputa;
- relatórios por parceiro.

Fluxo recomendado:

```txt
Pedido → Pagamento → Cálculo de comissão → Disponibilização de saldo → Repasse → Conciliação
```

---

# 2. Estrutura comercial

## 2.1 Catálogo de ofertas

Toda venda deve começar por uma oferta clara.

Campos recomendados:

```txt
id
uuid_publico
tipo_oferta
nome
descricao
status
preco_base
moeda
categoria_id
estoque_controlado
requer_agendamento
requer_entrega
requer_voucher
requer_acesso_digital
validade_dias
criado_em
atualizado_em
```

Tipos de oferta:

```txt
produto_fisico
produto_digital
servico
voucher
ingresso
pacote
credito
plano
assinatura
orcamento
proposta
evento
```

---

## 2.2 Preços

Preço deve ser rastreável.

Boas práticas:

- salvar preço no item do pedido no momento da compra;
- não depender apenas do preço atual do produto;
- permitir tabela de preço, se necessário;
- controlar desconto;
- controlar taxa;
- controlar frete;
- controlar juros;
- controlar parcelamento;
- controlar arredondamento;
- salvar moeda.

Campos úteis:

```txt
valor_unitario
quantidade
valor_bruto
valor_desconto
valor_taxas
valor_frete
valor_juros
valor_liquido
moeda
```

---

## 2.3 Descontos

Tipos:

```txt
cupom
desconto_manual
desconto_percentual
desconto_valor_fixo
desconto_por_plano
desconto_por_lote
desconto_por_parceiro
desconto_promocional
```

Regras obrigatórias:

- desconto não pode gerar valor negativo;
- desconto deve ter origem;
- desconto manual deve registrar usuário e motivo;
- cupom deve validar validade e limite;
- desconto precisa aparecer no pedido;
- alteração de desconto precisa gerar auditoria.

---

## 2.4 Taxas e custos

Sistemas de venda podem ter:

- taxa de gateway;
- taxa de plataforma;
- taxa de serviço;
- frete;
- juros de parcelamento;
- multa;
- desconto de antecipação;
- imposto;
- comissão.

A IA deve separar valor bruto, descontos, taxas, valor líquido e valor recebido.

---

# 3. Pedido

## 3.1 Pedido como centro da venda

Pedido é o registro principal da intenção comercial.

Campos recomendados:

```txt
id
uuid_publico
tenant_id
cliente_id
vendedor_id
origem
canal
tipo_venda
status
valor_bruto
valor_desconto
valor_taxas
valor_frete
valor_juros
valor_liquido
moeda
observacao
expira_em
confirmado_em
cancelado_em
criado_em
atualizado_em
```

Origens possíveis:

```txt
site
app
admin
pdv
whatsapp
api
link_pagamento
importacao
marketplace
```

---

## 3.2 Itens do pedido

Cada item deve guardar snapshot da oferta.

Campos recomendados:

```txt
id
pedido_id
oferta_id
tipo_item
nome_snapshot
descricao_snapshot
quantidade
valor_unitario
valor_total
desconto
status_entrega
metadata
criado_em
atualizado_em
```

Motivo:

```txt
Se o produto mudar de nome ou preço amanhã, o pedido antigo continua historicamente correto.
```

---

## 3.3 Status do pedido

Status recomendados:

```txt
rascunho
carrinho_abandonado
aguardando_dados
aguardando_aprovacao
aguardando_pagamento
pagamento_em_processamento
pago
confirmado
em_separacao
em_execucao
parcialmente_entregue
entregue
concluido
cancelado
expirado
reembolsado
parcialmente_reembolsado
em_disputa
chargeback
erro_integracao
```

Regras:

- não usar apenas `ativo` e `inativo`;
- não apagar pedido com erro;
- não sobrescrever status sem histórico;
- não liberar entrega em status incerto;
- mapear status externo para status interno padronizado;
- registrar transição de status.

---

## 3.4 Histórico de status

Toda mudança importante deve gerar histórico.

Campos:

```txt
id
pedido_id
status_anterior
status_novo
motivo
usuario_id
origem
criado_em
```

Isso facilita suporte, auditoria e investigação.

---

# 4. Checkout

## 4.1 Tipos de checkout

### Checkout simples

Para uma compra direta.

```txt
Oferta → Dados → Pagamento → Confirmação
```

### Checkout com carrinho

Para múltiplos itens.

```txt
Catálogo → Carrinho → Revisão → Dados → Pagamento → Confirmação
```

### Checkout com agendamento

Para serviço.

```txt
Serviço → Data/Horário → Dados → Pagamento/Sinal → Confirmação
```

### Checkout com proposta

Para venda consultiva.

```txt
Proposta → Aceite → Pedido → Pagamento/Faturamento
```

### Checkout por link

Para venda enviada por WhatsApp, e-mail ou atendimento.

```txt
Admin cria cobrança → Cliente acessa link → Paga → Sistema confirma
```

---

## 4.2 Dados mínimos no checkout

A tela deve mostrar:

- o que está sendo comprado;
- quantidade;
- preço unitário;
- desconto;
- taxas;
- frete, se houver;
- valor total;
- validade do pedido;
- formas de pagamento;
- regras de cancelamento;
- regras de entrega;
- dados do comprador;
- aceite de termos quando necessário.

Evite surpresa no final da compra.

---

## 4.3 Carrinho abandonado

Quando fizer sentido, registrar:

```txt
cliente_id
itens
valor_estimado
canal
criado_em
atualizado_em
expira_em
```

Possíveis ações:

- lembrete;
- recuperação por link;
- cupom;
- atendimento;
- análise de abandono.

Evitar spam e mensagens agressivas.

---

# 5. Pagamentos

## 5.1 Métodos de pagamento

A skill deve abranger:

```txt
Pix
cartao_credito
cartao_debito
boleto
dinheiro
transferencia
deposito
link_pagamento
carteira_digital
credito_interno
cortesia
faturado
pagamento_manual
split
```

Cada método tem regras próprias de confirmação, prazo, taxa, risco e conciliação.

---

## 5.2 Cobrança separada do pedido

A cobrança deve ser registrada separadamente porque um pedido pode ter várias tentativas de pagamento.

Campos recomendados:

```txt
id
pedido_id
gateway
gateway_payment_id
metodo_pagamento
status
valor
moeda
installments
expira_em
pago_em
cancelado_em
payload_solicitacao
payload_resposta
criado_em
atualizado_em
```

---

## 5.3 Status do pagamento

Status recomendados:

```txt
pendente
aguardando_confirmacao
em_processamento
autorizado
capturado
pago
recusado
cancelado
expirado
estornado
parcialmente_estornado
em_disputa
chargeback
erro
```

Regras:

- pagamento autorizado não é sempre pagamento capturado;
- Pix e boleto podem ficar pendentes;
- cartão pode ser recusado;
- pagamento manual exige auditoria;
- estorno deve atualizar pedido conforme regra;
- status externo deve ser traduzido para status interno.

---

## 5.4 Pagamento parcial

Algumas vendas exigem sinal, entrada ou parcelas.

Exemplos:

- reserva de vaga;
- serviço sob encomenda;
- proposta B2B;
- evento;
- venda faturada.

Regras:

- registrar valor total do pedido;
- registrar valor pago;
- registrar saldo pendente;
- definir quando liberar entrega;
- definir vencimentos;
- notificar inadimplência;
- permitir baixa manual com auditoria.

Status úteis:

```txt
sem_pagamento
parcialmente_pago
pago_total
saldo_pendente
inadimplente
```

---

## 5.5 Parcelamento

Parcelamento deve guardar:

```txt
numero_parcelas
valor_parcela
juros
valor_total_com_juros
gateway_installment_id
```

Regras:

- exibir total com clareza;
- não esconder juros;
- salvar condição aceita;
- conciliar parcelas quando aplicável;
- tratar reembolso parcial com cuidado.

---

## 5.6 Pagamento manual

Pagamento manual pode existir para dinheiro, transferência, cortesia ou ajuste.

Regras obrigatórias:

- exigir permissão administrativa;
- registrar usuário;
- registrar motivo;
- anexar comprovante, se necessário;
- gerar log de auditoria;
- não permitir baixa manual sem justificativa;
- destacar no painel financeiro.

---

# 6. Webhooks e idempotência

## 6.1 Webhook como evento crítico

O webhook deve ser validado antes de alterar pedido, pagamento ou entrega.

Validações obrigatórias:

- assinatura do gateway;
- origem confiável;
- identificador do evento;
- idempotência;
- status atual;
- valor;
- moeda;
- identificador externo;
- duplicidade;
- ordem dos eventos;
- payload obrigatório.

Regra:

```txt
Webhook repetido não pode duplicar pagamento, voucher, ingresso, crédito, notificação, comissão ou entrega.
```

---

## 6.2 Tabela de eventos externos

Campos recomendados:

```txt
id
provedor
event_id
event_type
external_payment_id
pedido_id
payload
signature_valid
processing_status
processado_em
error_message
criado_em
```

Status:

```txt
recebido
processado
ignorado_duplicado
erro
reprocessado
```

---

# 7. Entrega, liberação e cumprimento da venda

## 7.1 Separar pagamento de entrega

Pagamento confirmado não significa que a entrega foi concluída.

Exemplos:

- produto físico ainda precisa ser enviado;
- serviço ainda precisa ser executado;
- voucher precisa ser usado;
- ingresso precisa passar por check-in;
- curso digital precisa liberar acesso;
- assinatura precisa ativar plano.

Campos úteis:

```txt
status_entrega
entregue_em
responsavel_entrega
comprovante_entrega
```

---

## 7.2 Status de entrega

```txt
nao_aplicavel
aguardando_pagamento
aguardando_separacao
em_separacao
aguardando_envio
enviado
disponivel_para_retirada
entregue
liberado
em_execucao
executado
parcialmente_entregue
cancelado
bloqueado
```

---

## 7.3 Produto físico

Controlar:

- estoque;
- separação;
- envio;
- transportadora;
- código de rastreio;
- retirada;
- entrega;
- troca;
- devolução.

Tabelas possíveis:

```txt
estoques
movimentos_estoque
enderecos_entrega
envios
entregas
devolucoes
```

---

## 7.4 Produto digital

Controlar:

- acesso;
- download;
- expiração;
- licença;
- bloqueio;
- reenvio;
- histórico.

Tabelas possíveis:

```txt
acessos_digitais
downloads
licencas
```

---

## 7.5 Serviço

Controlar:

- agendamento;
- profissional;
- local;
- execução;
- presença;
- remarcação;
- no-show;
- conclusão.

Tabelas possíveis:

```txt
agendamentos
servico_execucoes
presencas
remarcacoes
```

---

## 7.6 Voucher

Controlar:

- código;
- validade;
- status;
- uso;
- reemissão;
- bloqueio;
- tentativa inválida.

Status:

```txt
gerado
disponivel
reservado
enviado
utilizado
cancelado
expirado
bloqueado
reemitido
```

Campos:

```txt
voucher_id
pedido_id
cliente_id
codigo
status
valor
valid_from
valid_until
usado_em
used_by
used_location
cancelado_em
cancelled_reason
criado_em
atualizado_em
```

---

## 7.7 Ingresso

Controlar:

- evento;
- lote;
- assento, se houver;
- titular;
- QR Code;
- check-in;
- transferência;
- cancelamento.

Tabelas possíveis:

```txt
eventos
lotes_ingressos
ingressos
checkins
```

---

## 7.8 Créditos e pacotes

Controlar:

- saldo inicial;
- saldo atual;
- consumo;
- validade;
- origem;
- estorno;
- expiração.

Tabelas possíveis:

```txt
carteiras_credito
movimentos_credito
pacotes
pacote_consumos
```

---

# 8. Assinaturas e recorrência

## 8.1 Separar plano, assinatura, cobrança e acesso

```txt
plano = define preço e benefícios.
assinatura = vínculo do cliente com o plano.
cobrança = tentativa financeira de um ciclo.
pagamento = confirmação do valor recebido.
acesso = funcionalidades liberadas.
```

---

## 8.2 Status de assinatura

```txt
trial
ativa
pendente_pagamento
inadimplente
pausada
cancelada
expirada
encerrada
```

---

## 8.3 Regras de recorrência

Definir:

- periodicidade;
- dia de cobrança;
- tolerância de atraso;
- tentativas de cobrança;
- comunicação de falha;
- bloqueio parcial;
- bloqueio total;
- reativação;
- cancelamento;
- reembolso;
- upgrade;
- downgrade;
- pró-rata, se existir.

---

# 9. Cancelamento, reembolso, devolução e chargeback

## 9.1 Cancelamento

Antes de cancelar, verificar:

- pedido está pago?
- item já foi entregue?
- voucher já foi usado?
- serviço já foi executado?
- produto já foi enviado?
- assinatura está ativa?
- existe regra de multa?
- existe prazo legal ou contratual?
- precisa aprovação?

Todo cancelamento deve ter motivo e histórico.

---

## 9.2 Reembolso

Tipos:

```txt
total
parcial
manual
automatico_gateway
credito_interno
```

Campos:

```txt
id
pedido_id
pagamento_id
valor
motivo
status
gateway_refund_id
solicitado_por
approved_by
criado_em
processado_em
```

Status:

```txt
solicitado
em_analise
aprovado
recusado
processando
reembolsado
erro
```

---

## 9.3 Devolução e troca

Para produto físico, controlar:

- solicitação;
- motivo;
- autorização;
- recebimento;
- avaliação;
- troca;
- reembolso;
- custo de frete;
- status.

---

## 9.4 Chargeback e disputa

Chargeback precisa de fluxo próprio.

Controlar:

- data;
- provedor;
- valor;
- motivo;
- evidências;
- prazo de resposta;
- status;
- decisão;
- impacto financeiro.

Status:

```txt
aberto
em_disputa
evidencia_enviada
ganho
perdido
encerrado
```

---

# 10. Nota fiscal, comprovante e documentos

Quando aplicável, controlar:

- comprovante de pedido;
- comprovante de pagamento;
- recibo;
- nota fiscal;
- contrato;
- aceite de termos;
- proposta aceita;
- comprovante de entrega.

Campos úteis:

```txt
documento_tipo
numero
serie
status
url_arquivo
emitido_em
cancelado_em
```

A IA deve prever integração fiscal somente quando fizer sentido para o projeto.

---

# 11. Comissões, afiliados e repasses

## 11.1 Comissão

Aplicável quando há:

- vendedor;
- professor;
- parceiro;
- afiliado;
- marketplace;
- indicação.

Campos:

```txt
id
pedido_id
beneficiario_id
tipo
percentual
valor_base
valor_comissao
status
disponivel_em
pago_em
criado_em
```

Status:

```txt
calculada
pendente
disponivel
paga
cancelada
estornada
```

---

## 11.2 Repasse

Controlar:

- valor a repassar;
- taxa;
- responsável;
- conta destino;
- data disponível;
- pagamento;
- comprovante;
- estorno.

Nunca pagar comissão antes de considerar cancelamento, reembolso ou chargeback conforme regra do negócio.

---

# 12. Segurança obrigatória

A IA deve aplicar:

- validação no backend;
- proteção contra SQL Injection;
- proteção contra CSRF;
- proteção contra XSS;
- rate limit em endpoints críticos;
- autenticação para área logada;
- autorização para admin;
- permissões por ação;
- logs sem dados sensíveis;
- HTTPS em produção;
- segredos fora do Git;
- variáveis de ambiente para chaves;
- validação de webhook;
- idempotência;
- UUID público para pedidos;
- auditoria para ações financeiras.

Nunca salvar:

- número completo de cartão;
- CVV;
- senha de gateway;
- token sensível em log aberto;
- payload com dado sensível sem necessidade.

---

# 13. LGPD e privacidade

Coletar apenas dados necessários para a venda.

Dados possíveis:

- nome;
- e-mail;
- telefone;
- CPF/CNPJ, se necessário;
- endereço, se necessário;
- dados fiscais, se necessário;
- histórico de compra;
- IP e user agent para segurança, se necessário.

Boas práticas:

- informar finalidade;
- proteger dados pessoais;
- limitar acesso;
- mascarar dados em painel admin;
- registrar auditoria de consulta sensível;
- respeitar retenção legal;
- não expor dados em notificações;
- não colocar dados pessoais em QR Code sem necessidade.

---

# 14. Painel administrativo comercial e financeiro

O painel deve permitir consultar:

- pedidos;
- itens;
- clientes;
- pagamentos;
- cobranças;
- vouchers;
- ingressos;
- assinaturas;
- reembolsos;
- cancelamentos;
- webhooks;
- entregas;
- estoque;
- comissões;
- repasses;
- conciliação.

Filtros:

- período;
- status;
- cliente;
- produto;
- canal;
- vendedor;
- método de pagamento;
- tipo de venda;
- origem;
- tenant/unidade;
- cupom;
- valor.

Ações:

- reenviar comprovante;
- reenviar voucher;
- cancelar pedido;
- solicitar reembolso;
- aprovar reembolso;
- registrar pagamento manual;
- consultar transação;
- forçar sincronização;
- reprocessar webhook;
- bloquear voucher;
- reemitir ingresso;
- exportar relatório;
- adicionar observação interna.

Toda ação crítica deve gerar auditoria.

---

# 15. Conciliação financeira

A conciliação compara sistema interno, gateway, banco, caixa e relatórios.

Verificar:

- pedido interno;
- pagamento externo;
- valor bruto;
- taxas;
- valor líquido;
- data;
- status;
- método;
- reembolso;
- chargeback;
- divergência;
- repasse;
- comissão.

Status:

```txt
nao_conciliado
conciliado
divergente
pendente_gateway
pendente_banco
ajuste_manual
```

Regra:

```txt
Nunca confiar cegamente apenas no gateway ou apenas no banco interno. Os dois precisam conversar.
```

---

# 16. Notificações

Eventos que podem gerar notificação:

- pedido criado;
- pagamento aguardando;
- pagamento aprovado;
- pagamento recusado;
- pedido expirado;
- voucher gerado;
- ingresso emitido;
- serviço agendado;
- assinatura renovada;
- assinatura inadimplente;
- entrega enviada;
- reembolso aprovado;
- cancelamento confirmado;
- chargeback aberto.

Canais:

- e-mail;
- WhatsApp;
- SMS;
- push;
- notificação interna.

Regras:

- não enviar entrega antes da confirmação;
- evitar mensagens duplicadas;
- registrar status de envio;
- permitir reenvio manual;
- não expor dados sensíveis.

---

# 17. Tratamento de erros

Erros comuns:

- gateway fora do ar;
- webhook atrasado;
- pagamento recusado;
- Pix expirado;
- boleto vencido;
- divergência de valor;
- pedido expirado;
- voucher já utilizado;
- ingresso já validado;
- cupom inválido;
- estoque esgotado;
- serviço sem agenda;
- falha ao liberar acesso;
- falha ao enviar notificação;
- falha de conciliação.

Cada erro deve ter:

- mensagem amigável para usuário;
- log técnico para suporte;
- código interno;
- status correto;
- ação recomendada;
- possibilidade de reprocessamento controlado quando seguro.

---

# 18. Tabelas recomendadas

Nem todo projeto precisa de todas. A IA deve adaptar ao escopo real.

## Núcleo comercial

```txt
clientes
ofertas
categorias
precos
pedidos
pedido_itens
pedido_status_historico
cupons
cupom_usos
```

## Pagamento

```txt
cobrancas
pagamentos
pagamento_eventos
reembolsos
conciliacoes
```

## Entrega e cumprimento

```txt
entregas
envios
enderecos_entrega
acessos_digitais
downloads
agendamentos
servico_execucoes
```

## Voucher, ingresso e crédito

```txt
vouchers
voucher_usos
eventos
lotes_ingressos
ingressos
checkins
carteiras_credito
movimentos_credito
pacotes
pacote_consumos
```

## Assinatura

```txt
planos
assinaturas
assinatura_ciclos
assinatura_cobrancas
beneficios
acessos
```

## Operação financeira

```txt
caixas
movimentos_caixa
comissoes
repasses
notas_fiscais
documentos_comerciais
```

## Suporte e auditoria

```txt
notificacoes
logs_auditoria
chamados
anexos
```

---

# 19. Endpoints comuns

Exemplos:

```txt
GET    /api/ofertas
GET    /api/ofertas/{uuid}
POST   /api/carrinho
POST   /api/pedidos
GET    /api/pedidos/{uuid}
POST   /api/pedidos/{uuid}/cancelar
POST   /api/checkout/iniciar
POST   /api/pagamentos/criar
POST   /api/webhooks/pagamento/{gateway}
GET    /api/admin/pedidos
GET    /api/admin/pagamentos
POST   /api/admin/pagamentos/manual
POST   /api/admin/webhooks/reprocessar
POST   /api/admin/reembolsos
GET    /api/admin/conciliacao
```

Endpoints devem validar autenticação, permissão, tenant, CSRF quando aplicável, payload e idempotência.

---

# 20. Testes obrigatórios

Testar:

- pedido criado corretamente;
- item salva preço histórico;
- cupom válido;
- cupom inválido;
- pagamento aprovado;
- pagamento recusado;
- webhook duplicado;
- webhook inválido;
- pagamento parcial;
- cancelamento;
- reembolso total;
- reembolso parcial;
- liberação de voucher;
- uso de voucher;
- emissão de ingresso;
- check-in duplicado;
- assinatura renovada;
- assinatura inadimplente;
- entrega de produto físico;
- liberação de produto digital;
- venda de serviço com agenda;
- estoque insuficiente;
- permissão administrativa;
- dados sensíveis em log;
- conciliação divergente.

Modelo:

```md
ID: VEN-001
Título: Pedido pago libera entrega correta
Pré-condição: Pedido criado com pagamento pendente
Passos:
1. Simular webhook de pagamento aprovado
2. Validar assinatura do webhook
3. Processar evento
4. Consultar pedido
Resultado esperado:
Pedido muda para pago, pagamento fica confirmado e entrega/liberação é criada uma única vez.
```

---

# 21. Checklist da IA antes de entregar

- [ ] O tipo de venda foi identificado.
- [ ] Oferta, pedido, pagamento e entrega estão separados.
- [ ] Pedido é criado no backend.
- [ ] Itens salvam snapshot de preço e descrição.
- [ ] Valor final é salvo no pedido.
- [ ] Pagamento tem status separado.
- [ ] Webhook é validado.
- [ ] Webhook é idempotente.
- [ ] Entrega/liberação só ocorre após regra confirmada.
- [ ] Existe histórico de status.
- [ ] Existe log de auditoria.
- [ ] Dados sensíveis não aparecem em log.
- [ ] Admin consegue consultar pedido, pagamento e entrega.
- [ ] Usuário recebe mensagem clara.
- [ ] Falhas do gateway são tratadas.
- [ ] Reembolso e cancelamento têm regra.
- [ ] Produto físico considera estoque e entrega.
- [ ] Produto digital considera acesso seguro.
- [ ] Serviço considera agendamento e execução.
- [ ] Voucher/ingresso considera uso e validação.
- [ ] Assinatura considera recorrência e inadimplência.
- [ ] Comissão/repasse considera cancelamento e chargeback.
- [ ] Testes cobrem sucesso, erro e duplicidade.
- [ ] Documentação explica fluxo completo.

---

# 22. Saída esperada da IA

Quando esta skill for usada, a IA deve entregar, conforme o pedido:

- identificação do tipo de venda;
- fluxo comercial;
- regras de checkout;
- modelagem de tabelas;
- endpoints da API;
- regras de status;
- regras de pagamento;
- regras de entrega/liberação;
- regras de voucher, ingresso, crédito ou assinatura, quando houver;
- regras de webhook;
- regras de cancelamento e reembolso;
- checklist de segurança;
- regras de painel admin;
- indicadores financeiros;
- casos de teste;
- documentação para desenvolvimento.

A entrega deve ser prática, objetiva e pronta para orientar implementação real em sistemas de venda simples ou complexos.
