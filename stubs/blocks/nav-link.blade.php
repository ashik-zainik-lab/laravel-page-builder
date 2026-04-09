@schema([
    'name' => 'Navigation link',
    'settings' => [
        ['id' => 'label', 'type' => 'text', 'label' => 'Label', 'default' => 'Menu'],
        ['id' => 'url', 'type' => 'url', 'label' => 'URL', 'default' => '#'],
        ['id' => 'new_tab', 'type' => 'checkbox', 'label' => 'Open in new tab', 'default' => false],
    ],
    'presets' => [
        ['name' => 'Navigation link'],
    ],
])

<a href="{{ $block->settings->url }}" {!! $block->editorAttributes() !!}
    class="hover:text-accent transition-colors"
    @if ($block->settings->new_tab) target="_blank" rel="noopener noreferrer" @endif>
    {{ $block->settings->label }}
</a>
