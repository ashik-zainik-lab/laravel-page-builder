@schema([
    'name' => 'Social bar',
    'max_blocks' => 8,
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading (optional)', 'default' => 'Follow us'],
        [
            'id' => 'align',
            'type' => 'text_alignment',
            'label' => 'Alignment',
            'default' => 'center',
        ],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Background', 'default' => '#f8fafc'],
    ],
    'blocks' => [
        ['type' => 'social-link'],
    ],
    'presets' => [
        [
            'name' => 'Social bar',
            'blocks' => [
                ['type' => 'social-link', 'settings' => ['network' => 'twitter', 'url' => '#', 'label' => '']],
                ['type' => 'social-link', 'settings' => ['network' => 'linkedin', 'url' => '#', 'label' => '']],
                ['type' => 'social-link', 'settings' => ['network' => 'youtube', 'url' => '#', 'label' => '']],
                ['type' => 'social-link', 'settings' => ['network' => 'github', 'url' => '#', 'label' => '']],
            ],
        ],
    ],
])

@php
    $align = (string) ($section->settings->align ?? 'center');
    $flexClass = match ($align) {
        'left' => 'justify-start',
        'right' => 'justify-end',
        default => 'justify-center',
    };
    $textClass = match ($align) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
@endphp

<section {!! $section->editorAttributes() !!}
    style="background-color: {{ $section->settings->background_color }};"
    class="py-10 lg:py-12">
    <div class="container mx-auto px-4">
        @if (filled($section->settings->heading))
            <h2 class="mb-6 text-sm font-semibold uppercase tracking-wider text-gray-500 {{ $textClass }}">
                {{ $section->settings->heading }}
            </h2>
        @endif
        <div class="flex flex-wrap gap-4 {{ $flexClass }}">
            @blocks($section)
        </div>
    </div>
</section>
