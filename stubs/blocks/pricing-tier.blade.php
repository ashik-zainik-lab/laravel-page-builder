@schema([
    'name' => 'Pricing tier',
    'settings' => [
        ['id' => 'name', 'type' => 'text', 'label' => 'Plan name', 'default' => 'Pro'],
        ['id' => 'price', 'type' => 'text', 'label' => 'Price', 'default' => '$49'],
        ['id' => 'period', 'type' => 'text', 'label' => 'Period label', 'default' => '/month'],
        ['id' => 'description', 'type' => 'text', 'label' => 'Short line under price', 'default' => 'Billed monthly. Cancel anytime.'],
        ['id' => 'badge', 'type' => 'text', 'label' => 'Badge (optional)', 'default' => ''],
        ['id' => 'features', 'type' => 'textarea', 'label' => 'Features (one per line)', 'default' => "Unlimited projects\nPriority support\nAdvanced analytics"],
        ['id' => 'button_label', 'type' => 'text', 'label' => 'Button label', 'default' => 'Choose plan'],
        ['id' => 'button_url', 'type' => 'url', 'label' => 'Button URL', 'default' => '#'],
        [
            'id' => 'highlighted',
            'type' => 'checkbox',
            'label' => 'Highlight this plan',
            'default' => false,
        ],
    ],
    'presets' => [
        ['name' => 'Pricing tier'],
    ],
])

@php
    $highlight = filter_var($block->settings->highlighted ?? false, FILTER_VALIDATE_BOOLEAN);
    $rawFeatures = (string) ($block->settings->features ?? '');
    $features = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $rawFeatures))));
    $cardClass = $highlight
        ? 'relative border-2 border-accent bg-white shadow-xl shadow-accent/10 ring-1 ring-accent/20 scale-[1.02] z-10'
        : 'relative border border-gray-200 bg-white shadow-sm';
@endphp

<div {!! $block->editorAttributes() !!} class="{{ $cardClass }} rounded-2xl p-8 flex flex-col h-full">
    @if (filled($block->settings->badge))
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-accent px-3 py-1 text-xs font-bold text-white">
            {{ $block->settings->badge }}
        </span>
    @endif
    <h3 class="text-lg font-semibold text-gray-900">{{ $block->settings->name }}</h3>
    <p class="mt-4 flex items-baseline gap-1">
        <span class="text-4xl font-extrabold tracking-tight text-gray-900">{{ $block->settings->price }}</span>
        <span class="text-sm font-medium text-gray-500">{{ $block->settings->period }}</span>
    </p>
    @if (filled($block->settings->description))
        <p class="mt-2 text-sm text-gray-500">{{ $block->settings->description }}</p>
    @endif
    <ul class="mt-8 space-y-3 text-sm text-gray-600 flex-1">
        @foreach ($features as $line)
            <li class="flex gap-2">
                <span class="mt-0.5 text-accent shrink-0" aria-hidden="true">✓</span>
                <span>{{ $line }}</span>
            </li>
        @endforeach
    </ul>
    @if (filled($block->settings->button_label))
        <a href="{{ $block->settings->button_url }}"
            @class([
                'mt-8 inline-flex w-full items-center justify-center rounded-xl px-5 py-3 text-sm font-bold transition-all',
                'bg-accent text-white hover:bg-accent-hover hover:scale-[1.02]' => $highlight,
                'border-2 border-gray-300 text-gray-900 hover:border-accent hover:text-accent' => ! $highlight,
            ])>
            {{ $block->settings->button_label }}
        </a>
    @endif
</div>
