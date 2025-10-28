#!/usr/bin/env bash
set -euo pipefail

# ===============================
# Deploy MASS site to Lightsail
# Source: mass_site/public-build/
# Dest:   /home/bitnami/htdocs/mass/
# Flags:
#   --dry-run | -n   (preview rsync only; skips Git)
#   --git            (force Git even in dry run)
#   -m "message"     (commit message)
# ===============================

DRY=""
RUN_GIT="yes"
MSG="Quick MASS deploy"

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

if [[ "${RUN_GIT}" == "yes" ]]; then
  echo "==> Git commit & push"
  git add -A
  git commit -m "${MSG}" || echo "Nothing to commit"
  git push origin "$(git rev-parse --abbrev-ref HEAD)"
else
  echo "==> Skipping Git (dry run)"
fi

REMOTE_ALIAS="xsb-lightsail"
SRC="mass_site/public-build/"
DEST="/home/bitnami/htdocs/mass/"

if [[ ! -d "${SRC}" ]]; then
  echo "❌ Source folder not found: ${SRC}" >&2
  exit 1
fi

RSYNC_EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
)

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
  # (no --chmod here; macOS rsync 2.6.9 doesn't support it)
)

if [[ -n "${DRY}" ]]; then
  RSYNC_FLAGS+=( "--dry-run" )
fi

echo "==> Deploying MASS → ${DEST}"
rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
  "${SRC}" \
  "${REMOTE_ALIAS}:${DEST}"

# Post-deploy permission fix (server-side), only on real deploys
if [[ -z "${DRY}" ]]; then
  echo "==> Normalizing permissions on server"
  ssh "${REMOTE_ALIAS}" '
    find /home/bitnami/htdocs/mass -type d -exec chmod 755 {} \; &&
    find /home/bitnami/htdocs/mass -type f -exec chmod 644 {} \;
  ' || {
    echo "⚠️  Permission normalization failed (non-fatal)."
  }
fi

echo
echo "✅ MASS deployment ${DRY:+(dry run) }complete."
