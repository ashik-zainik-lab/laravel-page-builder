# Blade Integration

The Page Builder provides several custom Blade directives to make theme development seamless.

## Core Directives

### `@schema`

Used inside section and block files to define their configuration. This directive is parsed statically and does not output anything at runtime.

### `@blocks($parent)`

Iterates through and renders all child blocks for a given parent (a `$section` or another `$block`).

```blade
<div class="column">
    @blocks($block)
</div>
```

### `@sections($key, $overrides)`

Used in layout files to render specific layout zones (e.g., header, footer).

```blade
@sections('header')
```

### `@pbEditorScripts`

Injects the necessary JavaScript for the editor's live preview. Place this in your theme's main layout file before the closing `</body>` tag.

### `@pbEditorClass`

Outputs a CSS class if the page is currently being viewed inside the editor.

```blade
<body class="@pbEditorClass">
```

## Component Variables

Every section and block template has access to a specialized instance variable:

- `$section`: Instance of `Coderstm\PageBuilder\Components\Section`.
- `$block`: Instance of `Coderstm\PageBuilder\Components\Block`.

These objects provide methods like `editorAttributes()` and properties like `settings` and `blocks` (for containers).

## Manual Rendering

You can also render sections manually from your own controllers or views using the `Renderer` service:

```php
use Coderstm\PageBuilder\Rendering\Renderer;

$html = app(Renderer::class)->renderRawSection('hero', [
    'settings' => ['title' => 'Custom Title'],
]);
```
