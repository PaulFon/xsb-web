# Developer Roadmap — Missal + Lectionary Builder
**File:** `docs/developer-roadmap.md`
**Status:** Authoritative reference
**Purpose:** Provide a clear, staged implementation plan for the entire Missal + Lectionary Builder, including milestone breakdown, component dependencies, developer workflow, and future expansions.

This roadmap contains **no copyrighted text**.

---

# 1. Introduction

The Missal + Lectionary Builder is a multi-stage project that will ultimately support:

- Generating complete celebrant booklets
- Generating worship aids for assembly
- EPUB, HTML, plain text, and eBraille output
- Integration with a full liturgical calendar resolver
- UI-based option selection (preface, EP, forms)
- Bulk importers for Lectionary XML
- Local calendar overlays
- Future: parish-level deployment and diocesan partnerships

This roadmap breaks the project into discrete, implementable phases.

---

# 2. Dependency Graph (What Must Exist First)

```
┌─────────┐
│Catalogs │  (content)
└─────┬───┘
      │
┌─────▼────────────────┐
│Day YAML Schema       │  (metadata only; references only)
└─────┬────────────────┘
      │
┌─────▼─────────────┐
│Resolver           │  (merge YAML + catalogs + context)
└─────┬─────────────┘
      │
┌─────▼──────────┐
│render_package  │  (canonical output model)
└─────┬──────────┘
      │
┌─────▼────────┐
│Templates     │  (celebrant & assembly)
└─────┬────────┘
      │
┌─────▼─────────┐
│Calendar Logic │  (pick correct YAML)
└─────┬─────────┘
      │
┌─────▼────────┐
│CLI / UI      │
└──────────────┘
```

Everything downstream depends on:

- Catalog format
- Day YAML format
- Resolver contract

These are now fully specified.

---

# 3. Roadmap Overview (Phases)

### Phase 1 — **Schemas & Documentation (DONE)**
(You now have 6 authoritative documents.)

### Phase 2 — **Catalog Infrastructure + Resolver Implementation**
(Implement code-level infrastructure.)

### Phase 3 — **Template Development**
(Create celebrant and assembly templates.)

### Phase 4 — **Minimal CLI + Output Build**
(Get HTML outputs working for some sample Masses.)

### Phase 5 — **Calendar Resolver Implementation**
(Matches dates to YAML uids; automates cycle logic.)

### Phase 6 — **Bulk Importers (Lectionary, Missal)**
(Long-term but essential.)

### Phase 7 — **UI Builder (Future UI/UX Work)**
(Select options, build booklets.)

### Phase 8 — **EPUB and SCML pipelines**
(Advanced outputs.)

### Phase 9 — **Parish Deployment and API**
(Far future; stable enough for production use.)

Each phase has sub-milestones below.

---

# 4. Phase Details

---

# Phase 2 — Catalog & Resolver (Start Here)

## 2.1 Implement the Catalog Loader

`builder/catalog_loader.py`

Features:
- Load all `.yaml` files under `catalog/`
- Index entries by `id`
- Validate metadata (`kind`, `speaker`)
- Provide method:

```python
def get(id: str, format: str = "html") -> CatalogEntry:
```

## 2.2 Implement the Day YAML Loader

- Validate `schema: xsb.missal.day.v2`
- Ensure metadata completeness
- Ensure all `ref` entries are syntactically valid ID strings

## 2.3 Implement the Resolver

`builder/resolver.py`

Normalize:
- Collect block
- Antiphons
- Readings (ABC, long/short/sequences)
- Preface
- Rubrics
- Notes

Must produce a **complete `render_package`** exactly matching the spec in:

```
docs/builder-architecture.md
```

## 2.4 Implement Override Injection

Support:

```
overrides = {
  "preface": "...",
  "reading_forms": {...}
}
```

## 2.5 Implement Basic Validation

- Invalid catalog ref
- Missing formats
- Missing kind/speaker
- Invalid cycle keys
- Incomplete YAML structures

**End of Phase 2 milestone:**
👉 The Resolver can successfully resolve 2–3 example Masses into full `render_package` objects.

---

# Phase 3 — Template Development

## 3.1 Create Celebrant Template

`templates/web/mass_day_celebrant.html.j2`

Must show:
- All prayers
- All readings
- All rubrics
- Optional dialogues
- Section headers

## 3.2 Create Assembly Template

`templates/web/mass_day_assembly.html.j2`

Must show:
- Readings
- Psalm response
- Key assembly responses
- No presider prayers
- No preface text
- No rubrics (default)

## 3.3 Hook Up Template Engine

Use Jinja:

```python
jinja_env.get_template("mass_day_celebrant.html.j2")
```

## 3.4 Make Starter CSS (Optional)

A minimal stylesheet makes development easier.

**End of Phase 3 milestone:**
👉 First usable HTML output for celebrant & assembly.

---

# Phase 4 — CLI Integration

## 4.1 Implement CLI command

`build-mass`

```
build-mass --uid advent_02_sunday --date 2025-12-07 --profile celebrant --format html
```

## 4.2 CLI Output Directory Structure

```
output/
  mass/
    advent_02_sunday/
      celebrant.html
      assembly.html
```

## 4.3 Build Logging & Debug Mode

- Show selected cycle
- Show selected readings
- Show override usage
- Show missing catalog entries

**End of Phase 4 milestone:**
👉 CLI reliably builds HTML for multiple Masses.

---

# Phase 5 — Calendar Resolver

## 5.1 Compute Movable Feasts
(`movable_feasts.py`)

## 5.2 Identify Sundays
(override memorials and weekdays)

## 5.3 Identify Fixed-Date Feasts
(e.g. St. Francis → Oct 4)

## 5.4 Apply Precedence Logic
(solemnity > feast > memorial > weekday)

## 5.5 Determine Vigil
(if next day is solemnity & time >= 16:00)

## 5.6 Integrate Local Calendars

## 5.7 Integrate with CLI

```
build-mass --date 2026-03-12
```

No UID required.

**End of Phase 5 milestone:**
👉 The entire calendar year can be built using only the civil date.

---

# Phase 6 — Bulk Importers (Advanced)

## 6.1 Lectionary XML → Readings Catalog
- Parse lectionary numbers
- Map to ID namespaces
- Validate cycle structure

## 6.2 Missal XML / DOCX → Collects, Antiphons, Prefaces
- Import text from licensed sources
- Split into catalog entries
- Ensure copyright compliance

**End of Phase 6 milestone:**
👉 Catalogs populated automatically.

---

# Phase 7 — Web UI Builder (Optional Future Work)

Features:

- Calendar picker
- Edit selection (preface, EP, long/short)
- “Build Celebrant Booklet”
- “Build Assembly Handout”
- Save/export options

Built on top of:

```
builder/api/
builder/views/
builder/templates/web/
```

---

# Phase 8 — EPUB and Braille Output

## 8.1 EPUB XHTML Templates
- Ensure valid spine order
- Create TOC
- Embed CSS

## 8.2 SCML Templates
- Tag rubrics appropriately
- Ensure braille line width consistency

## 8.3 Packaging
- Zip → .epub
- Validate with epubcheck

**End of Phase 8 milestone:**
👉 Outputs ready for XSB’s production workflow.

---

# Phase 9 — Production Deployment & API (Future)

- Containerize Builder (Docker)
- Expose build API endpoint
- Integrate with parish websites
- User accounts & authenticated builds
- Integration with XSB braille workflow

---

# 5. Suggested Branch and Commit Structure

```
main/
  docs/
  prototype/
develop/
  feature/catalog-loader/
  feature/resolver-core/
  feature/templates-html/
  feature/calendar-resolver/
  feature/cli-build/
```

Keep `main` stable with working builds.

---

# 6. What a Minimum Viable Product Includes

Minimum working system:

- Hand-written catalogs for:
  - A few Sundays (ABC)
  - A weekday cycle I/II
  - One feast with proper text
- Resolver implemented
- Celebrant & Assembly HTML templates
- CLI command working

That gives instant value for testing and internal use.

---

# 7. Long-Term Vision

Once fully built, the system should:

- Generate liturgical booklets for ANY date
- Support multiple locales
- Generate EPUB3 for digital accessibility
- Generate SCML for eBraille
- Allow UI-based customization
- Provide API endpoints for parishes/dioceses
- Integrate with XSB’s production pipeline

This roadmap ensures the architecture can scale to that level.

---

# END OF FILE
