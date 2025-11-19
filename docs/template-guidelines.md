# Template Guidelines — Missal + Lectionary Builder
**File:** `docs/template-guidelines.md`
**Status:** Authoritative reference
**Purpose:** Defines how all Builder output templates operate, including celebrant vs assembly profiles, filtering logic, required content blocks, layout patterns, and accessibility best practices.

This document contains **no copyrighted liturgical text**.

---

# 1. Introduction

Templates determine how a resolved `render_package` is transformed into:

- HTML (web view)
- EPUB XHTML (EPUB3 output)
- SCML (for eBraille)
- Plain text (for simple export)

The **Resolver** always produces the same structure.
Templates decide **which items to show** and **how to style them** based on:

- `kind` — prayer, reading, rubric, response, dialogue
- `speaker` — presider, deacon, lector, assembly, null
- `output_profile` — celebrant vs assembly

This document standardizes template structure across all outputs.

---

# 2. Template Directory Layout

```
builder/templates/
  web/
    mass_day_celebrant.html.j2
    mass_day_assembly.html.j2
  epub/
    mass_day_celebrant.xhtml.j2
    mass_day_assembly.xhtml.j2
  braille/
    mass_day_celebrant.scml.j2
    mass_day_assembly.scml.j2
  plain/
    mass_day_celebrant.txt.j2
    mass_day_assembly.txt.j2
```

All templates share:

- Structure
- Block names
- Section ordering
- Filtering rules

Only the markup style differs.

---

# 3. Required Template Inputs

All templates receive:

```python
render_package = {
    "meta": {...},
    "context": {...},
    "liturgy": {...}
}
ctx.output_profile == "celebrant" | "assembly"
ctx.output_format == "html" | "scml" | "plain"
```

The template engine must support:

- Conditionals (`if kind == ...`)
- Loops (`for reading in liturgy.readings.items`)
- Safe HTML injection (`{{ text | safe }}`)

---

# 4. Core Template Structure (Recommended)

All templates should follow this high-level structure:

```
1. Header (title, date, season/color)
2. Intro rubrics (optional)
3. Liturgy of the Word
   a. First Reading
   b. Psalm (with response)
   c. Second Reading (if present)
   d. Gospel Acclamation (if implemented)
   e. Gospel
4. Liturgy of the Eucharist
   a. Prayer over the Offerings
   b. Preface (celebrant only)
   c. Optional rubrics
   d. Communion Antiphon
5. Concluding Rites
   a. Prayer after Communion
   b. Final rubrics (if any)
```

For **assembly** templates, sections relating solely to presider prayers are omitted.

---

# 5. Filtering Rules for Templates

This is the most important section.

Templates must filter based on:

- `kind`
- `speaker`
- `output_profile`

## 5.1 Celebrant Profile

`output_profile: "celebrant"`

| kind        | Include? | Notes                          |
|-------------|----------|-------------------------------|
| prayer      | ✔        | Presider prayers               |
| reading     | ✔        | All readings                   |
| response    | ✔        | Include, clearly marked        |
| rubric      | ✔        | Styled as rubrics              |
| dialogue    | ✔        | As designed                    |

## 5.2 Assembly Profile

`output_profile: "assembly"`

| kind        | Include? | Notes                          |
|-------------|----------|-------------------------------|
| prayer      | ✘        | Hidden by default              |
| reading     | ✔        | Always shown                   |
| response    | ✔        | Psalm response, acclamations   |
| rubric      | optional | Normally hidden                 |
| dialogue    | optional | If needed for responses         |

### Assembly exceptions (optional):

- Include Creed (if implemented)
- Include selected responses ("Amen", "And with your spirit")

These can be controlled by future UI settings.

---

# 6. Recommended Styling (HTML/EPUB)

## 6.1 Headings

```
h1 — Title of celebration
h2 — Section header (Liturgy of the Word, etc.)
h3 — Reading type (First Reading)
h4 — Subsections (e.g., Psalm response)
```

## 6.2 Rubrics

Rubrics should be consistently styled across HTML, EPUB, and SCML.

Recommended:

```html
<p class="rubric"><em>[Rubric text]</em></p>
```

CSS should define:

```css
.rubric {
  color: #a00;
  font-style: italic;
}
```

## 6.3 Readings

Each reading block:

```html
<section class="reading reading-first">
  <h3>First Reading</h3>
  <p class="citation">[Citation]</p>
  <div class="reading-text">
    {{ text | safe }}
  </div>
</section>
```

## 6.4 Psalm Response

```html
<p class="psalm-response"><strong>R.</strong> {{ response }}</p>
```

---

# 7. SCML Styling Guidelines (eBraille)

SCML output should preserve:

- Section structure
- Proper newline use
- Indentation
- Rubric markers

Example:

```
<rubric>All stand.</rubric>
<reading role="first" citation="Isaiah 40">
    [Text]
</reading>
<response>R. Lord, hear our prayer.</response>
```

---

# 8. XHTML (EPUB) Guidelines

Follow EPUB 3.2 standards:

- Valid `<html xmlns="http://www.w3.org/1999/xhtml">`
- `<section>` + `<header>` recommended
- Avoid inline styles
- Use semantic classes only

Each celebration becomes one XHTML file in the EPUB spine.

---

# 9. Plain Text Guidelines

Plain text output should:

- Use blank lines between sections
- Prefix rubrics with `*` or `[`brackets`]`
- Prefix readings with section titles in uppercase

Example:

```
FIRST READING
[Citation]
(Text...)

RESPONSORIAL PSALM
R. (response text)
```

---

# 10. Reference: Jinja Template Functions

Templates may use helper functions:

- `format_date(meta, context)` — optional
- `select_preface(liturgy, context)`
- `select_readings(liturgy, context)`

Helper functions may be injected by the Builder.

---

# 11. Template File Naming Conventions

```
mass_day_<profile>.<format>.j2

profile:
  celebrant
  assembly

format:
  html
  xhtml
  scml
  txt
```

Examples:

```
mass_day_celebrant.html.j2
mass_day_assembly.xhtml.j2
mass_day_celebrant.scml.j2
mass_day_assembly.txt.j2
```

---

# 12. Testing Templates

Template test cases must verify:

- Correct filtering based on `output_profile`
- Presence/absence of rubrics
- Year A/B/C selection
- Weekday cycle I/II selection
- Long/short forms if available
- Preface selection rules
- Proper HTML validity

Recommended using pytest + jinja2 Template.

---

# 13. Summary

This document ensures:

- All output templates behave consistently
- Profiles (celebrant vs assembly) are implemented intact
- Filtering rules based on `kind` and `speaker` are standardized
- Templates are accessible, maintainable, and semantic
- All formats (HTML, EPUB, SCML, plain) share one conceptual structure

These guidelines are authoritative for all future template development.

---

# END OF FILE
