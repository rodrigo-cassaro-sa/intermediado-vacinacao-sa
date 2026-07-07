# Backup, Rollback e Monitoramento

## Objetivo

Documentar como proteger dados, restaurar sistema, voltar versão e monitorar produção.

---

# 1. Backups

| Tipo | Frequência | Local | Retenção | Responsável |
|---|---|---|---|---|
| Banco |  |  |  |  |
| Uploads |  |  |  |  |
| Arquivos críticos |  |  |  |  |

---

# 2. Scripts

| Script | Função | Caminho |
|---|---|---|
| backup-db.sh | backup do banco |  |
| restore-db.sh | restauração do banco |  |

---

# 3. Restore

```txt
Passos para restaurar:
1.
2.
3.
```

---

# 4. Rollback

```txt
Versão anterior:
Branch/tag:
Como voltar:
Banco precisa restore?
Uploads precisam restore?
```

---

# 5. Monitoramento

| Item | Como verificar | Status |
|---|---|---|
| Health check | `GET /api/v1/health` → app/banco/banco_ms/versao/ambiente | implementado |
| Versão publicada | env `APP_VERSION` (setar no EasyPanel com o commit) aparece no health e nas métricas | implementado |
| Métricas operacionais | `GET /api/v1/interno/metricas` (super_admin/operador): importações pendentes/falhas, aplicações do dia, campanhas ativas, falhas de login/permissão | implementado |
| Últimos eventos/erros | `GET /api/v1/interno/auditoria?evento=login.falha` (super_admin) | implementado |
| Logs | `storage/logs/php_erros.log` (volume persistente); auditoria em `log_auditoria` | implementado |
| Banco | `banco_ms` no health/métricas; MySQL interno | implementado |
| Fila de importação | `importacoes.pendentes/processando/falhas_24h` nas métricas | implementado |
| Espaço em disco | verificar no EasyPanel (volumes storage/uploads, MySQL) | manual |
| Rate limit | HTTP 429 nas APIs; tabela `rate_limite` | implementado |

> Sugestão: apontar um monitor externo (UptimeRobot/EasyPanel) para `GET /api/v1/health` e alertar
> se `success=false` ou `banco != ok`. Painel `/admin` seção 11 mostra métricas e auditoria.

---

# 6. Regra final

Backup que nunca foi testado não deve ser considerado confiável.
