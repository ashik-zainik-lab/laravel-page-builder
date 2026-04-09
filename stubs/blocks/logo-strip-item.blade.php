@schema([
    'name' => 'Logo',
    'settings' => [
        [
            'id' => 'image',
            'type' => 'image_picker',
            'label' => 'Logo image',
            'default' => '',
        ],
        ['id' => 'label', 'type' => 'text', 'label' => 'Accessible label', 'default' => 'Partner logo'],
        ['id' => 'url', 'type' => 'url', 'label' => 'Link (optional)', 'default' => ''],
    ],
    'presets' => [
        ['name' => 'Logo'],
    ],
])

@php
    $src = trim((string) ($block->settings->image ?? ''));
    $label = (string) ($block->settings->label ?: 'Partner logo');
@endphp

@if ($src !== '')
    <div {!! $block->editorAttributes() !!} class="flex h-14 items-center justify-center grayscale opacity-70 transition hover:opacity-100 md:h-16">
        @if (filled($block->settings->url))
            <a href="{{ $block->settings->url }}" class="block max-h-full" target="_blank" rel="noopener noreferrer">
                <img src="{{ $src }}" alt="{{ $label }}" loading="lazy" decoding="async" class="max-h-10 w-auto max-w-[140px] object-contain md:max-h-12">
            </a>
        @else
            <img src="{{ $src }}" alt="{{ $label }}" loading="lazy" decoding="async" class="max-h-10 w-auto max-w-[140px] object-contain md:max-h-12">
        @endif
    </div>
@else
    <div {!! $block->editorAttributes() !!} class="flex h-14 items-center justify-center rounded-lg border border-dashed border-gray-200 text-xs text-gray-400 md:h-16">
        Add logo
    </div>
@endif
