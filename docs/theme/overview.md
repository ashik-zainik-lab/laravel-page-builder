# Theme Development Overview

A theme in the Page Builder is a collection of Blade views, assets, and configurations that define the look and feel of your site.

## How Themes Work

The Page Builder acts as a rendering engine. It loads data from JSON files and maps that data to templates provided by your theme.

Themes are responsible for:

- Providing the base `layouts/`.
- Defining the visual appearance of `sections/`.
- Implementing reusable `blocks/`.
- Providing CSS/JS assets for the frontend.

## Key Principles

1. **Blade-Centric**: Themes are built using standard Laravel Blade templates.
2. **Schema-Driven**: The editor's UI is derived directly from the `@schema` definitions in your theme files.
3. **Flexible**: Themes can live in your main project or be packaged as standalone Laravel modules.
4. **Fast**: Pages are compiled to static Blade files for maximum performance in production.
