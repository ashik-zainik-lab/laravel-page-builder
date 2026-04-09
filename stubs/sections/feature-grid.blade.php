@schema([
    'name' => 'Feature grid',
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Everything you need'],
        ['id' => 'subheading', 'type' => 'textarea', 'label' => 'Subheading', 'default' => 'Build pages faster with flexible sections and blocks.'],
        [
            'id' => 'header_align',
            'type' => 'text_alignment',
            'label' => 'Header alignment',
            'default' => 'center',
        ],
    ],
    'blocks' => [
        ['type' => 'row'],
        ['type' => '@theme'],
    ],
    'presets' => [
        ['name' => 'Feature grid'],
    ],
])

@php
    $hAlign = (string) ($section->settings->header_align ?? 'center');
    $headerClass = match ($hAlign) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
@endphp

<section {!! $section->editorAttributes() !!} class="py-16 lg:py-24">
    <div class="container mx-auto px-4">
        <div class="mb-12 max-w-3xl lg:mb-16 {{ $headerClass }} {{ $hAlign === 'center' ? 'mx-auto' : '' }}">
            @if (filled($section->settings->heading))
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                    {{ $section->settings->heading }}
                </h2>
            @endif
            @if (filled($section->settings->subheading))
                <p class="mt-4 text-lg text-gray-600">{{ $section->settings->subheading }}</p>
            @endif
        </div>
        @blocks($section)
    </div>
</section>
