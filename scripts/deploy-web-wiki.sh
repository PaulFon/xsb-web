#!/usr/bin/env bash
set -euo pipefail

# ===============================
# Deploy WEB/WIKI to Lightsail
#
# WEB  src:  repo root (.)          → /home/bitnami/htdocs/wwwroot/
# WIKI src:  ./wiki/ (if present)   → /home/bitnami/htdocs/wiki/
#
# Flags:
#   --dry-run | -n    Preview rsync only; skip Git & remote chmod/chown
#   --git             Force Git even in dry run
#   -m "message"      Commit message for Git
#
# SSH:
#   Uses SSH Host alias: xsb-lightsail (set in ~/.ssh/config)
# ===============================

DRY=""
RUN_GIT="yes"
MSG="Quick WEB/WIKI deploy"
REMOTE_ALIAS="xsb-lightsail"

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
  echo "🔎 Dry run enabled (no rsync changes will be made; skipping remote chmod/chown)"
fi

# --- Sanity: ensure we're at repo root (has scripts/ & index.html)
if [[ ! -d "scripts" || ! -f "index.html" ]]; then
  echo "❌ Please run from the repo root (where scripts/ and index.html live)."
  exit 1
fi

# --- Optional Git step
if [[ "${RUN_GIT}" == "yes" ]]; then
  echo "==> Git commit & push"
  git add -A
  git commit -m "${MSG}" || echo "Nothing to commit"
  git push origin "$(git rev-parse --abbrev-ref HEAD)"
else
  echo "==> Skipping Git (dry run)"
fi

# --- Check SSH alias works
echo "==> Using SSH alias: ${REMOTE_ALIAS}"
if ! ssh -o BatchMode=yes -o ConnectTimeout=8 "${REMOTE_ALIAS}" 'echo ok' >/dev/null 2>&1; then
  echo "❌ SSH to '${REMOTE_ALIAS}' failed. Check your ~/.ssh/config Host entry."
  exit 1
fi

# Shared excludes (don’t ship local tooling)
EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git"
  "--exclude=.gitignore"
  "--exclude=.vscode"
  "--exclude=.venv"
  "--exclude=scripts"
  "--exclude=docs"
  "--exclude=README.md"
  "--exclude=readme.html"
  "--exclude=php_errorlog"
  # Keep app sources out of /wwwroot; they deploy elsewhere
  "--exclude=mass_site"
  "--exclude=missal-seed"
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

# --- Deploy WEB ---
echo "==> Deploying WEB → /home/bitnami/htdocs/wwwroot"
rsync "${RSYNC_FLAGS[@]}" "${EXCLUDES[@]}" \
  ./ \
  "${REMOTE_ALIAS}:/home/bitnami/htdocs/wwwroot/"

# Normalize perms/ownership (skip in dry run)
if [[ -z "${DRY}" ]]; then
  echo "==> Normalizing ownership & permissions on server (WEB)"
  ssh "${REMOTE_ALIAS}" '
    set -e
    sudo chown -R bitnami:daemon /home/bitnami/htdocs/wwwroot &&
    # setgid bit so new files inherit group=daemon
    sudo find /home/bitnami/htdocs/wwwroot -type d -exec chmod 2755 {} \; &&
    sudo find /home/bitnami/htdocs/wwwroot -type f -exec chmod 0644 {} \; &&
    sudo chmod g+s /home/bitnami/htdocs/wwwroot
  ' || echo "⚠️  Ownership/perm normalization (WEB) failed (non-fatal)"
fi

# --- Deploy WIKI (if present) ---
if [[ -d "wiki" ]]; then
  echo
  echo "==> Deploying WIKI → /home/bitnami/htdocs/wiki"
  rsync "${RSYNC_FLAGS[@]}" \
    --exclude=".DS_Store" --exclude="._*" \
    --exclude=".git" --exclude=".gitignore" \
    ./wiki/ \
    "${REMOTE_ALIAS}:/home/bitnami/htdocs/wiki/"

  if [[ -z "${DRY}" ]]; then
    echo "==> Normalizing ownership & permissions on server (WIKI)"
    ssh "${REMOTE_ALIAS}" '
      set -e
      sudo chown -R bitnami:daemon /home/bitnami/htdocs/wiki &&
      sudo find /home/bitnami/htdocs/wiki -type d -exec chmod 2755 {} \; &&
      sudo find /home/bitnami/htdocs/wiki -type f -exec chmod 0644 {} \; &&
      sudo chmod g+s /home/bitnami/htdocs/wiki
    ' || echo "⚠️  Ownership/perm normalization (WIKI) failed (non-fatal)"
  fi
else
  echo
  echo "==> Skipping WIKI (no local wiki/ folder)"
fi

echo
echo "✅ WEB/WIKI deployment ${DRY:+(dry run) }complete."