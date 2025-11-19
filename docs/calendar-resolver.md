# Calendar Resolver — Missal + Lectionary Builder
**File:** `docs/calendar-resolver.md`
**Status:** Authoritative reference
**Purpose:** Defines the rules, algorithms, and data structures needed to determine:
- What liturgical celebration occurs on a given date
- Whether a feast overrides a weekday
- Whether a vigil applies
- Sunday Cycle (A/B/C)
- Weekday Cycle (I/II)
- Local calendar overlay
- Proper handling of Solemnities, Feasts, Memorials, and Weekdays

This document contains **no copyrighted text**.

---

# 1. Introduction

The **calendar resolver** is responsible for answering the question:

> *“Given a civil date (YYYY-MM-DD), what liturgical celebration is to be used?”*

This includes:

- Seasonal rules
- Feasts with fixed dates
- Movable feasts (Easter-dependent)
- Sunday cycle (A/B/C)
- Weekday cycle (I/II)
- Rank precedence
- Optional memorial logic
- Vigil/day relationships
- Local calendar insertions

The Resolver then selects the correct **Day YAML file**.

This document defines:

- Required data sources
- Decision algorithms
- Precedence ordering
- Extension mechanisms

---

# 2. Directory Layout

Calendar logic lives in:

```
builder/
  calendar_resolver.py
  date_utils.py
  movable_feasts.py
  rank_rules.py
```

For separation of concerns:

- `calendar_resolver.py` → main entry point
- `movable_feasts.py` → compute Easter, Ascension, Pentecost, etc.
- `rank_rules.py` → handles precedence (solemnity > feast > memorial > weekday)
- `date_utils.py` → helpers

---

# 3. Core Concepts

## 3.1 Liturgical Rank (Highest → Lowest)

| Rank          | Precedence #
|---------------|-------------|
| solemnity     | 1           |
| feast         | 2           |
| sunday        | 2 (but seasonal rules apply) |
| memorial      | 3           |
| optional memorial | 4       |
| weekday       | 5           |

Lower number = higher importance.

This is used for override logic.

## 3.2 Seasonal Structure

The calendar resolver must know:

- Advent
- Christmas Season
- Ordinary Time (split into two parts)
- Lent
- Easter Season

And transitions between them.

## 3.3 Movable Feasts

The resolver must compute dates for:

- Easter Sunday
- Ash Wednesday
- Pentecost
- Ascension (Thursday or Sunday option)
- Trinity Sunday
- Corpus Christi
- Sacred Heart
- Christ the King
- First Sunday of Advent

These determine seasonal boundaries.

## 3.4 Cycles

### Sunday Cycle (A/B/C)
Determined by liturgical year (not calendar year):

```
Cycle A: Year mod 3 == 0
Cycle B: Year mod 3 == 1
Cycle C: Year mod 3 == 2
```

Liturgical year begins on **First Sunday of Advent**.

### Weekday Cycle (I/II)

```
If (liturgical_year is odd) → Cycle I
If (liturgical_year is even) → Cycle II
```

---

# 4. Inputs and Outputs

## 4.1 Input to calendar resolver

```
- date (YYYY-MM-DD)
- locale (e.g., "us")
```

## 4.2 Output of calendar resolver

```python
{
  "uid": "advent_02_sunday",         # Which YAML file to load
  "rank": "sunday",
  "rank_precedence": 2,
  "season": "advent",
  "cycle": {
      "sunday": "B",
      "weekday": "II"
  },
  "is_vigil": false,
  "is_optional_memorial": false,
  "local_overrides": ["st-patrick", ...]
}
```

This output becomes the seed for `BuilderContext`.

---

# 5. Date Resolution Algorithm (Step-by-Step)

The resolver follows this sequence:

---

## Step 1 — Compute Movable Feasts (Easter-dependent)

Use `movable_feasts.py`:

- Easter (computus)
- Lent start
- Pentecost
- Ascension
- Holy Week dates
- First Sunday of Advent
- Split Ordinary Time (# weeks before & after Lent)

---

## Step 2 — Identify if the date is a **Sunday**

If `date.weekday() == 6`:

- It's a Sunday
- Sunday automatically overrides memorials & weekdays
- Solemnities and feasts of the Lord may override Sundays
- Set `rank = "sunday"`

---

## Step 3 — Check for **fixed-date celebrations**

Check the date (month/day) against fixed-date YAML files, e.g.:

- st-francis-of-assisi (Oct 4)
- holy-family (Sunday after Christmas; special rule)

If found:

- Load metadata including rank
- Continue to precedence resolution

---

## Step 4 — Apply **Seasonal Rules**

Some days get special celebration types based purely on season:

- Weekdays of Advent
- Lenten weekdays
- Christmas Octave
- Easter Octave

These may override or adapt the rank.

---

## Step 5 — Check for **movable feasts**

Using Easter-related tables:

- Trinity Sunday
- Corpus Christi
- Christ the King

If the date matches:

- Rank = feast (or solemnity)
- Precedence applied

---

## Step 6 — Precedence Comparison

When multiple celebrations fall on the same date:

1. Pick the highest-rank celebration
2. If equal rank:
   - Prefer Proper over Common
   - Prefer local over general (if local is implemented)
3. If tie persists:
   - Follow official tables of precedence
   - (To be implemented in future full engine)

---

## Step 7 — Vigil Logic

If the date is **the evening before** a solemnity or feast:

- Set `is_vigil: true`
- UID becomes `xxx_vigil` if a Vigil YAML exists

Example:

```
date = Saturday PM before Pentecost Sunday
→ UID = "pentecost_vigil"
```

Implementation:

```
If tomorrow is a solemnity AND time >= 16:00:
    return vigil UID
```

Time-of-day is optional; date-only builds may ignore this and rely on explicit selection.

---

## Step 8 — Weekday Logic (fallback)

If nothing else matched:

- Determine “Nth Week of Ordinary Time / Advent / Lent / Easter”
- Determine weekday name
- UID becomes:

```
weekday-<week>-<season>-<weekday>.yaml
```

Example:

```
weekday-26-ordinary-thursday.yaml
```

---

# 6. Cycle Computation

## 6.1 Determine liturgical year

```
If date >= First Sunday of Advent:
    liturgical_year = date.year + 1
Else:
    liturgical_year = date.year
```

## 6.2 Sunday Cycle

```
cycle_index = liturgical_year % 3

0 → A
1 → B
2 → C
```

## 6.3 Weekday Cycle

```
If liturgical_year is odd → Cycle I
If even → Cycle II
```

---

# 7. Local Calendar Overrides

Local calendars build on top of the general calendar.

Directory:

```
calendar/local/us_bishops.yaml
calendar/local/archdiocese_of_washington.yaml
calendar/local/religious_orders/franciscans.yaml
```

Each layer may:

- Insert new celebrations
- Change rank
- Change precedence
- Change texts or proper readings

Resolver applies local calendars in ascending order of specificity:

```
General → National → Regional → Diocesan → Religious → Parish
```

Example override:

```yaml
- date: "03-17"
  uid: "st-patrick"
  rank: "solemnity"
  region: "archdiocese_of_new_york"
```

---

# 8. Output to BuilderContext

The calendar resolver produces a normalized package:

```python
result = {
  "uid": "advent_02_sunday",
  "rank": "sunday",
  "rank_precedence": 2,
  "season": "advent",
  "color": ["violet"],
  "cycle": {
      "sunday": "B",
      "weekday": "II"
  },
  "is_vigil": False,
  "overrides": []
}
```

BuilderContext then merges:

- this output
- user-selected options
- template preferences

into the final context object used by the Resolver.

---

# 9. Validation Rules

Calendar resolver must validate:

- UID exists in `missal/days/`
- Date is within supported range
- Movable feast computations succeed
- Cycles computed correctly
- Rank precedence resolved cleanly
- Vigil rules applied correctly
- Local calendars merged without conflict

Errors produce meaningful Python exceptions.

---

# 10. Future Enhancements

This architecture supports:

- Holy Day of Obligation rules
- Multiple vigil options
- Adaptations for US, Canada, UK, Australia
- Proper-of-time / Proper-of-saints merge logic
- Multiple locales (text selection in catalogs)
- Full Lectionary Number → UID cross-reference table
- Universal calendar synchronization with USCCB

---

# 11. Summary

The Calendar Resolver:

- Determines the correct celebration for any date
- Correctly applies precedence rules
- Handles Sunday, weekday, feast, memorial logic
- Computes Sunday Cycle & Weekday Cycle
- Supports Vigils
- Allows layered local calendars
- Provides input for the final Resolver and Builder

It is a critical component ensuring the Builder produces accurate liturgical output.

---

# END OF FILE
