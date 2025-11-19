# Builder Architecture — Missal + Lectionary Builder
**File:** `docs/builder-architecture.md`
**Status:** Authoritative reference
**Purpose:** Defines the full Builder pipeline, including the Resolver, context system, override logic, render_package format, directory layout, and CLI behavior.

---

# 1. Introduction

The Builder is the core processing system that transforms:

1. **Day YAML (`missal/days/*`)**
2. **Catalog entries (`catalog/*`)**
3. **BuilderContext**
4. **Optional override selections**

into a unified, normalized object called the **render_package**, which is then used by output templates (HTML, EPUB, SCML, plain).

This document explains:

- The Builder’s responsibilities
- How the Resolver works
- How catalog lookups are performed
- How year/cycle/option selection works
- The override system
- The output profiles (celebrant / assembly)
- The CLI interface

No copyrighted text is included.

---

# 2. High-Level Pipeline

```
Day YAML  →  Resolver  →  render_package  →  Template  →  Output file
Catalogs       ^                                         (HTML/EPUB/SCML/TXT)
               |
         Overrides
          Context
```

The Resolver is the engine that merges:

- Date-specific rules
- Lectionary cycles
- Options (preface, readings, forms)
- Catalog text
- Rubrics
- Metadata (`kind`, `speaker`)
- Overrides

into a single structured object.

---

# 3. BuilderContext

All builds are governed by `BuilderContext`.

```python
@dataclass
class BuilderContext:
    date: date
    sunday_cycle: str | None = None      # "A", "B", "C"
    weekday_cycle: str | None = None     # "I", "II"
    locale: str = "us"
    mass_form: str = "day"               # "day", "vigil", "dawn", etc.
    output_format: str = "html"          # "html", "xhtml", "scml", "plain"
    output_profile: str = "celebrant"    # "celebrant" or "assembly"
```

The **calendar resolver** (future component) will fill in:

- correct Sunday cycle
- correct weekday cycle
- automatic feast/weekday detection

For now, these values are passed manually or inferred.

---

# 4. Resolver Overview

The Resolver:

1. Loads the YAML file for the selected UID
2. Normalizes metadata
3. Selects correct readings based on:
   - year A/B/C
   - weekday cycle I/II
   - vigil/day
   - long/short forms (if overridden)
4. Selects correct Collect, Preface, Antiphon, etc.
5. Loads text from **catalogs**
6. Attaches semantic metadata:
   - `kind`
   - `speaker`
   - flags like `assembly_response`
7. Produces a unified `render_package`

The Resolver must not format text — only structure it.

---

# 5. Resolver Responsibilities (Detailed)

## 5.1 Load Day YAML

```python
yaml_data = yaml.safe_load(open(day_file))
```

Validate required fields:

- `meta.uid`
- `liturgy.collect`
- `liturgy.readings`

## 5.2 Merge Context Rules

For example:

- If context provides year B → choose year_b references
- If the YAML provides explicit year cycles → override default
- If vigil/day distinction applies → choose correct file

## 5.3 Resolve Option Blocks

Blocks like Collects follow:

```yaml
collect:
  options:
    - code: "default"
      matches: {}
      ref: "collects:advent:2_sunday"
```

Resolver picks:

- the first matching option
- or overridden option from UI (“celebrant chooses…”)
- or fallback `"default"`

## 5.4 Reading resolution

Reading entries may have:

### Type A — Simple reference

```
ref: "readings:ot_26_thursday:cycle_ii:first"
```

### Type B — Year A/B/C options

```
role: "first"
year_a: "readings:advent_2:year_a:first"
year_b: "readings:advent_2:year_b:first"
year_c: "readings:advent_2:year_c:first"
```

### Type C — Long/Short forms

```
gospel:
  long_form: "readings:gospel_long"
  short_form: "readings:gospel_short"
```

Resolver uses:

- BuilderContext
- override selections
- YAML’s own explicit selection

## 5.5 Lookup Catalogs

The Resolver loads the catalog entry:

```python
catalog_entry = catalog_lookup("readings:advent_2:year_b:first")
```

It extracts:

- metadata.kind
- metadata.speaker
- metadata.flags
- formats (html/plain/scml)

Then inserts the **correct format** into the `render_package`.

## 5.6 Attach Rubrics

Rubric blocks behave like Collect blocks:

```
rubric_before_gospel:
  ref: "rubric:intro-stand"
```

Rubrics:

- are never spoken
- must have `kind: rubric`
- appear in celebrant output
- may be hidden in assembly output

---

# 6. Override System

Overrides allow the celebrant (via future UI) to choose:

- Preface
- Eucharistic Prayer
- Long/short Gospel form
- Optional readings
- Common vs Proper

Example override dict:

```python
overrides = {
  "preface": "preface:advent:ii",
  "reading_forms": {
     "gospel": "short"
  }
}
```

Resolver steps:

1. Detect override keys
2. Apply them before catalog lookup
3. Merge into `render_package.context.selected_options`

Overrides never mutate YAML, only resolved output.

---

# 7. render_package (Full Specification)

The Resolver produces a Python dictionary like:

```json
{
  "meta": {
    "uid": "advent_02_sunday",
    "title": "Second Sunday of Advent",
    "season": "advent",
    "color": ["violet"],
    "rank": "sunday",
    "rank_precedence": 2
  },

  "context": {
    "date": "2025-12-07",
    "weekday_cycle": "II",
    "sunday_cycle": "B",
    "mass_form": "day",
    "output_profile": "assembly",
    "overrides": {}
  },

  "liturgy": {
    "collect": {
      "label": "Collect",
      "ref": "collects:advent:2_sunday",
      "text": "<p>...</p>",
      "kind": "prayer",
      "speaker": "presider"
    },

    "readings": {
      "pattern": "sunday_abc",
      "cycle_key": "B",
      "items": [
        {
          "role": "first",
          "citation": "...",
          "ref": "readings:advent_2:year_b:first",
          "text": "<p>...</p>",
          "kind": "reading",
          "speaker": "lector"
        },
        {
          "role": "psalm",
          "citation": "...",
          "psalm_response": "...",
          "assembly_response": true,
          "ref": "readings:advent_2:year_b:psalm",
          "text": "<p>...</p>",
          "kind": "reading",
          "speaker": "lector"
        }
      ]
    },

    "preface": {
      "ref": "preface:advent:i",
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
    }
  }
}
```

Templates rely on this structure.

---

# 8. Output Profile Logic

Templates use `output_profile` to filter:

## 8.1 Celebrant Profile

Show:

- all prayers
- all readings
- all antiphons
- all rubrics
- all dialogues
- all responses

## 8.2 Assembly Profile

Show:

- readings
- psalm response
- assembly responses
- optional: minimal common prayers (future UI)

Hide:

- presider prayers
- preface text
- Eucharistic Prayer (later)
- rubrics (default)

---

# 9. Error Handling and Validation

Builder must validate:

- Missing `ref` entries
- Invalid catalog IDs
- Missing catalog files
- Unknown `year_a`/`year_b`/`year_c` combinations
- Invalid override options
- Required `kind`/`speaker` metadata

All failures should produce clear Python exceptions.

---

# 10. CLI Structure

Command:

```
build-mass --uid <uid> --date <YYYY-MM-DD> --profile celebrant --format html
```

Future options will include:

- `--override preface=preface:advent:ii`
- `--override reading_form:gospel=short`
- `--list-options` (print all selectable elements)

Recommended structure:

```
builder/
  cli_day_builder.py
  resolver.py
  catalog_loader.py
  templates/
```

Example CLI output directory:

```
output/
  mass/
    advent_02_sunday/
      celebrant.html
      assembly.html
      celebrant.epub
      celebrant.scml
```

---

# 11. Performance Considerations

- Catalogs should be preloaded or cached in memory
- Resolver should avoid reloading YAML for repeated builds
- Use hashed lookups for catalog IDs
- Template rendering is lightweight and fast

---

# 12. Future Extensions

This architecture supports:

- Local calendars
- Optional rites
- Extended rubrics
- Multiple preface forms
- Multiple EP choices
- Blessed/Memorial/Solemnity adaptations
- Full Lectionary XML importer (USCCB)
- Full Roman Missal importer
- Real-time celebrant UI

---

# 13. Summary

The Builder architecture:

- Normalizes all liturgical structure
- Encodes every text with semantic metadata (kind/speaker)
- Separates metadata (Day YAML) from content (Catalogs)
- Provides a unified `render_package` for templates
- Supports overrides and selectable options
- Powers both Celebrant and Assembly outputs
- Is designed for long-term extensibility

This document is the authoritative reference for the entire Builder pipeline.

---

# END OF FILE
