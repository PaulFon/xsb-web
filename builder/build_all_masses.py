from pathlib import Path
import argparse
import sys
import traceback

from render_one_mass import render_mass


def find_yaml_files(days_dir: Path):
    """
    Yield all *.yaml files in the given directory, sorted by name.
    """
    return sorted(days_dir.glob("*.yaml"))


def main(argv=None):
    parser = argparse.ArgumentParser(
        description="Render XHTML for all Mass YAML files under missal/days/."
    )
    parser.add_argument(
        "--days-dir",
        default="missal/days",
        help="Directory containing day YAML files (default: missal/days).",
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
    parser.add_argument(
        "--fail-fast",
        action="store_true",
        help="Stop on first error instead of continuing.",
    )

    args = parser.parse_args(argv)

    root = Path(".").resolve()
    days_dir = root / args.days_dir
    out_dir = root / args.out_dir
    templates_dir = root / args.templates_dir

    if not days_dir.exists():
        print(f"ERROR: days directory not found: {days_dir}", file=sys.stderr)
        sys.exit(1)

    yaml_files = find_yaml_files(days_dir)
    if not yaml_files:
        print(f"No YAML files found in {days_dir}", file=sys.stderr)
        sys.exit(1)

    print(f"Found {len(yaml_files)} YAML file(s) in {days_dir}")
    print(f"Output directory: {out_dir}")
    print(f"Templates directory: {templates_dir}")
    print()

    successes = 0
    failures = []

    for yaml_path in yaml_files:
        rel = yaml_path.relative_to(root)
        print(f"Rendering {rel} ...")

        try:
            render_mass(yaml_path, out_dir, templates_dir)
            successes += 1
        except Exception as e:
            print(f"  ERROR rendering {rel}: {e}", file=sys.stderr)
            traceback.print_exc()
            failures.append((yaml_path, e))
            if args.fail_fast:
                break

    print()
    print(f"Done. Successfully rendered {successes} file(s).")

    if failures:
        print(f"{len(failures)} file(s) failed:", file=sys.stderr)
        for path, err in failures:
            rel = path.relative_to(root)
            print(f"  - {rel}: {err}", file=sys.stderr)
        sys.exit(1)

    sys.exit(0)


if __name__ == "__main__":
    main()