# Block Schema

Blocks, like sections, use the `@schema` directive to define their structure and settings.

## Local Block Schema

Local blocks do not have their own file. Their schema is embedded in the parent section's `blocks` array.

```php
'blocks' => [
    [
        'type' => 'feature_item',
        'name' => 'Feature',
        'limit' => 4,
        'settings' => [
            ['id' => 'icon', 'type' => 'image_picker', 'label' => 'Icon'],
            ['id' => 'title', 'type' => 'text', 'label' => 'Title', 'default' => 'New Feature'],
        ]
    ]
]
```

## Theme Block Schema

Theme blocks live in `resources/views/blocks/{type}.blade.php`. Their schema is identical to a section schema but often includes a `presets` array.

```php
@schema([
    'name' => 'Rich Text',
    'settings' => [
        ['id' => 'content', 'type' => 'richtext', 'label' => 'Body'],
        ['id' => 'align', 'type' => 'text_alignment', 'label' => 'Align'],
    ],
    'presets' => [
        ['name' => 'Text block']
    ]
])
```

## Block Schema Attributes

| Key        | Type   | Description                                                                     |
| :--------- | :----- | :------------------------------------------------------------------------------ |
| `name`     | string | Human-readable name.                                                            |
| `limit`    | int    | Max instances of this block per container.                                      |
| `settings` | array  | Input definitions.                                                              |
| `blocks`   | array  | Allowed children types. Use `[['type' => '@theme']]` to allow all theme blocks. |
| `presets`  | array  | (Theme Blocks only) Required to appear in the "Add Block" menu.                 |

---

## The `@theme` Wildcard

The `@theme` wildcard is a special type used to allow any registered theme block to be nested inside a section or container block.

> [!TIP]
> Use the `@theme` wildcard to allow any registered theme block. You can also specify certain blocks before it to "highlight" them in the picker.

### Usage in Sections

When a section should accept any global theme block, use the wildcard in its `blocks` array:

```php
'blocks' => [
    ['type' => '@theme'], // wildcard allows any theme block
],
```

### Highlighted vs. All Blocks

You can list specific block types before the wildcard to "highlight" them in the editor's block picker. These will appear first; all other blocks will be accessible via a "Show all" toggle.

```php
'blocks' => [
    ['type' => 'text'],    // Highlighted (appears first)
    ['type' => 'image'],   // Highlighted
    ['type' => '@theme'],   // All other theme blocks
],
```

### Usage in Container Blocks

Theme blocks can also be containers for other theme blocks by declaring the wildcard in their own `@schema`:

```php
@schema([
    'name' => 'Column',
    'blocks' => [
        ['type' => '@theme'], // allows nesting any block inside this column
    ],
    ...
])
```
