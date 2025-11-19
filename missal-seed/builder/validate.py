#!/usr/bin/env python3
"""
Quick checks for Missal + Lectionary data integrity.

Usage:
  python -m builder.validate 2026-06-23
  python -m builder.validate --day-key OT-12-Tue --cycle I
  python -m builder.validate --reading-set SET.Sanctoral-03-19-Joseph
"""
from __future__ import annotations
import argparse, sys
from pathlib import Path
import yaml

ROOT = Path(__file__).resolve().parents[1]

def yload(p: Path):
    return yaml.safe_load(p.read_text(encoding="utf-8"))

def check_exists(path: Path, what: str, issues: list[str]):
    if not path.exists():
        issues.append(f"❌ Missing {what}: {path}")

def check_reading_segment(rid: str, issues: list[str]):
    base = ROOT / f"segments/readings/{rid}"
    check_exists(base / "segment.yaml", f"reading segment metadata for {rid}", issues)
    check_exists(base / "text.html", f"text for {rid}", issues)
    # if psalm, response.html may be declared—warn if declared but missing
    if (base / "segment.yaml").exists():
        seg = yload(base / "segment.yaml")
        if "response_html" in (seg.get("paths") or {}):
            check_exists(base / "response.html", f"psalm response for {rid}", issues)

def check_reading_set(set_id: str, issues: list[str]) -> dict | None:
    rs_path = ROOT / f"segments/reading_sets/{set_id}.yaml"
    if not rs_path.exists():
        issues.append(f"❌ Reading set not found: {rs_path}")
        return None
    rs = yload(rs_path)
    # canonical_id presence check (when prefer=canonical)
    for item in rs.get("items", []):
        if item.get("prefer") == "canonical":
            cid = item.get("canonical_id")
            if not cid:
                issues.append(f"❌ {set_id}: role {item.get('role')} prefers canonical but has no canonical_id")
            else:
                check_reading_segment(cid, issues)
    # optional propers swap key
    if rs.get("propers_day_key"):
        pk = rs["propers_day_key"]
        # try temporal first, then sanctoral
        t = ROOT / f"segments/propers/temporal/{pk}/segment.yaml"
        s = ROOT / f"segments/propers/sanctoral/{pk}/segment.yaml"
        if not t.exists() and not s.exists():
            issues.append(f"❌ propers_day_key points to missing propers: {pk}")
    return rs

def check_propers(day_key: str, issues: list[str]):
    # temporal first, then sanctoral
    t = ROOT / f"segments/propers/temporal/{day_key}/segment.yaml"
    s = ROOT / f"segments/propers/sanctoral/{day_key}/segment.yaml"
    if t.exists():
        seg = yload(t)
        base = t.parent
    elif s.exists():
        seg = yload(s)
        base = s.parent
    else:
        issues.append(f"❌ No propers found for {day_key} in temporal/ or sanctoral/")
        return
    # verify declared pieces exist
    sets = (seg.get("variants", {}).get("sets") or {})
    default_key = seg.get("variants", {}).get("default", "day")
    v = sets.get(default_key) or next(iter(sets.values()), {})
    for label, rel in (v.get("pieces") or {}).items():
        check_exists(base / rel, f"propers piece {label}", issues)

def check_options_for_day(day_key: str, cycle: str | None, roles_expected: list[str], issues: list[str], day_type: str):
    cf = cycle if cycle else ""
    base = ROOT / f"segments/lectionary/temporal/{day_key}"
    if cf:
        base = base / cf
    opt = base / "options.yaml"
    if not opt.exists():
        issues.append(f"❌ Missing options.yaml for {day_key} {cycle or ''}: {opt}")
        return None
    o = yload(opt)
    # roles existence check by looking for per-role folders
    for r in roles_expected:
        check_exists(base / r / "segment.yaml", f"{day_key} {cycle or ''} role '{r}' segment.yaml", issues)
        check_exists(base / r / "text.html", f"{day_key} {cycle or ''} role '{r}' text.html", issues)
    # set IDs presence
    sets = [s.get("id") for s in (o.get("sets") or [])]
    if not sets:
        issues.append(f"❌ options.yaml has no sets for {day_key} {cycle or ''}")
    return o

def main(argv=None):
    ap = argparse.ArgumentParser(description="Validate scaffold before render")
    ap.add_argument("date", nargs="?", help="ISO date (YYYY-MM-DD) to resolve via ordo")
    ap.add_argument("--day-key", help="Validate by day key (e.g., OT-12-Tue)")
    ap.add_argument("--cycle", help="Cycle A|B|C or I|II")
    ap.add_argument("--reading-set", help="Validate this reading set ID")
    ap.add_argument("--type", choices=["sunday","weekday","sanctoral"], help="When using --day-key, provide type for role defaults")
    args = ap.parse_args(argv)

    issues: list[str] = []

    if args.reading_set:
        check_reading_set(args.reading_set, issues)

    ROLE_DEFAULTS = {
        "sunday": ["reading1","psalm","reading2","gospel"],
        "weekday": ["reading1","psalm","gospel"],
        "sanctoral": ["reading1","psalm","gospel"],
    }

    if args.day_key:
        roles = ROLE_DEFAULTS[args.type or "weekday"]
        check_options_for_day(args.day_key, args.cycle, roles, issues, args.type or "weekday")
        check_propers(args.day_key, issues)

    if not args.day_key and not args.reading_set and args.date:
        # Light date check: ensure the ordo entry exists and default set resolves
        from .resolve import load_common_ordo, resolve_date
        cfg = yload(ROOT / "builder.config.yaml")
        ordo = load_common_ordo(ROOT, cfg["year"], cfg["conference"], cfg["form"])
        try:
            res = resolve_date(ordo, args.date)
        except Exception as e:
            issues.append(f"❌ Ordo resolve failed for {args.date}: {e}")
        else:
            # default reading set file exists?
            dset = res.get("default_reading_set")
            if dset:
                check_reading_set(dset, issues)
            check_propers(res["day_key"], issues)

    if issues:
        print("\n".join(issues))
        print(f"\n❗ Found {len(issues)} issue(s).")
        return 1
    print("✅ Validation passed. No issues found.")
    return 0

if __name__ == "__main__":
    sys.exit(main())
