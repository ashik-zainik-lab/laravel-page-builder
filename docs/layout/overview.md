# Layouts Overview

Layouts in the Page Builder provide a structural wrapper for your pages. Unlike traditional global templates, the Page Builder supports **per-page layouts**.

## What is a Layout?

A layout defines the surrounding structure of a page, typically including:

- Site Header
- Site Footer
- Any other persistent elements (e.g., sidebars)

Layouts map directly to Blade files located in your theme's `layouts/` directory (e.g., `layouts/page.blade.php`).

## Per-Page Settings

The key differentiator is that layout sections (like the header) are **ordinary sections** with their own settings, but those settings are stored **per-page** in the page JSON.

This allows you to have:

- A different logo or menu on specific landing pages.
- A hidden footer on a checkout page.
- Sticky or transparent headers on a per-page basis.

## Core Features

1. **Named Slots**: Layouts define slots (like 'header' and 'footer') where sections can be placed.
2. **Standard Registration**: Layout sections are registered in the `SectionRegistry` just like body sections.
3. **Draft Support**: Layout sections support the same visibility toggles and draft states as content sections.
