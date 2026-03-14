@schema([
    'name' => 'Banner',
    'settings' => [
        ['id' => 'text', 'type' => 'text', 'label' => 'Text', 'default' => 'Banner'],
    ],
    'presets' => [
        ['name' => 'Banner'],
    ],
])

<div class="banner" {!! $section->editorAttributes() !!}>{{ $section->settings->text }}</div>
