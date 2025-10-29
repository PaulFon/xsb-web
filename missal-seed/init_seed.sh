#!/usr/bin/env bash
# init_seed.sh
# Creates the initial missal-seed scaffold

set -e

echo "Creating core directories..."
mkdir -p builder ordos/2026/US segments/{propers,lectionary,reading_sets,readings} shared/{templates,styles}

# Minimal files
cat > requirements.txt <<'EOF'
jinja2
pydantic>=2
PyYAML
EOF

cat > builder.config.yaml <<'EOF'
conference: "US"
form: "roman-missal-3e"
year: 2026
locale: "en-US"
templates_dir: "shared/templates"
out_dir: "dist/html"
EOF

mkdir -p dist/html

echo "Creating .gitignore..."
cat > .gitignore <<'EOF'
__pycache__/
*.pyc
.venv/
dist/
EOF

echo "Seed scaffold created under missal-seed/"
