#!/usr/bin/env bash
set -euo pipefail

# --- SSH/Host config (alias with IP fallback) ---------------------------------
HOST_ALIAS="xsb-lightsail"
HOST_IP="35.174.112.225"
HOST_USER="bitnami"
SSH_KEY="${HOME}/.ssh/LightsailDefaultKey-us-east-1.pem"

SSH_CMD=(ssh -i "$SSH_KEY" -o IdentitiesOnly=yes)
DEST="${HOST_USER}@${HOST_ALIAS}"
if ! "${SSH_CMD[@]}" -o BatchMode=yes -o ConnectTimeout=5 "${DEST}" true 2>/dev/null; then
  DEST="${HOST_USER}@${HOST_IP}"
fi
RSYNC_SSH=(-e "ssh -i ${SSH_KEY} -o IdentitiesOnly=yes")
rsync_safe() { rsync "${RSYNC_SSH[@]}" "$@"; }
remote() { "${SSH_CMD[@]}" "${DEST}" "$@"; }
# ------------------------------------------------------------------------------

# Defaults
DRY=""
RUN_GIT="yes"
MSG="Quick deploy (MASS)"

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

if [[ "${RUN_GIT}" == "yes" ]]; then
  echo "==> Git commit & push"
  git add .
  git commit -m "${MSG}" || echo "Nothing to commit"
  git push origin main
else
  echo "==> Skipping Git (dry run)"
fi

# ---- Rsync config -------------------------------------------------------------
# Optional one-time --checksum for initial sync correctness (see web-wiki script)
RSYNC_FLAGS=(
  -avz
  ${DRY:+--dry-run}
  --delete
  --delete-delay
  --delete-excluded
  --human-readable
  --itemize-changes
  --omit-dir-times
  --no-perms
  --no-group
  --modify-window=2
  # --checksum   # (optional one-time, if needed)
)

RSYNC_EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
  "--exclude=scripts"
  "--exclude=wiki"
  "--exclude=WEB"
  "--exclude=web"
  "--exclude=.venv/"
)
# ------------------------------------------------------------------------------

echo "==> Deploying MASS → /home/bitnami/htdocs/mass"
if [[ -d "mass_site/public-build" ]]; then
  rsync_safe "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
    mass_site/public-build/ \
    "${DEST}:/home/bitnami/htdocs/mass/"
else
  echo "❌ mass_site/public-build/ does not exist. Build first:"
  echo "   python scripts/build-mass.py --date YYYY-MM-DD"
  exit 2
fi

echo
echo "✅ MASS deployment ${DRY:+(dry run) }complete."
