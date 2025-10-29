#!/usr/bin/env bash
# init_OT12Tue.sh
# Scaffold for OT-12-Tue (Cycle I) with St. Joseph alternative

set -e

BASE="segments/lectionary/temporal/OT-12-Tue/I"

echo "Creating directory structure..."
mkdir -p "$BASE/reading1" "$BASE/psalm" "$BASE/gospel"
mkdir -p segments/reading_sets
mkdir -p segments/readings   # only if you want the global canonical library

echo "Creating lectionary options file..."
cat > "$BASE/options.yaml" <<'EOF'
id: "LEC.OT-12-Tue.I"
day_key: "OT-12-Tue"
cycle: { sunday: null, weekday: "I" }
sets:
  - id: "SET.OT-12-Tue.I"
    label: "Week 12 OT Tuesday (Cycle I)"
  - id: "SET.Sanctoral-03-19-Joseph"
    label: "St. Joseph (alt)"
EOF

echo "Creating reading1 files..."
cat > "$BASE/reading1/segment.yaml" <<'EOF'
role: "First Reading"
reference: "2 Kings 19:9b-11, 14-21, 31-35a, 36"
versification: "NABRE"
canonical_id: null
paths:
  text_html: "text.html"
EOF

echo "<p>[OT-12 Tuesday (I) — First Reading placeholder text]</p>" > "$BASE/reading1/text.html"

echo "Creating psalm files..."
cat > "$BASE/psalm/segment.yaml" <<'EOF'
role: "Responsorial Psalm"
reference: "Psalm 48:2-3ab, 3cd-4, 10-11"
versification: "NABRE"
canonical_id: null
paths:
  text_html: "text.html"
  response_html: "response.html"
EOF

echo "<p>[OT-12 Tuesday (I) — Psalm verses placeholder]</p>" > "$BASE/psalm/text.html"
echo "<p>God upholds his city forever.</p>" > "$BASE/psalm/response.html"

echo "Creating gospel files..."
cat > "$BASE/gospel/segment.yaml" <<'EOF'
role: "Gospel"
reference: "Matthew 7:6, 12-14"
versification: "NABRE"
canonical_id: null
paths:
  text_html: "text.html"
EOF

echo "<p>[OT-12 Tuesday (I) — Gospel placeholder text]</p>" > "$BASE/gospel/text.html"

echo "Creating reading set definitions..."
cat > segments/reading_sets/SET.OT-12-Tue.I.yaml <<'EOF'
id: "SET.OT-12-Tue.I"
label: "Week 12 OT Tuesday (I)"
items:
  - role: "reading1"
    prefer: "local"
  - role: "psalm"
    prefer: "local"
  - role: "gospel"
    prefer: "local"
EOF

cat > segments/reading_sets/SET.Sanctoral-03-19-Joseph.yaml <<'EOF'
id: "SET.Sanctoral-03-19-Joseph"
label: "St. Joseph, Spouse of the BVM"
items:
  - role: "reading1"
    prefer: "canonical"
    canonical_id: "READING.2Sam-7-4_5a_12-14a_16.NABRE"
  - role: "psalm"
    prefer: "canonical"
    canonical_id: "READING.Ps-89-2-3_4-5_27_29.NABRE"
  - role: "gospel"
    prefer: "canonical"
    canonical_id: "READING.Mt-1-16_18-21_24a.NABRE"
EOF

echo "Done! Structure for OT-12-Tue (I) created."
