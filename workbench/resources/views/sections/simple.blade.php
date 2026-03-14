@schema([
    'name' => 'Simple',
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Default'],
    ],
    'presets' => [
        ['name' => 'Simple'],
    ],
])

<section {!! $section->editorAttributes() !!}><h1>{{ $section->settings->heading }}</h1></section>
