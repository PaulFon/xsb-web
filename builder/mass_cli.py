from pathlib import Path
import argparse
import sys

from render_one_mass import render_mass


def find_yaml_for_date(date_str: str, base_dir: Path) -> Path:
    """
    Look for files in missal/days/ named like:
      YYYY-MM-DD_*.yaml
    and return the first match.
    """
    pattern = f"{date_str}_*.yaml"
    matches = list((base_dir / "missal" / "days").glob(pattern))

    if not matches:
        raise FileNotFoundError(
            f"No YAML file found for date {date_str!r} "
            f"with pattern missal/days/{pattern}"
        )

    if len(matches) > 1:
        # You can tighten this later (e.g., pick by year, season, etc.)
        print(
            f"Warning: multiple YAML files found for {date_str}: "
            f"{', '.join(str(m) for m in matches)}",
            file=sys.stderr,
        )

    return matches[0]


def main(argv=None):
    parser = argparse.ArgumentParser(
        description="Render a Mass XHTML page for a given calendar date."
    )
    parser.add_argument(
        "-d", "--date",
        required=True,
        help="Calendar date in YYYY-MM-DD format (e.g. 2025-03-09).",
    )
    parser.add_argument(
        "-o", "--out-dir",
        default="public/mass",
        help="Output directory for rendered XHTML (default: public/mass).",
    )
    parser.add_argument(
        "--templates-dir",
        default="templates",
        help="Templates directory (default: templates).",
    )

    args = parser.parse_args(argv)

    root = Path(".").resolve()
    out_dir = root / args.out_dir
    templates_dir = root / args.templates_dir

    try:
        yaml_path = find_yaml_for_date(args.date, root)
    except FileNotFoundError as e:
        print(str(e), file=sys.stderr)
        sys.exit(1)

    # Reuse the existing render_mass helper from render_one_mass.py
    render_mass(yaml_path, out_dir, templates_dir)


if __name__ == "__main__":
    main()