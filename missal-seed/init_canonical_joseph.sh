#!/usr/bin/env bash
set -e

# Canonical folders
mkdir -p segments/readings/READING.2Sam-7-4_5a_12-14a_16.NABRE
mkdir -p segments/readings/READING.Ps-89-2-3_4-5_27_29.NABRE
mkdir -p segments/readings/READING.Mt-1-16_18-21_24a.NABRE

# 1) First Reading: 2 Sam 7:4-5a, 12-14a, 16
cat > segments/readings/READING.2Sam-7-4_5a_12-14a_16.NABRE/segment.yaml <<'YML'
id: "READING.2Sam-7-4_5a_12-14a_16.NABRE"
role: "reading1"
reference: "2 Samuel 7:4-5a, 12-14a, 16"
versification: "NABRE"
paths:
  text_html: "text.html"
  response_html: null
YML
echo "<p>[St. Joseph alt — First Reading placeholder]</p>" > segments/readings/READING.2Sam-7-4_5a_12-14a_16.NABRE/text.html

# 2) Psalm: Ps 89:2-3, 4-5, 27, 29
cat > segments/readings/READING.Ps-89-2-3_4-5_27_29.NABRE/segment.yaml <<'YML'
id: "READING.Ps-89-2-3_4-5_27_29.NABRE"
role: "psalm"
reference: "Psalm 89:2-3, 4-5, 27, 29"
versification: "NABRE"
paths:
  text_html: "text.html"
  response_html: "response.html"
YML
echo "<p>[St. Joseph alt — Psalm verses placeholder]</p>" > segments/readings/READING.Ps-89-2-3_4-5_27_29.NABRE/text.html
echo "<p>The son of David will live for ever.</p>" > segments/readings/READING.Ps-89-2-3_4-5_27_29.NABRE/response.html

# 3) Gospel: Mt 1:16, 18-21, 24a
cat > segments/readings/READING.Mt-1-16_18-21_24a.NABRE/segment.yaml <<'YML'
id: "READING.Mt-1-16_18-21_24a.NABRE"
role: "gospel"
reference: "Matthew 1:16, 18-21, 24a"
versification: "NABRE"
paths:
  text_html: "text.html"
  response_html: null
YML
echo "<p>[St. Joseph alt — Gospel placeholder]</p>" > segments/readings/READING.Mt-1-16_18-21_24a.NABRE/text.html

echo "Canonical St. Joseph readings created."
