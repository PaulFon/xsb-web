# Day YAML Examples — Missal + Lectionary Builder
**File:** `docs/day-yaml-examples.md`
**Status:** Authoritative reference
**Purpose:** Provide complete and realistic example YAML files using `xsb.missal.day.v2`.

This document contains **no copyrighted liturgical text**.
All text is represented by catalog reference IDs.

---

# 1. Overview

This file provides high-quality examples demonstrating:

- **Simple weekday**
- **Feast overriding weekday (with optional celebrant choice)**
- **Sunday with ABC cycle**
- **Vigil + Day pair (separate YAML files)**
- **Rubrics embedded in a day file**
- **Optional reading forms (long/short)**

All examples follow the canonical structure defined in:

```
docs/schema-overview.md
docs/catalog-conventions.md
```

---

# 2. Example 1 — Simple Weekday (Ordinary Time)

**Filename:**

```
missal/days/weekdays/weekday-26-ordinary-thursday.yaml
```

```yaml
version: 2
schema: xsb.missal.day.v2

meta:
  uid: "weekday-26-ordinary-thursday"
  title: "Thursday of the 26th Week in Ordinary Time"
  calendar: "roman_general"
  rank: "weekday"
  rank_precedence: 12
  season: "ordinary"
  color: ["green"]

  date_rule:
    month: null            # Determined by date in BuilderContext
    day: null

  lectionary:
    weekday_cycle: "II"
    sunday_cycle: null
    numbers:
      first: 458
      psalm: 459
      gospel: 460

  missal:
    preface_of: "preface:ordinary:iv"

liturgy:
  celebration_type: "weekday"

  collect:
    id: "collect"
    label: "Collect"
    options:
      - code: "default"
        matches: {}
        ref: "collects:ordinary:week_26_thursday"

  readings:
    pattern: "weekday"
    items:
      - role: "first"
        ref: "readings:ot_26_thursday:cycle_ii:first"

      - role: "psalm"
        ref: "readings:ot_26_thursday:cycle_ii:psalm"

      - role: "gospel"
        ref: "readings:ot_26_thursday:cycle_ii:gospel"

  preface:
    selection: "automatic"

  prayer_over_the_offerings:
    options:
      - code: "default"
        matches: {}
        ref: "collects:offerings:ordinary:weekday"

  communion_antiphon:
    options:
      - code: "default"
        matches: {}
        ref: "antiphon:communion:weekday:ordinary"

  prayer_after_communion:
    options:
      - code: "default"
        matches: {}
        ref: "collects:after_communion:ordinary:weekday"
```

---

# 3. Example 2 — Feast that Overrides a Weekday
(and includes **rubrics**)

**Filename:**

```
missal/days/feasts/st-francis-of-assisi.yaml
```

```yaml
version: 2
schema: xsb.missal.day.v2

meta:
  uid: "st-francis-of-assisi"
  title: "Saint Francis of Assisi"
  calendar: "roman_general"
  rank: "feast"
  rank_precedence: 3
  season: "ordinary"
  color: ["white"]

  date_rule:
    month: 10
    day: 4

  lectionary:
    sunday_cycle: null
    weekday_cycle: "II"
    numbers:
      first: 650
      psalm: 651
      gospel: 652

  missal:
    preface_of: "preface:holy-men-women:ii"
    common_of: "common:pastors"

liturgy:
  celebration_type: "feast"

  collect:
    id: "collect"
    label: "Collect"
    options:
      - code: "default"
        ref: "collects:saints:francis:collect"

  readings:
    pattern: "proper"
    items:
      - role: "first"
        ref: "readings:saints:francis:first"

      - role: "psalm"
        ref: "readings:saints:francis:psalm"

      - role: "gospel"
        ref: "readings:saints:francis:gospel"

  preface:
    selection: "explicit"
    ref: "preface:holy-men-women:ii"

  # Rubric before the Gospel
  rubric_before_gospel:
    id: "rubric-before-gospel"
    label: "Rubric"
    options:
      - code: "default"
        ref: "rubric:intro-stand"

  prayer_over_the_offerings:
    options:
      - code: "default"
        ref: "collects:offerings:saints:general"

  communion_antiphon:
    options:
      - code: "default"
        ref: "antiphon:communion:saints:francis"

  prayer_after_communion:
    options:
      - code: "default"
        ref: "collects:after_communion:saints:general"
```

---

# 4. Example 3 — Sunday with ABC Cycle Variations

**Filename:**

```
missal/days/sundays/advent_02_sunday.yaml
```

```yaml
version: 2
schema: xsb.missal.day.v2

meta:
  uid: "advent_02_sunday"
  title: "Second Sunday of Advent"
  calendar: "roman_general"
  rank: "sunday"
  rank_precedence: 2
  season: "advent"
  color: ["violet"]

  date_rule:
    sunday_of_advent: 2

  lectionary:
    sunday_cycle: "B"      # Defaults overridden by BuilderContext
    weekday_cycle: null
    numbers:
      first: 5
      psalm: 6
      second: 7
      gospel: 8

liturgy:
  celebration_type: "sunday"

  collect:
    options:
      - code: "default"
        ref: "collects:advent:2_sunday"

  readings:
    pattern: "sunday_abc"
    items:
      - role: "first"
        year_a: "readings:advent_2:year_a:first"
        year_b: "readings:advent_2:year_b:first"
        year_c: "readings:advent_2:year_c:first"

      - role: "psalm"
        year_a: "readings:advent_2:year_a:psalm"
        year_b: "readings:advent_2:year_b:psalm"
        year_c: "readings:advent_2:year_c:psalm"

      - role: "second"
        year_a: "readings:advent_2:year_a:second"
        year_b: "readings:advent_2:year_b:second"
        year_c: "readings:advent_2:year_c:second"

      - role: "gospel"
        year_a: "readings:advent_2:year_a:gospel"
        year_b: "readings:advent_2:year_b:gospel"
        year_c: "readings:advent_2:year_c:gospel"

  preface:
    selection: "seasonal"
    seasonal_options:
      - code: "advent-i"
        ref: "preface:advent:i"
      - code: "advent-ii"
        ref: "preface:advent:ii"

  communion_antiphon:
    options:
      - code: "default"
        ref: "antiphon:communion:advent_2_sunday:default"

  prayer_over_the_offerings:
    options:
      - code: "default"
        ref: "collects:offerings:advent"

  prayer_after_communion:
    options:
      - code: "default"
        ref: "collects:after_communion:advent"
```

---

# 5. Example 4 — Vigil and Day (Separate Files)

## 4.1 Vigil Mass

**Filename:**

```
missal/days/solemnities/pentecost_vigil.yaml
```

```yaml
version: 2
schema: xsb.missal.day.v2

meta:
  uid: "pentecost_vigil"
  title: "Pentecost Sunday — Vigil Mass"
  calendar: "roman_general"
  rank: "solemnity"
  rank_precedence: 1
  season: "easter"
  color: ["red"]

  date_rule:
    relative_to_easter: "pentecost_vigil"

  lectionary:
    sunday_cycle: "B"
    numbers:
      first: 62
      psalm: 63
      gospel: 64

liturgy:
  celebration_type: "vigil"

  collect:
    options:
      - code: "default"
        ref: "collects:pentecost:vigil"

  readings:
    pattern: "vigil_multiple_options"
    items:
      - role: "first"
        ref: "readings:pentecost:vigil:first"

      - role: "psalm"
        ref: "readings:pentecost:vigil:psalm"

      - role: "gospel"
        long_form: "readings:pentecost:vigil:gospel_long"
        short_form: "readings:pentecost:vigil:gospel_short"

  preface:
    selection: "explicit"
    ref: "preface:pentecost"

  communion_antiphon:
    options:
      - code: "default"
        ref: "antiphon:communion:pentecost"

  prayer_over_the_offerings:
    options:
      - code: "default"
        ref: "collects:offerings:pentecost"

  prayer_after_communion:
    options:
      - code: "default"
        ref: "collects:after_communion:pentecost"
```

---

## 4.2 Day Mass

**Filename:**

```
missal/days/solemnities/pentecost_day.yaml
```

```yaml
version: 2
schema: xsb.missal.day.v2

meta:
  uid: "pentecost_day"
  title: "Pentecost Sunday — Day Mass"
  calendar: "roman_general"
  rank: "solemnity"
  rank_precedence: 1
  season: "easter"
  color: ["red"]

  date_rule:
    relative_to_easter: "pentecost"

  lectionary:
    sunday_cycle: "B"
    numbers:
      first: 62
      psalm: 63
      second: 64
      gospel: 65

liturgy:
  celebration_type: "solemnity"

  collect:
    options:
      - code: "default"
        ref: "collects:pentecost:day"

  readings:
    pattern: "sunday_abc"
    items:
      - role: "first"
        year_a: "readings:pentecost:year_a:first"
        year_b: "readings:pentecost:year_b:first"
        year_c: "readings:pentecost:year_c:first"

      - role: "psalm"
        year_a: "readings:pentecost:year_a:psalm"
        year_b: "readings:pentecost:year_b:psalm"
        year_c: "readings:pentecost:year_c:psalm"

      - role: "second"
        year_a: "readings:pentecost:year_a:second"
        year_b: "readings:pentecost:year_b:second"
        year_c: "readings:pentecost:year_c:second"

      - role: "gospel"
        year_a: "readings:pentecost:year_a:gospel"
        year_b: "readings:pentecost:year_b:gospel"
        year_c: "readings:pentecost:year_c:gospel"

  preface:
    selection: "explicit"
    ref: "preface:pentecost"

  communion_antiphon:
    options:
      - code: "default"
        ref: "antiphon:communion:pentecost"

  prayer_over_the_offerings:
    options:
      - code: "default"
        ref: "collects:offerings:pentecost"

  prayer_after_communion:
    options:
      - code: "default"
        ref: "collects:after_communion:pentecost"
```

---

# 6. Summary

These examples demonstrate:

- Simple weekday
- Feast with its own texts
- Sunday with ABC cycle control
- Vigil + Day (separate YAML files, same feast)
- Rubric references
- Optional long/short reading forms

They represent the **recommended style and structure** for all future YAML files in the Missal + Lectionary Builder.

---

# END OF FILE
