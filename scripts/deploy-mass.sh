#!/usr/bin/env bash
# deploy-mass.sh — Deploy the Mass site
# - Calls sync-hub.sh to copy mass/index.html → mass_site/public-build/index.html
# - Rebuilds manifest (assets/manifest.json) so "Latest" button is current
# - Rsyncs public-build → Lightsail:/home/bitnami/htdocs/mass/
# Usage:
#   ./scripts/deploy-mass.sh -m "Commit message"
#   ./scripts/deploy-mass.sh -n            # dry run
#   ./scripts/deploy-mass.sh -n -m "Msg"

set -euo pipefail

# --- Config ---
SSH_ALIAS="xsb-lightsail"
# REMOTE_DIR="/home/bitnami/htdocs/mass/"
REMOTE_DIR="/opt/bitnami/apache/htdocs/mass/"
BUILD_DIR="mass_site/public-build"

# --- Flags ---
DRY_RUN=0
COMMIT_MSG=""

while getopts ":nm:" opt; do
  case "$opt" in
    n) DRY_RUN=1 ;;
    m) COMMIT_MSG="$OPTARG" ;;
    \?) echo "Unknown option: -$OPTARG" >&2; exit 2 ;;
    :)  echo "Option -$OPTARG requires an argument." >&2; exit 2 ;;
  esac
done
shift $((OPTIND -1))

# --- Helpers ---
say() { printf "%b\n" "$1"; }
die() { say "❌ $1"; exit 1; }

require() {
  command -v "$1" >/dev/null 2>&1 || die "Missing required command: $1"
}

# --- Preconditions ---
require rsync
require ssh
require git

[ -d "$BUILD_DIR" ] || die "Build directory not found: $BUILD_DIR (did you clone the repo?)"

if ! ssh -o BatchMode=yes -o ConnectTimeout=5 "$SSH_ALIAS" "echo ok" >/dev/null 2>&1; then
  die "Cannot reach SSH alias '$SSH_ALIAS'. Check ~/.ssh/config or your key."
fi

# --- Git commit/push (optional) ---
if [ "$DRY_RUN" -eq 0 ] && [ -n "$COMMIT_MSG" ]; then
  say "==> Git commit & push"
  # Stash untracked/changed files to avoid partial commits (optional safety)
  git add -A
  if ! git diff --cached --quiet; then
    git commit -m "$COMMIT_MSG" || true
  fi
  git push || true
else
  say "==> Skipping Git (dry run or no -m message)"
fi

# --- Ensure hub is in the build root ---
say "==> Sync hub index into build root"
if [ "$DRY_RUN" -eq 1 ]; then
  say "🔎 Dry run: would run ./scripts/sync-hub.sh"
else
  ./scripts/sync-hub.sh
fi

# --- Rebuild manifest so 'Latest' button is current ---
say "==> Build manifest (latest link)"
if [ "$DRY_RUN" -eq 1 ]; then
  say "🔎 Dry run: would run ./scripts/build-mass-manifest.sh"
else
  ./scripts/build-mass-manifest.sh
fi

# --- Rsync deploy ---
say "==> Deploying MASS → $REMOTE_DIR"
RSYNC_OPTS=(
  -az
  --delete
  --checksum
  --human-readable
  --stats
  # Excludes (belt & suspenders; BUILD_DIR shouldn't contain these anyway)
  "--exclude=.git/"
  "--exclude=.github/"
  "--exclude=.DS_Store"
  "--exclude=.venv/"
  "--exclude=node_modules/"
)

if [ "$DRY_RUN" -eq 1 ]; then
  RSYNC_OPTS+=( -n )
  say "🔎 Dry run enabled (no changes will be made)"
fi

# Trailing slash on source means "contents of BUILD_DIR"
rsync "${RSYNC_OPTS[@]}" "$BUILD_DIR/" "$SSH_ALIAS:$REMOTE_DIR"

# --- Permissions ---
if [ "$DRY_RUN" -eq 1 ]; then
  say "==> Skipping remote chmod (dry run)"
else
  say "==> Normalizing permissions on server"
  ssh "$SSH_ALIAS" "find '$REMOTE_DIR' -type d -exec chmod 755 {} \; &&
                    find '$REMOTE_DIR' -type f -exec chmod 644 {} \;"
fi


say "✅ MASS deployment complete."
