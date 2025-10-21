#!/usr/bin/env bash
set -euo pipefail

MSG=${1:-"Quick deploy"}

echo "==> Git commit & push"
git add .
git commit -m "$MSG" || echo "Nothing to commit"
git push origin main
echo "==> Deploying WEB/WIKI…"
./scripts/deploy-web-wiki.sh

echo
echo "==> Deploying MASS…"
./scripts/deploy-mass.sh

echo
echo "✅ All deployments complete."
