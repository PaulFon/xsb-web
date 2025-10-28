#!/usr/bin/env bash
set -euo pipefail

# ===============================
# Deploy MASS site to Lightsail
# - Source: mass_site/public-build/
# - Dest:   /home/bitnami/htdocs/mass/
# Supports:
#   --dry-run | -n     (preview only; skips Git)
#   --git              (force Git even in dry run)
#   -m "message"       (commit message)
# ===============================

# Defaults
DRY=""
RUN_GIT="yes"
MSG="Quick MASS deploy"

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

if [[ -n "${DRY}" ]]; then
  echo "🔎 Dry run enabled (no rsync changes will be made)"
fi

# Optional Git step (skip when --dry-run unless --git provided)
if [[ "${RUN_GIT}" == "yes" ]]; then
  echo "==> Git commit & push"
  git add -A
  git commit -m "${MSG}" || echo "Nothing to commit"
  git push origin "$(git rev-parse --abbrev-ref HEAD)"
else
  echo "==> Skipping Git (dry run)"
fi

# Remote host alias (configured in ~/.ssh/config)
REMOTE_ALIAS="xsb-lightsail"

# Paths
SRC="mass_site/public-build/"
DEST="/home/bitnami/htdocs/mass/"

# Safety: ensure source exists
if [[ ! -d "${SRC}" ]]; then
  echo "❌ Source folder not found: ${SRC}" >&2
  exit 1
fi

# Excludes (we only deploy built files)
RSYNC_EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
)

# Rsync flags (hardened)
RSYNC_FLAGS=(
  -avz
  --itemize-changes
  --human-readable
  --delete
  --delete-delay
  --delete-excluded
  --omit-dir-times
  --no-perms
  --no-group
  --modify-window=2
  --chmod=F644,D755     # ensure Apache-readable perms on target
)

# Add dry-run if requested
if [[ -n "${DRY}" ]]; then
  RSYNC_FLAGS+=( "--dry-run" )
fi

echo "==> Deploying MASS → ${DEST}"
rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
  "${SRC}" \
  "${REMOTE_ALIAS}:${DEST}"

echo
echo "✅ MASS deployment ${DRY:+(dry run) }complete."