@schema([
    'name' => 'CTA banner',
    'settings' => [
        ['id' => 'eyebrow', 'type' => 'text', 'label' => 'Eyebrow', 'default' => ''],
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading', 'default' => 'Ready to move faster?'],
        ['id' => 'text', 'type' => 'textarea', 'label' => 'Supporting text', 'default' => 'Join teams who ship better landing pages without waiting on engineering.'],
        ['id' => 'button_label', 'type' => 'text', 'label' => 'Button label', 'default' => 'Get started'],
        ['id' => 'button_url', 'type' => 'url', 'label' => 'Button URL', 'default' => '#'],
        [
            'id' => 'align',
            'type' => 'text_alignment',
            'label' => 'Alignment',
            'default' => 'center',
        ],
        ['id' => 'background_color', 'type' => 'color', 'label' => 'Background', 'default' => '#0f172a'],
        ['id' => 'text_color', 'type' => 'color', 'label' => 'Text color', 'default' => '#ffffff'],
        [
            'id' => 'button_style',
            'type' => 'select',
            'label' => 'Button style',
            'default' => 'solid',
            'options' => [
                ['value' => 'solid', 'label' => 'Solid (accent)'],
                ['value' => 'outline', 'label' => 'Outline'],
            ],
        ],
    ],
    'presets' => [
        ['name' => 'CTA banner'],
    ],
])

@php
    $align = (string) ($section->settings->align ?? 'center');
    $alignClass = match ($align) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
    $btnSolid = ($section->settings->button_style ?? 'solid') === 'solid';
@endphp

<section {!! $section->editorAttributes() !!}
    style="background-color: {{ $section->settings->background_color }}; color: {{ $section->settings->text_color }};"
    class="py-16 lg:py-20">
    <div class="container mx-auto max-w-4xl px-4 {{ $alignClass }}">
        @if (filled($section->settings->eyebrow))
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider opacity-80">
                {{ $section->settings->eyebrow }}
            </p>
        @endif
        <h2 class="text-3xl font-extrabold tracking-tight md:text-4xl">
            {{ $section->settings->heading }}
        </h2>
        @if (filled($section->settings->text))
            <p class="mt-4 text-lg opacity-90 {{ $align === 'center' ? 'mx-auto max-w-2xl' : '' }}">
                {{ $section->settings->text }}
            </p>
        @endif
        @if (filled($section->settings->button_label))
            <a href="{{ $section->settings->button_url }}"
                @class([
                    'mt-8 inline-flex items-center justify-center rounded-xl px-8 py-3 text-sm font-bold transition-all',
                    'bg-accent text-white hover:bg-accent-hover hover:scale-[1.02]' => $btnSolid,
                    'border-2 border-current hover:bg-white/10' => ! $btnSolid,
                ])>
                {{ $section->settings->button_label }}
            </a>
        @endif
    </div>
</section>
