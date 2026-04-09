<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Rendering;

use Coderstm\PageBuilder\Collections\BlockCollection;
use Coderstm\PageBuilder\Components\Block;
use Coderstm\PageBuilder\Components\Section;
use Coderstm\PageBuilder\Components\Settings;
use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Registry\BlockRegistry;
use Coderstm\PageBuilder\Registry\SectionRegistry;
use Coderstm\PageBuilder\Schema\BlockSchema;
use Coderstm\PageBuilder\Schema\SectionSchema;
use Illuminate\Support\Facades\View;

/**
 * Renders sections and blocks into HTML strings.
 *
 * Hydrates raw JSON data into typed runtime objects (Section, Block)
 * using schema definitions from the SectionRegistry, then renders
 * them through Blade views.
 */
class Renderer
{
    public function __construct(
        protected readonly SectionRegistry $registry,
        protected readonly BlockRegistry $blockRegistry,
    ) {}

    /**
     * Render a Section object into HTML.
     */
    public function renderSection(Section $section): string
    {
        $meta = $this->registry->get($section->type);

        if ($meta === null) {
            return "<!-- Section type '{$section->type}' not found -->";
        }

        $viewName = $meta['view'];

        if (! View::exists($viewName)) {
            return "<!-- View '{$viewName}' not found -->";
        }

        $html = (string) view($viewName, ['section' => $section])->render();
        $html = $this->applyUniversalSectionWrapper($html, $section);

        if (PageBuilder::editor()) {
            $html = EditorAttributes::autoInjectLiveText($html, $section);
        } else {
            $html = $this->wrapScrollReveal($html, $section);
        }

        return $html;
    }

    /**
     * When a section has `scroll_reveal` set (see section schema), wrap HTML
     * so front-end assets can animate it into view. No-op in editor mode.
     *
     * @param  array<int, string>  $allowed
     */
    private function wrapScrollReveal(string $html, Section $section): string
    {
        $effect = (string) $section->settings->get('scroll_reveal', 'none');
        $allowed = ['fade-up', 'fade', 'slide-left', 'slide-right', 'zoom'];

        if ($effect === '' || $effect === 'none' || ! in_array($effect, $allowed, true)) {
            return $html;
        }

        $class = 'pb-reveal pb-reveal--'.htmlspecialchars($effect, ENT_QUOTES, 'UTF-8');

        return '<div class="'.$class.'" data-pb-reveal>'.$html.'</div>';
    }

    /**
     * Apply universal wrapper attributes/styles for every section.
     *
     * These are edited from the section settings panel and work even when a
     * section schema does not explicitly define them.
     */
    private function applyUniversalSectionWrapper(string $html, Section $section): string
    {
        $id = $this->sanitizeCssIdentifier((string) $section->settings->get('pb_section_id', ''));
        $class = trim((string) $section->settings->get('pb_section_class', ''));
        $bg = trim((string) $section->settings->get('pb_bg_color', ''));
        $overlay = trim((string) $section->settings->get('pb_overlay_color', ''));
        $pt = $this->sanitizeCssSize((string) $section->settings->get('pb_padding_top', ''));
        $pb = $this->sanitizeCssSize((string) $section->settings->get('pb_padding_bottom', ''));
        $mt = $this->sanitizeCssSize((string) $section->settings->get('pb_margin_top', ''));
        $mb = $this->sanitizeCssSize((string) $section->settings->get('pb_margin_bottom', ''));
        $radius = $this->sanitizeCssSize((string) $section->settings->get('pb_border_radius', ''));
        $borderWidth = $this->sanitizeCssSize((string) $section->settings->get('pb_border_width', ''));
        $borderColor = trim((string) $section->settings->get('pb_border_color', ''));

        $styles = [];
        if ($bg !== '') {
            $styles[] = 'background-color: '.$bg;
        }
        if ($mt !== null) {
            $styles[] = 'margin-top: '.$mt;
        }
        if ($mb !== null) {
            $styles[] = 'margin-bottom: '.$mb;
        }
        if ($pt !== null) {
            $styles[] = 'padding-top: '.$pt;
        }
        if ($pb !== null) {
            $styles[] = 'padding-bottom: '.$pb;
        }
        if ($radius !== null) {
            $styles[] = 'border-radius: '.$radius;
            $styles[] = 'overflow: hidden';
        }
        if ($borderWidth !== null && $borderColor !== '') {
            $styles[] = 'border-style: solid';
            $styles[] = 'border-width: '.$borderWidth;
            $styles[] = 'border-color: '.$borderColor;
        }

        if ($id === '' && $class === '' && $styles === [] && $overlay === '') {
            return $html;
        }

        if ($overlay !== '') {
            $overlayStyle = htmlspecialchars($overlay, ENT_QUOTES, 'UTF-8');
            $html = '<div style="position:absolute;inset:0;background-color:'.$overlayStyle.';pointer-events:none;z-index:1"></div>'
                .'<div style="position:relative;z-index:2">'.$html.'</div>';
            $styles[] = 'position: relative';
        }

        $attrs = [];
        if ($id !== '') {
            $attrs[] = 'id="'.htmlspecialchars($id, ENT_QUOTES, 'UTF-8').'"';
        }
        if ($class !== '') {
            $attrs[] = 'class="'.htmlspecialchars($class, ENT_QUOTES, 'UTF-8').'"';
        }
        if ($styles !== []) {
            $attrs[] = 'style="'.htmlspecialchars(implode('; ', $styles), ENT_QUOTES, 'UTF-8').'"';
        }

        return '<div '.implode(' ', $attrs).'>'.$html.'</div>';
    }

    private function sanitizeCssIdentifier(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        return preg_replace('/[^a-zA-Z0-9\-_:.]/', '', $value) ?? '';
    }

    private function sanitizeCssSize(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Allow bare numbers from UI inputs and treat them as px.
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $value) === 1) {
            return $value.'px';
        }

        if (preg_match('/^-?\d+(?:\.\d+)?(?:px|rem|em|vh|vw|%)$/', $value) === 1) {
            return $value;
        }

        return null;
    }

    /**
     * Render a single Block into HTML.
     *
     * The parent $section is passed so block views can access section-level context.
     */
    public function renderBlock(Block $block, ?Section $section = null): string
    {
        $viewName = "blocks.{$block->type}";

        if (! View::exists($viewName)) {
            return "<!-- Block view '{$viewName}' not found -->";
        }

        return (string) view($viewName, [
            'block' => $block,
            'section' => $section,
        ])->render();
    }

    /**
     * Render all blocks within a section, concatenating the HTML.
     */
    public function renderBlocks(Section $section): string
    {
        $html = '';

        foreach ($section->blocks as $block) {
            $html .= $this->renderBlock($block, $section);
        }

        return $html;
    }

    /**
     * Render all child blocks within a Block (e.g. columns inside a row).
     */
    public function renderBlockChildren(Block $block, ?Section $section = null): string
    {
        $html = '';

        foreach ($block->blocks as $child) {
            $html .= $this->renderBlock($child, $section);
        }

        return $html;
    }

    /**
     * Hydrate a raw block array into a typed Block object.
     */
    public function hydrateBlock(string $blockId, array $data, bool $editor = false): Block
    {
        $blockType = $data['type'] ?? 'block';

        $themeEntry = $this->blockRegistry->get($blockType);
        $blockSchema = $themeEntry['schema'] ?? null;

        $settingDefaults = $blockSchema instanceof BlockSchema
            ? $blockSchema->settingDefaults()
            : [];

        $nestedBlocks = $this->hydrateBlocks(
            rawBlocks: is_array($data['blocks'] ?? null) ? $data['blocks'] : [],
            blockOrder: $data['order'] ?? null,
            schema: null,
            editor: $editor,
        );

        return new Block([
            'id' => $blockId,
            'type' => $blockType,
            'name' => $blockSchema?->name,
            'disabled' => ! empty($data['disabled']),
            'settings' => new Settings(
                values: $data['settings'] ?? [],
                defaults: $settingDefaults,
            ),
            'blocks' => $nestedBlocks,
        ]);
    }

    /**
     * Hydrate a raw section array (from page JSON) into a typed Section object.
     */
    public function hydrateSection(string $sectionId, array $data, bool $editor = false): Section
    {
        $type = $data['type'] ?? '';
        $meta = $this->registry->get($type);

        /** @var SectionSchema|null $schema */
        $schema = $meta['schema'] ?? null;

        $settingDefaults = $schema ? $schema->settingDefaults() : [];

        return new Section([
            'id' => $sectionId,
            'type' => $type,
            'name' => $schema?->name,
            'disabled' => $data['disabled'] ?? false,
            'settings' => new Settings(
                values: $data['settings'] ?? [],
                defaults: $settingDefaults,
            ),
            'blocks' => $this->hydrateBlocks(
                rawBlocks: is_array($data['blocks'] ?? null) ? $data['blocks'] : [],
                blockOrder: $data['order'] ?? null,
                schema: $schema,
                editor: $editor,
            ),
        ]);
    }

    /**
     * Build an ordered BlockCollection from raw block data.
     *
     * Disabled blocks are always skipped.
     *
     * Schema resolution: inline section block → BlockRegistry fallback.
     * Nested blocks are hydrated recursively.
     */
    protected function hydrateBlocks(
        array $rawBlocks,
        ?array $blockOrder,
        ?SectionSchema $schema,
        bool $editor,
    ): BlockCollection {
        $order = $blockOrder ?? array_keys($rawBlocks);
        $ordered = [];

        foreach ($order as $blockId) {
            if (! isset($rawBlocks[$blockId])) {
                continue;
            }

            $raw = $rawBlocks[$blockId];
            $disabled = ! empty($raw['disabled']);

            if ($disabled) {
                continue;
            }

            $blockType = $raw['type'] ?? 'block';

            // 1. Look for an inline (static) block schema in the section.
            $blockSchema = $schema?->blockSchema($blockType);

            // 2. Fall back to BlockRegistry for type-reference-only entries,
            //    @theme wildcards, and recursive nested hydration (schema === null).
            if ($blockSchema === null) {
                $themeEntry = $this->blockRegistry->get($blockType);
                $blockSchema = $themeEntry['schema'] ?? null;
            }

            $settingDefaults = $blockSchema instanceof BlockSchema
                ? $blockSchema->settingDefaults()
                : [];

            // Recursively hydrate nested blocks (e.g. columns inside a row).
            $nestedBlocks = $this->hydrateBlocks(
                rawBlocks: is_array($raw['blocks'] ?? null) ? $raw['blocks'] : [],
                blockOrder: $raw['order'] ?? null,
                schema: null,
                editor: $editor,
            );

            $ordered[$blockId] = new Block([
                'id' => $blockId,
                'type' => $blockType,
                'name' => $blockSchema?->name,
                'disabled' => $disabled,
                'settings' => new Settings(
                    values: $raw['settings'] ?? [],
                    defaults: $settingDefaults,
                ),
                'blocks' => $nestedBlocks,
            ]);
        }

        return new BlockCollection($ordered);
    }

    /**
     * Render a raw section array directly (for API preview calls).
     *
     * Hydrates the data into a Section object, then renders it.
     */
    public function renderRawSection(string $sectionId, array $sectionData, bool $editor = false): string
    {
        if ($editor) {
            PageBuilder::enableEditor();
        }

        try {
            $section = $this->hydrateSection($sectionId, $sectionData, $editor);

            return $this->renderSection($section);
        } finally {
            if ($editor) {
                PageBuilder::disableEditor();
            }
        }
    }

    /**
     * Render a raw block array directly (for API preview calls).
     *
     * Hydrates the data into a Block object, then renders it.
     */
    public function renderRawBlock(string $blockId, array $blockData, bool $editor = false): string
    {
        if ($editor) {
            PageBuilder::enableEditor();
        }

        try {
            $block = $this->hydrateBlock($blockId, $blockData, $editor);

            return $this->renderBlock($block);
        } finally {
            if ($editor) {
                PageBuilder::disableEditor();
            }
        }
    }
}
