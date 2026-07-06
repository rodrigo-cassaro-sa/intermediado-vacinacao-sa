# Skill: Backup, Recuperação e Plano de Desastre

## Objetivo da skill

Esta skill orienta uma IA a definir, revisar e implementar boas práticas de **backup, restauração, retenção, teste de recuperação e plano de desastre** para sistemas web, SaaS e apps.

O foco é proteger banco de dados, arquivos enviados, configurações e histórico do sistema contra perda acidental, falha de servidor, erro humano, deploy quebrado ou incidente de segurança.

---

## Limite desta skill

Esta skill define como proteger, restaurar e manter a continuidade dos dados e arquivos importantes do sistema.

Ela deve focar em backup, restauração, retenção, teste de recuperação, segurança dos backups e plano de desastre.

Ela pode citar banco, migrações, deploy, segurança e LGPD quando isso afetar recuperação de dados, mas não deve substituir:

- `skill-mysql.md` para implementação do banco;
- `skill-migracoes-banco.md` para alteração versionada do schema;
- `skill-deploy-ci-cd.md` para publicação e rollback de aplicação;
- `skill-seguranca.md` para proteção geral do sistema;
- `skill-lgpd-privacidade.md` para tratamento legal de dados pessoais;
- `skill-monitoramento-observabilidade.md` para alertas e acompanhamento contínuo.

Esta skill responde "se algo der errado, conseguimos recuperar o sistema e os dados com segurança?".

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

Ao usar esta skill, aja como uma pessoa desenvolvedora sênior com visão de infraestrutura, banco de dados, segurança, operação e continuidade de negócio.

A IA deve pensar em:

- o que precisa ser salvo;
- frequência de backup;
- retenção;
- criptografia;
- armazenamento separado;
- restauração testada;
- backup antes de deploy crítico;
- plano de ação em incidente.

A IA deve tomar decisões práticas, explicáveis e alinhadas com projetos reais de software, app e SaaS.

---

## Relação com outras skills

Esta skill complementa:

```txt
skill-mysql.md
skill-dados.md
skill-deploy-ci-cd.md
skill-migracoes-banco.md
skill-seguranca.md
skill-lgpd-privacidade.md
```

---

## Princípio central

```txt
Backup que nunca foi testado não é garantia; é apenas esperança.
```

Sempre que existir dúvida entre uma solução sofisticada e uma solução clara, segura e fácil de manter, a IA deve preferir a solução clara.

---

# 1. O que precisa de backup

A IA deve mapear todos os dados importantes.

Itens comuns:

- banco MySQL/MariaDB;
- arquivos enviados por usuários;
- anexos privados;
- imagens e documentos;
- arquivos `.env` e configurações, com segurança;
- scripts de migração;
- logs de auditoria quando necessário;
- certificados ou chaves, com cuidado;
- configurações do servidor;
- volumes Docker.

Código-fonte deve estar no Git. Backup de código não substitui versionamento.

---

# 2. Tipos de backup

Usar tipos conforme necessidade.

| Tipo | Uso |
|---|---|
| Completo | cópia integral do banco ou arquivos |
| Incremental | salva somente mudanças desde último backup |
| Manual | antes de deploy, migração ou ação crítica |
| Automático | rotina diária/semanal planejada |
| Snapshot | imagem do servidor/volume |
| Exportação lógica | `mysqldump` para banco MySQL |

Projetos simples podem começar com backup completo diário + backup manual antes de alterações críticas.

---

# 3. Frequência recomendada

A frequência depende do volume e impacto da perda.

Exemplo:

```txt
Sistema pequeno: backup diário
Sistema operacional: backup diário + antes de deploy
Sistema crítico: backup diário + snapshots + retenção por hora quando necessário
Arquivos enviados: backup diário ou sincronização contínua
```

Pergunta principal:

> Quanto dado o negócio aceita perder se o servidor falhar agora?

Essa resposta define o RPO.

---

# 4. Retenção

Definir por quanto tempo guardar backups.

Modelo simples:

```txt
Diários: 7 dias
Semanais: 4 semanas
Mensais: 6 a 12 meses
Antes de deploy crítico: manter até estabilizar versão
```

Boas práticas:

- não manter backups infinitamente sem política;
- remover backups antigos com segurança;
- considerar LGPD e dados pessoais;
- proteger backup com acesso restrito;
- registrar data, ambiente e origem.

---

# 5. Local de armazenamento

Backup não deve ficar somente no mesmo servidor.

Estratégia recomendada:

- uma cópia local temporária;
- uma cópia externa em storage seguro;
- acesso restrito;
- criptografia quando houver dados sensíveis;
- separação por ambiente;
- nomes padronizados.

Se o servidor apagar ou corromper, backup no mesmo disco pode ser perdido junto.

---

# 6. Backup antes de deploy e migração

Antes de mudanças críticas, fazer backup manual.

Obrigatório quando:

- alterar estrutura de tabela;
- remover coluna;
- executar script em massa;
- importar dados;
- atualizar versão grande;
- alterar regra financeira, permissões ou dados sensíveis;
- limpar registros.

Registrar:

- quem fez;
- quando fez;
- arquivo gerado;
- ambiente;
- motivo;
- como restaurar.

---

# 7. Restauração

A IA deve documentar como restaurar.

Roteiro mínimo:

1. Identificar backup correto.
2. Confirmar ambiente de destino.
3. Colocar sistema em manutenção se necessário.
4. Restaurar banco.
5. Restaurar arquivos.
6. Ajustar permissões.
7. Rodar verificações.
8. Testar login e fluxo crítico.
9. Registrar conclusão.

Nunca restaurar backup de produção em desenvolvimento sem mascarar dados sensíveis quando houver risco.

---

# 8. Teste de recuperação

Backup precisa ser testado periodicamente.

Teste deve responder:

- o arquivo abre?
- o banco restaura?
- as tabelas existem?
- os dados principais aparecem?
- os arquivos enviados continuam acessíveis?
- o sistema sobe após restauração?
- quanto tempo demorou?

Registrar resultado do teste. Backup que falha silenciosamente é comum quando ninguém testa.

---

# 9. Plano de desastre

Criar plano para cenários graves.

Cenários:

- banco apagado;
- deploy que corrompe dados;
- servidor fora do ar;
- storage perdido;
- ransomware ou invasão;
- erro humano em massa;
- credencial vazada.

Para cada cenário, definir:

- responsável;
- primeira ação;
- backup usado;
- tempo estimado;
- comunicação;
- validação final.

---

# 10. Segurança dos backups

Backups carregam dados sensíveis.

Regras:

- acesso restrito;
- criptografia quando aplicável;
- não enviar por WhatsApp/e-mail comum sem proteção;
- não deixar `.sql` público no servidor;
- não versionar backup no Git;
- não armazenar senha em nome de arquivo;
- apagar cópias temporárias após envio seguro;
- registrar acesso a backups críticos.

---

# Checklist obrigatório antes de concluir

- [ ] Banco de dados está incluído no backup.
- [ ] Arquivos enviados estão incluídos.
- [ ] Backup não fica somente no mesmo servidor.
- [ ] Existe política de retenção.
- [ ] Backup antes de deploy crítico foi previsto.
- [ ] Restauração está documentada.
- [ ] Teste de restauração foi planejado.
- [ ] Backups sensíveis estão protegidos.
- [ ] Plano de desastre existe.

---

# Modelo de entrega esperado

Ao criar política de backup, entregue:

1. Itens protegidos.
2. Frequência.
3. Retenção.
4. Local de armazenamento.
5. Comando ou processo de backup.
6. Processo de restauração.
7. Teste de recuperação.
8. Responsáveis.
9. Plano para incidentes.

---

# Regra final da skill

A IA deve entregar uma solução que outro programador consiga entender, revisar, testar, publicar e manter sem depender de explicação verbal. Toda decisão importante deve ser documentada no próprio arquivo, no código, no README ou no documento do projeto.
