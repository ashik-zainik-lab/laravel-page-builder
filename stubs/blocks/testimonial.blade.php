@schema([
    'name' => 'Testimonial',
    'settings' => [
        ['id' => 'quote', 'type' => 'richtext', 'label' => 'Quote', 'default' => '<p>The product exceeded our expectations. Onboarding was smooth and support is responsive.</p>'],
        ['id' => 'author', 'type' => 'text', 'label' => 'Author name', 'default' => 'Alex Morgan'],
        ['id' => 'role', 'type' => 'text', 'label' => 'Role / company', 'default' => 'CEO, Acme Inc.'],
        [
            'id' => 'photo',
            'type' => 'image_picker',
            'label' => 'Photo (optional)',
            'default' => '',
        ],
        [
            'id' => 'rating',
            'type' => 'select',
            'label' => 'Star rating',
            'default' => '5',
            'options' => [
                ['value' => '0', 'label' => 'None'],
                ['value' => '4', 'label' => '4 stars'],
                ['value' => '5', 'label' => '5 stars'],
            ],
        ],
    ],
    'presets' => [
        ['name' => 'Testimonial'],
    ],
])

@php
    $photo = trim((string) ($block->settings->photo ?? ''));
    $rating = (int) ($block->settings->rating ?? 5);
    $rating = max(0, min(5, $rating));
@endphp

<article {!! $block->editorAttributes() !!} class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
    @if ($rating > 0)
        <p class="mb-4 text-amber-500" role="img" aria-label="{{ $rating }} out of 5 stars">
            @for ($i = 0; $i < $rating; $i++)
                <span aria-hidden="true">★</span>
            @endfor
        </p>
    @endif
    <blockquote class="prose prose-gray mb-6 max-w-none flex-1 text-gray-600">
        {!! $block->settings->quote !!}
    </blockquote>
    <footer class="flex items-center gap-4 border-t border-gray-100 pt-6">
        @if ($photo !== '')
            <img src="{{ $photo }}" alt="" width="48" height="48" loading="lazy" decoding="async"
                class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-gray-100">
        @endif
        <div>
            <cite class="not-italic text-sm font-semibold text-gray-900">{{ $block->settings->author }}</cite>
            @if (filled($block->settings->role))
                <p class="text-sm text-gray-500">{{ $block->settings->role }}</p>
            @endif
        </div>
    </footer>
</article>
