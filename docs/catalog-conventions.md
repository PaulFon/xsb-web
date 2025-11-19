# Catalog Conventions — Missal + Lectionary Builder
**File:** `docs/catalog-conventions.md`
**Status:** Authoritative reference
**Purpose:** Defines the structure, naming rules, metadata conventions, and directory layout for all catalog text files.

---

# 1. Introduction

Catalogs store **all actual liturgical text**, in multiple formats (HTML, plain, SCML).
Day YAML files **never** contain text themselves — they contain **references** to catalog IDs.

This document defines:

- Folder structure
- Catalog filename patterns
- Entry ID namespaces
- Metadata fields (`kind`, `speaker`, etc.)
- Text formats (`plain`, `html`, `scml`)
- Conventions for variants (Year A/B/C, Optional forms, long/short, etc.)

This file contains no copyrighted text.

---

# 2. Catalog Directory Layout

All catalogs live under:

```
catalog/
  collects/
  antiphons/
  prefaces/
  readings/
  rubrics/
  ep/                    # Eucharistic Prayers (future)
  commons/               # Common of Saints, Pastors, Virgins, etc.
```

Each subfolder contains multiple `.yaml` files.

Example:

```
catalog/collects/advent.yaml
catalog/readings/ordinary_time_10.yaml
catalog/rubrics/general.yaml
```

Each file may contain *multiple* catalog entries.

---

# 3. Catalog Entry Structure

Every catalog entry uses the same format:

```yaml
- id: "collects:advent:2_sunday"
  title: "Second Sunday of Advent"
  metadata:
    kind: "prayer"            # prayer | reading | response | rubric | dialogue
    speaker: "presider"       # presider | deacon | lector | assembly | dialogue | null
    assembly_response: false  # optional helper flag
    applies_to: null          # used only for rubrics
  formats:
    plain: |
      ...
    html: |
      <p>...</p>
    scml: |
      ...
```

All fields in this structure are mandatory **except** format variants you do not need yet (SCML may be omitted for now).

---

# 4. Namespace Rules for IDs

Every ID must be namespaced:

```
collects:advent:2_sunday
antiphon:communion:easter_sunday:default
readings:advent_2:year_b:gospel
rubric:intro-stand
preface:advent:i
common:martyrs:common_text_1
```

### Namespace format

```
<category>:<subgroup>:<unique_key>
```

Where:

| Category      | Meaning                           |
|---------------|-----------------------------------|
| collects      | Opening prayers, etc.             |
| antiphon      | Entrance/communion antiphons      |
| readings      | First, Psalm, Second, Gospel      |
| preface       | Prefaces of the Missal            |
| rubric        | Rubrics                           |
| ep            | Eucharistic Prayers               |
| common        | Commons (Pastors, Virgins, etc.)  |

### Required conventions

- Use lowercase only
- Use underscores for readability
- Never include dates or cycles in the root ID unless necessary
- Year/cycle information lives in the subgroup (e.g., `year_b`)

---

# 5. Metadata Conventions

## 5.1 `kind`

`kind` indicates what type of text this is.

| kind        | meaning                                       |
|-------------|-----------------------------------------------|
| prayer      | Presider prayers (Collect, etc.)              |
| reading     | Scriptural readings                           |
| response    | Assembly responses                            |
| rubric      | Instruction, not spoken                        |
| dialogue    | Combined call/response                         |

## 5.2 `speaker`

Indicates **who speaks** the text.

| speaker     | meaning                     |
|-------------|-----------------------------|
| presider    | Priest                      |
| deacon      | Deacon                      |
| lector      | Lector / reader             |
| assembly    | People                      |
| dialogue    | Mixed                       |
| null        | Rubric or non-spoken text   |

### Core rule

- `kind: "rubric"` must always have `speaker: null`.

## 5.3 Helper metadata

Optional helpers:

- `assembly_response: true/false` — for responsorial psalms
- `applies_to:` — for rubrics (e.g., “assembly”, “presider”, “all”)
- `seasonal_variant:` — optional marker for seasonal texts

---

# 6. Text Formats

Each entry may include one or more of:

```
formats:
  plain:
  html:
  scml:
```

## 6.1 `plain`

- Plain text, line breaks allowed
- No markup, no HTML
- Used for exporting to plain formats or debugging

## 6.2 `html`

- Safe HTML markup allowed
- Must be valid and minimal (avoid inline styles)
- Used for web and EPUB rendering

## 6.3 `scml`

- Future-proof for eBraille
- Should follow simplified SCML conventions
- If not yet available, omit this format

At least **one** format must be supplied.

---

# 7. Readings Catalog Conventions

Readings have special rules.

Example entry:

```yaml
- id: "readings:advent_2:year_b:first"
  role: "first"
  citation: "Isaiah 40"
  lectionary_number: 5
  metadata:
    kind: "reading"
    speaker: "lector"
  formats:
    plain: |
      ...
    html: |
      <p>...</p>
```

## 7.1 Required reading fields

- `role` (first, psalm, second, gospel, sequence)
- `citation`
- `lectionary_number`

## 7.2 Long/short forms

Use:

```
id: "readings:advent_2:year_b:gospel"
forms:
  - code: "long"
    ref: "readings:advent_2:year_b:gospel_long"
  - code: "short"
    ref: "readings:advent_2:year_b:gospel_short"
```

Or store both in the same catalog file.

---

# 8. Rubrics Catalog Conventions

Rubrics are simple:

```yaml
- id: "rubric:intro-stand"
  title: "All stand"
  metadata:
    kind: "rubric"
    speaker: null
    applies_to: "assembly"
  formats:
    html: "<p><em>All stand.</em></p>"
```

Rules:

- `kind: rubric`
- `speaker: null`
- Avoid formatting beyond italics/semantic tags

---

# 9. Prefaces Catalog Conventions

Prefaces often come in numbered series:

```
preface:advent:i
preface:advent:ii
preface:christmas:i
preface:christmas:ii
```

Each contains:

- Dialogue (if stored in catalog)
- Proper preface text
- Optional seasonal variants

---

# 10. Antiphons Catalog Conventions

Antiphons use variants based on day + season.

Example:

```
antiphon:communion:easter_sunday:default
antiphon:communion:easter_sunday:year_a
```

Stored under:

```
catalog/antiphons/easter.yaml
```

Variants use the same structure as collects.

---

# 11. Common Texts

Commons go under:

```
catalog/commons/
```

IDs:

```
common:pastors:prayer_1
common:virgins:preface
common:martyrs:reading_option_b
```

---

# 12. File Naming Guidelines

- Use lowercase
- Underscores allowed
- Avoid excessively long filenames
- Group entries logically in each file

Examples:

```
collects/advent.yaml
collects/ordinary_time_10.yaml
readings/easter_vigil.yaml
rubrics/general.yaml
prefaces/advent.yaml
```

---

# 13. Summary

Catalog conventions define:

- Predictable ID namespaces
- Clean folder structure
- Common metadata for text type and speaker
- Multiple formats (plain, HTML)
- Extensible reading/antiphon variants
- First-class rubrics
- Proper organization for Prefaces, Commons, EPs

Catalog structure is foundational for all Builder operations.

---

# END OF FILE
