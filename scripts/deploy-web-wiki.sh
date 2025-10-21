#!/usr/bin/env bash
set -euo pipefail

# Flags/args
DRY=""
MSG="Quick deploy"

# Parse args: support --dry-run / -n and -m "message" or a single positional message
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run|-n) DRY="-n"; shift ;;
    -m) shift; MSG="${1:-$MSG}"; shift || true ;;
    --) shift; break ;;             # end of flags
    *) MSG="$1"; shift ;;           # positional message
  esac
done

if [[ -n "$DRY" ]]; then
  echo "🔎 Dry run enabled (no changes will be made)"
fi

echo "==> Git commit & push"
git add .
git commit -m "$MSG" || echo "Nothing to commit"
git push origin main

REMOTE_ALIAS="xsb-lightsail"

# Common excludes (protect Mass and local tooling)
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
# Tuned flags: -O avoids dir mtime churn; --itemize-changes shows exactly what changed
RSYNC_FLAGS=(
  -avz ${DRY}
  --delete
  -O
  --itemize-changes
  --human-readable
)

echo "==> Deploying WEB → /home/bitnami/htdocs/wwwroot"
rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
  ./ \
  "${REMOTE_ALIAS}:/home/bitnami/htdocs/wwwroot/"

if [[ -d "wiki" ]]; then
  echo
  echo "==> Deploying WIKI → /home/bitnami/htdocs/wiki"
  rsync "${RSYNC_FLAGS[@]}" "${RSYNC_EXCLUDES[@]}" \
    ./wiki/ \
    "${REMOTE_ALIAS}:/home/bitnami/htdocs/wiki/"
else
  echo
  echo "==> Skipping WIKI (no local wiki/ folder)"
fi

echo
echo "✅ WEB/WIKI deployment ${DRY:+(dry run) }complete."