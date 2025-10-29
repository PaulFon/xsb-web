# CanonicalEye.org

Code and resources for all projects related to **Canonical Eye**.

## Documentation

- [Daily Workflow](docs/Daily-Workflow.txt) – quick daily checklist (sync, edit, build, commit, deploy).
- [HOWTO: Edit → Sync → Deploy](docs/HOWTO-Edit-Sync-Deploy.txt) – full detailed guide.

---

This repository supports multiple sub-projects:
- **WEB/WIKI** – deployed to `/home/bitnami/htdocs/wwwroot` and `/home/bitnami/htdocs/wiki`.
- **MASS** – built with Python (Jinja2 + YAML) and deployed to `/home/bitnami/htdocs/mass`.
- **ALL** – combined deploy for both WEB/WIKI and MASS.

Deployment scripts support:
- `--dry-run` preview
- `--git` flag to force Git even in dry runs
- `-m "message"` commit messages
