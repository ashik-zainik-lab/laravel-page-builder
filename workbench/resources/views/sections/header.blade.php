@schema([
    'name' => 'Header',
    'settings' => [
        ['id' => 'title', 'type' => 'text', 'label' => 'Title', 'default' => 'Default Header Title'],
    ],
])
<header {!! $section->editorAttributes() !!}>{{ $section->settings->title }}</header>
