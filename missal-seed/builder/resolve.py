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
