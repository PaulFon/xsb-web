#!/usr/bin/env python3
from pathlib import Path
import argparse, yaml
from .resolve import load_common_ordo, resolve_date
from .loaders import load_propers, load_readings_for_day, load_reading_set_meta
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

    # Load propers
    propers = load_propers(ROOT, res["day_key"], res["variant"])

    # Determine reading set (override or default)
    chosen_set = args.reading_set or res.get("default_reading_set")

    # Load readings
    readings = load_readings_for_day(
        ROOT, res["day_key"], res["sunday"], res["weekday"], chosen_set
    )

    # Lookup chosen set label (for subheader)
    chosen_set_label = None
    if chosen_set:
        try:
            chosen_meta = load_reading_set_meta(ROOT, chosen_set)
            chosen_set_label = chosen_meta.get("label")
        except Exception:
            chosen_set_label = None  # keep going even if missing

    env = env_for(ROOT / cfg["templates_dir"])

    cycles = []
    if res.get("sunday"):
        cycles.append(f"Sunday Cycle {res['sunday']}")
    if res.get("weekday"):
        cycles.append(f"Weekday Cycle {res['weekday']}")

    payload = {
        "date": args.date,
        "locale": cfg.get("locale", "en-US"),
        "calendar_label": res["label"],         # legacy field
        "ordo": {"label": res["label"]},        # new field used by template
        "chosen_set_label": chosen_set_label,   # shows "St. Joseph ..." when alt is chosen
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