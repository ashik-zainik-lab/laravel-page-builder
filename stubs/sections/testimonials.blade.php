@schema([
    'name' => 'Testimonials',
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Loved by teams worldwide'],
        ['id' => 'subheading', 'type' => 'textarea', 'label' => 'Subheading', 'default' => 'Real feedback from people who use our product every day.'],
        [
            'id' => 'columns',
            'type' => 'select',
            'label' => 'Columns (desktop)',
            'default' => '3',
            'options' => [
                ['value' => '2', 'label' => '2 columns'],
                ['value' => '3', 'label' => '3 columns'],
            ],
        ],
    ],
    'blocks' => [
        ['type' => 'testimonial'],
    ],
    'presets' => [
        [
            'name' => 'Testimonials',
            'blocks' => [
                [
                    'type' => 'testimonial',
                    'settings' => [
                        'quote' => '<p>We shipped our new site in days instead of weeks. The editor is intuitive for the whole marketing team.</p>',
                        'author' => 'Samira Khan',
                        'role' => 'Marketing Director',
                        'photo' => '',
                        'rating' => '5',
                    ],
                ],
                [
                    'type' => 'testimonial',
                    'settings' => [
                        'quote' => '<p>Finally a CMS that does not fight us. Structured sections keep our brand consistent.</p>',
                        'author' => 'Jordan Lee',
                        'role' => 'Founder, Northwind',
                        'photo' => '',
                        'rating' => '5',
                    ],
                ],
                [
                    'type' => 'testimonial',
                    'settings' => [
                        'quote' => '<p>Support was helpful during migration. Performance on mobile is excellent.</p>',
                        'author' => 'Casey Ng',
                        'role' => 'Engineering Lead',
                        'photo' => '',
                        'rating' => '4',
                    ],
                ],
            ],
        ],
    ],
])

@php
    $cols = (string) ($section->settings->columns ?? '3');
    $gridClass = $cols === '2' ? 'md:grid-cols-2' : 'md:grid-cols-3';
@endphp

<section {!! $section->editorAttributes() !!} class="py-16 lg:py-24">
    <div class="container mx-auto px-4">
        <header class="mx-auto mb-14 max-w-2xl text-center">
            @if (filled($section->settings->heading))
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                    {{ $section->settings->heading }}
                </h2>
            @endif
            @if (filled($section->settings->subheading))
                <p class="mt-4 text-lg text-gray-600">{{ $section->settings->subheading }}</p>
            @endif
        </header>
        <div class="mx-auto grid max-w-6xl gap-8 {{ $gridClass }}">
            @blocks($section)
        </div>
    </div>
</section>
