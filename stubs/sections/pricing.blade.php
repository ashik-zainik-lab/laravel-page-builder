@schema([
    'name' => 'Pricing',
    'max_blocks' => 4,
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Simple, transparent pricing'],
        ['id' => 'subheading', 'type' => 'textarea', 'label' => 'Subheading', 'default' => 'Pick a plan that fits your team. You can change or cancel anytime.'],
        [
            'id' => 'header_align',
            'type' => 'text_alignment',
            'label' => 'Header alignment',
            'default' => 'center',
        ],
    ],
    'blocks' => [
        ['type' => 'pricing-tier'],
    ],
    'presets' => [
        [
            'name' => 'Pricing (3 plans)',
            'blocks' => [
                [
                    'type' => 'pricing-tier',
                    'settings' => [
                        'name' => 'Starter',
                        'price' => '$19',
                        'period' => '/mo',
                        'description' => 'For individuals',
                        'badge' => '',
                        'features' => "5 projects\nEmail support\nBasic analytics",
                        'button_label' => 'Choose Starter',
                        'button_url' => '#',
                        'highlighted' => false,
                    ],
                ],
                [
                    'type' => 'pricing-tier',
                    'settings' => [
                        'name' => 'Pro',
                        'price' => '$49',
                        'period' => '/mo',
                        'description' => 'For growing teams',
                        'badge' => 'Popular',
                        'features' => "Unlimited projects\nPriority support\nAdvanced analytics\nSSO",
                        'button_label' => 'Choose Pro',
                        'button_url' => '#',
                        'highlighted' => true,
                    ],
                ],
                [
                    'type' => 'pricing-tier',
                    'settings' => [
                        'name' => 'Enterprise',
                        'price' => 'Custom',
                        'period' => '',
                        'description' => 'For large organizations',
                        'badge' => '',
                        'features' => "Dedicated success manager\nSLA & security review\nCustom contracts",
                        'button_label' => 'Contact sales',
                        'button_url' => '#',
                        'highlighted' => false,
                    ],
                ],
            ],
        ],
    ],
])

@php
    $hAlign = (string) ($section->settings->header_align ?? 'center');
    $headerClass = match ($hAlign) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
@endphp

<section {!! $section->editorAttributes() !!} class="bg-gray-50 py-16 lg:py-24">
    <div class="container mx-auto px-4">
        <header class="mx-auto mb-14 max-w-2xl {{ $headerClass }} {{ $hAlign === 'center' ? 'mx-auto' : '' }}">
            @if (filled($section->settings->heading))
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                    {{ $section->settings->heading }}
                </h2>
            @endif
            @if (filled($section->settings->subheading))
                <p class="mt-4 text-lg text-gray-600">{{ $section->settings->subheading }}</p>
            @endif
        </header>
        <div class="mx-auto grid max-w-6xl items-stretch gap-8 md:grid-cols-3">
            @blocks($section)
        </div>
    </div>
</section>
