#!/usr/bin/env bash
set -euo pipefail

echo "==> Deploying WEB/WIKI…"
# If your existing web/wiki script has a different name/path, change this line:
./scripts/deploy.sh

echo
echo "==> Deploying MASS…"
# Consistent excludes for macOS junk:
rsync -avz --delete \
  --exclude '.DS_Store' \
  --exclude '._*' \
  ./mass_site/public-build/ \
  xsb-lightsail:/home/bitnami/htdocs/mass/

echo
echo "✅ All deployments complete."
