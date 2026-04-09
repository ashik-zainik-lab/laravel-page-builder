@schema([
    'name' => 'FAQ',
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Frequently asked questions'],
        ['id' => 'subheading', 'type' => 'textarea', 'label' => 'Subheading', 'default' => 'Everything you need to know before you get started.'],
    ],
    'blocks' => [
        ['type' => 'faq-item'],
    ],
    'presets' => [
        [
            'name' => 'FAQ',
            'blocks' => [
                [
                    'type' => 'faq-item',
                    'settings' => [
                        'question' => 'Can I try before I buy?',
                        'answer' => '<p>Yes. Start with a free trial or contact us for a demo tailored to your team.</p>',
                    ],
                ],
                [
                    'type' => 'faq-item',
                    'settings' => [
                        'question' => 'How does billing work?',
                        'answer' => '<p>Plans are billed monthly or annually. You can upgrade, downgrade, or cancel from your account settings.</p>',
                    ],
                ],
                [
                    'type' => 'faq-item',
                    'settings' => [
                        'question' => 'Is my data secure?',
                        'answer' => '<p>We use industry-standard encryption in transit and at rest. Enterprise plans include additional compliance options.</p>',
                    ],
                ],
            ],
        ],
    ],
])

<section {!! $section->editorAttributes() !!} class="py-16 lg:py-24">
    <div class="container mx-auto max-w-3xl px-4">
        @if (filled($section->settings->heading))
            <h2 class="text-center text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                {{ $section->settings->heading }}
            </h2>
        @endif
        @if (filled($section->settings->subheading))
            <p class="mt-4 text-center text-lg text-gray-600">{{ $section->settings->subheading }}</p>
        @endif
        <div class="mt-12 divide-y divide-gray-200 border-t border-b border-gray-200">
            @blocks($section)
        </div>
    </div>
</section>
