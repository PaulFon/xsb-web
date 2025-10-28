#!/usr/bin/env python3
"""
Static builder: YAML (Missal + Lectionary) → HTML (Jinja2)
- Build by UID or by date (via date_index.yaml)
- Inlines HTML snippets for readings/propers if present
"""

import argparse
import pathlib
import sys
import yaml
from jinja2 import Environment, FileSystemLoader, select_autoescape

# --- Paths --------------------------------------------------------------------
ROOT = pathlib.Path(__file__).resolve().parents[1]
MASS = ROOT / "mass_site"
CONTENT = MASS / "content"
TPL = MASS / "templates"
OUT = MASS / "public-build"
INDEX = CONTENT / "shared" / "notes" / "index.yaml"
DATE_INDEX = CONTENT / "shared" / "notes" / "date_index.yaml"
PRIVATE = MASS / "content_private"

# --- Helpers ------------------------------------------------------------------
def load_yaml(p: pathlib.Path):
    with open(p, "r", encoding="utf-8") as f:
        return yaml.safe_load(f)

def read_html_if_exists(path: pathlib.Path):
    return path.read_text(encoding="utf-8").strip() if path.exists() else None

def pericope_html_path(osis_id: str) -> pathlib.Path:
    return PRIVATE / "pericopes" / "usccb" / f"{osis_id}.html"

def missal_html_path(text_id: str) -> pathlib.Path:
    return PRIVATE / "missal" / "icel" / f"{text_id}.html"

def path_from_uid(kind: str, uid: str) -> pathlib.Path:
    index = load_yaml(INDEX) or {}
    rel = (index.get(kind) or {}).get(uid)
    if not rel:
        raise FileNotFoundError(f"No {kind} entry for UID: {uid}")
    return ROOT / rel

def uid_from_date(datestr: str) -> str:
    if not DATE_INDEX.exists():
        raise FileNotFoundError(f"Missing date index: {DATE_INDEX}")
    data = load_yaml(DATE_INDEX) or {}
    uid = data.get(str(datestr))
    if not uid:
        raise SystemExit(f"No UID mapped for date {datestr} in {DATE_INDEX}")
    return uid

# --- Core build ---------------------------------------------------------------
def render_day(missal_uid: str, lectionary_uid: str, output_name: str):
    missal = load_yaml(path_from_uid("missal", missal_uid))
    lectionary = load_yaml(path_from_uid("lectionary", lectionary_uid))

    # Attach full Missal texts if available
    props = missal.get("propers", {}) or {}
    for key in ["collect", "prayer_over_offerings", "prayer_after_communion"]:
        node = props.get(key)
        if isinstance(node, dict) and node.get("id"):
            html = read_html_if_exists(missal_html_path(node["id"]))
            if html: node["html"] = html

    # Attach full readings if available
    for r in lectionary.get("readings", []) or []:
        osis = r.get("osis")
        if osis:
            html = read_html_if_exists(pericope_html_path(osis))
            if html: r["html"] = html

    # Metadata
    date_str = str(missal.get("date", ""))
    page = {
        "title": f"Mass for {missal.get('title','')} (Year {lectionary.get('year','')})",
        "description": "Readings and propers for the day.",
        "canonical_url": f"/mass/{date_str.replace('-', '/')}/" if date_str else "/mass/",
    }

    env = Environment(
        loader=FileSystemLoader(str(TPL)),
        autoescape=select_autoescape(["html"]),
    )
    tmpl = env.get_template("page.mass.html")
    html_out = tmpl.render(page=page, missal=missal, readings=lectionary.get("readings", []))

    OUT.mkdir(parents=True, exist_ok=True)
    out_path = OUT / output_name
    out_path.write_text(html_out, encoding="utf-8")
    print(f"Built {out_path}")

# --- CLI ----------------------------------------------------------------------
def main(argv=None):
    parser = argparse.ArgumentParser(description="Build Mass page(s).")
    parser.add_argument("--uid", help="UID to build (must exist in index.yaml).")
    parser.add_argument("--out", help="Output filename (e.g., 2025-10-26.html).")
    parser.add_argument("--date", help="Build by date (YYYY-MM-DD) using date_index.yaml.")
    args = parser.parse_args(argv)

    if args.date:
        uid = uid_from_date(args.date)
        out = args.out or f"{args.date}.html"
        render_day(uid, uid, out)
        return 0

    if args.uid:
        if not args.out:
            print("Error: --out is required when using --uid", file=sys.stderr)
            return 2
        render_day(args.uid, args.uid, args.out)
        return 0

    # Default example
    render_day(
        "2025-10-26-roman-sunday-30-ot",
        "2025-10-26-roman-sunday-30-ot",
        "2025-10-26.html",
    )
    return 0

if __name__ == "__main__":
    raise SystemExit(main())