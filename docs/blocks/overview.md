# Blocks Overview

Blocks are reusable components that live inside sections and containers. They allow for granular control over content structure.

## Local vs. Theme Blocks

Choosing the right block type is crucial for maintainability.

### 1. Local Blocks

- **Definition**: Defined inline inside a section's `@schema`.
- **Scope**: Exclusive to that section.
- **Complexity**: Single-level only (cannot contain other blocks).
- **Use Case**: Slides, FAQ items, testimonials, social links.

### 2. Theme Blocks

- **Definition**: Standalone Blade files in `resources/views/blocks/`.
- **Scope**: Global (can be used in any section that allows `@theme`).
- **Complexity**: Support nested children (allowing for columns, rows, etc.).
- **Use Case**: Text, images, buttons, container rows, columns.

## Field Types

Blocks use the same input settings as sections. For a full list of available field types, see the [Field Reference](../field-reference.md).

## Block Detection Rule

The Page Builder identifies blocks based on the keys present in the `blocks` array:

- **Theme Reference**: `['type' => 'text']` (Bare reference).
- **Theme Wildcard**: `['type' => '@theme']` (Allows any theme block).
- **Local Definition**: `['type' => 'slide', 'name' => 'Slide', 'settings' => [...]]` (Has `name` or `settings`).

## Rendering Blocks

Blocks are rendered using the `@blocks($parent)` directive, where `$parent` can be either the `$section` or a parent `$block`.

```blade
{{-- Renders all child blocks defined for this section --}}
<div class="container">
    @blocks($section)
</div>
```
