@schema([
    'name' => 'Hero',
    'settings' => [
        ['id' => 'title', 'type' => 'text', 'label' => 'Title', 'default' => 'Welcome'],
        ['id' => 'subtitle', 'type' => 'text', 'label' => 'Subtitle', 'default' => 'Hello World'],
    ],
    'presets' => [
        ['name' => 'Hero'],
    ],
])

<section {!! $section->editorAttributes() !!}>
    <h1>{{ $section->settings->title }}</h1>
    <p>{{ $section->settings->subtitle }}</p>
</section>
