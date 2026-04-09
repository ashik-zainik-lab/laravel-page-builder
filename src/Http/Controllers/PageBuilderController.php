<?php

namespace Coderstm\PageBuilder\Http\Controllers;

use Coderstm\PageBuilder\Facades\Page;
use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Registry\BlockRegistry;
use Coderstm\PageBuilder\Registry\LayoutParser;
use Coderstm\PageBuilder\Registry\SectionRegistry;
use Coderstm\PageBuilder\Rendering\Renderer;
use Coderstm\PageBuilder\Services\PageRegistry;
use Coderstm\PageBuilder\Services\PageRenderer;
use Coderstm\PageBuilder\Services\PageRevisionHistory;
use Coderstm\PageBuilder\Services\PageStorage;
use Coderstm\PageBuilder\Services\TemplateStorage;
use Coderstm\PageBuilder\Services\ThemeSettings;
use Coderstm\PageBuilder\Support\PageData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    /**
     * True when the client expects a JSON body (editor API), not a browser document.
     *
     * Top-level navigations (address bar, new tab) send Sec-Fetch-Mode: navigate; those
     * should open the Blade editor even if Accept includes broad wildcards.
     */
    private function shouldReturnJson(Request $request): bool
    {
        if ($request->header('Sec-Fetch-Mode') === 'navigate') {
            return false;
        }

        return $request->expectsJson();
    }

    public function __construct(
        protected PageRenderer $pageRenderer,
        protected PageStorage $pageStorage,
        protected PageRevisionHistory $pageRevisionHistory,
        protected SectionRegistry $sectionRegistry,
        protected BlockRegistry $blockRegistry,
        protected PageRegistry $pageRegistry,
        protected Renderer $renderer,
        protected ThemeSettings $themeSettings,
        protected TemplateStorage $templateStorage,
        protected LayoutParser $layoutParser,
    ) {}

    /**
     * GET /pagebuilder/{slug?}
     *
     * Render the PageBuilder editor React application.
     */
    public function editor(?string $slug = null): View
    {
        return view('pagebuilder::layout', [
            'config' => PageBuilder::scriptVariables(),
        ]);
    }

    /**
     * GET /pagebuilder/pages
     *
     * List all available pages from the canonical page registry.
     */
    public function pages(Request $request): JsonResponse|RedirectResponse
    {
        if (! $this->shouldReturnJson($request)) {
            return redirect()->route('pagebuilder.editor', ['slug' => 'home']);
        }

        return response()->json(Page::listPagesForEditor());
    }

    /**
     * GET /pagebuilder/page/{slug}
     *
     * Get a specific page JSON data.
     * Returns an empty page structure when no JSON file exists,
     * allowing the editor to create new pages from scratch.
     *
     * The response always includes a `layout` key with `type`, `sections`,
     * and `order` — populated from the active layout Blade file when the
     * page JSON has no `layout` stored yet.
     */
    public function page(Request $request, string $slug = 'home'): JsonResponse|RedirectResponse
    {
        if (! $this->shouldReturnJson($request)) {
            return redirect()->route('pagebuilder.editor', ['slug' => $slug]);
        }

        $stored = $this->pageStorage->load($slug);
        $layoutType = $stored?->layoutType() ?? 'page';
        $defaultLayout = $this->layoutParser->defaultLayout($layoutType);

        $page = $stored !== null
            ? PageData::fromArray($stored->toArray(), $defaultLayout)
            : PageData::fromArray([], $defaultLayout);

        $data = $page->toArray();

        // Merge database meta into the response so the editor has it.
        $dbPage = Page::findBySlug($slug);

        $storedTemplate = null;
        if ($stored !== null) {
            $storedArray = $stored->toArray();
            $storedTemplate = is_string($storedArray['template'] ?? null)
                ? (string) $storedArray['template']
                : null;
        }

        if ($dbPage) {
            $data['title'] = $dbPage->title;
            $data['template'] = $dbPage->template ?: ($storedTemplate ?: 'page');
            $data['meta'] = array_filter([
                'meta_title' => $dbPage->meta_title,
                'meta_description' => $dbPage->meta_description,
                'meta_keywords' => $dbPage->meta_keywords,
            ]);
            $data['is_active'] = (bool) $dbPage->is_active;
        } else {
            $data['template'] = $storedTemplate ?: 'page';
            $data['is_active'] = true;
        }

        return response()->json($data);
    }

    /**
     * GET /pagebuilder/page/{slug}/revisions
     *
     * List stored JSON snapshots for undo / version history.
     */
    public function pageRevisions(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        if (! $this->shouldReturnJson($request)) {
            return redirect()->route('pagebuilder.editor', ['slug' => $slug]);
        }

        return response()->json([
            'revisions' => $this->pageRevisionHistory->list($slug),
        ]);
    }

    /**
     * POST /pagebuilder/page/{slug}/revisions/{revisionId}/restore
     *
     * Restore page JSON from a snapshot (does not write a new snapshot).
     */
    public function restorePageRevision(Request $request, string $slug, string $revisionId): JsonResponse
    {
        $raw = $this->pageRevisionHistory->read($slug, $revisionId);

        if ($raw === null) {
            return response()->json([
                'message' => 'Revision not found.',
            ], 404);
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return response()->json([
                'message' => 'Invalid revision file.',
            ], 422);
        }

        $saved = $this->pageStorage->save($slug, $data, false);

        if (! $saved) {
            return response()->json([
                'message' => 'Failed to restore page.',
            ], 500);
        }

        $this->pageRegistry->reload();

        return response()->json([
            'message' => __('Revision restored.'),
        ]);
    }

    /**
     * POST /pagebuilder/render-section
     *
     * Render a single section with provided settings.
     * Used by the editor for live preview updates.
     */
    public function renderSection(Request $request): JsonResponse
    {
        $request->validate([
            'section_id' => 'required|string',
            'section_type' => 'required|string',
            'settings' => 'nullable|array',
            'blocks' => 'nullable|array',
            'order' => 'nullable|array',
        ]);

        $sectionId = $request->input('section_id');
        $sectionData = [
            'type' => $request->input('section_type'),
            'settings' => $request->input('settings', []),
            'blocks' => $request->input('blocks', []),
            'order' => $request->input('order', []),
        ];

        $html = $this->renderer->renderRawSection($sectionId, $sectionData, editor: true);

        return response()->json([
            'html' => $html,
        ]);
    }

    /**
     * POST /pagebuilder/render-block
     *
     * Render a single block with provided settings.
     * Used by the editor for block previews in modals.
     */
    public function renderBlock(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable|array',
            'blocks' => 'nullable|array',
            'order' => 'nullable|array',
        ]);

        $blockData = [
            'type' => $request->input('type'),
            'settings' => $request->input('settings', []),
            'blocks' => $request->input('blocks', []),
            'order' => $request->input('order', []),
        ];

        $html = $this->renderer->renderRawBlock('preview_block', $blockData, editor: true);

        return response()->json([
            'html' => $html,
        ]);
    }

    /**
     * POST /pagebuilder/save-page
     *
     * Save page JSON data and persist page meta to the database.
     */
    public function savePage(Request $request): JsonResponse
    {
        $request->validate([
            'slug' => 'required|string|regex:#^[a-zA-Z0-9\-_\/]+$#',
            'data' => 'required|array',
            'template' => 'nullable|string|regex:/^[a-zA-Z0-9._-]+$/',
            'meta' => 'nullable|array',
            'meta.title' => 'nullable|string|max:255',
            'meta.meta_title' => 'nullable|string|max:255',
            'meta.meta_description' => 'nullable|string|max:500',
            'meta.meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'theme_settings' => 'nullable|array',
        ]);

        $slug = $request->input('slug');
        $template = trim((string) $request->input('template', 'page'));
        $template = $template !== '' ? strtolower($template) : 'page';
        $isActive = $request->has('is_active')
            ? $request->boolean('is_active')
            : true;

        if (PageBuilder::isPreservedPage($slug)) {
            $isActive = true;
        }

        $data = array_merge($request->input('data'), [
            'title' => $request->input('meta.title'),
            'template' => $template,
            'meta' => $request->input('meta'),
        ]);

        $saved = $this->pageStorage->save($slug, $data);

        if (! $saved) {
            return response()->json([
                'message' => 'Failed to save page.',
            ], 500);
        }

        // Persist page meta to the database
        $meta = $request->input('meta', []);

        if (! empty($meta)) {
            Page::saveMeta($slug, array_filter([
                'title' => $meta['title'] ?? null,
                'meta_title' => $meta['meta_title'] ?? null,
                'meta_description' => $meta['meta_description'] ?? null,
                'meta_keywords' => $meta['meta_keywords'] ?? null,
            ], fn ($v) => $v !== null));
        }

        // Ensure the page appears in the editor page list by creating/updating
        // a DB record for non-preserved slugs when missing.
        if (! PageBuilder::isPreservedPage($slug)) {
            $pageModel = PageBuilder::$pageModel;
            $existingPage = Page::findBySlug($slug);
            $title = $request->input('meta.title')
                ?? $request->input('data.title')
                ?? Str::headline(str_replace(['-', '_'], ' ', $slug));

            if ($existingPage === null) {
                $pageModel::create([
                    'slug' => $slug,
                    'title' => (string) $title,
                    'template' => $template,
                    'is_active' => $isActive,
                    'meta_title' => $request->input('meta.meta_title'),
                    'meta_description' => $request->input('meta.meta_description'),
                    'meta_keywords' => $request->input('meta.meta_keywords'),
                ]);
            } else {
                $existingPage->update(array_filter([
                    'title' => (string) $title,
                    'template' => $template,
                    'meta_title' => $request->input('meta.meta_title'),
                    'meta_description' => $request->input('meta.meta_description'),
                    'meta_keywords' => $request->input('meta.meta_keywords'),
                    'is_active' => $isActive,
                ], static fn ($value): bool => $value !== null));
            }
        }

        $this->pageRegistry->reload();

        // Save theme settings when included in the same request
        if ($request->has('theme_settings')) {
            $this->themeSettings->save($request->input('theme_settings', []));
        }

        return response()->json([
            'message' => __('Page has been saved successfully'),
        ]);
    }

    /**
     * GET /pagebuilder/templates
     *
     * Return all available JSON templates.
     */
    public function templates(): JsonResponse
    {
        return response()->json([
            'templates' => $this->templateStorage->all(),
        ]);
    }

    /**
     * POST /pagebuilder/templates
     *
     * Create a new JSON template file.
     */
    public function createTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|regex:/^[a-zA-Z0-9._-]+$/',
            'layout' => 'nullable|string',
            'wrapper' => 'nullable|string|max:255',
            'force' => 'sometimes|boolean',
        ]);

        $name = strtolower(trim((string) $request->input('name')));
        $layoutInput = trim((string) $request->input('layout', 'page'));
        $layout = in_array(strtolower($layoutInput), ['false', 'none', 'null'], true)
            ? false
            : $layoutInput;

        $created = $this->templateStorage->create(
            $name,
            [
                'layout' => $layout,
                'wrapper' => trim((string) $request->input('wrapper', '')),
            ],
            (bool) $request->boolean('force')
        );

        if (! $created) {
            return response()->json([
                'message' => 'Template could not be created. It may already exist.',
            ], 422);
        }

        return response()->json([
            'message' => __('Template created successfully.'),
            'templates' => $this->templateStorage->all(),
        ]);
    }

    /**
     * DELETE /pagebuilder/page/{slug}
     *
     * Delete page JSON data and matching database record.
     */
    public function deletePage(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        if (! $this->shouldReturnJson($request)) {
            return redirect()->route('pagebuilder.editor', ['slug' => $slug]);
        }

        if (PageBuilder::isPreservedPage($slug)) {
            return response()->json([
                'message' => 'Preserved pages cannot be deleted.',
            ], 422);
        }

        $deleted = Page::deleteBySlug($slug);

        if (! $deleted) {
            return response()->json([
                'message' => 'Failed to delete page.',
            ], 500);
        }

        $this->pageRegistry->reload();

        return response()->json([
            'message' => __('Page deleted successfully.'),
        ]);
    }

    /**
     * GET /pagebuilder/theme-settings
     *
     * Return the theme settings schema and current values.
     */
    public function themeSettings(): JsonResponse
    {
        return response()->json($this->themeSettings->toArray());
    }

    /**
     * POST /pagebuilder/theme-settings
     *
     * Save theme settings values.
     */
    public function saveThemeSettings(Request $request): JsonResponse
    {
        $request->validate([
            'values' => 'required|array',
        ]);

        $saved = $this->themeSettings->save($request->input('values'));

        if (! $saved) {
            return response()->json([
                'message' => 'Failed to save theme settings.',
            ], 500);
        }

        return response()->json([
            'message' => __('Theme settings have been saved successfully'),
        ]);
    }
}
