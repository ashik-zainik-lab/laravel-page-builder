@schema([
    'name' => 'Hero',
    'settings' => [
        ['type' => 'header', 'content' => 'Layout'],
        [
            'id' => 'text_align',
            'type' => 'text_alignment',
            'label' => 'Text alignment',
            'default' => 'center',
        ],
        [
            'id' => 'section_height',
            'type' => 'select',
            'label' => 'Section height',
            'default' => 'default',
            'options' => [
                ['value' => 'compact', 'label' => 'Compact'],
                ['value' => 'default', 'label' => 'Default'],
                ['value' => 'tall', 'label' => 'Tall'],
                ['value' => 'screen', 'label' => 'Most of viewport'],
            ],
        ],
        ['type' => 'header', 'content' => 'Media'],
        [
            'id' => 'background_image',
            'type' => 'image_picker',
            'label' => 'Background image',
            'info' => 'Optional. Shown behind the background color with cover.',
            'default' => '',
        ],
        [
            'id' => 'background_position',
            'type' => 'select',
            'label' => 'Image position',
            'default' => 'center',
            'options' => [
                ['value' => 'center', 'label' => 'Center'],
                ['value' => 'top', 'label' => 'Top'],
                ['value' => 'bottom', 'label' => 'Bottom'],
                ['value' => 'left', 'label' => 'Left'],
                ['value' => 'right', 'label' => 'Right'],
            ],
        ],
        [
            'id' => 'overlay_color',
            'type' => 'color',
            'label' => 'Image overlay color',
            'default' => '#000000',
        ],
        [
            'id' => 'overlay_opacity',
            'type' => 'range',
            'label' => 'Overlay strength',
            'min' => 0,
            'max' => 85,
            'step' => 5,
            'unit' => '%',
            'default' => 0,
        ],
        ['type' => 'header', 'content' => 'Content'],
        ['id' => 'title', 'type' => 'text', 'label' => 'Title', 'default' => 'Welcome to Our Site'],
        ['id' => 'subtitle', 'type' => 'text', 'label' => 'Subtitle', 'default' => 'We build amazing experiences for the web.'],
        ['id' => 'button_label', 'type' => 'text', 'label' => 'Button label', 'default' => 'Get Started'],
        ['id' => 'button_url', 'type' => 'url', 'label' => 'Button URL', 'default' => '#'],
        ['type' => 'header', 'content' => 'Colors'],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Fallback / tint', 'default' => '#f8fafc'],
        ['id' => 'title_color', 'type' => 'color', 'label' => 'Title color', 'default' => ''],
        ['id' => 'subtitle_color', 'type' => 'color', 'label' => 'Subtitle color', 'default' => ''],
        ['type' => 'header', 'content' => 'Motion'],
        [
            'id' => 'scroll_reveal',
            'type' => 'select',
            'label' => 'Scroll reveal',
            'default' => 'none',
            'info' => 'Animates this section when it enters the viewport (live site only).',
            'options' => [
                ['value' => 'none', 'label' => 'None'],
                ['value' => 'fade-up', 'label' => 'Fade up'],
                ['value' => 'fade', 'label' => 'Fade'],
                ['value' => 'slide-left', 'label' => 'Slide from left'],
                ['value' => 'slide-right', 'label' => 'Slide from right'],
                ['value' => 'zoom', 'label' => 'Zoom in'],
            ],
        ],
    ],
    'blocks' => [
        ['type' => 'row'],
        ['type' => '@theme'],
    ],
    'presets' => [
        ['name' => 'Hero'],
    ],
])

@php
    $heroBg = trim((string) ($section->settings->background_image ?? ''));
    $align = (string) ($section->settings->text_align ?? 'center');
    $alignClass = match ($align) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
    $height = (string) ($section->settings->section_height ?? 'default');
    $heightClass = match ($height) {
        'compact' => 'py-12 lg:py-16',
        'tall' => 'py-28 lg:py-40',
        'screen' => 'flex min-h-[75vh] flex-col justify-center py-16 lg:py-24',
        default => 'py-20 lg:py-32',
    };
    $bgPos = (string) ($section->settings->background_position ?? 'center');
    $overlayOpacity = (int) ($section->settings->overlay_opacity ?? 0);
    $overlayOpacity = max(0, min(85, $overlayOpacity));
    $titleColor = trim((string) ($section->settings->title_color ?? ''));
    $subtitleColor = trim((string) ($section->settings->subtitle_color ?? ''));
@endphp

<section {!! $section->editorAttributes() !!}
    style="background-color: {{ $section->settings->background_color }};"
    class="relative overflow-hidden {{ $heightClass }}">
    @if ($heroBg !== '')
        <div class="pointer-events-none absolute inset-0 z-0" aria-hidden="true"
            style="background-image: url({{ json_encode($heroBg) }}); background-size: cover; background-position: {{ $bgPos }};"></div>
        @if ($overlayOpacity > 0)
            <div class="pointer-events-none absolute inset-0 z-[1]" aria-hidden="true"
                style="background-color: {{ $section->settings->overlay_color }}; opacity: {{ $overlayOpacity / 100 }};"></div>
        @endif
    @endif

    <div class="container relative z-10 mx-auto px-4 {{ $alignClass }}">
        <h1 class="mb-6 text-4xl font-extrabold tracking-tight md:text-6xl {{ $titleColor === '' ? 'text-gray-900' : '' }}"
            @if ($titleColor !== '') style="color: {{ $section->settings->title_color }};" @endif>
            {{ $section->settings->title }}
        </h1>
        <p class="mb-10 max-w-2xl text-lg md:text-xl {{ $subtitleColor === '' ? 'text-gray-500' : '' }} {{ $align === 'center' ? 'mx-auto' : '' }}"
            @if ($subtitleColor !== '') style="color: {{ $section->settings->subtitle_color }};" @endif>
            {{ $section->settings->subtitle }}
        </p>
        @if (filled($section->settings->button_label))
            <a href="{{ $section->settings->button_url }}"
                class="inline-block rounded-xl bg-accent px-8 py-3 font-bold text-white transition-all hover:scale-105 hover:bg-accent-hover">
                {{ $section->settings->button_label }}
            </a>
        @endif
        <div class="mt-8">
            @blocks($section)
        </div>
    </div>
</section>
