# Layer Panel (Layer Tree)

The **Layer Panel** is the left sidebar in the editor that provides a hierarchical view of all sections and blocks on the page.

## Overview

- **Hierarchy**: Sections are top-level rows; blocks are indented beneath their parent or container.
- **Selection**: Clicking a label selects the item and opens its settings in the right sidebar.
- **Hover Styles**: Hovering over a row subtly highlights the label and icon without changing the background color.

## Inline Rename

Both sections and blocks support inline renaming directly from the tree.

1. **Double-click** the label of the item you want to rename.
2. An input field will appear.
3. Type the new name and press **Enter** (or click away) to save.
4. Press **Escape** to cancel the change.

### Custom Labels

- Custom names are stored as a `_name` field in the page JSON.
- If a custom name is set, it overrides the default schema name in the Layer Panel.
- To revert to the default name, simply clear the input and save.

## Layout Sections

The Layer Panel also displays fixed **Layout Sections** (like Header and Footer).

- **Navigation**: These are rendered fixed at the top and bottom of the list.
- **Restrictions**: Unlike standard sections, layout sections cannot be reordered or deleted from the Layer Panel.
- **Visibility**: They support the same visibility toggle protocol as standard sections.
