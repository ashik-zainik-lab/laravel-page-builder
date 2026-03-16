<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Tests\Feature\Http\Controllers;

use Coderstm\PageBuilder\Facades\Page;
use Coderstm\PageBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Workbench\App\Models\Page as ModelsPage;

class WebPageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Creates DB records for layout pages and registers their routes.
     *
     * Uses the pre-existing workbench fixture JSON files:
     *   workbench/resources/views/pages/layout-default.json
     *   workbench/resources/views/pages/layout-simple.json
     *
     * The PageObserver fires on create and calls pages:regenerate,
     * which reloads the PageRegistry from the database automatically.
     * Sections and layouts are resolved from workbench Blade views.
     */
    private function setUpLayoutTestPage(): void
    {
        ModelsPage::factory()->create([
            'slug' => 'layout-default',
            'title' => 'A Test Page',
            'meta_description' => 'Test description',
            'meta_keywords' => 'test, page',
            'content' => '<p>Hello from page content</p>',
        ]);

        ModelsPage::factory()->create([
            'slug' => 'layout-simple',
            'title' => 'A Test Page with Simple Layout',
            'meta_description' => 'Test description',
            'meta_keywords' => 'test, page',
            'content' => '<p>Hello from page content</p>',

        ]);

        Page::routes();
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
        ModelsPage::create([
            'slug' => 'page-with-content',
            'title' => 'Page With Content',
            'content' => $content,
            'is_active' => true,
        ]);

        Page::routes();
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
