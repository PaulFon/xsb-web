"""
Catalog Loader for Missal + Lectionary Builder

Responsibilities:
- Load all YAML catalog files under `catalog/`
- Index entries by `id`
- Provide an API to fetch entries in a given format (html/plain/scml)
- Enforce basic validation (id uniqueness, required fields)

This module DOES NOT know anything about liturgical logic.
It only knows how to read and normalize catalog entries.
"""

from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Any, Dict, Optional

import yaml


# ---- Data model -----------------------------------------------------------------


@dataclass
class CatalogEntry:
    id: str
    title: Optional[str]
    metadata: Dict[str, Any]
    formats: Dict[str, str]
    raw: Dict[str, Any]  # original dict from YAML (for debugging / future use)


class CatalogIndex:
    """
    In-memory index of all catalog entries, keyed by `id`.

    Typical usage:

        index = CatalogIndex.from_project_root(Path(__file__).resolve().parents[1])
        entry = index.get("collects:advent:2_sunday", fmt="html")
    """

    def __init__(self) -> None:
        self._entries: Dict[str, CatalogEntry] = {}

    # -------------------------------------------------------------------------
    # Construction / loading
    # -------------------------------------------------------------------------

    @classmethod
    def from_project_root(cls, project_root: Path) -> "CatalogIndex":
        """
        Load all catalog YAML files under <project_root>/catalog.

        This assumes the repo layout:

            project_root/
              catalog/
                collects/*.yaml
                readings/*.yaml
                rubrics/*.yaml
                ...

        Raises:
            FileNotFoundError if catalog directory does not exist.
            ValueError on duplicate IDs.
        """
        catalog_root = project_root / "catalog"
        if not catalog_root.is_dir():
            raise FileNotFoundError(f"Catalog directory not found: {catalog_root}")

        index = cls()
        index._load_dir(catalog_root)
        return index

    def _load_dir(self, root: Path) -> None:
        """
        Recursively load all .yaml files under root.
        """
        for path in root.rglob("*.yaml"):
            self._load_file(path)

    def _load_file(self, path: Path) -> None:
        """
        Load a single YAML file and add all entries to the index.

        Supports two patterns:
        - Top-level list:
            - id: "collects:advent:2_sunday"
              ...
            - id: ...
        - Top-level mapping of lists:
            rubrics:
              - id: "rubric:intro-stand"
                ...
        """
        with path.open("r", encoding="utf-8") as f:
            data = yaml.safe_load(f)

        if data is None:
            # Empty file, ignore
            return

        if isinstance(data, list):
            # List of entries
            entries = data
        elif isinstance(data, dict):
            # One or more keys that map to lists of entries
            entries = []
            for value in data.values():
                if isinstance(value, list):
                    entries.extend(value)
        else:
            raise ValueError(f"Unexpected YAML structure in {path}: {type(data)}")

        for entry_dict in entries:
            if not isinstance(entry_dict, dict):
                raise ValueError(f"Invalid catalog entry in {path}: {entry_dict!r}")

            entry = self._normalize_entry(entry_dict, path)
            if entry.id in self._entries:
                raise ValueError(
                    f"Duplicate catalog id '{entry.id}' in file {path}; "
                    f"already defined elsewhere."
                )
            self._entries[entry.id] = entry

    def _normalize_entry(self, raw: Dict[str, Any], source_path: Path) -> CatalogEntry:
        """
        Validate and normalize a single catalog entry.
        """
        entry_id = raw.get("id")
        if not entry_id or not isinstance(entry_id, str):
            raise ValueError(f"Catalog entry missing valid 'id' in {source_path}: {raw!r}")

        title = raw.get("title")
        metadata = raw.get("metadata") or {}
        if not isinstance(metadata, dict):
            raise ValueError(f"'metadata' must be a mapping in {source_path}: {raw!r}")

        formats = raw.get("formats") or {}
        if not isinstance(formats, dict) or not formats:
            raise ValueError(
                f"Catalog entry '{entry_id}' in {source_path} "
                f"must have a non-empty 'formats' mapping."
            )

        # Optional: enforce presence of at least 'html' OR 'plain'
        if "html" not in formats and "plain" not in formats:
            raise ValueError(
                f"Catalog entry '{entry_id}' in {source_path} "
                f"must define at least 'html' or 'plain' in formats."
            )

        return CatalogEntry(
            id=entry_id,
            title=title,
            metadata=metadata,
            formats=formats,
            raw=raw,
        )

    # -------------------------------------------------------------------------
    # Public API
    # -------------------------------------------------------------------------

    def get_entry(self, entry_id: str) -> CatalogEntry:
        """
        Return the full CatalogEntry for the given ID.

        Raises:
            KeyError if the id is not found.
        """
        try:
            return self._entries[entry_id]
        except KeyError as exc:
            raise KeyError(f"Catalog id not found: {entry_id}") from exc

    def get_text(self, entry_id: str, fmt: str = "html") -> str:
        """
        Return the text content for the given entry ID in the desired format.

        If the requested format is not available, falls back:
        - html -> plain (if html missing)
        - plain -> html (if plain missing)

        Raises:
            KeyError if entry not found.
            ValueError if no compatible format exists.
        """
        entry = self.get_entry(entry_id)
        formats = entry.formats

        if fmt in formats:
            return formats[fmt]

        # Simple fallback strategy
        if fmt == "html" and "plain" in formats:
            return formats["plain"]
        if fmt == "plain" and "html" in formats:
            return formats["html"]

        raise ValueError(
            f"Catalog entry '{entry_id}' does not support format '{fmt}', "
            f"available formats: {list(formats.keys())}"
        )

    def all_ids(self) -> list[str]:
        """Return a sorted list of all catalog IDs."""
        return sorted(self._entries.keys())

    def __len__(self) -> int:
        return len(self._entries)

    def __contains__(self, entry_id: str) -> bool:
        return entry_id in self._entries
