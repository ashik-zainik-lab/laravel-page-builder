@schema([
    'name' => 'Image',
    'settings' => [
        [
            'id' => 'src',
            'type' => 'image_picker',
            'label' => 'Image',
            'default' => '',
        ],
        ['id' => 'alt', 'type' => 'text', 'label' => 'Alt text', 'default' => ''],
        [
            'id' => 'rounded',
            'type' => 'select',
            'label' => 'Corners',
            'default' => 'lg',
            'options' => [
                ['value' => 'none', 'label' => 'Square'],
                ['value' => 'md', 'label' => 'Medium'],
                ['value' => 'lg', 'label' => 'Large'],
                ['value' => 'full', 'label' => 'Fully rounded'],
            ],
        ],
        [
            'id' => 'align',
            'type' => 'select',
            'label' => 'Alignment',
            'default' => 'center',
            'options' => [
                ['value' => 'left', 'label' => 'Left'],
                ['value' => 'center', 'label' => 'Center'],
                ['value' => 'right', 'label' => 'Right'],
            ],
        ],
    ],
    'presets' => [
        ['name' => 'Image'],
    ],
])

@php
    $src = trim((string) ($block->settings->src ?? ''));
    $rounded = (string) ($block->settings->rounded ?? 'lg');
    $roundClass = match ($rounded) {
        'none' => 'rounded-none',
        'md' => 'rounded-md',
        'full' => 'rounded-full',
        default => 'rounded-2xl',
    };
    $align = (string) ($block->settings->align ?? 'center');
    $wrapClass = match ($align) {
        'left' => 'flex justify-start',
        'right' => 'flex justify-end',
        default => 'flex justify-center',
    };
@endphp

@if ($src !== '')
    <div class="{{ $wrapClass }}" {!! $block->editorAttributes() !!}>
        <img src="{{ $src }}" alt="{{ $block->settings->alt }}" loading="lazy" decoding="async"
            class="h-auto max-w-full shadow-md {{ $roundClass }}">
    </div>
@else
    <div {!! $block->editorAttributes() !!} class="{{ $wrapClass }} rounded-lg border-2 border-dashed border-gray-200 py-8 px-4 text-center text-sm text-gray-400">
        Select an image in the sidebar
    </div>
@endif
