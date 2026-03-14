# Block Settings

Block settings use the same input types as sections but have some unique behaviors regarding display in the editor.

## Dynamic Sidebar Labels

To help users identify blocks in the Layer Panel, the editor automatically uses setting values as labels. It checks for these IDs in order:

1. `heading`
2. `title`
3. `text`

**Example:**
If your block has a setting with `id="title"`, whatever text the user enters there will be shown as the block's name in the sidebar tree. If none of these exist, the default `name` from the schema is used.

## Accessing Meta Information

Every block view has access to meta-data about its instance:

- `$block->id`: Unique identifier.
- `$block->type`: The type (e.g., `text`).
- `$block->name`: Display name (custom `_name` or schema `name`).
- `$block->disabled`: Boolean visibility flag.
- `$block->settings`: The settings object.
- `$block->blocks`: Collection of child blocks (for containers).
- `$section`: Reference to the parent section instance.

## Editor Attributes

To enable block-level selection and highlighting in the editor, you must output the `editorAttributes()` on the block's wrapper element:

```blade
<div {!! $block->editorAttributes() !!} class="block-item">
    <h3>{{ $block->settings->title }}</h3>
</div>
```
