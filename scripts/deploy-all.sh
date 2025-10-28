#!/usr/bin/env bash
set -euo pipefail

# Defaults
MSG="Quick deploy (ALL)"

# Pass-through CLI (we don’t reinterpret flags; we forward them verbatim)
ARGS=()
while [[ $# -gt 0 ]]; do
  case "$1" in
    -m) shift; MSG="${1:-$MSG}"; ARGS+=("-m" "${1:-$MSG}"); shift || true ;;
    *) ARGS+=("$1"); shift ;;
  esac
done

echo "==> Deploying WEB/WIKI…"
./scripts/deploy-web-wiki.sh "${ARGS[@]:-}" || { echo "❌ WEB/WIKI deploy failed"; exit 1; }

echo
echo "==> Deploying MASS…"
./scripts/deploy-mass.sh "${ARGS[@]:-}" || { echo "❌ MASS deploy failed"; exit 1; }

echo
echo "✅ ALL deployment complete."
