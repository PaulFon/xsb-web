#!/usr/bin/env bash
# Re-sync hub page to local build folder
set -e
cd "$(dirname "$0")/.."
mkdir -p mass_site/public-build
cp -f mass/index.html mass_site/public-build/index.html
echo "✅ Hub page copied into mass_site/public-build/"
