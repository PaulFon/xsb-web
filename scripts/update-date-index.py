#!/usr/bin/env python3
"""
Regenerate mass_site/content/shared/notes/date_index.yaml
by scanning Missal YAML files and mapping DATE -> UID.

Usage (from WWWROOT):
  # Preview only (no files written)
  python scripts/update-date-index.py

  # Write date_index.yaml (backs up old file as .bak)
  python scripts/update-date-index.py --write

Options:
  --source   Path to Missal YAML root (default: mass_site/content/missal)
  --index    Output date_index.yaml path (default: mass_site/content/shared/notes/date_index.yaml)
"""
import argparse
import pathlib
import sys
import yaml
from datetime import date

ROOT = pathlib.Path(__file__).resolve().parents[1]
DEFAULT_SOURCE = ROOT / "mass_site" / "content" / "missal"
DEFAULT_INDEX  = ROOT / "mass_site" / "content" / "shared" / "notes" / "date_index.yaml"

def to_datestr(v) -> str:
    # YAML may parse date as datetime.date; normalize to 'YYYY-MM-DD' string
    if isinstance(v, date):
        return v.isoformat()
    return str(v)

def collect_mappings(source_root: pathlib.Path) -> dict[str, str]:
    mappings: dict[str, str] = {}
    warnings = []
    for yml in source_root.rglob("*.yaml"):
        try:
            data = yaml.safe_load(yml.read_text(encoding="utf-8"))
        except Exception as e:
            warnings.append(f"[WARN] YAML parse error in {yml}: {e}")
            continue
        if not isinstance(data, dict):
            warnings.append(f"[WARN] Top-level YAML must be a mapping: {yml}")
            continue
        uid = data.get("uid")
        dt  = data.get("date")
        if not uid or not dt:
            # Not a missal day or missing fields—skip quietly
            continue
        dstr = to_datestr(dt)
        if dstr in mappings and mappings[dstr] != uid:
            warnings.append(f"[WARN] Duplicate date {dstr}: '{mappings[dstr]}' vs '{uid}' (keeping latter)")
        mappings[dstr] = str(uid)
    # Print warnings (if any) to stderr
    for w in warnings:
        print(w, file=sys.stderr)
    return dict(sorted(mappings.items(), key=lambda kv: kv[0]))

def preview_yaml(mapping: dict[str, str]) -> str:
    # Force quoted keys for dates so YAML doesn’t re-parse as dates later
    lines = [f"\"{k}\": {v}" for k, v in mapping.items()]
    return "\n".join(lines) + ("\n" if lines else "")

def write_index(path: pathlib.Path, content: str):
    path.parent.mkdir(parents=True, exist_ok=True)
    if path.exists():
        backup = path.with_suffix(path.suffix + ".bak")
        backup.write_text(path.read_text(encoding="utf-8"), encoding="utf-8")
        print(f"[INFO] Backed up existing file to {backup}")
    path.write_text(content, encoding="utf-8")
    print(f"[INFO] Wrote {path}")

def main(argv=None):
    p = argparse.ArgumentParser()
    p.add_argument("--source", type=str, default=str(DEFAULT_SOURCE))
    p.add_argument("--index",  type=str, default=str(DEFAULT_INDEX))
    p.add_argument("--write", action="store_true", help="Write to file (otherwise preview to stdout)")
    args = p.parse_args(argv)

    source_root = pathlib.Path(args.source)
    index_path  = pathlib.Path(args.index)

    if not source_root.exists():
        print(f"[ERROR] Source path not found: {source_root}", file=sys.stderr)
        return 2

    mapping = collect_mappings(source_root)
    out_text = preview_yaml(mapping)

    if args.write:
        write_index(index_path, out_text)
    else:
        print("# Preview of date_index.yaml (not written)\n" + out_text)
        print("# Run with --write to save this to:", index_path)
    return 0

if __name__ == "__main__":
    raise SystemExit(main())