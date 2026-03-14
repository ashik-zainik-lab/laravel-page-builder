# Rendering Layouts

Layouts are rendered using the `@sections()` Blade directive, which bridges the gap between the page JSON data and your theme's Blade views.

## The `@sections` Directive

The `@sections` directive takes a key and an optional array of overrides.

### Basic Usage

```blade
{{-- Renders the 'header' layout section defined in the JSON --}}
@sections('header')
```

### With Overrides

You can pass temporary overrides to a layout section. This is useful if you want to force certain settings from the layout file itself:

```blade
{{-- Overrides the 'sticky' setting for this specific layout --}}
@sections('header', ['sticky' => false])
```

## Resolution Logic

When `@sections('header')` is called:

1. The renderer looks for the `header` key in the `layout.sections` map of the current page JSON.
2. If found and not `disabled`, it resolves the section `type`.
3. It fetches the schema and merges the per-page `settings`.
4. It renders the section using the corresponding `sections/{type}.blade.php` file.

## Header & Footer Areas

The Layer Panel in the editor automatically partitions layout sections based on their key or position:

- **Top Zone**: Keys matching `header` or where `position === "top"`.
- **Bottom Zone**: Keys matching `footer` or where `position === "bottom"`.

These appear as fixed rows in the editor sidebar, allowing users to configure them separately from the sortable page body.
