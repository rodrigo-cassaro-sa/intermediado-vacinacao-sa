#!/bin/sh
# ============================================================================
# docker/entrypoint.sh
# Aplica as migrations pendentes no start do container (idempotente) e sobe o
# Apache. Controle por env AUTO_MIGRAR (default: true). Em produção, pode-se
# definir AUTO_MIGRAR=false para aplicar migrations manualmente (com backup).
# ============================================================================
set -e

if [ "${AUTO_MIGRAR:-true}" = "false" ]; then
  echo "[entrypoint] AUTO_MIGRAR=false — pulando migrations automáticas."
else
  echo "[entrypoint] aplicando migrations (incremental)..."
  tentativa=0
  # Tenta algumas vezes caso o MySQL ainda esteja subindo.
  until php /var/www/html/scripts/migrar.php; do
    tentativa=$((tentativa + 1))
    if [ "$tentativa" -ge 10 ]; then
      echo "[entrypoint] banco indisponível após $tentativa tentativas; seguindo sem aplicar (verifique /api/v1/health)."
      break
    fi
    echo "[entrypoint] banco não pronto (tentativa $tentativa) — aguardando 3s..."
    sleep 3
  done
fi

echo "[entrypoint] iniciando Apache..."
exec apache2-foreground
