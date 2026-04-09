@schema([
    'name' => 'Video embed',
    'settings' => [
        [
            'id' => 'video_url',
            'type' => 'video_url',
            'label' => 'YouTube or Vimeo URL',
            'default' => '',
        ],
        ['id' => 'caption', 'type' => 'text', 'label' => 'Caption (optional)', 'default' => ''],
        [
            'id' => 'rounded',
            'type' => 'select',
            'label' => 'Corners',
            'default' => 'xl',
            'options' => [
                ['value' => 'lg', 'label' => 'Large'],
                ['value' => 'xl', 'label' => 'XL'],
                ['value' => 'none', 'label' => 'Square'],
            ],
        ],
    ],
    'presets' => [
        ['name' => 'Video embed'],
    ],
])

@php
    $url = trim((string) ($block->settings->video_url ?? ''));
    $youtubeId = null;
    $vimeoId = null;
    if ($url !== '') {
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([a-zA-Z0-9_-]{11})#', $url, $m)) {
            $youtubeId = $m[1];
        } elseif (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
            $vimeoId = $m[1];
        }
    }
    $round = (string) ($block->settings->rounded ?? 'xl');
    $roundClass = match ($round) {
        'lg' => 'rounded-xl',
        'none' => 'rounded-none',
        default => 'rounded-2xl',
    };
@endphp

<div {!! $block->editorAttributes() !!} class="w-full">
    @if ($youtubeId !== null)
        <div class="relative aspect-video w-full overflow-hidden bg-black shadow-md ring-1 ring-gray-200 {{ $roundClass }}">
            <iframe class="absolute inset-0 h-full w-full"
                src="https://www.youtube-nocookie.com/embed/{{ $youtubeId }}?rel=0"
                title="{{ filled($block->settings->caption) ? $block->settings->caption : 'YouTube video' }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen loading="lazy"></iframe>
        </div>
    @elseif ($vimeoId !== null)
        <div class="relative aspect-video w-full overflow-hidden bg-black shadow-md ring-1 ring-gray-200 {{ $roundClass }}">
            <iframe class="absolute inset-0 h-full w-full"
                src="https://player.vimeo.com/video/{{ $vimeoId }}"
                title="{{ filled($block->settings->caption) ? $block->settings->caption : 'Vimeo video' }}"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen loading="lazy"></iframe>
        </div>
    @else
        <div class="border-2 border-dashed border-gray-200 bg-gray-50 py-12 text-center text-xs text-gray-400 {{ $roundClass }}">
            Add a YouTube or Vimeo URL
        </div>
    @endif
    @if (filled($block->settings->caption) && ($youtubeId !== null || $vimeoId !== null))
        <p class="mt-2 text-center text-xs text-gray-500">{{ $block->settings->caption }}</p>
    @endif
</div>
