@schema([
    'name' => 'Raw',
    'settings' => [
        ['id' => 'text', 'type' => 'text', 'label' => 'Text', 'default' => 'Default'],
    ],
    'presets' => [
        ['name' => 'Raw'],
    ],
])

<div {!! $section->editorAttributes() !!}>{{ $section->settings->text }}</div>
