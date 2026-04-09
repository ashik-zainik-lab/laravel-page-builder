@schema([
    'name' => 'Stat',
    'settings' => [
        ['id' => 'prefix', 'type' => 'text', 'label' => 'Prefix (optional)', 'default' => ''],
        ['id' => 'value', 'type' => 'text', 'label' => 'Value', 'default' => '10'],
        ['id' => 'suffix', 'type' => 'text', 'label' => 'Suffix (optional)', 'default' => 'k+'],
        ['id' => 'label', 'type' => 'text', 'label' => 'Label', 'default' => 'Active customers'],
        ['id' => 'hint', 'type' => 'text', 'label' => 'Small caption (optional)', 'default' => ''],
    ],
    'presets' => [
        ['name' => 'Stat'],
    ],
])

<div {!! $block->editorAttributes() !!} class="px-2 text-center sm:px-4">
    <p class="text-4xl font-extrabold tracking-tight text-gray-900 tabular-nums sm:text-5xl">
        @if (filled($block->settings->prefix))
            <span class="text-accent">{{ $block->settings->prefix }}</span>
        @endif
        {{ $block->settings->value }}
        @if (filled($block->settings->suffix))
            <span class="text-gray-400">{{ $block->settings->suffix }}</span>
        @endif
    </p>
    <p class="mt-3 text-sm font-semibold text-gray-900 sm:text-base">
        {{ $block->settings->label }}
    </p>
    @if (filled($block->settings->hint))
        <p class="mt-1 text-xs text-gray-500 sm:text-sm">{{ $block->settings->hint }}</p>
    @endif
</div>
