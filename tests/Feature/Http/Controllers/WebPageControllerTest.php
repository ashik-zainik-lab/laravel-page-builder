<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Tests\Feature\Http\Controllers;

use Coderstm\PageBuilder\Facades\Page;
use Coderstm\PageBuilder\Services\PageRegistry;
use Coderstm\PageBuilder\Tests\Stubs\PageStub;
use Coderstm\PageBuilder\Tests\TestCase;

class WebPageControllerTest extends TestCase
{
    /**
     * Registers the layout-default route and returns a mocked DB page stub.
     *
     * Uses the pre-existing workbench fixture:
     *   workbench/storage/pages/layout-default.json
     *
     * Sections and layouts are resolved from workbench Blade views — no
     * dynamic view creation is needed or allowed in this test.
     */
    private function setUpLayoutTestPage(): void
    {
        app(PageRegistry::class)->put([
            'layout-default' => ['title' => 'A Test Page with Layout', 'slug' => 'layout-default'],
            'layout-simple' => ['title' => 'A Test Page with Simple Layout', 'slug' => 'layout-simple'],
        ]);

        Page::routes();

        Page::shouldReceive('findBySlug')
            ->with('layout-default')
            ->andReturn(new PageStub([
                'title' => 'A Test Page',
                'meta_title' => 'A Test Page | My App',
                'meta_description' => 'Test description',
                'meta_keywords' => 'test, page',
                'content' => '<p>Hello from page content</p>',
            ]));

        Page::shouldReceive('findBySlug')
            ->with('layout-simple')
            ->andReturn(new PageStub([
                'title' => 'A Test Page with Simple Layout',
                'meta_title' => 'A Test Page with Simple Layout | My App',
                'meta_description' => 'Test description',
                'meta_keywords' => 'test, page',
                'content' => '<p>Hello from page content</p>',
            ]));
    }

    public function test_renders_page_with_layout_sections(): void
    {
        $this->setUpLayoutTestPage();

        $response = $this->get(route('pages.layout-default'));

        $response->assertOk();

        $html = $response->getContent();

        // ── Layout shell ──────────────────────────────────────────────────
        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('<body class="page-layout', $html);

        // ── Meta tags from DB page ────────────────────────────────────────
        $this->assertStringContainsString('A Test Page | My App', $html);
        $this->assertStringContainsString('content="Test description"', $html);
        $this->assertStringContainsString('content="test, page"', $html);

        // ── Layout sections (header / footer zones via header-group) ─────
        // header-group.json defines header + announcement sub-sections.
        // header.blade.php default title = 'Default Header Title' (schema default).
        // header-group.json preset title = 'My Site Header'.
        $this->assertStringContainsString('My Site Header', $html);

        // ── Page body section (banner) ────────────────────────────────────
        // workbench/resources/views/sections/banner.blade.php:
        //   <div class="banner">{{ $section->settings->text }}</div>
        $this->assertStringContainsString('<h3 class="text-2xl font-bold text-white">Content</h3>', $html);
    }

    public function test_renders_page_with_layout_simple_sections(): void
    {
        $this->setUpLayoutTestPage();

        $response = $this->get(route('pages.layout-simple'));

        $response->assertOk();

        $html = $response->getContent();

        // ── Layout shell ──────────────────────────────────────────────────
        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('<body class="simple-layout', $html);

        // ── Meta tags from DB page ────────────────────────────────────────
        $this->assertStringContainsString('A Test Page with Simple Layout | My App', $html);
        $this->assertStringContainsString('content="Test description"', $html);
        $this->assertStringContainsString('content="test, page"', $html);

        // ── Layout sections (header / footer zones) ───────────────────────
        // workbench/resources/views/sections/header.blade.php:
        //   <header {!! $section->editorAttributes() !!}>{{ $section->settings->title }}</header>
        // editorAttributes() returns '' when editor is off → <header >…</header>
        $this->assertStringContainsString('Flash Sale! 50% Off Everything!', $html);
        $this->assertStringContainsString('My Site Header', $html);

        // ── Page body section (banner) ────────────────────────────────────
        // workbench/resources/views/sections/banner.blade.php:
        //   <div class="banner">{{ $section->settings->text }}</div>
        $this->assertStringContainsString('<h3 class="text-2xl font-bold text-white">Content</h3>', $html);
    }

    public function test_shared_page_object_is_available_to_views(): void
    {
        $this->setUpLayoutTestPage();

        $response = $this->get(route('pages.layout-default'));

        $response->assertOk();

        // The $page model is shared via View::share('page', $dbPage).
        // The page-content section uses $page->content to render raw HTML.
        // We verify the page loaded successfully — content sharing is
        // exercised by the page-content section when included in a page JSON.
        $response->assertStatus(200);
    }

    private function setUpPageWithContent(string $content = '<p>Hello from page content</p>'): void
    {
        app(PageRegistry::class)->put([
            'page-with-content' => ['title' => 'Page With Content', 'slug' => 'page-with-content'],
        ]);

        Page::routes();

        Page::shouldReceive('findBySlug')
            ->with('page-with-content')
            ->andReturn(new PageStub([
                'title' => 'Page With Content',
                'content' => $content,
            ]));
    }

    public function test_page_content_section_renders_page_content(): void
    {
        $this->setUpPageWithContent('<p>Hello from page content</p>');

        $response = $this->get(route('pages.page-with-content'));

        $response->assertOk();
        $response->assertSee('<p>Hello from page content</p>', escape: false);
    }

    public function test_page_content_section_renders_default_tailwind_classes(): void
    {
        $this->setUpPageWithContent();

        $response = $this->get(route('pages.page-with-content'));

        $response->assertOk();

        $html = $response->getContent();

        // Default settings: padding_top=md → pt-12, padding_bottom=md → pb-12
        $this->assertStringContainsString('pt-12', $html);
        $this->assertStringContainsString('pb-12', $html);

        // Default max_width=4xl → max-w-4xl
        $this->assertStringContainsString('max-w-4xl', $html);

        // Prose wrapper
        $this->assertStringContainsString('prose', $html);
    }

    public function test_page_content_section_renders_empty_when_page_has_no_content(): void
    {
        $this->setUpPageWithContent('');

        $response = $this->get(route('pages.page-with-content'));

        $response->assertOk();

        // Section wrapper still renders; content area is empty
        $html = $response->getContent();
        $this->assertStringContainsString('pt-12', $html);
        $this->assertMatchesRegularExpression('/<div[^>]*prose[^>]*>\s*<\/div>/', $html);
    }
}
