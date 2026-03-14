# Field Types Reference

This page provides a comprehensive list of all available input and content settings for sections and blocks.

## Standard Attributes

All input settings share these core attributes:

| Attribute     | Type   | Description                                             |
| :------------ | :----- | :------------------------------------------------------ |
| `type`        | string | **Required.** The input type identifier.                |
| `id`          | string | **Required.** Unique key used to access value in Blade. |
| `label`       | string | Display label shown in the editor.                      |
| `default`     | mixed  | Default value.                                          |
| `info`        | string | Helper text displayed below the field.                  |
| `placeholder` | string | Placeholder text (supported by most text inputs).       |

---

## Basic Input Settings

### `text`

Single-line text input. Returns a string.

```json
{ "type": "text", "id": "heading", "label": "Heading", "default": "Hello" }
```

### `textarea`

Multi-line text input. Returns a string.

```json
{ "type": "textarea", "id": "content", "label": "Body Text" }
```

### `number`

Numeric input. Returns a number.

```json
{ "type": "number", "id": "columns", "label": "Columns", "default": 3 }
```

### `checkbox`

Boolean toggle. Returns `true` or `false`.

```json
{
  "type": "checkbox",
  "id": "show_title",
  "label": "Show title",
  "default": true
}
```

### `radio` / `select`

Options selection. Requires an `options` array.

```json
{
  "type": "select",
  "id": "position",
  "label": "Position",
  "options": [
    { "value": "left", "label": "Left" },
    { "value": "center", "label": "Center" }
  ]
}
```

### `range`

Slider input. Requires `min`, `max`, and `step`.

```json
{
  "type": "range",
  "id": "font_size",
  "label": "Font size",
  "min": 12,
  "max": 48,
  "step": 2,
  "unit": "px"
}
```

---

## Specialized Input Settings

### `color`

Hex color picker. Returns a string (e.g., `#ffffff`).

### `color_background`

CSS background picker. Supports gradients. Returns a CSS string.

### `url`

Link/URL picker. Returns a string. Standard text input with a URL placeholder.

### `image_picker`

Media library integration. Returns a URL string. Includes an image preview and "Select Image" button.

### `video_url`

YouTube/Vimeo URL input. Standard text input with validation and preview support.

### `icon_fa`

Font Awesome icon picker. Returns the full class string (e.g., `fas fa-star`). Requires FontAwesome to be loaded in your theme.

### `icon_md`

Material Design icon picker. Returns the icon ligature name (e.g., `star`). Requires Material Icons font to be loaded.

### `html` / `blade`

Multi-line code editors with monospace font. Used for raw HTML or Blade template logic.

### `richtext`

Visual WYSIWYG editor for multi-line content. Returns an HTML string.

### `inline_richtext`

A simplified version of the rich text editor intended for single-line inputs (e.g., subtitles with bold/italic support).

### `text_alignment`

Segmented control providing Left, Center, and Right alignment options. Returns the alignment string.

---

## Content Settings (Non-Input)

These fields are used for organization in the editor sidebar and do not store values or receive an `id`.

### `header`

Visual divider with a bold label to group related settings.

```json
{ "type": "header", "content": "Style Settings" }
```

### `paragraph`

Informational text to guide the user through complex configuration.

```json
{
  "type": "paragraph",
  "content": "Adjust the padding below to fine-tune spacing."
}
```

---

## Advanced Field Types

### `external`

Dynamic selection fields where options are fetched from a remote API or a provider function. Usually used for choosing products, collections, or blog posts.

### Custom Fields

Developers can register their own field types using the JavaScript API:

```javascript
PageBuilder.registerFieldType(
  "my_custom_field",
  ({ setting, value, onChange }) => {
    // Return a DOM element or React component
  },
);
```
