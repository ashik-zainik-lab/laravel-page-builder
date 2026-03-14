# Section Settings

Settings define the inputs available to the user in the editor sidebar.

## Standard Attributes

All settings share these core attributes:

| Attribute | Type   | Description                                                 |
| :-------- | :----- | :---------------------------------------------------------- |
| `type`    | string | **Required.** The input type.                               |
| `id`      | string | **Required.** Unique key used to access the value in Blade. |
| `label`   | string | Display label shown in the sidebar.                         |
| `default` | mixed  | Default value if the user hasn't set one.                   |
| `info`    | string | Small helper text shown below the input.                    |

## Common Setting Types

### 1. Basic Inputs

- `text`: Single-line text.
- `textarea`: Multi-line text.
- `number`: Numeric input.
- `checkbox`: Boolean toggle.
- `range`: Slider with `min`, `max`, and `step`.

### 2. Selection

- `select`: Dropdown menu (requires `options` array).
- `radio`: Radio buttons (requires `options` array).

### 3. Specialized

- `color`: Hex color picker.
- `url`: Link/URL picker.
- `image_picker`: Media library integration.
- `richtext`: Visual text editor.
- `text_alignment`: Left/Center/Right alignment icons.

## Accessing Settings in Blade

Settings are available via the `$section->settings` object:

```blade
<h2 style="text-align: {{ $section->settings->align }}">
    {{ $section->settings->title }}
</h2>

@if($section->settings->show_button)
    <a href="{{ $section->settings->link }}">Click Here</a>
@endif
```
