# JSON Rendering Engine

The Page Builder uses a structured JSON data format as its source of truth. This data is hydrated into typed PHP objects before being rendered through Blade.

## JSON Structure

A typical page JSON (`about.json`) looks like this:

```json
{
  "title": "About Us",
  "layout": {
    "type": "page",
    "header": { "sections": { ... }, "order": [...] },
    "footer": { "sections": { ... }, "order": [...] }
  },
  "sections": {
    "section_123": {
      "type": "hero",
      "settings": { "title": "Welcome" },
      "blocks": {
        "block_456": {
          "type": "text",
          "settings": { "content": "Hello" }
        }
      },
      "order": ["block_456"]
    }
  },
  "order": ["section_123"]
}
```

## The Hydration Process

When a page is requested:

1. **Loading**: `PageStorage` loads the raw JSON from disk.
2. **Hydration**: `PageData::fromArray()` converts the raw array into a `PageData` object.
3. **Registry Match**: For each section and block, the renderer looks up its schema in the `SectionRegistry` or `BlockRegistry`.
4. **Default Merging**: User-provided settings are merged with schema defaults.
5. **Component Injection**: Sections are converted to `Section` objects, and blocks to `Block` objects.

## Performance Optimization

To ensure fast page loads in production:

- **Schema Caching**: Schemas are extracted once and cached.
- **Blade Publishing**: Pages can be "published" to static Blade files, bypassing the JSON hydration step for public visitors.
- **Fragment Selection**: During live editing, only the modified section is re-rendered via AJAX, not the whole page.
