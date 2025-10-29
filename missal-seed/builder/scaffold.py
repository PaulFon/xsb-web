#!/usr/bin/env python3
"""
General-purpose scaffolder for Missal + Lectionary content.

Examples:
  # Sunday, Cycle C (4 readings), with propers and ordo date
  python -m builder.scaffold --day-key OT-31-Sun --type sunday --cycle C \
    --date 2026-11-01 --label "31st Sunday in Ordinary Time (Cycle C)" --with-propers

  # Weekday, Cycle I (3 readings), with alt set in options; default set in ordo
  python -m builder.scaffold --day-key OT-12-Tue --type weekday --cycle I \
    --date 2026-06-23 --label "Tuesday, Week 12 in Ordinary Time (Cycle I)" \
    --roles reading1,psalm,gospel --with-propers

  # Canonical readings batch (file in canonicals/ or repo root)
  python -m builder.scaffold --canonical-yaml joseph.yaml
"""
from __future__ import annotations
import argparse, sys
from pathlib import Path
from typing import List, Optional, Dict
import yaml

ROOT = Path(__file__).resolve().parents[1]  # repo root (missal-seed/)
DEFAULT_CONF = "US"
DEFAULT_FORM = "roman-missal-3e"

# ---------- utilities ----------

def write_text(path: Path, content: str, overwrite: bool = False):
    path.parent.mkdir(parents=True, exist_ok=True)
    if path.exists() and not overwrite:
        return
    path.write_text(content, encoding="utf-8")

def yload(path: Path) -> dict:
    return yaml.safe_load(path.read_text(encoding="utf-8"))

def ydump(data: dict) -> str:
    return yaml.safe_dump(data, sort_keys=False)

def ensure_file(path: Path, data: dict):
    write_text(path, ydump(data))

# ---------- defaults ----------

ROLE_DEFAULTS = {
    "sunday": ["reading1", "psalm", "reading2", "gospel"],
    "weekday": ["reading1", "psalm", "gospel"],
    "sanctoral": ["reading1", "psalm", "gospel"],
}

def cycle_folder(day_type: str, cycle: Optional[str]) -> str:
    if day_type == "sunday":
        return (cycle or "").strip()  # A|B|C
    if day_type == "weekday":
        return (cycle or "").strip()  # I|II
    return ""  # sanctoral (usually no cycle folder)

def default_set_id(day_key: str, cycle: Optional[str]) -> str:
    suffix = f".{cycle}" if cycle else ""
    return f"SET.{day_key}{suffix}"

def default_labels(day_key: str, cycle: Optional[str], day_type: str) -> Dict[str, str]:
    set_label = f"{day_key.replace('-', ' ')}"
    if cycle:
        set_label += f" ({cycle})"
    ordo_label = set_label
    return {"set": set_label, "ordo": ordo_label}

# ---------- scaffold steps ----------

def scaffold_propers(day_key: str):
    base = ROOT / f"segments/propers/temporal/{day_key}"
    ensure_file(base / "segment.yaml", {
        "id": day_key,
        "group": "temporal",
        "title": day_key.replace("-", " "),
        "rank": "Sunday" if day_key.endswith("Sun") else "Weekday",
        "season": "Ordinary Time",
        "color": ["green"],
        "variants": {
            "default": "day",
            "sets": {
                "day": {
                    "label": "Mass During the Day",
                    "pieces": {
                        "entrance_html": "day/entrance.html",
                        "collect_html": "day/collect.html",
                        "communion_html": "day/communion.html",
                        "post_communion_html": "day/post-communion.html",
                    }
                }
            }
        }
    })
    write_text(base / "day/entrance.html", "<p>[Entrance — placeholder]</p>")
    write_text(base / "day/collect.html", "<p>[Collect — placeholder]</p>")
    write_text(base / "day/communion.html", "<p>[Communion — placeholder]</p>")
    write_text(base / "day/post-communion.html", "<p>[Post-Communion — placeholder]</p>")

def scaffold_lectionary_roles(day_key: str, day_type: str, roles: List[str], cycle: Optional[str]):
    cf = cycle_folder(day_type, cycle)
    base = ROOT / f"segments/lectionary/temporal/{day_key}"
    if cf:
        base = base / cf

    for role in roles:
        seg_yaml = {
            "role": {
                "reading1": "First Reading",
                "reading2": "Second Reading",
                "psalm": "Responsorial Psalm",
                "gospel": "Gospel",
            }.get(role, role),
            "reference": "",
            "versification": "NABRE",
            "canonical_id": None,
            "paths": {"text_html": "text.html"}
        }
        if role == "psalm":
            seg_yaml["paths"]["response_html"] = "response.html"
        folder = base / role
        ensure_file(folder / "segment.yaml", seg_yaml)
        write_text(folder / "text.html", f"<p>[{day_key} {cycle or ''} — {seg_yaml['role']} placeholder]</p>")
        if role == "psalm":
            write_text(folder / "response.html", "<p>[Psalm response placeholder]</p>")

def scaffold_options(day_key: str, day_type: str, cycle: Optional[str], set_id: str, set_label: str):
    cf = cycle_folder(day_type, cycle)
    base = ROOT / f"segments/lectionary/temporal/{day_key}"
    if cf:
        base = base / cf
    o = {
        "id": f"LEC.{day_key}{('.' + cycle) if cycle else ''}",
        "day_key": day_key,
        "cycle": {"sunday": cycle if day_type == "sunday" else None,
                  "weekday": cycle if day_type == "weekday" else None},
        "sets": [{"id": set_id, "label": set_label}],
    }
    ensure_file(base / "options.yaml", o)

def scaffold_reading_set(set_id: str, set_label: str, roles: List[str], prefer: str = "local"):
    rs = {
        "id": set_id,
        "label": set_label,
        "items": [{"role": r, "prefer": prefer} for r in roles]
    }
    ensure_file(ROOT / f"segments/reading_sets/{set_id}.yaml", rs)

def update_ordo(conference: str, form: str, date_iso: str, day_key: str,
                day_type: str, cycle: Optional[str], default_set_id_str: str, ordo_label: str):
    year = date_iso.split("-")[0]
    ordo_path = ROOT / f"ordos/{year}/{conference}/{form}.common.yaml"
    if not ordo_path.exists():
        ensure_file(ordo_path, {
            "meta": {
                "id": f"{conference}:{form}:common:{year}",
                "calendar": conference,
                "form": form,
                "locale": "en-US",
                "defaults": {"sunday_cycle": "C", "weekday_cycle": "II"},
                "source": {"name": "Common baseline (demo)", "license": "internal"},
            },
            "days": {}
        })
    doc = yload(ordo_path)
    doc.setdefault("days", {})
    node = {
        "label": ordo_label,
        "primary": {
            "day_key": day_key,
            "variant": "day",
            "cycle": {
                "sunday": cycle if day_type == "sunday" else None,
                "weekday": cycle if day_type == "weekday" else None,
            },
            "default_reading_set": default_set_id_str,
        },
        "overlays": [],
        "precedence_rule": "Sunday in OT" if day_type == "sunday" else "Weekday",
    }
    doc["days"][date_iso] = node
    write_text(ordo_path, ydump(doc), overwrite=True)

# ---------- canonical readings ----------

def scaffold_canonical_reading(reading: dict):
    """
    reading:
      id: READING.Mt-1-16_18-21_24a.NABRE
      role: gospel | reading1 | reading2 | psalm
      reference: "Matthew 1:16, 18-21, 24a"
      versification: "NABRE"
      text_html: "<p>...</p>"                 # optional (placeholder if missing)
      response_html: "<p>...</p>"             # psalm only; optional
    """
    rid = reading["id"]
    base = ROOT / f"segments/readings/{rid}"
    seg = {
        "id": rid,
        "role": reading["role"],
        "reference": reading.get("reference", ""),
        "versification": reading.get("versification", "NABRE"),
        "paths": {"text_html": "text.html"}
    }
    # psalm refrain support
    if reading.get("response_html") is not None or reading.get("role") == "psalm":
        seg["paths"]["response_html"] = "response.html"

    ensure_file(base / "segment.yaml", seg)
    write_text(base / "text.html", reading.get("text_html", f"<p>[{rid} text placeholder]</p>"))
    if "response_html" in seg["paths"]:
        write_text(base / "response.html", reading.get("response_html", "<p>[Response placeholder]</p>"))

def scaffold_canon_from_yaml(path: Path):
    # If relative path not found, try canonicals/<path>
    if not path.is_absolute():
        candidate = ROOT / path
        if not candidate.exists():
            candidate = ROOT / "canonicals" / path
        path = candidate
    if not path.exists():
        raise SystemExit(f"Canonical YAML not found: {path}")
    doc = yload(path)
    items = doc.get("readings", [])
    for r in items:
        scaffold_canonical_reading(r)

# ---------- CLI ----------

def main(argv=None):
    p = argparse.ArgumentParser(description="Scaffold Missal + Lectionary segments and ordo entry.")
    p.add_argument("--day-key", help="Key like OT-31-Sun, OT-12-Tue, Sanctoral-03-19-Joseph")
    p.add_argument("--type", choices=["sunday","weekday","sanctoral"])
    p.add_argument("--cycle", help="A|B|C for Sundays, I|II for weekdays (omit for sanctoral)")
    p.add_argument("--roles", help="Comma list of roles (default based on type)")
    p.add_argument("--set-id", help="Override reading set ID")
    p.add_argument("--set-label", help="Human label for the reading set")
    p.add_argument("--date", help="ISO date to map in the ordo (YYYY-MM-DD)")
    p.add_argument("--label", help="Ordo display label for that date")
    p.add_argument("--conference", default=DEFAULT_CONF)
    p.add_argument("--form", default=DEFAULT_FORM)
    p.add_argument("--with-propers", action="store_true", help="Also scaffold minimal propers")
    p.add_argument("--prefer", choices=["local","canonical"], default="local",
                   help="Default preference for reading set items")
    p.add_argument("--canonical-yaml", help="YAML file listing canonical readings to scaffold (looked up in canonicals/ by default)")
    args = p.parse_args(argv)

    # Canonical batch-only mode
    if args.canonical_yaml and not args.day_key:
        scaffold_canon_from_yaml(Path(args.canonical_yaml))
        print("Canonical readings scaffold complete.")
        return 0

    # Normal day scaffold
    roles = (args.roles.split(",") if args.roles else ROLE_DEFAULTS[args.type])
    set_id = args.set_id or default_set_id(args.day_key, args.cycle)
    labels = default_labels(args.day_key, args.cycle, args.type)
    set_label = args.set_label or labels["set"]

    scaffold_lectionary_roles(args.day_key, args.type, roles, args.cycle)
    scaffold_options(args.day_key, args.type, args.cycle, set_id, set_label)
    scaffold_reading_set(set_id, set_label, roles, prefer=args.prefer)

    if args.with_propers:
        scaffold_propers(args.day_key)

    if args.date:
        ordo_label = args.label or labels["ordo"]
        update_ordo(args.conference, args.form, args.date, args.day_key,
                    args.type, args.cycle, set_id, ordo_label)

    if args.canonical_yaml:
        scaffold_canon_from_yaml(Path(args.canonical_yaml))

    print("Scaffold complete.")
    return 0

if __name__ == "__main__":
    sys.exit(main())