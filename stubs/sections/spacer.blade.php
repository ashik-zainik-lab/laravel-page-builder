@schema([
    'name' => 'Spacer',
    'settings' => [
        [
            'id' => 'size',
            'type' => 'select',
            'label' => 'Vertical space',
            'default' => 'md',
            'options' => [
                ['value' => 'xs', 'label' => 'Extra small'],
                ['value' => 'sm', 'label' => 'Small'],
                ['value' => 'md', 'label' => 'Medium'],
                ['value' => 'lg', 'label' => 'Large'],
                ['value' => 'xl', 'label' => 'Extra large'],
            ],
        ],
        [
            'id' => 'variant',
            'type' => 'select',
            'label' => 'Style',
            'default' => 'blank',
            'options' => [
                ['value' => 'blank', 'label' => 'Whitespace only'],
                ['value' => 'line', 'label' => 'Divider line'],
                ['value' => 'fade', 'label' => 'Soft gradient fade'],
            ],
        ],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Background (optional)', 'default' => ''],
    ],
    'presets' => [
        ['name' => 'Spacer'],
        [
            'name' => 'Section divider',
            'settings' => ['size' => 'sm', 'variant' => 'line'],
        ],
    ],
])

@php
    $size = (string) ($section->settings->size ?? 'md');
    $py = match ($size) {
        'xs' => 'py-6',
        'sm' => 'py-10',
        'lg' => 'py-20 lg:py-24',
        'xl' => 'py-24 lg:py-32',
        default => 'py-14 lg:py-16',
    };
    $variant = (string) ($section->settings->variant ?? 'blank');
    $bg = trim((string) ($section->settings->background_color ?? ''));
@endphp

<section {!! $section->editorAttributes() !!}
    @if ($bg !== '') style="background-color: {{ $bg }};" @endif
    class="{{ $py }}"
    role="presentation">
    <div class="container mx-auto px-4">
        @if ($variant === 'line')
            <hr class="border-0 border-t border-gray-200">
        @elseif ($variant === 'fade')
            <div class="h-px w-full bg-gradient-to-r from-transparent via-gray-200 to-transparent" aria-hidden="true"></div>
        @endif
    </div>
</section>
