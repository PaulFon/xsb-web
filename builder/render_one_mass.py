from pathlib import Path
import copy
import sys

import yaml
from jinja2 import Environment, FileSystemLoader, select_autoescape


def get_by_dotted_path(root: dict, dotted: str):
    """Follow a.b.c path in a nested dict."""
    cur = root
    for part in dotted.split("."):
        if part not in cur:
            raise KeyError(f"Path '{dotted}' not found at '{part}'")
        cur = cur[part]
    return cur


def resolve_refs(node, root):
    """
    Recursively resolve {$ref: "components.collects.lent_1"} into
    deep-copied dicts taken from root.
    """
    if isinstance(node, dict):
        if "$ref" in node and isinstance(node["$ref"], str):
            target = get_by_dotted_path(root, node["$ref"])
            return resolve_refs(copy.deepcopy(target), root)
        return {k: resolve_refs(v, root) for k, v in node.items()}
    elif isinstance(node, list):
        return [resolve_refs(x, root) for x in node]
    else:
        return node


def select_readings(mass: dict, reading_sets: dict) -> list:
    """
    For this first pass, we only support:
      mass.readings.use_set + mass.readings.resolve_with.liturgical_year
    and we pick reading_sets[use_set][f"year_{year}"].
    """
    readings_cfg = mass.get("readings", {})
    if "explicit" in readings_cfg:
        return readings_cfg["explicit"]

    use_set = readings_cfg.get("use_set")
    if not use_set:
        return []

    ctx = readings_cfg.get("resolve_with", {})
    year = ctx.get("liturgical_year")
    if not year:
        raise ValueError("resolve_with.liturgical_year is required when using reading_sets")

    key = f"year_{year}"
    try:
        return reading_sets[use_set][key]
    except KeyError as e:
        raise KeyError(f"Could not find readings for set '{use_set}' and key '{key}'") from e


def render_mass(input_yaml: Path, out_dir: Path, templates_dir: Path):
    # 1) Load YAML
    raw = yaml.safe_load(input_yaml.read_text(encoding="utf-8"))

    # 2) Resolve $ref
    data = resolve_refs(raw, raw)

    # 3) Take the first calendar entry for now
    entries = data.get("calendar", {}).get("entries", [])
    if not entries:
        raise ValueError("No calendar.entries found in YAML")

    entry = entries[0]
    metadata = entry["metadata"]
    mass = entry["mass"]

    # 4) Resolve readings
    reading_sets = data.get("reading_sets", {})
    readings = select_readings(mass, reading_sets)

    # 5) Prepare pieces for the template
    collect = mass.get("collect")
    communion_antiphon = mass.get("communion_antiphon")

    # 6) Jinja environment
    env = Environment(
        loader=FileSystemLoader(str(templates_dir)),
        autoescape=select_autoescape(["html", "xhtml", "xml"])
    )

    template = env.get_template("mass/page.xhtml.j2")

    html = template.render(
        locale=data.get("locale", "en"),
        metadata=metadata,
        mass=mass,
        collect=collect,
        communion_antiphon=communion_antiphon,
        readings=readings,
    )

    # 7) Write output
    out_dir.mkdir(parents=True, exist_ok=True)
    out_path = out_dir / f"{metadata['id']}.xhtml"
    out_path.write_text(html, encoding="utf-8")
    print(f"Wrote {out_path}")


def main():
    if len(sys.argv) < 2:
        print("Usage: python3 builder/render_one_mass.py <input_yaml> [out_dir]")
        sys.exit(1)

    input_yaml = Path(sys.argv[1])
    if len(sys.argv) >= 3:
        out_dir = Path(sys.argv[2])
    else:
        out_dir = Path("out")

    templates_dir = Path("templates")

    render_mass(input_yaml, out_dir, templates_dir)


if __name__ == "__main__":
    main()