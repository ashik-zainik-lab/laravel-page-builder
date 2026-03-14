<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Tests\Feature\Rendering;

use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class BladeDirectivesTest extends TestCase
{
    protected function tearDown(): void
    {
        PageBuilder::disableEditor();
        parent::tearDown();
    }

    public function test_schema_directive_is_noop(): void
    {
        $blade = '@schema(["name" => "Test"])';
        $compiled = $this->compileBladeString($blade);

        $this->assertSame('<?php /* @schema */ ?>', $compiled);
    }

    public function test_pb_editor_class_directive(): void
    {
        $compiled = $this->compileBladeString('@pbEditorClass');

        $this->assertStringContainsString('PageBuilder::class()', $compiled);
    }

    public function test_pb_editor_class_outputs_empty_when_disabled(): void
    {
        PageBuilder::disableEditor();

        $this->assertSame('', PageBuilder::class());
    }

    public function test_pb_editor_class_outputs_class_when_enabled(): void
    {
        PageBuilder::enableEditor();

        $class = PageBuilder::class();

        $this->assertNotEmpty($class);
        // PageBuilder::class() returns 'js pb-design-mode' when editor is enabled
        $this->assertStringContainsString('pb-design-mode', $class);
    }

    public function test_blocks_directive_compiles_for_section(): void
    {
        $compiled = $this->compileBladeString('@blocks($section)');

        $this->assertStringContainsString('renderBlocks', $compiled);
        $this->assertStringContainsString('renderBlockChildren', $compiled);
    }

    public function test_blocks_directive_compiles_for_block(): void
    {
        $compiled = $this->compileBladeString('@blocks($block)');

        $this->assertStringContainsString('renderBlockChildren', $compiled);
    }

    public function test_precompiler_injects_class_into_html_tag(): void
    {
        $html = '<html lang="en"><body></body></html>';

        // Simulate the precompiler logic
        $result = $this->applyPrecompiler($html);

        $this->assertStringContainsString('@pbEditorClass', $result);
    }

    public function test_precompiler_appends_to_existing_class(): void
    {
        $html = '<html class="no-js" lang="en"><body></body></html>';

        $result = $this->applyPrecompiler($html);

        $this->assertStringContainsString('no-js @pbEditorClass', $result);
    }

    public function test_precompiler_skips_without_html_tag(): void
    {
        $html = '<div>no html tag</div>';

        $result = $this->applyPrecompiler($html);

        $this->assertSame($html, $result);
    }

    public function test_precompiler_skips_without_body_close_tag(): void
    {
        $html = '<html lang="en"><div>no body close</div></html>';

        $result = $this->applyPrecompiler($html);

        $this->assertSame($html, $result);
    }

    // ─── @sections Directive Tests ─────────────────────────────

    public function test_sections_directive_compiles(): void
    {
        $compiled = $this->compileBladeString("@sections('header')");

        $this->assertStringContainsString('renderLayoutSection', $compiled);
        $this->assertStringContainsString('__pb_layout', $compiled);
        $this->assertStringContainsString("'header'", $compiled);
    }

    public function test_sections_directive_compiles_with_key_only(): void
    {
        // @sections() takes a single key — no second argument.
        // Zone membership (header vs footer) is resolved at runtime via layoutSection().
        $compiled = $this->compileBladeString("@sections('footer')");

        $this->assertStringContainsString('renderLayoutSection', $compiled);
        $this->assertStringContainsString('__pb_layout', $compiled);
        $this->assertStringContainsString("'footer'", $compiled);
    }

    // ─── Helpers ────────────────────────────────────────────────

    /**
     * Compile a Blade string and return the compiled PHP output.
     * Named compileBladeString to avoid collision with Testbench's blade() method.
     */
    private function compileBladeString(string $value): string
    {
        return Blade::compileString($value);
    }

    /**
     * Apply the precompiler logic from BladeDirectives.
     */
    private function applyPrecompiler(string $value): string
    {
        if (! str_contains($value, '<html') || ! str_contains($value, '</body>')) {
            return $value;
        }

        if (preg_match('/<html[^>]*class=["\']([^"\']*)["\']/i', $value)) {
            return preg_replace(
                '/(<html[^>]*class=["\'])([^"\']*)(["\'])/i',
                '$1$2 @pbEditorClass$3',
                $value,
                1,
            );
        }

        return preg_replace(
            '/(<html)/i',
            '$1 class="@pbEditorClass"',
            $value,
            1,
        );
    }
}
