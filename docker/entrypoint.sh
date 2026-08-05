#!/bin/sh
# ============================================================================
# docker/entrypoint.sh
# Aplica as migrations pendentes no start do container (idempotente), liga o
# worker da fila e sobe o Apache.
#
# Envs:
#   AUTO_MIGRAR=false        -> não aplica migrations no boot (fazer manualmente)
#   WORKERS_EMBUTIDOS=false  -> não roda o worker aqui (use os Cron Jobs do painel)
#   WORKER_INTERVALO=15      -> segundos entre as passadas do worker
#
# Por que o worker mora aqui (BUG-003): importações acima de ~2000 linhas vão
# para uma fila e dependem de alguém rodar scripts/processar_importacoes.php.
# Enquanto isso ficava só como Cron Job manual no EasyPanel, toda lista grande
# parava em 'pendente' e o cliente via "processando em segundo plano" para sempre.
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

if [ "${WORKERS_EMBUTIDOS:-true}" = "false" ]; then
  echo "[entrypoint] WORKERS_EMBUTIDOS=false — fila desligada aqui; configure os Cron Jobs (docs/13)."
else
  INTERVALO="${WORKER_INTERVALO:-15}"
  LOG=/var/www/html/storage/logs/worker.log
  echo "[entrypoint] worker da fila embutido (a cada ${INTERVALO}s) — log em storage/logs/worker.log"
  (
    while true; do
      # Corta o log se passar de ~5MB, senão ele cresce sem fim no volume.
      if [ -f "$LOG" ] && [ "$(wc -c < "$LOG")" -gt 5242880 ]; then : > "$LOG"; fi
      # Roda como www-data: mesmo dono dos arquivos gravados em storage/uploads.
      su -s /bin/sh www-data -c "php /var/www/html/scripts/processar_importacoes.php" >> "$LOG" 2>&1 || true
      su -s /bin/sh www-data -c "php /var/www/html/scripts/processar_webhooks.php"    >> "$LOG" 2>&1 || true
      sleep "$INTERVALO"
    done
  ) &
fi

echo "[entrypoint] iniciando Apache..."
exec apache2-foreground
