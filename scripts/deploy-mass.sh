#!/usr/bin/env bash
set -euo pipefail

# Defaults
DRY=""
RUN_GIT="yes"
MSG="Quick deploy"

# Parse args
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run|-n) DRY="-n"; RUN_GIT="no"; shift ;;
    --git) RUN_GIT="yes"; shift ;;
    -m) shift; MSG="${1:-$MSG}"; shift || true ;;
    --) shift; break ;;
    *) MSG="$1"; shift ;;
  esac
done

if [[ -n "$DRY" ]]; then
  echo "🔎 Dry run enabled (no rsync changes will be made)"
fi

if [[ "$RUN_GIT" == "yes" ]]; then
  echo "==> Git commit & push"
  git add .
  git commit -m "$MSG" || echo "Nothing to commit"
  git push origin main
else
  echo "==> Skipping Git (dry run)"
fi

REMOTE_ALIAS="xsb-lightsail"
MASS_LOCAL_DIR="./mass_site/public-build"
MASS_REMOTE_DIR="/home/bitnami/htdocs/mass"

RSYNC_EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
)

RSYNC_FLAGS=(
  -avz ${DRY}
  --delete
  -O
  --itemize-changes
  --human-readable
)

echo "==> Deploying MASS → ${MASS_REMOTE_DIR}"
rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
  "${MASS_LOCAL_DIR}/" \
  "${REMOTE_ALIAS}:${MASS_REMOTE_DIR}/"

echo
echo "✅ MASS deployment ${DRY:+(dry run) }complete."