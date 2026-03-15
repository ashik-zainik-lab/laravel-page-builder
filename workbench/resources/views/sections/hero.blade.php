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

<section {!! $section->editorAttributes() !!} class="relative py-20 lg:py-32 overflow-hidden">
    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight mb-6">
            {{ $section->settings->title }}
        </h1>
        <p class="text-lg md:text-xl text-text-body max-w-2xl mx-auto mb-10">
            {{ $section->settings->subtitle }}
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <button class="bg-accent hover:bg-accent-hover text-white px-8 py-3 rounded-xl font-bold transition-all hover:scale-105">
                Explore More
            </button>
        </div>
    </div>
</section>
