@schema([
    'name' => 'Banner',
    'settings' => [
        ['id' => 'text', 'type' => 'text', 'label' => 'Text', 'default' => 'Banner'],
    ],
    'presets' => [
        ['name' => 'Banner'],
    ],
])

<div class="banner" {!! $section->editorAttributes() !!}>
    <h3>{{ $section->settings->text }}</h3>
    <img src="https://picsum.photos/seed/picsum/200/300" alt="{{ $section->settings->text }}">
</div>
