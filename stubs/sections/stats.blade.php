@schema([
    'name' => 'Stats',
    'max_blocks' => 6,
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading (optional)', 'default' => ''],
        ['id' => 'subheading', 'type' => 'textarea', 'label' => 'Subheading (optional)', 'default' => ''],
        [
            'id' => 'columns',
            'type' => 'select',
            'label' => 'Columns on desktop',
            'default' => '4',
            'options' => [
                ['value' => '2', 'label' => '2'],
                ['value' => '3', 'label' => '3'],
                ['value' => '4', 'label' => '4'],
            ],
        ],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Background', 'default' => '#ffffff'],
        ['id' => 'show_divider', 'type' => 'checkbox', 'label' => 'Top & bottom border', 'default' => true],
    ],
    'blocks' => [
        ['type' => 'stat-item'],
    ],
    'presets' => [
        [
            'name' => 'Stats (4 metrics)',
            'blocks' => [
                [
                    'type' => 'stat-item',
                    'settings' => [
                        'prefix' => '',
                        'value' => '10',
                        'suffix' => 'k+',
                        'label' => 'Active customers',
                        'hint' => 'Across 40 countries',
                    ],
                ],
                [
                    'type' => 'stat-item',
                    'settings' => [
                        'prefix' => '',
                        'value' => '99.9',
                        'suffix' => '%',
                        'label' => 'Uptime SLA',
                        'hint' => 'On enterprise tier',
                    ],
                ],
                [
                    'type' => 'stat-item',
                    'settings' => [
                        'prefix' => '',
                        'value' => '24',
                        'suffix' => '/7',
                        'label' => 'Support coverage',
                        'hint' => 'Chat & email',
                    ],
                ],
                [
                    'type' => 'stat-item',
                    'settings' => [
                        'prefix' => '<',
                        'value' => '50',
                        'suffix' => 'ms',
                        'label' => 'Median API latency',
                        'hint' => 'Global edge network',
                    ],
                ],
            ],
        ],
    ],
])

@php
    $cols = (string) ($section->settings->columns ?? '4');
    $gridClass = match ($cols) {
        '2' => 'grid-cols-1 sm:grid-cols-2',
        '3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        default => 'grid-cols-2 sm:grid-cols-2 lg:grid-cols-4',
    };
    $border = filter_var($section->settings->show_divider ?? true, FILTER_VALIDATE_BOOLEAN);
@endphp

<section {!! $section->editorAttributes() !!}
    style="background-color: {{ $section->settings->background_color }};"
    @class([
        'py-14 lg:py-16',
        'border-y border-gray-200' => $border,
    ])>
    <div class="container mx-auto px-4">
        @if (filled($section->settings->heading) || filled($section->settings->subheading))
            <div class="mx-auto mb-10 max-w-2xl text-center lg:mb-12">
                @if (filled($section->settings->heading))
                    <h2 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">
                        {{ $section->settings->heading }}
                    </h2>
                @endif
                @if (filled($section->settings->subheading))
                    <p class="mt-3 text-gray-600">{{ $section->settings->subheading }}</p>
                @endif
            </div>
        @endif
        <div class="grid items-start gap-10 {{ $gridClass }}">
            @blocks($section)
        </div>
    </div>
</section>
