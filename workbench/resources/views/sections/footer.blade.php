@schema([
    'name' => 'Footer',
    'settings' => [
        ['id' => 'copyright', 'type' => 'text', 'label' => 'Copyright', 'default' => '© 2025'],
    ],
])

<footer {!! $section->editorAttributes() !!}>{{ $section->settings->copyright }}</footer>
