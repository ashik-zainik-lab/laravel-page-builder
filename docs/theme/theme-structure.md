# Theme Structure

A well-organized theme makes it easy for developers to manage sections and blocks.

## Recommended Folder Structure

While you can customize your paths, we recommend the following structure within your `resources/views/` directory:

```text
resources/views/
├── layouts/               # Site containers (header, footer, etc.)
│   └── page.blade.php
│
├── sections/              # Top-level page sections
│   ├── hero.blade.php
│   ├── features.blade.php
│   └── footer.blade.php
│
├── blocks/                # Reusable content blocks
│   ├── text.blade.php
│   ├── image.blade.php
│   └── row.blade.php
│
└── pages/                 # Compiled Blade output and JSON source
    ├── home.json          # Source JSON (Editor's truth)
    └── home.blade.php     # Compiled output (Public view)
```

## Section and Block Files

Every file in `sections/` and `blocks/` must follow this pattern:

1. **Schema**: The `@schema([...])` directive at the top.
2. **Logic (Optional)**: Any `@php` logic needed for data preparation.
3. **HTML**: The Blade template for the component.

## Overriding Default Components

If the Page Builder core provides default sections or blocks, you can override them by creating a file with the same name in your theme's directory. Custom paths registered later in the boot process take precedence.
