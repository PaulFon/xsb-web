# Missal + Lectionary Builder — Schema & Architecture Overview (v1)
**File:** `docs/schema-overview.md`
**Status:** Authoritative reference
**Purpose:** Canonical schema, directory layout, catalog conventions, rubrics model, resolver output model, builder profiles, and template behavior.

---

# 1. Introduction

This document defines the core architecture of the Missal + Lectionary Builder:

- YAML structure for liturgical celebrations
- Catalog system for storing full liturgical texts
- Semantic tagging for spoken text vs rubrics
- Builder profiles (celebrant vs assembly)
- Directory layout
- The Resolver → `render_package` data contract

This file contains **no copyrighted text**.

---

# 2. Directory Architecture

```
canonical_eye/
  missal/
    days/              # One celebration per YAML
      solemnities/
      feasts/
      memorials/
      weekdays/
    packs/             # Optional groupings; not used by Builder
  catalog/             # Full text content
    collects/
    antiphons/
    prefaces/
    readings/
    rubrics/
    ep/                # Eucharistic Prayers (future)
  builder/
    resolver.py
    cli_day_builder.py
    templates/
      web/
        mass_day_celebrant.html.j2
        mass_day_assembly.html.j2
      epub/
      braille/
  docs/
    schema-overview.md  ← YOU ARE HERE
```

**Important Rule:**
Each liturgical **celebration** lives in its **own YAML file** under `missal/days/`.

---

# 3. Celebration YAML Schema (`xsb.missal.day.v2`)

Each day file has:

- `meta` — metadata
- `liturgy` — structured references to catalog texts

Example structure:

```yaml
version: 2
schema: xsb.missal.day.v2

meta:
  uid: "st-francis-of-assisi"
  title: "Saint Francis of Assisi"
  calendar: "roman_general"
  rank: "feast"                      # weekday | memorial | feast | solemnity
  rank_precedence: 3                 # lower = higher priority
  season: "ordinary"
  color: ["white"]
  date_rule: {...}

  lectionary:
    sunday_cycle: null               # A/B/C
    weekday_cycle: null              # I/II
    numbers: {...}

  missal:
    preface_of: "preface:holy-men-women:ii"
    common_of: "common:pastors"

  audio_braille: {...}
  local_calendar: {...}
  is_obligatory: false               # optional

liturgy:
  celebration_type: "feast"

  collect: {...}
  readings: {...}
  preface: {...}
  communion_antiphon: {...}
  prayer_over_the_offerings: {...}
  prayer_after_communion: {...}

  rubric_before_gospel:
    id: "rubric-before-gospel"
    label: "Rubric Before Gospel"
    options:
      - code: "default"
        matches: {}
        ref: "rubric:intro-stand"
```

---

# 4. Catalog System

Catalogs store the **actual liturgical texts**.

Locations:

```
catalog/
  collects/*.yaml
  antiphons/*.yaml
  readings/*.yaml
  prefaces/*.yaml
  rubrics/*.yaml
  ep/*.yaml
```

Standard catalog entry format:

```yaml
id: "collects:advent:2_sunday"
title: "Second Sunday of Advent"
metadata:
  kind: "prayer"            # prayer | reading | response | rubric | dialogue
  speaker: "presider"       # presider | deacon | lector | assembly | dialogue | null
  assembly_response: false  # optional helper
  applies_to: null          # for rubrics
formats:
  plain: |
    ...
  html: |
    <p>...</p>
  scml: |
    ...
```

Resolver includes `metadata.kind` and `metadata.speaker` in the `render_package`.

---

# 5. Semantic Tagging

## 5.1 `kind` — What type of text?

| kind        | meaning                                       |
|-------------|-----------------------------------------------|
| `prayer`    | Collect, Preface, Offerings, Communion        |
| `reading`   | First, Psalm, Second, Gospel, Sequence        |
| `response`  | People’s responses                            |
| `rubric`    | Instruction (not spoken)                      |
| `dialogue`  | Combined call/response                        |

## 5.2 `speaker` — Who says it?

| speaker       | meaning                     |
|---------------|-----------------------------|
| `presider`    | Priest                      |
| `deacon`      | Deacon                      |
| `lector`      | Lector                      |
| `assembly`    | People                      |
| `dialogue`    | Combined response           |
| `null`        | Non-spoken (rubrics)        |

These tags allow templates to decide what to display.

---

# 6. Rubrics

Rubrics are stored as catalog entries.

Example:

```yaml
rubrics:
  - id: "rubric:intro-stand"
    title: "All stand"
    metadata:
      kind: "rubric"
      speaker: null
      applies_to: "assembly"
    formats:
      plain: "All stand."
      html: "<p><em>All stand.</em></p>"
```

Rubrics appear in the **celebrant** output; optional in the **assembly** output.

---

# 7. Builder Profiles

Builder supports two profiles:

## 7.1 Celebrant Profile
`output_profile: "celebrant"`

Includes:

- All prayers
- All rubrics
- All antiphons
- All readings
- Dialogues
- (Future) Eucharistic Prayers

Produces the **full Missal text**.

## 7.2 Assembly Profile
`output_profile: "assembly"`

Includes:

- Readings (lectors)
- Assembly responses (psalm refrain, acclamations)
- Optional common prayers
- Rubrics hidden by default
- Presider/deacon prayers hidden

Produces a **worship aid**.

All profiles operate on the same `render_package`.

---

# 8. BuilderContext

```python
@dataclass
class BuilderContext:
    date: date
    sunday_cycle: str | None = None      # A/B/C
    weekday_cycle: str | None = None     # I/II
    locale: str = "us"
    mass_form: str = "day"               # day, vigil, dawn, etc.
    output_format: str = "html"          # html, plain, scml
    output_profile: str = "celebrant"    # celebrant or assembly
```

---

# 9. Resolver Output: `render_package`

This is the structured object templates render.

Example:

```json
{
  "meta": {...},
  "context": {...},
  "liturgy": {
    "collect": {
      "id": "collect",
      "label": "Collect",
      "ref": "collects:advent:2_sunday",
      "text": "<p>...</p>",
      "kind": "prayer",
      "speaker": "presider"
    },
    "rubric_before_gospel": {
      "ref": "rubric:intro-stand",
      "text": "<p><em>All stand.</em></p>",
      "kind": "rubric",
      "speaker": null,
      "applies_to": "assembly"
    },
    "readings": {
      "pattern": "sunday_abc",
      "cycle_key": "B",
      "items": [
        {
          "role": "first",
          "citation": "...",
          "ref": "readings:advent_2:year_b:first",
          "kind": "reading",
          "speaker": "lector",
          "text": "<p>...</p>"
        },
        {
          "role": "psalm",
          "citation": "...",
          "ref": "readings:advent_2:year_b:psalm",
          "kind": "reading",
          "speaker": "lector",
          "assembly_response": true,
          "psalm_response": "...",
          "text": "<p>...</p>"
        }
      ]
    }
  }
}
```

---

# 10. Templates

Templates determine what to show based on:

- `output_profile`
- `kind`
- `speaker`
- helper flags (`assembly_response`)

## 10.1 Celebrant Template

Shows everything:

- prayers
- rubrics
- readings
- antiphons
- dialogues

## 10.2 Assembly Template

Shows:

- readings
- psalm response
- optional responses

Hides:

- rubrics
- presider prayers
- Eucharistic Prayer
- Preface text

---

# 11. Future Option Selection (UI-Compatible)

The system supports celebrant choice:

- Preface (`allowed_references`)
- Eucharistic Prayer (`ep.allowed`)
- Reading form (long/short)
- Feast vs Weekday (calendar resolver)
- Alternative antiphons

Overrides are passed via:

```python
overrides = {
  "preface": "preface:holy-men-women:i",
  "eucharistic_prayer": "ep:iii",
  "reading_form": {"gospel": "short"}
}
```

Resolver can apply overrides before building the `render_package`.

---

# 12. Naming Conventions

## 12.1 YAML filenames match `meta.uid`

```
missal/days/feasts/st-francis-of-assisi.yaml
missal/days/solemnities/easter_sunday_day.yaml
missal/days/weekdays/weekday-26-ordinary-thursday.yaml
```

## 12.2 Catalog IDs use namespaces

```
collects:advent:2_sunday
readings:advent_2:year_b:first
antiphon:communion:easter_sunday:default
rubric:intro-stand
```

---

# 13. Summary

This architecture enables:

- Full Missal output for the **celebrant**
- Worship aid output for the **assembly**
- Clear modeling of rubrics and spoken text
- Clean metadata/text separation via catalogs
- Semantic tagging for flexible template logic
- Long-term extensibility (EPs, options, local calendars)
- Predictable and maintainable YAML + Python workflow

This is the authoritative schema and builder overview for the project.

---

# END OF FILE
