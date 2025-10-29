#!/usr/bin/env python3
"""
Build one Mass page into mass_site/public-build/YYYY-MM-DD.html

Usage:
  python3 scripts/build-mass.py --date 2025-10-26
"""
import os
import sys
import argparse
from pathlib import Path

import yaml
from jinja2 import Environment, FileSystemLoader, select_autoescape

# --- Paths
BASE_DIR = Path(__file__).resolve().parents[1]
TEMPLATE_DIR = BASE_DIR / "mass_site" / "templates"
OUTPUT_DIR = BASE_DIR / "mass_site" / "public-build"
CONTENT_DIR = BASE_DIR / "mass_site" / "content"
DATE_INDEX = CONTENT_DIR / "shared" / "notes" / "date_index.yaml"

# --- Jinja2 env
env = Environment(
    loader=FileSystemLoader(str(TEMPLATE_DIR)),
    autoescape=select_autoescape(["html", "xml"]),
)

def uid_from_date(date_str: str) -> str:
    """Return UID for a date from date_index.yaml, or sensible fallback."""
    if DATE_INDEX.exists():
        with DATE_INDEX.open("r", encoding="utf-8") as f:
            data = yaml.safe_load(f) or {}
        if isinstance(data, dict) and date_str in data:
            return str(data[date_str])
    # Fallback UID if not in index
    return f"{date_str}-roman-sunday"

def render_day(date_str: str, uid: str) -> Path:
    """
    Render one day using templates/mass_day.html.
    Minimal context for now; expand as we wire real content.
    """
    template = env.get_template("mass_day.html")

    # You can adjust canonical_url to match your preferred URL pattern.
    # If your vhost DocumentRoot is /home/bitnami/htdocs/wwwroot and pages live under /mass/,
    # this produces /mass/YYYY/MM/DD/ style. Today we emit flat YYYY-MM-DD.html, which works
    # with both patterns.
    context = {
        "missal": {"date": date_str, "uid": uid},
        "canonical_url": f"/mass/{date_str.replace('-', '/')}/",
    }

    html = template.render(**context)

    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    out_path = OUTPUT_DIR / f"{date_str}.html"
    out_path.write_text(html, encoding="utf-8")
    print(f"Built {out_path}")
    return out_path

def main() -> int:
    parser = argparse.ArgumentParser(description="Build one Mass page.")
    parser.add_argument("--date", required=True, help="YYYY-MM-DD")
    args = parser.parse_args()

    date_str = args.date.strip()
    uid = uid_from_date(date_str)
    render_day(date_str, uid)
    return 0

if __name__ == "__main__":
    sys.exit(main())
