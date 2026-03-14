# Section Schema

The `@schema` directive is the source of truth for a section's capabilities. It is defined as a PHP array at the top of your section file.

## Schema Attributes

| Key          | Type   | Description                                                                  |
| :----------- | :----- | :--------------------------------------------------------------------------- |
| `name`       | string | Human-readable name shown in the editor.                                     |
| `limit`      | int    | Max instances allowed on a single page (0 = unlimited).                      |
| `max_blocks` | int    | Max total blocks allowed inside this section (0 = unlimited).                |
| `settings`   | array  | Definitions for input fields (see [Settings](./section-settings.md)).        |
| `blocks`     | array  | Allowed block types. Use `[['type' => '@theme']]` to allow all theme blocks. |
| `presets`    | array  | Pre-configured versions of the section for the "Add Section" menu.           |

## Example Schema

```php
@schema([
    'name' => 'Featured Hero',
    'limit' => 1,
    'max_blocks' => 0,
    'settings' => [
        ['id' => 'title', 'type' => 'text', 'label' => 'Main Heading', 'default' => 'Welcome'],
        ['id' => 'image', 'type' => 'image_picker', 'label' => 'Background Image'],
    ],
    'blocks' => [
        ['type' => 'button'],
        ['type' => 'text'],
    ],
    'presets' => [
        [
            'name' => 'Default Hero',
            'settings' => ['title' => 'Hello World']
        ]
    ]
])
```

## The `@theme` Wildcard

The `@theme` wildcard allows a section to accept any registered theme block.

```php
'blocks' => [
    ['type' => '@theme'], // wildcard allows any theme block
],
```

### Highlighted vs. All Blocks

You can list specific block types before the wildcard to "highlight" them in the editor's block picker.

```php
'blocks' => [
    ['type' => 'text'],    // Highlighted (appears first)
    ['type' => 'image'],   // Highlighted
    ['type' => '@theme'],   // All other theme blocks
],
```

## Presets

Presets define how your section appears in the "Add Section" dialog. A section **must have at least one preset** to be selectable by the user in the editor.
