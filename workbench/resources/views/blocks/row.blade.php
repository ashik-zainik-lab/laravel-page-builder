@schema([
    'name' => 'Row',
    'settings' => [
        [
            'id' => 'columns',
            'type' => 'select',
            'label' => 'Columns',
            'default' => '2',
            'options' => [
                ['value' => '1', 'label' => '1 Column'],
                ['value' => '2', 'label' => '2 Columns'],
                ['value' => '3', 'label' => '3 Columns'],
                ['value' => '4', 'label' => '4 Columns'],
            ],
        ],
        [
            'id' => 'gap',
            'type' => 'select',
            'label' => 'Gap',
            'default' => '6',
            'options' => [
                ['value' => '2', 'label' => 'Small'],
                ['value' => '6', 'label' => 'Medium'],
                ['value' => '10', 'label' => 'Large'],
            ],
        ],
    ],
    'blocks' => [
        ['type' => 'column', 'name' => 'Column'],
    ],
    'presets' => [
        [
            'name' => 'Two Columns',
            'settings' => ['columns' => '2', 'gap' => '6'],
            'blocks' => [
                ['type' => 'column'],
                ['type' => 'column'],
            ],
        ],
    ],
])

@php
    $gridCols = match((string) $block->settings->columns) {
        '1' => 'grid-cols-1',
        '2' => 'grid-cols-1 md:grid-cols-2',
        '3' => 'grid-cols-1 md:grid-cols-3',
        '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-2',
    };

    $gap = match((string) $block->settings->gap) {
        '2' => 'gap-2',
        '10' => 'gap-10',
        default => 'gap-6',
    };
@endphp

<div class="grid {{ $gridCols }} {{ $gap }} w-full" {!! $block->editorAttributes() !!}>
    @blocks($block)
</div>
