#!/usr/bin/env bash
# verify_wiki_structure.sh — Check wiki folder structure + permissions

TARGET="$1"

if [ -z "$TARGET" ]; then
  echo "Usage: $0 /path/to/wiki"
  exit 1
fi

echo "Verifying: $TARGET"

if [ ! -d "$TARGET" ]; then
  echo "❌ Directory not found: $TARGET"
  exit 1
fi
echo "✅ Directory exists"

# Check key expected files/directories
FILE_LIST=("index.php" "LocalSettings.php" "includes" "cache")
for f in "${FILE_LIST[@]}"; do
  if [ -e "$TARGET/$f" ] || [ -d "$TARGET/$f" ]; then
    echo "✅ Found: $f"
  else
    echo "⚠️   Missing: $f"
  fi
done

# Detect OS to choose correct stat flags
OS="$(uname)"
if [ "$OS" = "Darwin" ]; then
  # macOS
  STAT_PERM="stat -f %Lp"
  STAT_OWNER="stat -f %Su"
  STAT_GROUP="stat -f %Sg"
else
  # assume Linux
  STAT_PERM="stat -c %a"
  STAT_OWNER="stat -c %U"
  STAT_GROUP="stat -c %G"
fi

PERM=$($STAT_PERM "$TARGET")
OWNER=$($STAT_OWNER "$TARGET")
GROUP=$($STAT_GROUP "$TARGET")

echo "Top-level permissions: $PERM  (owner: $OWNER  group: $GROUP)"

echo "-- Checking sample subfile permissions"
find "$TARGET" -maxdepth 2 -type f | head -n 5 | while read file; do
  if [ "$OS" = "Darwin" ]; then
    P=$(stat -f "%Lp %Su %Sg %n" "$file")
  else
    P=$(stat -c "%a %U %G %n" "$file")
  fi
  echo "$P"
done

echo "✅ Verification complete"
