#!/usr/bin/env bash
set -euo pipefail

MSG=${1:-"Quick deploy"}

echo "==> Git commit & push"
git add .
git commit -m "$MSG" || echo "Nothing to commit"
git push origin main

# Usage:
#   ./scripts/deploy-web-wiki.sh         # real deploy
#   ./scripts/deploy-web-wiki.sh --dry-run  # preview only

REMOTE_ALIAS="xsb-lightsail"

DRY=""
if [[ "${1-}" == "--dry-run" ]]; then
  DRY="-n"
  echo "🔎 Dry run enabled (no changes will be made)"
fi

# Common excludes
# Common excludes
RSYNC_EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
  "--exclude=scripts"
  "--exclude=mass_site"
  "--exclude=mass"        # <— prevent deleting the live Mass site
  "--exclude=mass/"       # <— double-safe
)
# Tuned flags: -O avoids dir mtime churn; --itemize-changes shows exactly what changed
RSYNC_FLAGS=(
  -avz ${DRY}
  --delete
  -O
  --itemize-changes
  --human-readable
)

# WEB
echo "==> Deploying WEB → /home/bitnami/htdocs/wwwroot"
rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
  ./ \
  "${REMOTE_ALIAS}:/home/bitnami/htdocs/wwwroot/"

# WIKI (optional if you have a local ./wiki folder)
if [[ -d "wiki" ]]; then
  echo
  echo "==> Deploying WIKI → /home/bitnami/htdocs/wiki"
  rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
    ./wiki/ \
    "${REMOTE_ALIAS}:/home/bitnami/htdocs/wiki/"
else
  echo
  echo "==> Skipping WIKI (no local wiki/ folder)"
fi

echo
echo "✅ WEB/WIKI deployment ${DRY:+(dry run) }complete."