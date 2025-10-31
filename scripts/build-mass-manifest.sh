#!/usr/bin/env bash
set -euo pipefail

# Where your built Mass site lives in the repo:
ROOT="mass_site/public-build"

# Find newest YYYY/MM/DD that actually has an index.html
latest_path=$(find "$ROOT/en-US/general" -type f -name index.html \
  | awk -F'/en-US/general/' '{print $2}' \
  | sed 's|/index\.html$||' \
  | sort -r \
  | head -n 1 || true)

if [[ -z "${latest_path}" ]]; then
  echo "No generated days found under $ROOT/en-US/general/" >&2
  exit 1
fi

# Write a tiny manifest the hub can read
mkdir -p "$ROOT/assets"
cat > "$ROOT/assets/manifest.json" <<EOF
{
  "latest_url": "/en-US/general/${latest_path}/",
  "generated_at": "$(date -u +%FT%TZ)"
}
EOF

echo "Wrote $ROOT/assets/manifest.json → latest_url=/en-US/general/${latest_path}/"