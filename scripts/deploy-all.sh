#!/usr/bin/env bash
set -euo pipefail

# Run WEB/WIKI then MASS, passing along flags and message

MSG="Quick ALL deploy"
ARGS=("$@")

# Show what we’ll run
echo "==> Running WEB/WIKI, then MASS with args: ${ARGS[*]}"

# WEB/WIKI
./scripts/deploy-web-wiki.sh "${ARGS[@]}"

# MASS
./scripts/deploy-mass.sh "${ARGS[@]}"

echo
echo "✅ ALL deployment complete."
