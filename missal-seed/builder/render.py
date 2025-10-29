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
