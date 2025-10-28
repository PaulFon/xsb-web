#!/usr/bin/env bash
set -euo pipefail

# Defaults
DRY=""
RUN_GIT="yes"
MSG="Quick deploy"

# Parse args
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run|-n) DRY="--dry-run"; RUN_GIT="no"; shift ;;
    --git) RUN_GIT="yes"; shift ;;
    -m) shift; MSG="${1:-$MSG}"; shift || true ;;
    --) shift; break ;;
    *) MSG="$1"; shift ;;
  esac
done

if [[ "$RUN_GIT" == "yes" ]]; then
  echo "==> Git commit & push"
  git add .
  git commit -m "$MSG" || echo "Nothing to commit"
  git push origin main
else
  echo "==> Skipping Git (dry run)"
fi

echo
echo "==> Deploying WEB/WIKI…"
./scripts/deploy-web-wiki.sh $DRY -m "$MSG"

echo
echo "==> Deploying MASS…"
./scripts/deploy-mass.sh $DRY -m "$MSG"

echo
echo "✅ All deployments complete ${DRY:+(dry run) }."