# Page Builder — Developer Documentation

## Settings Reference

### Standard Attributes

All input settings share these attributes:

| Attribute     | Type   | Description                                                 |
| ------------- | ------ | ----------------------------------------------------------- |
| `type`        | string | **Required.** The input type (see below)                    |
| `id`          | string | **Required.** Unique identifier for the setting             |
| `label`       | string | Display label shown above the field                         |
| `default`     | mixed  | Default value                                               |
| `info`        | string | Helper text displayed below the field                       |
| `placeholder` | string | Placeholder text (text, textarea, url, number, html, blade) |

### Basic Input Settings

#### `text`

Single-line text input. Returns a string.

```json
{
  "type": "text",
  "id": "heading",
  "label": "Heading",
  "default": "Hello World",
  "placeholder": "Enter heading…"
}
```

#### `textarea`

Multi-line text input. Returns a string.

```json
{
  "type": "textarea",
  "id": "description",
  "label": "Description",
  "default": ""
}
```

#### `number`

Numeric input. Returns a number or empty string.

```json
{
  "type": "number",
  "id": "columns",
  "label": "Columns",
  "default": 3,
  "placeholder": "1-6"
}
```

#### `checkbox`

Toggle checkbox. Returns a boolean.

```json
{
  "type": "checkbox",
  "id": "show_title",
  "label": "Show title",
  "default": true
}
```

#### `radio`

Radio button group. Returns a string. Requires `options` array.

```json
{
  "type": "radio",
  "id": "layout",
  "label": "Layout",
  "default": "grid",
  "options": [
    { "value": "grid", "label": "Grid" },
    { "value": "list", "label": "List" }
  ]
}
```

#### `range`

Slider with numeric value. Returns a number. Requires `min`, `max`, `step`.

```json
{
  "type": "range",
  "id": "font_size",
  "label": "Font size",
  "default": 16,
  "min": 12,
  "max": 48,
  "step": 2,
  "unit": "px"
}
```

#### `select`

Dropdown select. Returns a string. Requires `options` array.

```json
{
  "type": "select",
  "id": "position",
  "label": "Position",
  "default": "center",
  "options": [
    { "value": "left", "label": "Left" },
    { "value": "center", "label": "Center" },
    { "value": "right", "label": "Right" }
  ]
}
```

### Specialized Input Settings

#### `color`

Color picker with hex input. Returns a hex color string.

```json
{
  "type": "color",
  "id": "text_color",
  "label": "Text color",
  "default": "#333333"
}
```

#### `color_background`

CSS background input (supports gradients). Returns a CSS background string.

```json
{
  "type": "color_background",
  "id": "bg",
  "label": "Background",
  "default": "linear-gradient(135deg, #667eea 0%, #764ba2 100%)"
}
```

#### `url`

URL input. Returns a string.

```json
{ "type": "url", "id": "link", "label": "Button link", "default": "/signup" }
```

#### `image_picker`

Image URL with preview. Returns a URL string.

```json
{ "type": "image_picker", "id": "image", "label": "Image" }
```

#### `video_url`

YouTube/Vimeo URL input. Returns a URL string.

```json
{ "type": "video_url", "id": "video", "label": "Video URL" }
```

#### `icon_fa`

Font Awesome icon picker. Returns a string with the Font Awesome class, e.g. `"fas fa-star"`.

```json
{ "type": "icon_fa", "id": "icon", "label": "Icon" }
```

#### `icon_md`

Material Design icon picker. Returns a string with the Material Design icon class, e.g. `"star"`.

```json
{ "type": "icon_md", "id": "icon", "label": "Icon" }
```

#### `html`

Multi-line code editor (monospace) for HTML. Returns a string.

```json
{ "type": "html", "id": "custom_html", "label": "Custom HTML", "default": "" }
```

#### `blade`

Multi-line code editor for Blade template code. Returns a string.

```json
{ "type": "blade", "id": "custom_code", "label": "Custom code" }
```

#### `inline_richtext`

Single-line text input (renders as text, output can contain basic HTML). Returns a string.

```json
{ "type": "inline_richtext", "id": "subtitle", "label": "Subtitle" }
```

#### `richtext`

Alias for `textarea`. Multi-line text.

```json
{ "type": "richtext", "id": "body", "label": "Body text" }
```

#### `text_alignment`

Segmented control with left/center/right alignment icons. Returns `"left"`, `"center"`, or `"right"`.

```json
{
  "type": "text_alignment",
  "id": "align",
  "label": "Text alignment",
  "default": "left"
}
```

### Content Settings (Non-Input)

#### `header`

Visual section divider with label. No value stored.

```json
{ "type": "header", "content": "Layout" }
```

#### `paragraph`

Informational text block. No value stored.

```json
{ "type": "paragraph", "content": "Configure the layout options below." }
```

---

## Custom Asset Providers

By default the editor uses the built-in **Laravel asset provider**, which stores files in `storage/app/public/pagebuilder` and serves them through the package's own API routes. If you need to store assets elsewhere (S3, Cloudflare R2, Cloudinary, DigitalOcean Spaces, etc.) you can swap the provider without touching any UI code.

### How it works

The asset system is built around a single `AssetProvider` interface. The editor's `createEditor()` factory accepts a provider object at init time and passes it to `AssetService`, which the image picker and asset browser use exclusively.

```typescript
// types/asset.ts — the contract every provider must satisfy
interface AssetProvider {
  list(params: { page?: number; search?: string }): Promise<AssetList>;
  upload(file: File): Promise<Asset>;
}

interface Asset {
  id: string;
  name: string;
  url: string; // full public URL used in Blade templates
  thumbnail: string; // URL of a preview/thumbnail image
  size: number; // bytes
  type: string; // MIME type, e.g. "image/jpeg"
}

interface AssetList {
  data: Asset[];
  pagination: { page: number; per_page: number; total: number };
}
```

### Registering a custom provider via `PageBuilder.init()`

Pass your provider object in the `assets.provider` key when calling `PageBuilder.init()`:

```html
<script src="/vendor/pagebuilder/app.js"></script>
<script>
  PageBuilder.init({
    baseUrl: "/pagebuilder",
    assets: {
      provider: myCustomProvider,
    },
  });
</script>
```

### Writing a provider

A provider is a plain object with two async methods. Both methods **must** return data shaped exactly like `AssetList` / `Asset` above.

#### Example — AWS S3 (via a Laravel signed-URL proxy)

The simplest approach: keep uploads server-side through a thin Laravel endpoint that writes to S3, and return signed CloudFront/S3 URLs.

```js
const s3AssetProvider = {
  async list({ page = 1, search = "" } = {}) {
    const query = new URLSearchParams({ page, q: search });
    const res = await fetch(`/api/pagebuilder/assets?${query}`, {
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) throw new Error("Failed to fetch assets");
    return res.json(); // { data: Asset[], pagination: {...} }
  },

  async upload(file) {
    const body = new FormData();
    body.append("file", file);

    const res = await fetch("/api/pagebuilder/assets/upload", {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN":
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "",
      },
      body,
    });
    if (!res.ok) throw new Error("Upload failed");
    return res.json(); // Asset
  },
};
```

Your Laravel controller then uses `Storage::disk('s3')` to write the file and returns a JSON response shaped like `Asset`.

#### Example — Cloudinary (direct browser upload)

For providers that support direct browser-to-CDN uploads, generate a signed upload URL server-side and upload directly from the browser:

```js
const cloudinaryProvider = {
  async list({ page = 1, search = "" } = {}) {
    const query = new URLSearchParams({ page, q: search });
    const res = await fetch(`/api/pagebuilder/cloudinary/assets?${query}`);
    if (!res.ok) throw new Error("Failed to fetch assets");
    return res.json();
  },

  async upload(file) {
    // 1. Get a signed upload preset from your Laravel backend
    const sigRes = await fetch("/api/pagebuilder/cloudinary/sign", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN":
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") ?? "",
      },
      body: JSON.stringify({ filename: file.name }),
    });
    const { signature, timestamp, cloudName, apiKey, folder } =
      await sigRes.json();

    // 2. Upload directly to Cloudinary
    const body = new FormData();
    body.append("file", file);
    body.append("api_key", apiKey);
    body.append("timestamp", timestamp);
    body.append("signature", signature);
    body.append("folder", folder);

    const upRes = await fetch(
      `https://api.cloudinary.com/v1_1/${cloudName}/image/upload`,
      { method: "POST", body },
    );
    if (!upRes.ok) throw new Error("Cloudinary upload failed");

    const data = await upRes.json();

    // 3. Return an Asset-shaped object
    return {
      id: data.public_id,
      name: data.original_filename,
      url: data.secure_url,
      thumbnail: data.secure_url.replace(
        "/upload/",
        "/upload/w_200,h_200,c_fill/",
      ),
      size: data.bytes,
      type: data.resource_type + "/" + data.format,
    };
  },
};
```

#### Example — DigitalOcean Spaces / Cloudflare R2

These are S3-compatible, so the S3 example above applies directly. Point your Laravel `s3` disk at the Spaces/R2 endpoint via `AWS_ENDPOINT` in `.env`:

```env
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com  # or r2.cloudflarestorage.com/...
AWS_USE_PATH_STYLE_ENDPOINT=true
```

No changes to the provider JS are needed — the browser always talks to your Laravel proxy.

### Provider checklist

Before registering a custom provider, verify:

- ✅ `list()` returns `{ data: Asset[], pagination: { page, per_page, total } }`
- ✅ `upload()` returns a single `Asset` object
- ✅ `Asset.url` is a **publicly accessible** URL (the value stored in page JSON and rendered in Blade)
- ✅ `Asset.thumbnail` is a smaller/resized version suitable for the asset browser grid
- ✅ Both methods `throw` on failure (the editor catches and displays the error)
- ✅ CSRF token is included for any `POST` request hitting your Laravel backend

---

## Custom Field Types

The Page Builder supports developer-extensible custom field types. This allows projects to add domain-specific inputs (e.g., product picker, blog post selector) without modifying the core package.

### Registering a Custom Field Type

Use `window.PageBuilder.registerFieldType(type, renderer)` in your layout or a script tag loaded before the editor initializes.

```html
<script>
  // Register a custom 'product' field type
  window.PageBuilder.registerFieldType(
    "product",
    function ({ setting, value, onChange, container }) {
      const wrapper = document.createElement("div");

      const select = document.createElement("select");
      select.className =
        "w-full px-2.5 py-1.5 border border-gray-200 rounded-md text-xs text-gray-800 bg-white";
      select.value = value || "";

      // Fetch products from your API
      fetch("/api/products")
        .then((res) => res.json())
        .then((products) => {
          products.forEach((p) => {
            const opt = document.createElement("option");
            opt.value = p.id;
            opt.textContent = p.name;
            select.appendChild(opt);
          });
          select.value = value || "";
        });

      select.addEventListener("change", () => onChange(select.value));
      wrapper.appendChild(select);
      return wrapper;
    },
  );
</script>
```

### Renderer Function Signature

```typescript
function renderer(params: {
  setting: object; // The full setting definition (type, id, label, default, etc.)
  value: any; // Current value of the setting
  onChange: (newValue: any) => void; // Callback to update the value
  container: HTMLElement; // The DOM container where the field will be mounted
}): HTMLElement | string | void;
```

**Return value options:**

- **HTMLElement**: Appended to the container automatically
- **String**: Set as innerHTML of the container
- **void**: You must append to `container` directly

### Using in Blade Templates

Define your section with a custom type in the `@schema` directive:

```php
@schema([
    'name' => 'Featured Products',
    'settings' => [
        ['type' => 'product', 'id' => 'product_id', 'label' => 'Select product'],
    ],
])
```

### Integration from a Laravel Project

In your project's layout or service provider, add custom field types after the Page Builder layout loads:

```php
// In your AppServiceProvider or a Blade @push
@push('pagebuilder-scripts')
<script>
  window.PageBuilder.registerFieldType('blog', function({ setting, value, onChange }) {
    // Your custom blog picker implementation
  });
</script>
@endpush
```

Or include a dedicated JS file:

```html
<script src="{{ asset('js/pagebuilder-extensions.js') }}"></script>
```

---

## Layer Panel (Layer Tree)

The **Layer Panel** is the left sidebar in the editor that shows a hierarchical tree of all sections on the current page and the blocks nested inside each section.

### Overview

- Sections appear as collapsible top-level rows
- Blocks (and nested child blocks) appear indented beneath their parent section or container block
- The panel uses a **text-colour-only** hover style — no background changes on hover: hovering a row changes the label and icon text colour, never the row background
- The currently selected section or block is highlighted with a blue label (`text-blue-600`) with no background colour change

### Inline Rename

Both section rows and block rows support **inline renaming** directly in the layer panel.

#### How to rename

1. **Double-click** the section or block label in the layer panel
2. An inline `<input>` replaces the label text
3. Type the new name
4. Press **Enter** or click outside (blur) to **commit** the rename
5. Press **Escape** to **cancel** and restore the original name

> Single-clicking a label only selects that item. Rename is triggered exclusively on double-click.

#### Custom name vs schema name

- When a section or block is renamed, the custom label is stored as a `_name` field on the instance in the page JSON (see §Page JSON Structure in the theme guide)
- If `_name` is present and non-empty, it overrides the schema `name` as the display label in the layer panel
- Clearing the name (submitting an empty string) removes `_name`, reverting to the default schema name
- The `_name` value is available at render time on the instance object (see §13 of theme-development.md)

#### Store actions

The rename feature is wired through two Zustand store actions:

| Action                  | Signature                                | Description                                                                  |
| ----------------------- | ---------------------------------------- | ---------------------------------------------------------------------------- |
| `renameSectionInstance` | `(sectionId, name)`                      | Sets `_name` on the section instance                                         |
| `renameBlockInstance`   | `(sectionId, blockId, name, parentPath)` | Sets `_name` on the block instance (supports nested blocks via `parentPath`) |

### Layer Panel UI Behaviour Summary

| State                        | Visual change                                                            |
| ---------------------------- | ------------------------------------------------------------------------ |
| Default                      | Label: `text-gray-600` / `text-gray-700`, icon: `text-gray-400`          |
| Hover (section or block row) | Label: `text-gray-900`, icon: `text-gray-600` — **no background change** |
| Selected (section)           | Label: `text-blue-600` — no background, no left border strip             |
| Selected (block)             | Label: `text-blue-600 font-semibold` — no background                     |
| Renaming                     | Inline `<input>` replaces label; blur or Enter commits, Escape cancels   |

### Layout Section Rows

In addition to the sortable page-section rows, the Layer Panel renders **fixed layout section rows** for structural slots such as the site header and footer. These are rendered from `currentPage.layout?.sections` and displayed as non-sortable `LayoutSectionRow` entries above and below the sortable list.

#### How layout zones are partitioned

`LayoutPanel` reads `currentPage.layout.sections` and splits the keys into two zones:

- **Top zone** — keys where `position === "top"` or key `=== "header"` — rendered above the sortable list, labelled _"Layout"_.
- **Bottom zone** — keys where `position === "bottom"` or key `=== "footer"`, plus any unassigned keys — rendered below the sortable list.

#### `LayoutSectionRow` vs `SortableSectionRow`

| Feature             | `SortableSectionRow`  | `LayoutSectionRow`                               |
| ------------------- | --------------------- | ------------------------------------------------ |
| Drag handle         | ✅                    | ❌ (replaced by spacer div)                      |
| Duplicate button    | ✅                    | ❌                                               |
| Remove button       | ✅                    | ❌                                               |
| Icon                | Section-type icon     | `PanelTop` (top) / `PanelBottom` (bottom)        |
| Visibility toggle   | On hover              | Always visible when disabled; on hover otherwise |
| Selection highlight | `text-blue-600` label | `text-blue-600` label + `text-blue-500` icon     |
| Hover background    | `bg-gray-50`          | `bg-gray-50`                                     |
| Selected background | `bg-blue-50`          | `bg-blue-50`                                     |

#### Selection — dual source design

Clicking a page section sets a URL search param (`?section=id`), tracked by `useEditorNavigation`. Clicking a layout section row sets Zustand store state (`selectedLayoutSection`). These two sources are kept mutually exclusive:

1. `onSelectLayoutSection(key)` calls `navigation.clearSection()` **first** (clears URL) then `store.setSelectedLayoutSection(key)`.
2. A `useEffect` in `useEditor.ts` clears `selectedLayoutSection` from the store whenever the URL param becomes non-null.
3. `showSettings` in `useEditor.ts` is `!!(selectedSection || selectedLayoutSection)`.
4. `settingsPanelProps.selectedSection` is `selectedSection ?? selectedLayoutSection`.

#### Settings panel for layout sections

`SettingsPanel` uses the following lookup chain to find the active section instance:

```
currentPage.sections[id]               // page section — fast path
  ?? currentPage.layout?.sections[id]  // layout section — fallback
  ?? null
```

This means the same `SettingsPanel` component renders settings for both page sections and layout sections without modification. The `currentPage` prop type is widened to include `layout?: { sections?: Record<string, SectionInstance> }`.

#### Visibility toggle for layout sections

The eye icon on a `LayoutSectionRow` calls `onToggleLayoutSectionDisabled(key)`, which dispatches `store.toggleLayoutSectionDisabled(key)`. This Immer action flips `currentPage.layout.sections[key].disabled`. When `disabled: true`, the `@sections()` Blade directive returns an empty string for that slot.

The canvas update for layout section visibility follows the same canvas-only protocol as page sections and blocks — see [Canvas Visibility Protocol](#canvas-visibility-protocol) below.

---

## Section & Block Visibility

Sections and blocks support a `disabled` property that hides them from the published page while keeping them in the editor for preview and configuration.

### In the Editor

- Use the **eye icon** on section/block rows to toggle visibility
- Use the **⋯ menu** on block settings to Hide/Show, Duplicate, or Remove
- Disabled items appear dimmed (35% opacity) with a "Hidden" badge

### Published Output

Disabled sections and blocks are automatically excluded from published pages (both in the rendered HTML and the generated Blade template).

### Canvas Visibility Protocol

**Toggling visibility never triggers a server re-render.** The canvas update is a synchronous CSS class toggle performed entirely in the iframe — sub-millisecond, zero network cost.

#### How it works

1. The user clicks the eye icon on a section or block row in `LayoutPanel`.
2. `useSectionActions` fires the Zustand/Immer store mutation (`toggleSectionDisabled` / `toggleBlockDisabled`) to persist the new `disabled` state in page JSON.
3. Immediately after, `toggleVisibilityInPreview()` (from `usePreviewSync`) sends a `toggle-visibility` postMessage to the iframe.
4. The injected `EditorScriptInjector` JS in the iframe receives the message and calls `classList.toggle()` on the target element — no HTML fetch, no Blade render.

#### `toggle-visibility` message contract

```typescript
// Sent by: usePreviewSync.toggleVisibilityInPreview()
// Received by: EditorScriptInjector.ts (injected iframe JS)
{
    type: "toggle-visibility",
    kind: "section" | "block",   // what is being toggled
    sectionId: string,           // owning section ID (always required)
    blockId: string | null,      // block element ID (required when kind === "block")
    disabled: boolean,           // the NEW disabled value after the toggle
}
```

#### CSS classes applied in the iframe

| Class                 | Applies to                  | Visual effect                              |
| --------------------- | --------------------------- | ------------------------------------------ |
| `pb-disabled-section` | `[data-section-id]` element | 35% opacity + "Hidden" badge via `::after` |
| `pb-disabled-block`   | `[data-block-id]` element   | 35% opacity + "Hidden" badge via `::after` |

#### Key implementation detail — computing `newDisabled`

Because `toggleSectionDisabled` / `toggleBlockDisabled` are Immer mutations that haven't flushed to a new React reference by the time the callback reads `currentPage`, the `newDisabled` value is computed as the **inverse of the current (pre-toggle) value**:

```typescript
// Section toggle
const handleToggleSectionDisabled = useCallback(
  (sectionId: string) => {
    toggleSectionDisabled(sectionId);
    const newDisabled = !currentPage?.sections?.[sectionId]?.disabled;
    toggleVisibilityInPreview("section", sectionId, newDisabled);
  },
  [toggleSectionDisabled, toggleVisibilityInPreview, currentPage],
);

// Block toggle (supports nested blocks via parentPath)
const handleToggleBlockDisabled = useCallback(
  (sectionId: string, blockId: string, parentPath: string[] = []) => {
    toggleBlockDisabled(sectionId, blockId, parentPath);
    const section = currentPage?.sections?.[sectionId];
    const parentBlock =
      parentPath.length > 0
        ? getNestedBlock(section?.blocks, parentPath)
        : section;
    const newDisabled = !parentBlock?.blocks?.[blockId]?.disabled;
    toggleVisibilityInPreview("block", sectionId, newDisabled, blockId);
  },
  [toggleBlockDisabled, toggleVisibilityInPreview, currentPage],
);
```

#### Rules

- **NEVER call `debouncedRerender()`** inside `handleToggleSectionDisabled` or `handleToggleBlockDisabled` — a full server re-render is wasteful and introduces 400 ms of latency for a pure CSS operation.
- Always pass `disabled` as the **new** value (post-toggle), not the current value.
- The `toggle-visibility` handler in `EditorScriptInjector.ts` is the single place that applies/removes `pb-disabled-section` and `pb-disabled-block` via postMessage — do not duplicate this logic elsewhere in the iframe injector.
