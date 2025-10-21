#!/usr/bin/env bash
set -euo pipefail

MSG=${1:-"Quick deploy"}

echo "==> Git commit & push"
git add .
git commit -m "$MSG" || echo "Nothing to commit"
git push origin main

echo
echo "==> Deploying WEB/WIKI…"
./scripts/deploy.sh

echo
echo "==> Deploying MASS…"
rsync -avz --delete \
  --exclude '.DS_Store' \
  --exclude '._*' \
  ./mass_site/public-build/ \
  xsb-lightsail:/home/bitnami/htdocs/mass/

echo
echo "✅ All deployments complete (GitHub + Lightsail)"
