# Skill: Performance para Frontend, Backend e Banco de Dados

## Objetivo da skill

Esta skill orienta uma IA a criar, revisar e otimizar **desempenho de sistemas web, SaaS e apps**, considerando frontend, backend, banco de dados, APIs, imagens, cache, paginação, carregamento e experiência percebida pelo usuário.

O foco é deixar o sistema rápido, leve, escalável e estável sem criar complexidade desnecessária.

---

## Regra de medição antes/depois

Toda otimização de performance deve registrar, quando possível:

- métrica antes;
- alteração aplicada;
- métrica depois;
- ambiente usado;
- volume de dados usado;
- tela, endpoint ou consulta testada;
- risco da mudança;
- resultado percebido pelo usuário.

Evitar afirmar que algo ficou mais rápido sem evidência mínima.

Quando não for possível medir com ferramenta, registrar pelo menos observação manual controlada com cenário repetível.

---

## Limite desta skill

Esta skill define análise, medição e otimização de performance em frontend, backend, banco de dados, APIs, payloads, imagens, cache, paginação, relatórios e carregamento percebido.

Ela pode orientar HTML, CSS, JavaScript, Fetch, PHP, MySQL e infraestrutura quando isso afetar desempenho, mas não deve substituir:

- `skill-frontend.md` para implementação completa da interface;
- `skill-backend.md` para regra de negócio do servidor;
- `skill-mysql.md` para implementação completa do banco;
- `skill-fetch.md` para padrão completo de comunicação HTTP;
- `skill-qa.md` para plano completo de testes;
- `skill-monitoramento-observabilidade.md` para acompanhamento contínuo em produção.

Esta skill responde "qual é o gargalo real e como melhorar velocidade, estabilidade e uso de recursos sem quebrar o sistema?".

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

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior especialista em performance web, PHP procedural, MySQL/MariaDB, JavaScript puro, Fetch API, UX de carregamento e operação de sistemas reais.

A IA deve pensar em:

- tempo de carregamento;
- consultas SQL;
- índices;
- payload JSON;
- quantidade de requisições;
- imagens;
- cache;
- paginação;
- processamento assíncrono;
- gargalos reais medidos.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-frontend.md
skill-fetch.md
skill-backend.md
skill-mysql.md
skill-dados.md
skill-responsividade.md
skill-qa.md
```

---

## Princípio central

```txt
Performance profissional começa medindo o gargalo real e evitando carregar, processar ou transferir aquilo que o usuário não precisa naquele momento.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. Mentalidade de performance

A IA deve otimizar com propósito.

Perguntas principais:

- qual tela está lenta?
- qual ação demora?
- é problema de rede, banco, backend ou frontend?
- quantos registros estão sendo carregados?
- qual consulta SQL demora?
- o usuário precisa de todos esses dados agora?
- há imagens grandes demais?
- existe repetição desnecessária?

Evitar otimizar no escuro sem entender o gargalo.

---

# 2. Performance percebida

O usuário percebe velocidade pela resposta visual.

Boas práticas:

- mostrar loading em ações demoradas;
- desabilitar botão durante envio;
- dar feedback imediato;
- carregar primeiro o essencial;
- usar skeleton ou estado vazio quando útil;
- evitar tela travada;
- manter navegação responsiva.

Às vezes, melhorar feedback visual resolve mais que uma micro-otimização técnica.

---

# 3. Frontend

No frontend, verificar:

- HTML sem excesso de elementos;
- CSS sem seletores pesados;
- JavaScript modular;
- eventos sem duplicação;
- manipulação de DOM em lote;
- imagens otimizadas;
- ícones leves;
- lazy loading quando adequado;
- evitar bibliotecas grandes sem necessidade;
- carregar scripts no momento correto.

Evitar renderizar centenas de itens no DOM se o usuário só vê poucos.

---

# 4. Fetch API e payload

Requisições devem transferir apenas o necessário.

Boas práticas:

- usar paginação;
- enviar filtros ao backend;
- evitar JSON gigante;
- retornar somente campos usados na tela;
- compactar resposta no servidor quando aplicável;
- tratar timeout;
- cancelar requisições antigas em busca dinâmica;
- evitar múltiplas chamadas repetidas para o mesmo dado.

Exemplo ruim:

```txt
Carregar 10.000 clientes para filtrar no navegador.
```

Exemplo bom:

```txt
Enviar filtro ao backend e retornar 20 clientes por página.
```

---

# 5. Backend PHP

No backend, revisar:

- loops desnecessários;
- consultas dentro de loops;
- validações repetidas;
- leitura de arquivos grandes;
- geração de relatórios síncronos;
- endpoints que fazem muita coisa;
- resposta JSON com dados desnecessários;
- falta de cache para dados estáveis.

Regra importante:

```txt
Evite N+1 queries: não faça uma consulta para cada item dentro de uma lista.
```

---

# 6. MySQL e consultas

No banco, performance depende muito de modelagem e índices.

Verificar:

- `WHERE` usando campos indexados;
- joins necessários e bem relacionados;
- consultas sem `SELECT *` quando não precisa;
- uso de `LIMIT`;
- ordenação em campo indexado quando possível;
- filtros por data;
- cardinalidade dos índices;
- explain plan em consultas lentas.

Boas práticas:

```sql
SELECT id, nome, status
FROM clientes
WHERE status = 'ativo'
ORDER BY nome
LIMIT 20 OFFSET 0;
```

Evitar carregar tudo para filtrar no PHP.

---

# 7. Paginação e busca

Toda listagem com possibilidade de crescimento deve ter paginação.

Definir:

- `page`;
- `per_page`;
- limite máximo;
- total de registros quando necessário;
- filtros;
- ordenação permitida.

Para buscas digitadas, considerar debounce no frontend para evitar uma requisição a cada tecla sem controle.

---

# 8. Cache

Cache deve ser usado quando o dado é reutilizado e não precisa estar atualizado a cada segundo.

Exemplos:

- configurações do sistema;
- permissões do usuário durante a sessão;
- listas estáveis;
- resultados de relatórios pesados;
- menus;
- templates.

Cuidado:

- cache precisa de expiração;
- cache precisa ser invalidado quando o dado muda;
- dados sensíveis não devem ser cacheados sem segurança;
- cache errado gera informação desatualizada.

---

# 9. Imagens e arquivos

Imagens grandes prejudicam muito a experiência.

Boas práticas:

- redimensionar imagem para tamanho real de uso;
- comprimir sem destruir qualidade;
- usar formatos modernos quando possível;
- usar lazy loading em listas;
- evitar base64 gigante no HTML;
- separar thumbnails de arquivo original;
- limitar tamanho de upload;
- processar imagem no backend quando necessário.

---

# 10. Relatórios e tarefas pesadas

Relatórios pesados podem travar o sistema.

Soluções:

- filtros obrigatórios por período;
- paginação;
- exportação assíncrona;
- fila de processamento;
- cache de relatório;
- limite de linhas;
- aviso de tempo estimado;
- baixar arquivo quando pronto.

Evitar gerar relatório de milhares de registros em requisição comum sem controle.

---

# 11. Medição

A IA deve propor métricas simples.

Medir:

- tempo de resposta da API;
- tempo de consulta SQL;
- tamanho do payload;
- quantidade de requisições por tela;
- tempo até primeiro conteúdo;
- erros de timeout;
- uso de memória quando possível;
- endpoints mais lentos.

Sem medição, a otimização vira chute.

---

# Checklist obrigatório antes de concluir

- [ ] Evidência mínima foi gerada ou checklist manual foi deixado.
- [ ] Comportamento existente foi preservado ou mudança funcional foi aprovada.
- [ ] Risco de regressão foi avaliado.

- [ ] Listagens têm paginação.
- [ ] Endpoints retornam somente dados necessários.
- [ ] Consultas SQL críticas têm índices adequados.
- [ ] `SELECT *` foi evitado quando não necessário.
- [ ] Imagens foram otimizadas.
- [ ] Botões e telas têm feedback de carregamento.
- [ ] Requisições repetidas foram reduzidas.
- [ ] Relatórios pesados têm limite ou processamento controlado.
- [ ] Performance foi medida antes/depois quando possível.

---

# Modelo de entrega esperado

Ao revisar performance, entregue:

1. Gargalos encontrados.
2. Impacto para o usuário.
3. Causa provável.
4. Ajuste recomendado.
5. Risco da mudança.
6. Teste para confirmar melhora.
7. Métrica antes/depois quando possível.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
