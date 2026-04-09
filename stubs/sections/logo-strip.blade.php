@schema([
    'name' => 'Logo strip',
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Trusted by innovative teams'],
        ['id' => 'subheading', 'type' => 'text', 'label' => 'Subheading (optional)', 'default' => ''],
    ],
    'blocks' => [
        ['type' => 'logo-strip-item'],
    ],
    'presets' => [
        [
            'name' => 'Logo strip',
            'blocks' => [
                ['type' => 'logo-strip-item', 'settings' => ['image' => '', 'label' => 'Company A', 'url' => '']],
                ['type' => 'logo-strip-item', 'settings' => ['image' => '', 'label' => 'Company B', 'url' => '']],
                ['type' => 'logo-strip-item', 'settings' => ['image' => '', 'label' => 'Company C', 'url' => '']],
                ['type' => 'logo-strip-item', 'settings' => ['image' => '', 'label' => 'Company D', 'url' => '']],
            ],
        ],
    ],
])

<section {!! $section->editorAttributes() !!} class="border-y border-gray-100 bg-gray-50/80 py-12">
    <div class="container mx-auto px-4">
        @if (filled($section->settings->heading))
            <p class="text-center text-sm font-semibold uppercase tracking-wider text-gray-500">
                {{ $section->settings->heading }}
            </p>
        @endif
        @if (filled($section->settings->subheading))
            <p class="mt-2 text-center text-sm text-gray-500">{{ $section->settings->subheading }}</p>
        @endif
        <div class="mt-10 flex flex-wrap items-center justify-center gap-x-12 gap-y-8 md:gap-x-16">
            @blocks($section)
        </div>
    </div>
</section>
