#!/usr/bin/env bash
set -euo pipefail

DRYRUN=false
SSH_ALIAS="xsb-lightsail"
REMOTE_DIR="/home/bitnami/htdocs/wiki"

while getopts "n" opt; do
  case $opt in
    n) DRYRUN=true ;;
  esac
done

if $DRYRUN; then
  echo "🔎 Dry run enabled (no rsync changes will be made; skipping remote chmod/chown)"
fi

if [ ! -d "wiki" ]; then
  echo "⚠️  No local wiki/ folder; nothing to deploy."
  exit 0
fi

RSYNC_OPTS="-avz --delete --human-readable --progress"
$DRYRUN && RSYNC_OPTS="$RSYNC_OPTS -n"

echo "==> Deploying WIKI → ${REMOTE_DIR}"
rsync $RSYNC_OPTS \
  -e "ssh" \
  wiki/ "${SSH_ALIAS}:${REMOTE_DIR}/"

if ! $DRYRUN; then
  echo "==> Normalizing permissions on server"
  ssh "$SSH_ALIAS" "sudo chown -R bitnami:daemon ${REMOTE_DIR} && sudo find ${REMOTE_DIR} -type d -exec chmod 2775 {} \; && sudo find ${REMOTE_DIR} -type f -exec chmod 664 {} \;"
fi

echo "✅ WIKI deployment complete."
