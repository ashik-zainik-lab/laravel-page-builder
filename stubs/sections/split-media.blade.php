@schema([
    'name' => 'Split media',
    'settings' => [
        [
            'id' => 'image',
            'type' => 'image_picker',
            'label' => 'Image',
            'default' => '',
        ],
        ['id' => 'image_alt', 'type' => 'text', 'label' => 'Image alt text', 'default' => ''],
        ['id' => 'eyebrow', 'type' => 'text', 'label' => 'Eyebrow', 'default' => 'Product'],
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Designed for clarity and speed'],
        ['id' => 'body', 'type' => 'richtext', 'label' => 'Body', 'default' => '<p>Use this section for product shots, app previews, or team photos. Toggle image position on larger screens.</p>'],
        [
            'id' => 'reverse',
            'type' => 'checkbox',
            'label' => 'Image on the right (desktop)',
            'default' => false,
        ],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Section background', 'default' => '#ffffff'],
    ],
    'blocks' => [
        ['type' => 'row'],
        ['type' => '@theme'],
    ],
    'presets' => [
        ['name' => 'Split media'],
    ],
])

@php
    $reverse = filter_var($section->settings->reverse ?? false, FILTER_VALIDATE_BOOLEAN);
    $img = trim((string) ($section->settings->image ?? ''));
    $imgOrder = $reverse ? 'md:order-2' : 'md:order-1';
    $textOrder = $reverse ? 'md:order-1' : 'md:order-2';
@endphp

<section {!! $section->editorAttributes() !!}
    style="background-color: {{ $section->settings->background_color }};"
    class="py-16 lg:py-24">
    <div class="container mx-auto px-4">
        @if ($img !== '')
            <div class="grid grid-cols-1 items-center gap-10 md:grid-cols-2 lg:gap-16">
                <div class="{{ $imgOrder }}">
                    <img src="{{ $img }}" alt="{{ $section->settings->image_alt }}" loading="lazy" decoding="async"
                        class="aspect-[4/3] w-full rounded-2xl object-cover shadow-lg">
                </div>
                <div class="{{ $textOrder }}">
                    @if (filled($section->settings->eyebrow))
                        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-accent">
                            {{ $section->settings->eyebrow }}
                        </p>
                    @endif
                    <h2 class="mb-6 text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                        {{ $section->settings->heading }}
                    </h2>
                    <div class="prose prose-gray max-w-none text-gray-600">
                        {!! $section->settings->body !!}
                    </div>
                    <div class="mt-8">
                        @blocks($section)
                    </div>
                </div>
            </div>
        @else
            <div class="mx-auto max-w-3xl">
                @if (filled($section->settings->eyebrow))
                    <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-accent">
                        {{ $section->settings->eyebrow }}
                    </p>
                @endif
                <h2 class="mb-6 text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                    {{ $section->settings->heading }}
                </h2>
                <div class="prose prose-gray max-w-none text-gray-600">
                    {!! $section->settings->body !!}
                </div>
                <div class="mt-8">
                    @blocks($section)
                </div>
            </div>
        @endif
    </div>
</section>
