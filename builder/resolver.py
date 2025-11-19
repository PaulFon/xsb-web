"""
Resolver — Missal + Lectionary Builder

This module transforms:

    - Day YAML metadata (no copyrighted text)
    - Catalog entries (text and metadata)
    - BuilderContext (cycles, date, profile)
    - Optional overrides

Into a normalized `render_package` for use by templates.

This is a **minimal scaffold** that resolves:
    - meta (title/uid)
    - collect block (single reference)
    - catalog lookup (html/plain)

Future development will add:
    - readings (ABC, I/II, long/short)
    - antiphons
    - preface selection
    - dialogue roles
    - rubrics
    - everything else in the full schema
"""

from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, Optional

import yaml

from .catalog_loader import CatalogIndex


# ------------------------------------------------------------------------------
# BuilderContext (minimal version)
# ------------------------------------------------------------------------------

@dataclass
class BuilderContext:
    date: Optional[str] = None
    sunday_cycle: Optional[str] = None   # A/B/C
    weekday_cycle: Optional[str] = None  # I/II
    mass_form: str = "day"
    output_profile: str = "celebrant"
    output_format: str = "html"
    overrides: Dict[str, Any] = None

    def __post_init__(self):
        if self.overrides is None:
            self.overrides = {}


# ------------------------------------------------------------------------------
# Resolver class
# ------------------------------------------------------------------------------

class Resolver:
    def __init__(self, project_root: Path):
        self.project_root = project_root
        self.catalog_index = CatalogIndex.from_project_root(project_root)

    # ------------------------------------------------------------------
    # Public API
    # ------------------------------------------------------------------

    def resolve(self, uid: str, ctx: BuilderContext) -> Dict[str, Any]:
        """
        Main entry point.

        Steps:
        - Load day YAML metadata
        - Normalize metadata
        - Resolve Collect block
        - Return a minimal `render_package`
        """

        day_yaml = self._load_day_yaml(uid)

        # Build core structure of the render_package
        render_package = {
            "meta": self._resolve_meta(day_yaml),
            "context": self._resolve_context(ctx),
            "liturgy": {
                # Will populate in steps:
                "collect": self._resolve_collect(day_yaml.get("liturgy", {}), ctx),
            },
        }

        return render_package

    # ------------------------------------------------------------------
    # YAML Loader
    # ------------------------------------------------------------------

    def _load_day_yaml(self, uid: str) -> Dict[str, Any]:
        """
        Loads a Day YAML file based on UID.
        Expected location: project_root/missal/days/<uid>.yaml
        """
        day_path = self.project_root / "missal" / "days" / f"{uid}.yaml"
        if not day_path.is_file():
            raise FileNotFoundError(f"Day YAML not found: {day_path}")

        with day_path.open("r", encoding="utf-8") as f:
            data = yaml.safe_load(f)

        if not isinstance(data, dict):
            raise ValueError(f"Day YAML must contain a mapping: {uid}")

        return data

    # ------------------------------------------------------------------
    # Meta
    # ------------------------------------------------------------------

    def _resolve_meta(self, day_yaml: Dict[str, Any]) -> Dict[str, Any]:
        meta = day_yaml.get("meta", {})
        return {
            "uid": meta.get("uid"),
            "title": meta.get("title"),
            "rank": meta.get("rank"),
            "rank_precedence": meta.get("rank_precedence"),
            "season": meta.get("season"),
            "color": meta.get("color"),
        }

    # ------------------------------------------------------------------
    # Context
    # ------------------------------------------------------------------

    def _resolve_context(self, ctx: BuilderContext) -> Dict[str, Any]:
        return {
            "date": ctx.date,
            "sunday_cycle": ctx.sunday_cycle,
            "weekday_cycle": ctx.weekday_cycle,
            "mass_form": ctx.mass_form,
            "output_profile": ctx.output_profile,
            "output_format": ctx.output_format,
            "overrides": ctx.overrides,
        }

    # ------------------------------------------------------------------
    # Collect Resolution
    # ------------------------------------------------------------------

    def _resolve_collect(self, liturgy: Dict[str, Any], ctx: BuilderContext) -> Dict[str, Any]:
        """
        Minimal example:
        - Resolve only a single `ref`
        - Look up via catalog
        """

        collect_block = liturgy.get("collect")
        if not collect_block:
            return None

        ref = collect_block.get("ref")
        if not ref:
            raise ValueError("Collect block missing required 'ref' field")

        # Catalog lookup: "html" or "plain" based on output_format
        text = self.catalog_index.get_text(ref, fmt=ctx.output_format)

        # Build normalized block
        return {
            "label": "Collect",
            "ref": ref,
            "text": text,
            "kind": "prayer",
            "speaker": "presider",
        }
