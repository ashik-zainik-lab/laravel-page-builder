@schema([
    'name' => 'Plain Block',
    'settings' => [
        ['id' => 'content', 'type' => 'text', 'default' => 'Plain text content', 'label' => 'Content'],
    ],
])

<div class="plain-block" {!! $block->editorAttributes() !!}>
    {{ $block->settings->content }}
</div>
