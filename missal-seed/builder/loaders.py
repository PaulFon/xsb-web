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
