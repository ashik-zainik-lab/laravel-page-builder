@schema([
    'name' => 'Content',
    'blocks' => [
        [
            'type' => 'row',
            'name' => 'Row',
            'settings' => [
                ['id' => 'columns', 'type' => 'select', 'label' => 'Columns', 'default' => '2'],
            ]
        ],
    ],
    'presets' => [
        ['name' => 'Content'],
    ],
])

<div {!! $section->editorAttributes() !!}>@blocks($section)</div>
