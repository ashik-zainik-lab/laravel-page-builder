@schema([
    'name' => 'Hero',
    'settings' => [
        ['id' => 'title', 'type' => 'text', 'label' => 'Title', 'default' => 'Welcome to Our Site'],
        ['id' => 'subtitle', 'type' => 'text', 'label' => 'Subtitle', 'default' => 'We build amazing experiences for the web.'],
        ['id' => 'button_label', 'type' => 'text', 'label' => 'Button Label', 'default' => 'Get Started'],
        ['id' => 'button_url', 'type' => 'url', 'label' => 'Button URL', 'default' => '#'],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Background Color', 'default' => '#f8fafc'],
    ],
    'presets' => [
        ['name' => 'Hero'],
    ],
])

<section {!! $section->editorAttributes() !!}
    style="background-color: {{ $section->settings->background_color }};"
    class="relative py-20 lg:py-32 overflow-hidden">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 tracking-tight mb-6">
            {{ $section->settings->title }}
        </h1>
        <p class="text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10">
            {{ $section->settings->subtitle }}
        </p>
        @if ($section->settings->button_label)
            <a href="{{ $section->settings->button_url }}"
                class="inline-block bg-accent hover:bg-accent-hover text-white px-8 py-3 rounded-xl font-bold transition-all hover:scale-105">
                {{ $section->settings->button_label }}
            </a>
        @endif
    </div>
</section>
