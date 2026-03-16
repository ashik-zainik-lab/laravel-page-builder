@schema([
    'name' => 'Text',
    'settings' => [
        ['id' => 'content', 'type' => 'text', 'label' => 'Content', 'default' => 'Add your text here.'],
        [
            'id' => 'alignment',
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
        [
            'name' => 'Text Block',
            'settings' => ['content' => 'Add your text here.', 'alignment' => 'left'],
        ],
    ],
])

@php
    $alignClass = match((string) ($block->settings->alignment ?? 'left')) {
        'center' => 'text-center',
        'right'  => 'text-right',
        default  => 'text-left',
    };
@endphp

<div class="{{ $alignClass }} text-gray-600" {!! $block->editorAttributes() !!}>
    {{ $block->settings->content ?? '' }}
</div>
