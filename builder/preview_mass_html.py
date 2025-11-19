from pathlib import Path

from jinja2 import Environment, FileSystemLoader, select_autoescape

from .resolver import Resolver, BuilderContext


def build_mass_html(
    project_root: Path,
    uid: str,
    date: str,
    sunday_cycle: str,
    weekday_cycle: str,
    output_path: Path,
) -> None:
    # 1. Resolve the liturgy into a render_package
    resolver = Resolver(project_root)
    ctx = BuilderContext(
        date=date,
        sunday_cycle=sunday_cycle,
        weekday_cycle=weekday_cycle,
        output_format="html",
        output_profile="celebrant",
    )
    pkg = resolver.resolve(uid, ctx)

    # 2. Load Jinja environment
    templates_root = project_root / "templates" / "web"
    env = Environment(
        loader=FileSystemLoader(str(templates_root)),
        autoescape=select_autoescape(["html", "xml"]),
    )

    template = env.get_template("mass_day_celebrant.html.j2")

    # 3. Render template
    html = template.render(pkg=pkg)

    # 4. Write output
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(html, encoding="utf-8")
    print(f"Wrote {output_path}")


if __name__ == "__main__":
    root = Path(__file__).resolve().parents[1]  # repo root
    out = root / "output" / "advent_02_sunday_celebrant.html"

    build_mass_html(
        project_root=root,
        uid="advent_02_sunday",
        date="2025-12-07",
        sunday_cycle="B",
        weekday_cycle="II",
        output_path=out,
    )
