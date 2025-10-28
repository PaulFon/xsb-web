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

RSYNC_EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
  "--exclude=scripts"
  "--exclude=mass_site"
  "--exclude=mass"
  "--exclude=mass/"
)

RSYNC_FLAGS=(
  -avz ${DRY}
  --delete
  -O
  --itemize-changes
  --human-readable
)

echo "==> Deploying WEB → /home/bitnami/htdocs/wwwroot"
rsync_safe "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
  ./ \
  "${DEST}:/home/bitnami/htdocs/wwwroot/"

if [[ -d "wiki" ]]; then
  echo
  echo "==> Deploying WIKI → /home/bitnami/htdocs/wiki"
  rsync_safe "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
    ./wiki/ \
    "${DEST}:/home/bitnami/htdocs/wiki/"
else
  echo
  echo "==> Skipping WIKI (no local wiki/ folder)"
fi

echo
echo "✅ WEB/WIKI deployment ${DRY:+(dry run) }complete."
