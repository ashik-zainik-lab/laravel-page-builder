@schema([
    'name' => 'Banner',
    'settings' => [
        ['id' => 'text', 'type' => 'text', 'label' => 'Text', 'default' => 'Banner'],
    ],
    'presets' => [
        ['name' => 'Banner'],
    ],
])

<div class="banner bg-bg-card border border-border-dark rounded-xl p-8 flex flex-col items-center gap-6" {!! $section->editorAttributes() !!}>
    <h3 class="text-2xl font-bold text-white">{{ $section->settings->text }}</h3>
    <img class="rounded-lg shadow-lg max-w-full h-auto" src="https://picsum.photos/seed/picsum/600/400" alt="{{ $section->settings->text }}">
</div>
