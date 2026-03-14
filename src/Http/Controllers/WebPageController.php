<?php

namespace Coderstm\PageBuilder\Http\Controllers;

use Coderstm\PageBuilder\Facades\Page;
use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Registry\LayoutParser;
use Coderstm\PageBuilder\Services\EditorPreviewShell;
use Coderstm\PageBuilder\Services\PageRenderer;
use Coderstm\PageBuilder\Services\PageStorage;
use Coderstm\PageBuilder\Support\PageData;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class WebPageController extends Controller
{
    public function __construct(
        protected PageRenderer $pageRenderer,
        protected PageStorage $pageStorage,
        protected LayoutParser $layoutParser,
        protected EditorPreviewShell $editorPreviewShell,
    ) {}

    /**
     * Handle a dynamic page request.
     *
     * Resolution order:
     *   1. Editor mode  — always renders from the stored JSON (Blade views bypassed).
     *   2. Custom Blade view  — view("pages.{slug}") if it exists.
     *   3. Page builder JSON  — sections rendered through PageRenderer.
     *   4. 404.
     */
    public function pages(Request $request, string $slug): mixed
    {
        if (! preg_match('/^[a-z0-9\-_]+$/i', $slug)) {
            abort(404);
        }

        $defaultLayout = $this->layoutParser->defaultLayout();
        $dbPage = Page::findBySlug($slug);

        // Share the DB page model with all views rendered in this request,
        // so section views (e.g. page-content) can access $page->title, $page->content, etc.
        View::share('page', $dbPage);

        // ── 1. Editor mode ────────────────────────────────────────────────
        if (PageBuilder::editor()) {
            $page = $this->buildPage($this->pageStorage->load($slug), $defaultLayout, $dbPage);

            return view('pagebuilder::page', [
                ...$this->pageMeta($dbPage),
                'slug' => $slug,
                '__pb_content' => $request->boolean('pb-preview')
                    ? $this->editorPreviewShell->render()
                    : $this->pageRenderer->renderPage($page, editor: true),
                '__pb_layout' => $page,
            ]);
        }

        // ── 2. Custom Blade view ──────────────────────────────────────────
        if (View::exists("pages.{$slug}")) {
            return view("pages.{$slug}", [
                ...$this->pageMeta($dbPage),
                'slug' => $slug,
            ]);
        }

        // ── 3. Page builder JSON ──────────────────────────────────────────
        $stored = $this->pageStorage->load($slug);

        if ($stored !== null) {
            $page = $this->buildPage($stored, $defaultLayout, $dbPage);

            return view('pagebuilder::page', [
                ...$this->pageMeta($dbPage),
                'slug' => $slug,
                '__pb_content' => $this->pageRenderer->renderPage($page),
                '__pb_layout' => $page,
            ]);
        }

        // ── 4. Nothing found ──────────────────────────────────────────────
        abort(404);
    }

    /**
     * Build a PageData instance from stored JSON, merging the DB page title.
     */
    private function buildPage(?PageData $stored, array $defaultLayout, mixed $dbPage): PageData
    {
        $data = $stored?->toArray() ?? [];
        $data['title'] = $dbPage?->title ?? '';

        return PageData::fromArray($data, $defaultLayout);
    }

    /**
     * Extract SEO meta fields from the DB record for passing to views.
     *
     * @return array{meta_title: ?string, meta_description: ?string, meta_keywords: ?string}
     */
    private function pageMeta(mixed $dbPage): array
    {
        return [
            'title' => $dbPage?->title,
            'meta_title' => $dbPage?->meta_title,
            'meta_description' => $dbPage?->meta_description,
            'meta_keywords' => $dbPage?->meta_keywords,
        ];
    }
}
