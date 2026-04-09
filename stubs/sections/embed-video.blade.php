@schema([
    'name' => 'Embed video',
    'settings' => [
        ['id' => 'heading', 'type' => 'text', 'label' => 'Heading (optional)', 'default' => 'See it in action'],
        ['id' => 'subheading', 'type' => 'textarea', 'label' => 'Subheading (optional)', 'default' => ''],
        [
            'id' => 'video_url',
            'type' => 'video_url',
            'label' => 'YouTube or Vimeo URL',
            'default' => '',
        ],
        ['id' => 'caption', 'type' => 'text', 'label' => 'Caption under player (optional)', 'default' => ''],
        [
            'id' => 'align',
            'type' => 'text_alignment',
            'label' => 'Header alignment',
            'default' => 'center',
        ],
        ['id' => 'max_width', 'type' => 'select', 'label' => 'Player width', 'default' => '4xl',
            'options' => [
                ['value' => '3xl', 'label' => 'Narrow'],
                ['value' => '4xl', 'label' => 'Medium'],
                ['value' => '5xl', 'label' => 'Wide'],
            ],
        ],
    ],
    'presets' => [
        ['name' => 'Embed video'],
    ],
])

@php
    $url = trim((string) ($section->settings->video_url ?? ''));
    $youtubeId = null;
    $vimeoId = null;
    if ($url !== '') {
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([a-zA-Z0-9_-]{11})#', $url, $m)) {
            $youtubeId = $m[1];
        } elseif (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
            $vimeoId = $m[1];
        }
    }
    $align = (string) ($section->settings->align ?? 'center');
    $headerClass = match ($align) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
    $mw = (string) ($section->settings->max_width ?? '4xl');
    $mwClass = match ($mw) {
        '3xl' => 'max-w-3xl',
        '5xl' => 'max-w-5xl',
        default => 'max-w-4xl',
    };
@endphp

<section {!! $section->editorAttributes() !!} class="py-16 lg:py-20">
    <div class="container mx-auto px-4">
        @if (filled($section->settings->heading) || filled($section->settings->subheading))
            <div class="mx-auto mb-10 {{ $mwClass }} {{ $headerClass }}">
                @if (filled($section->settings->heading))
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 md:text-4xl">
                        {{ $section->settings->heading }}
                    </h2>
                @endif
                @if (filled($section->settings->subheading))
                    <p class="mt-4 text-lg text-gray-600">{{ $section->settings->subheading }}</p>
                @endif
            </div>
        @endif

        <div class="mx-auto {{ $mwClass }}">
            @if ($youtubeId !== null)
                <div class="relative aspect-video w-full overflow-hidden rounded-2xl bg-black shadow-lg ring-1 ring-gray-200">
                    <iframe class="absolute inset-0 h-full w-full"
                        src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?rel=0"
                        title="{{ filled($section->settings->caption) ? $section->settings->caption : 'YouTube video player' }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen loading="lazy"></iframe>
                </div>
            @elseif ($vimeoId !== null)
                <div class="relative aspect-video w-full overflow-hidden rounded-2xl bg-black shadow-lg ring-1 ring-gray-200">
                    <iframe class="absolute inset-0 h-full w-full"
                        src="https://player.vimeo.com/video/{{ $vimeoId }}"
                        title="{{ filled($section->settings->caption) ? $section->settings->caption : 'Vimeo video player' }}"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen loading="lazy"></iframe>
                </div>
            @else
                <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 py-16 text-center text-sm text-gray-500">
                    Paste a valid <strong>YouTube</strong> or <strong>Vimeo</strong> URL in the sidebar.
                </div>
            @endif
            @if (filled($section->settings->caption) && ($youtubeId !== null || $vimeoId !== null))
                <p class="mt-4 text-center text-sm text-gray-500">{{ $section->settings->caption }}</p>
            @endif
        </div>
    </div>
</section>
