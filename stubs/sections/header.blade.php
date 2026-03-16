@schema([
    'name' => 'Header',
    'settings' => [
        ['id' => 'logo_text', 'type' => 'text', 'label' => 'Logo Text', 'default' => 'My Site'],
        ['id' => 'sticky', 'type' => 'checkbox', 'label' => 'Sticky Header', 'default' => true],
    ],
    'presets' => [
        [
            'name' => 'Default Header',
            'settings' => [
                'logo_text' => 'My Site',
                'sticky' => true,
            ],
        ],
    ],
])

@php
    $stickyClass = $section->settings->sticky ? 'sticky top-0 z-50' : '';
@endphp

<header {!! $section->editorAttributes() !!}
    class="{{ $stickyClass }} w-full border-b border-gray-200 bg-white/90 backdrop-blur-md">
    <div class="container mx-auto flex h-16 items-center justify-between px-4">
        <div class="flex items-center gap-3">
            <a href="/" class="text-xl font-bold text-gray-900">
                {{ $section->settings->logo_text }}
            </a>
        </div>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
            <a href="#" class="hover:text-accent transition-colors">Home</a>
            <a href="#" class="hover:text-accent transition-colors">About</a>
            <a href="#" class="hover:text-accent transition-colors">Contact</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="#" class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                Get Started
            </a>
        </div>
    </div>
</header>
