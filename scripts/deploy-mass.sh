#!/usr/bin/env bash
set -euo pipefail

MSG=${1:-"Quick deploy"}

echo "==> Git commit & push"
git add .
git commit -m "$MSG" || echo "Nothing to commit"
git push origin main

REMOTE_ALIAS="xsb-lightsail"

echo "==> Deploying MASS → /home/bitnami/htdocs/mass"
rsync -avz --delete \
  --exclude '.DS_Store' \
  --exclude '._*' \
  ./mass_site/public-build/ \
  ${REMOTE_ALIAS}:/home/bitnami/htdocs/mass/

echo
echo "✅ MASS deployment complete."
