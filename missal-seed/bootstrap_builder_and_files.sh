#!/usr/bin/env bash
set -e

echo "==> Creating builder files..."
mkdir -p builder shared/templates shared/styles ordos/2026/US segments/propers/temporal/OT-12-Tue/day

# __init__.py
cat > builder/__init__.py <<'PY'
__all__ = []
PY

# loaders.py
cat > builder/loaders.py <<'PY'
from pathlib import Path
import yaml

def yload(path: Path):
    return yaml.safe_load(path.read_text(encoding="utf-8"))

def rtext(path: Path) -> str:
    return path.read_text(encoding="utf-8")

def load_propers(root: Path, day_key: str, variant: str | None):
    seg = yload(root / f"segments/propers/temporal/{day_key}/segment.yaml")
    vkey = (variant or seg["variants"]["default"])
    pieces = seg["variants"]["sets"][vkey]["pieces"]
    base = root / f"segments/propers/temporal/{day_key}"
    html = {k: rtext(base / rel) for k, rel in pieces.items()}
    meta = {"name": seg["title"], "rank": seg["rank"], "season": seg["season"], "color": seg["color"]}
    return {"meta": meta, "html": html}

ROLE_FOLDERS = ["reading1", "psalm", "reading2", "gospel"]

def load_local_role_segment(base: Path, role: str):
    seg = yload(base / role / "segment.yaml")
    text = rtext(base / role / seg["paths"]["text_html"])
    resp = seg["paths"].get("response_html")
    return {
        "role": seg.get("role", role.title()),
        "ref_meta": {"reference": seg.get("reference"), "versification": seg.get("versification")},
        "text_html": text,
        "response_html": (rtext(base / role / resp) if resp else None),
        "canonical_id": seg.get("canonical_id"),
    }

def load_lectionary_options(root: Path, day_key: str, cycle_folder: str):
    p = root / f"segments/lectionary/temporal/{day_key}/{cycle_folder}/options.yaml"
    return yload(p) if p.exists() else None

def load_reading_set_meta(root: Path, set_id: str):
    return yload(root / f"segments/reading_sets/{set_id}.yaml")

def load_canonical_reading(root: Path, canonical_id: str):
    base = root / f"segments/readings/{canonical_id}"
    meta = yload(base / "segment.yaml")
    text = rtext(base / meta["paths"]["text_html"])
    resp = meta["paths"].get("response_html")
    return {
        "role": meta.get("role"),
        "ref_meta": {"reference": meta.get("reference"), "versification": meta.get("versification")},
        "text_html": text,
        "response_html": (rtext(base / resp) if resp else None),
    }

def load_readings_for_day(root: Path, day_key: str, sunday: str | None, weekday: str | None, reading_set: str | None):
    cycle_folder = sunday or weekday or ""
    base = root / f"segments/lectionary/temporal/{day_key}/{cycle_folder}"
    # If a set is chosen (or default exists), use it; else load locals by convention.
    opts = load_lectionary_options(root, day_key, cycle_folder)
    if reading_set or (opts and opts.get("sets")):
        chosen = reading_set
        if not chosen and opts and opts.get("sets"):
            chosen = opts["sets"][0]["id"]
        set_meta = load_reading_set_meta(root, chosen)
        out = []
        for item in set_meta["items"]:
            role = item["role"]
            prefer = item.get("prefer", "local")
            if prefer == "canonical" and item.get("canonical_id"):
                rd = load_canonical_reading(root, item["canonical_id"])
                rd["role"] = role
                out.append(rd)
            else:
                # local role under day/cycle
                out.append(load_local_role_segment(base, role))
        return out
    # Fallback: just load whatever local role folders exist
    out = []
    for role in ROLE_FOLDERS:
        if (base / role / "segment.yaml").exists():
            out.append(load_local_role_segment(base, role))
    return out
PY

# resolve.py
cat > builder/resolve.py <<'PY'
from pathlib import Path
from .loaders import yload

def load_common_ordo(root: Path, year: int, conference: str, form: str):
    p = root / f"ordos/{year}/{conference}/{form}.common.yaml"
    return yload(p)

def resolve_date(ordo_map: dict, iso_date: str):
    node = ordo_map["days"].get(iso_date)
    if not node:
        raise SystemExit(f"No ordo entry for {iso_date}")
    primary = node["primary"]
    cycles = primary.get("cycle", {})
    return {
        "label": node.get("label"),
        "day_key": primary["day_key"],
        "variant": primary.get("variant", "day"),
        "sunday": cycles.get("sunday"),
        "weekday": cycles.get("weekday"),
        "default_reading_set": primary.get("default_reading_set"),
    }
PY

# render.py
cat > builder/render.py <<'PY'
from jinja2 import Environment, FileSystemLoader, select_autoescape
from pathlib import Path

def env_for(templates_dir: Path) -> Environment:
    return Environment(
        loader=FileSystemLoader(str(templates_dir)),
        autoescape=select_autoescape(enabled_extensions=("html","xml")),
        trim_blocks=True, lstrip_blocks=True
    )

def render_day(payload: dict, out_path: Path, env: Environment, template="missal_day.html.j2"):
    html = env.get_template(template).render(**payload)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(html, encoding="utf-8")
PY

# cli.py
cat > builder/cli.py <<'PY'
from pathlib import Path
import argparse, yaml
from .resolve import load_common_ordo, resolve_date
from .loaders import load_propers, load_readings_for_day
from .render import env_for, render_day

ROOT = Path(__file__).resolve().parents[1]

def main():
    ap = argparse.ArgumentParser(description="Render a liturgical day")
    ap.add_argument("date", help="ISO date, e.g., 2026-06-23")
    ap.add_argument("--config", default=str(ROOT / "builder.config.yaml"))
    ap.add_argument("--reading-set", help="Override reading set ID")
    args = ap.parse_args()

    cfg = yaml.safe_load(Path(args.config).read_text(encoding="utf-8"))
    ordo = load_common_ordo(ROOT, cfg["year"], cfg["conference"], cfg["form"])
    res = resolve_date(ordo, args.date)

    propers = load_propers(ROOT, res["day_key"], res["variant"])
    chosen_set = args.reading_set or res.get("default_reading_set")
    readings = load_readings_for_day(ROOT, res["day_key"], res["sunday"], res["weekday"], chosen_set)

    env = env_for(ROOT / cfg["templates_dir"])
    cycles = []
    if res.get("sunday"): cycles.append(f"Sunday Cycle {res['sunday']}")
    if res.get("weekday"): cycles.append(f"Weekday Cycle {res['weekday']}")

    payload = {
        "date": args.date,
        "locale": cfg.get("locale","en-US"),
        "calendar_label": res["label"],
        "liturgical_day": {
            "name": propers["meta"]["name"],
            "rank": propers["meta"]["rank"],
            "season": propers["meta"]["season"],
            "color": propers["meta"]["color"],
        },
        "propers": {
            "collect_html": propers["html"]["collect_html"],
            "entrance_html": propers["html"]["entrance_html"],
            "communion_html": propers["html"]["communion_html"],
            "post_communion_html": propers["html"]["post_communion_html"],
        },
        "readings": readings,
        "cycles": " • ".join(cycles) if cycles else None,
        "permissions": None,
    }
    out_path = ROOT / cfg["out_dir"] / f"{args.date}.html"
    render_day(payload, out_path, env)
    print(f"Rendered → {out_path}")

if __name__ == "__main__":
    main()
PY

echo "==> Creating shared template & styles (if missing)..."
# Template
cat > shared/templates/missal_day.html.j2 <<'J2'
<!doctype html>
<html lang="{{ locale }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ liturgical_day.name }} — {{ date }}</title>
  <link rel="stylesheet" href="../styles/large-print.css">
</head>
<body>
  <header>
    <h1>{{ liturgical_day.name }}</h1>
    <p>
      {{ liturgical_day.season }} • {{ liturgical_day.rank }}
      • Colors: {{ liturgical_day.color | join(", ") }}
      {% if cycles %}• {{ cycles }}{% endif %}
    </p>
    {% if calendar_label %}<p><em>{{ calendar_label }}</em></p>{% endif %}
  </header>

  <section aria-labelledby="propers">
    <h2 id="propers">Propers</h2>
    <article aria-labelledby="collect"><h3 id="collect">Collect</h3>{{ propers.collect_html | safe }}</article>
    <article aria-labelledby="entrance"><h3 id="entrance">Entrance</h3>{{ propers.entrance_html | safe }}</article>
    <article aria-labelledby="communion"><h3 id="communion">Communion</h3>{{ propers.communion_html | safe }}</article>
    <article aria-labelledby="post-communion"><h3 id="post-communion">Post-Communion</h3>{{ propers.post_communion_html | safe }}</article>
  </section>

  <section aria-labelledby="readings">
    <h2 id="readings">Lectionary Readings</h2>
    {% for r in readings %}
      <article>
        <h3>{{ r.role | replace('_', ' ') | title }}{% if r.ref_meta.reference %} — {{ r.ref_meta.reference }}{% endif %}</h3>
        {{ r.text_html | safe }}
        {% if r.response_html %}<p><strong>R/</strong> {{ r.response_html | safe }}</p>{% endif %}
      </article>
    {% endfor %}
  </section>
</body>
</html>
J2

# Styles
cat > shared/styles/large-print.css <<'CSS'
html { font-size: 20px; }
body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; line-height: 1.6; margin: 1.25rem; }
h1,h2,h3 { line-height: 1.25; }
article { margin-block: 1rem; }
CSS

echo "==> Creating minimal ORDO entry for 2026-06-23..."
mkdir -p ordos/2026/US
if [ ! -f ordos/2026/US/roman-missal-3e.common.yaml ]; then
  cat > ordos/2026/US/roman-missal-3e.common.yaml <<'YML'
meta:
  id: "US:roman-missal-3e:common:2026"
  calendar: "US"
  form: "Roman Missal, 3rd Ed."
  locale: "en-US"
  defaults:
    sunday_cycle: "C"
    weekday_cycle: "II"
  source:
    name: "Common baseline (demo)"
    license: "internal"
days: {}
YML
fi
# Append or update the day entry
python3 - <<'PY'
import yaml, sys, pathlib
p = pathlib.Path("ordos/2026/US/roman-missal-3e.common.yaml")
doc = yaml.safe_load(p.read_text(encoding="utf-8"))
doc.setdefault("days", {})
doc["days"]["2026-06-23"] = {
  "label": "Tuesday, Week 12 in Ordinary Time (Cycle I)",
  "primary": {
    "day_key": "OT-12-Tue",
    "variant": "day",
    "cycle": {"sunday": None, "weekday": "I"},
    "default_reading_set": "SET.OT-12-Tue.I",
  },
  "overlays": [
    {"day_key": "Sanctoral-03-19-Joseph", "type": "optional-memorial"}
  ],
  "precedence_rule": "Weekday with optional memorial",
}
p.write_text(yaml.safe_dump(doc, sort_keys=False), encoding="utf-8")
print("Updated ordo →", p)
PY

echo "==> Creating minimal PROPERS for OT-12-Tue..."
cat > segments/propers/temporal/OT-12-Tue/segment.yaml <<'YML'
id: "OT-12-Tue"
group: "temporal"
title: "Tuesday of Week 12 in Ordinary Time"
rank: "Weekday"
season: "Ordinary Time"
color: ["green"]
variants:
  default: "day"
  sets:
    day:
      label: "Mass During the Day"
      pieces:
        entrance_html: "day/entrance.html"
        collect_html: "day/collect.html"
        communion_html: "day/communion.html"
        post_communion_html: "day/post-communion.html"
YML

echo "<p>[Entrance Antiphon — placeholder]</p>" > segments/propers/temporal/OT-12-Tue/day/entrance.html
echo "<p>[Collect — placeholder]</p>" > segments/propers/temporal/OT-12-Tue/day/collect.html
echo "<p>[Communion Antiphon — placeholder]</p>" > segments/propers/temporal/OT-12-Tue/day/communion.html
echo "<p>[Post-Communion Prayer — placeholder]</p>" > segments/propers/temporal/OT-12-Tue/day/post-communion.html

echo "Bootstrap complete."
