# Section & Block Visibility

Sections and blocks support a `disabled` property that hides them from the published page while keeping them available in the editor for preview and configuration.

## Editor Behavior

- **Toggle Visibility**: Use the **eye icon** on section or block rows in the Layer Panel to toggle visibility.
- **Visual Feedback**: Disabled items appear dimmed (reduced opacity) in the editor canvas with a "Hidden" badge.
- **Settings Menu**: You can also hide/show blocks from the "⋯" menu in the block settings panel.

## Published Output

Disabled items are automatically excluded from the final HTML output. The `@sections()`, `@blocks()`, and `Renderer` logic checks the `disabled` state before rendering.

## Canvas Visibility Protocol

To ensure a smooth editing experience, toggling visibility is handled via a lightweight CSS protocol rather than a full server-side re-render.

### How it works

1. **Store Update**: The `toggleSectionDisabled` or `toggleBlockDisabled` action is dispatched to the editor's state store.
2. **Preview Message**: The editor sends a `toggle-visibility` message to the iframe via `postMessage`.
3. **DOM Update**: An injected script inside the iframe receives the message and toggles CSS classes (`pb-disabled-section` or `pb-disabled-block`) on the element.
4. **CSS Styles**: Themes include default styles for these classes to dim the content and add the "Hidden" overlay.

### Message Contract

```typescript
{
    type: "toggle-visibility",
    kind: "section" | "block",
    sectionId: string,
    blockId: string | null,
    disabled: boolean, // New state
}
```

> [!NOTE]
> This protocol avoids the latency of a server re-render (usually 400ms+), providing instant visual feedback.
