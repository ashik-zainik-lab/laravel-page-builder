@schema([
    'name' => 'Test',
    'blocks' => [['type' => '@theme']],
    'presets' => [
        ['name' => 'Test', 'blocks' => [['type' => 'text']]],
    ],
])

<div class="test-section" {!! $section->editorAttributes() !!}>
    @blocks($section)
</div>
