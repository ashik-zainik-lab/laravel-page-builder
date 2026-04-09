@schema([
    'name' => 'Heading',
    'settings' => [
        ['id' => 'text', 'type' => 'text', 'label' => 'Text', 'default' => 'Section heading'],
        [
            'id' => 'level',
            'type' => 'select',
            'label' => 'Level',
            'default' => 'h2',
            'options' => [
                ['value' => 'h2', 'label' => 'Heading 2'],
                ['value' => 'h3', 'label' => 'Heading 3'],
                ['value' => 'h4', 'label' => 'Heading 4'],
            ],
        ],
        [
            'id' => 'align',
            'type' => 'text_alignment',
            'label' => 'Alignment',
            'default' => 'left',
        ],
    ],
    'presets' => [
        ['name' => 'Heading'],
    ],
])

@php
    $level = (string) ($block->settings->level ?? 'h2');
    if (! in_array($level, ['h2', 'h3', 'h4'], true)) {
        $level = 'h2';
    }
    $align = (string) ($block->settings->align ?? 'left');
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
    $sizeClass = match ($level) {
        'h4' => 'text-xl font-bold md:text-2xl',
        'h3' => 'text-2xl font-bold md:text-3xl',
        default => 'text-3xl font-extrabold tracking-tight md:text-4xl',
    };
@endphp

<div class="{{ $alignClass }}" {!! $block->editorAttributes() !!}>
    @if ($level === 'h2')
        <h2 class="{{ $sizeClass }} text-gray-900">{{ $block->settings->text }}</h2>
    @elseif ($level === 'h3')
        <h3 class="{{ $sizeClass }} text-gray-900">{{ $block->settings->text }}</h3>
    @else
        <h4 class="{{ $sizeClass }} text-gray-900">{{ $block->settings->text }}</h4>
    @endif
</div>
