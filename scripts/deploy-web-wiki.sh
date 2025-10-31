#!/usr/bin/env bash
set -euo pipefail

# ===============================
# Deploy WEB/WIKI to Lightsail
# WEB src:  . (repo root)       → /home/bitnami/htdocs/wwwroot/
# WIKI src: ./wiki/ (if exists)  → /home/bitnami/htdocs/wiki/
#
# Flags:
#   --dry-run | -n       (preview rsync only; skips Git)
#   --git                (force Git even in dry run)
#   -m "message"         (commit message)
#
# SSH:
#   Prefers Host alias "xsb-lightsail" if present in ~/.ssh/config.
#   Else falls back to bitnami@35.174.112.225 with key:
#     ~/.ssh/LightsailDefaultKey-us-east-1.pem
# ===============================

DRY=""
RUN_GIT="yes"
MSG="Quick WEB/WIKI deploy"

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

# ---- SSH destination & rsync transport ----
HOST_ALIAS="xsb-lightsail"
FALLBACK_HOST="bitnami@35.174.112.225"
KEYFILE="${HOME}/.ssh/LightsailDefaultKey-us-east-1.pem"

SSH_DEST=""
declare -a RSYNC_SSH

if grep -qE "^[[:space:]]*Host[[:space:]]+${HOST_ALIAS}(\$|[[:space:]])" "${HOME}/.ssh/config" 2>/dev/null; then
  SSH_DEST="${HOST_ALIAS}"
  RSYNC_SSH=( -e "ssh" )
  echo "==> Using SSH alias: ${HOST_ALIAS}"
else
  SSH_DEST="${FALLBACK_HOST}"
  RSYNC_SSH=( -e "ssh -i ${KEYFILE}" )
  echo "==> Using fallback SSH target: ${FALLBACK_HOST}"
fi

# ---- Shared rsync config ----
EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
  "--exclude=.vscode"
  "--exclude=scripts"
  "--exclude=mass_site"
  "--exclude=README.md"
  "--exclude=docs"
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
)

[[ -n "${DRY}" ]] && RSYNC_FLAGS+=( "--dry-run" )

# ===============================
# Deploy WEB (repo root → wwwroot)
# ===============================
echo
echo "==> Deploying WEB → /home/bitnami/htdocs/wwwroot"
rsync "${RSYNC_FLAGS[@]}" "${EXCLUDES[@]}" \
  "${RSYNC_SSH[@]}" \
  ./ \
  "${SSH_DEST}:/home/bitnami/htdocs/wwwroot/"

# Normalize ownership & perms (Bitnami-friendly) unless dry run
if [[ -z "${DRY}" ]]; then
  echo "==> Normalizing ownership & permissions on server (WEB)"
  ssh ${RSYNC_SSH[1]:-ssh} ${SSH_DEST} '
    sudo chown -R bitnami:daemon /home/bitnami/htdocs/wwwroot &&
    find /home/bitnami/htdocs/wwwroot -type d -exec chmod 775 {} \; &&
    find /home/bitnami/htdocs/wwwroot -type f -exec chmod 664 {} \;
  ' || echo "⚠️  Perm normalization (WEB) failed (non-fatal)"
fi

# ===============================
# Deploy WIKI (if ./wiki exists)
# ===============================
if [[ -d "wiki" ]]; then
  echo
  echo "==> Deploying WIKI → /home/bitnami/htdocs/wiki"
  rsync "${RSYNC_FLAGS[@]}" \
    --exclude=".DS_Store" --exclude="._*" \
    --exclude=".git" --exclude=".gitignore" \
    "${RSYNC_SSH[@]}" \
    ./wiki/ \
    "${SSH_DEST}:/home/bitnami/htdocs/wiki/"

  if [[ -z "${DRY}" ]]; then
    echo "==> Normalizing ownership & permissions on server (WIKI)"
    ssh ${RSYNC_SSH[1]:-ssh} ${SSH_DEST} '
      sudo chown -R bitnami:daemon /home/bitnami/htdocs/wiki &&
      find /home/bitnami/htdocs/wiki -type d -exec chmod 775 {} \; &&
      find /home/bitnami/htdocs/wiki -type f -exec chmod 664 {} \;
    ' || echo "⚠️  Perm normalization (WIKI) failed (non-fatal)"
  fi
else
  echo
  echo "==> Skipping WIKI (no local wiki/ folder)"
fi

echo
echo "✅ WEB/WIKI deployment ${DRY:+(dry run) }complete."