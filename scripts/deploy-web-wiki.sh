#!/usr/bin/env bash
set -euo pipefail

# ==========================================
# Deploy WEB/WIKI to Lightsail
# WEB  src:  . (repo root)       → /home/bitnami/htdocs/wwwroot/
# WIKI src:  ./wiki/ (if exists)  → /home/bitnami/htdocs/wiki/
#
# Flags:
#   --dry-run | -n   Preview rsync only; skips remote chmod; skips Git unless --git is set
#   --git            Force Git commit/push even in dry-run
#   -m "message"     Commit message (default below)
# ==========================================

DRY=""
RUN_GIT="yes"
MSG="Quick WEB/WIKI deploy"
REMOTE_ALIAS="xsb-lightsail"   # relies on ~/.ssh/config; change if needed

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

if [[ "${RUN_GIT}" == "yes" ]]; then
  echo "==> Git commit & push"
  git add -A
  git commit -m "${MSG}" || echo "Nothing to commit"
  git push origin "$(git rev-parse --abbrev-ref HEAD)"
else
  echo "==> Skipping Git (dry run)"
fi

# Shared excludes (don’t ship tooling, caches, or private projects)
EXCLUDES=(
  "--exclude=.DS_Store"
  "--exclude=._*"
  "--exclude=.git/"
  "--exclude=.gitignore"
  "--exclude=.vscode/"
  "--exclude=.idea/"
  "--exclude=.cache/"
  "--exclude=node_modules/"
  "--exclude=.venv/"           # local virtualenvs must never be deployed
  "--exclude=scripts/"         # deploy scripts themselves
  "--exclude=docs/"
  "--exclude=README.md"
  "--exclude=mass_site"        # source for Mass app; deployed by deploy-mass.sh
  "--exclude=missal-seed"      # any seed or private repo you mentioned
)

# rsync behavior
RSYNC_FLAGS=(
  -avz
  --itemize-changes
  --human-readable
  --delete
  --delete-delay
  --delete-excluded            # clean up previously uploaded excluded junk
  --omit-dir-times
  --no-perms
  --no-group
  --modify-window=2
)

# Protect filters: never let rsync delete these even with --delete-excluded
# (Best of both worlds: we keep cleanup *and* preserve shared subtrees like /mass)
PROTECT_FILTER=(
  --filter='P mass/'
)

[[ -n "${DRY}" ]] && RSYNC_FLAGS+=( "--dry-run" )

echo "==> Using SSH alias: ${REMOTE_ALIAS}"

# ---------------------------
# Deploy WEB (repo root → wwwroot)
# ---------------------------
echo "==> Deploying WEB → /home/bitnami/htdocs/wwwroot"
rsync "${RSYNC_FLAGS[@]}" "${PROTECT_FILTER[@]}" "${EXCLUDES[@]}" \
  ./ \
  "${REMOTE_ALIAS}:/home/bitnami/htdocs/wwwroot/"

# Normalize on server (skip for dry run)
if [[ -z "${DRY}" ]]; then
  echo "==> Normalizing ownership & permissions on server (WEB)"
  ssh "${REMOTE_ALIAS}" '
    sudo chown -R bitnami:daemon /home/bitnami/htdocs/wwwroot &&
    find /home/bitnami/htdocs/wwwroot -type d -exec chmod 755 {} \; &&
    find /home/bitnami/htdocs/wwwroot -type f -exec chmod 644 {} \;
  ' || echo "⚠️  Perm/owner normalization (WEB) failed (non-fatal)"
fi

# ---------------------------
# Deploy WIKI (optional)
# ---------------------------
if [[ -d "wiki" ]]; then
  echo
  echo "==> Deploying WIKI → /home/bitnami/htdocs/wiki"
  rsync "${RSYNC_FLAGS[@]}" \
    --exclude=".DS_Store" --exclude="._*" \
    --exclude=".git" --exclude=".gitignore" \
    ./wiki/ \
    "${REMOTE_ALIAS}:/home/bitnami/htdocs/wiki/"

  if [[ -z "${DRY}" ]]; then
    echo "==> Normalizing permissions on server (WIKI)"
    ssh "${REMOTE_ALIAS}" '
      sudo chown -R bitnami:daemon /home/bitnami/htdocs/wiki &&
      find /home/bitnami/htdocs/wiki -type d -exec chmod 755 {} \; &&
      find /home/bitnami/htdocs/wiki -type f -exec chmod 644 {} \;
    ' || echo "⚠️  Perm/owner normalization (WIKI) failed (non-fatal)"
  fi
else
  echo
  echo "==> Skipping WIKI (no local wiki/ folder)"
fi

echo
echo "✅ WEB/WIKI deployment ${DRY:+(dry run) }complete."