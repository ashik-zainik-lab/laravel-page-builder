@schema([
    'name' => 'Text',
    'settings' => [
        ['id' => 'content', 'type' => 'text', 'default' => 'Default text', 'label' => 'Content'],
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
            'name' => 'Default Text',
            'settings' => ['content' => 'Sample text block.', 'alignment' => 'left'],
        ],
    ],
])

@php
    $alignmentClass = match((string) ($block->settings->alignment ?? 'left')) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp

<div class="py-4 {{ $alignmentClass }} text-text-body" {!! $block->editorAttributes() !!}>
    {{ $block->settings->content ?? '' }}
</div>
