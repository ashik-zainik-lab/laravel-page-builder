@schema([
    'name' => 'Button',
    'settings' => [
        ['id' => 'label', 'type' => 'text', 'label' => 'Label', 'default' => 'Learn more'],
        ['id' => 'url', 'type' => 'url', 'label' => 'URL', 'default' => '#'],
        [
            'id' => 'variant',
            'type' => 'select',
            'label' => 'Style',
            'default' => 'primary',
            'options' => [
                ['value' => 'primary', 'label' => 'Primary (accent)'],
                ['value' => 'secondary', 'label' => 'Secondary (gray)'],
                ['value' => 'outline', 'label' => 'Outline'],
                ['value' => 'link', 'label' => 'Text link'],
            ],
        ],
        [
            'id' => 'align',
            'type' => 'select',
            'label' => 'Alignment',
            'default' => 'left',
            'options' => [
                ['value' => 'left', 'label' => 'Left'],
                ['value' => 'center', 'label' => 'Center'],
                ['value' => 'right', 'label' => 'Right'],
            ],
        ],
    ],
    'presets' => [
        ['name' => 'Button'],
    ],
])

@php
    $variant = (string) ($block->settings->variant ?? 'primary');
    $align = (string) ($block->settings->align ?? 'left');
    $wrapClass = match ($align) {
        'center' => 'flex justify-center',
        'right' => 'flex justify-end',
        default => 'flex justify-start',
    };
    $classes = match ($variant) {
        'secondary' => 'inline-flex items-center justify-center rounded-xl bg-gray-200 px-6 py-2.5 text-sm font-semibold text-gray-900 transition-colors hover:bg-gray-300',
        'outline' => 'inline-flex items-center justify-center rounded-xl border-2 border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-800 transition-colors hover:border-accent hover:text-accent',
        'link' => 'inline-flex items-center text-sm font-semibold text-accent underline-offset-4 hover:text-accent-hover hover:underline',
        default => 'inline-flex items-center justify-center rounded-xl bg-accent px-6 py-2.5 text-sm font-bold text-white transition-all hover:bg-accent-hover hover:scale-[1.02]',
    };
@endphp

<div class="{{ $wrapClass }}" {!! $block->editorAttributes() !!}>
    <a href="{{ $block->settings->url }}" class="{{ $classes }}">
        {{ $block->settings->label }}
    </a>
</div>
