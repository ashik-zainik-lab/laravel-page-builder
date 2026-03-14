# Developer Documentation

Welcome to the Laravel Page Builder developer documentation. This guide covers everything from basic theme development to extending the core rendering engine.

## Getting Started

- [Theme Development](theme/theme-development.md) — How to structure your Laravel project for the Page Builder.
- [Feature Overview](theme/feature-overview.md) — Key concepts and architectural workflow.

## Core Components

- [Layouts](layout/layout-structure.md) — Understanding per-page headers and footers.
- [Sections](sections/overview.md) — Creating top-level content slices.
- [Blocks](blocks/overview.md) — Building reusable components and nested structures.
- [Layer Panel](theme/layer-panel.md) — Managing the hierarchical layer tree.
- [Field Reference](../field-reference.md) — Complete list of all available setting types.

## Deep Dives

- [Section Schema](sections/section-schema.md) — Detailed reference for the `@schema` directive in sections.
- [Block Schema](blocks/block-schema.md) — Detailed reference for the `@schema` directive in blocks.
- [JSON Rendering Engine](rendering/json-rendering.md) — How page data is transformed into HTML.
- [Visibility Protocol](rendering/visibility-protocol.md) — How hidden sections/blocks are handled.

## Extending the Builder

- [Custom Field Types](../field-reference.md#custom-fields) — Adding your own input components to the editor.
- [Custom Asset Providers](rendering/json-rendering.md) — (See README) Integrating S3, Cloudinary, or other storage backends.
