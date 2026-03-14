# Sections Overview

Sections are the primary building blocks of a page. They represent top-level horizontal slices of content that can be added, removed, and reordered.

## What is a Section?

A section is a standalone Blade component that includes both its visual template and its configuration schema.

Key characteristics:

- **Self-contained**: Each section lives in its own `.blade.php` file.
- **Configurable**: Settings are defined via the `@schema` directive.
- **Nestable**: Sections can contain child blocks (Local or Theme blocks).
- **Reusable**: The same section type can be used multiple times on a single page with different settings.

## Key Documentation sections

- [Schema](./section-schema.md) — The `@schema` directive reference for sections.
- [Settings](./section-settings.md) — Standard and specialized input settings.
- [Field Reference](../field-reference.md) — A full list of available field types.
- [Registration](./section-registration.md) — Registering section paths and programmatic registration.

## Section File Location

By default, sections are located in `resources/views/sections/`. The filename determines the section **type** (e.g., `hero.blade.php` has a type of `hero`).

## Data Lifecycle

1. **Schema Extraction**: The Page Builder scans your section files to extract the `@schema`.
2. **Editor Configuration**: The editor uses the schema to build the settings UI.
3. **JSON Persistence**: User settings are saved in a `{slug}.json` file.
4. **Hydration**: At render time, the JSON data is merged with schema defaults.
5. **Rendering**: The section Blade view is rendered with a `$section` variable.
