@schema([
    'name' => 'Social link',
    'settings' => [
        [
            'id' => 'network',
            'type' => 'select',
            'label' => 'Network',
            'default' => 'twitter',
            'options' => [
                ['value' => 'twitter', 'label' => 'X (Twitter)'],
                ['value' => 'facebook', 'label' => 'Facebook'],
                ['value' => 'instagram', 'label' => 'Instagram'],
                ['value' => 'linkedin', 'label' => 'LinkedIn'],
                ['value' => 'youtube', 'label' => 'YouTube'],
                ['value' => 'github', 'label' => 'GitHub'],
                ['value' => 'tiktok', 'label' => 'TikTok'],
                ['value' => 'website', 'label' => 'Website / other'],
            ],
        ],
        ['id' => 'url', 'type' => 'url', 'label' => 'Profile URL', 'default' => '#'],
        ['id' => 'label', 'type' => 'text', 'label' => 'Accessible name', 'default' => ''],
    ],
    'presets' => [
        ['name' => 'Social link'],
    ],
])

@php
    $net = (string) ($block->settings->network ?? 'website');
    $icon = match ($net) {
        'facebook' => 'fa-brands fa-facebook-f',
        'instagram' => 'fa-brands fa-instagram',
        'linkedin' => 'fa-brands fa-linkedin-in',
        'youtube' => 'fa-brands fa-youtube',
        'github' => 'fa-brands fa-github',
        'tiktok' => 'fa-brands fa-tiktok',
        'twitter' => 'fa-brands fa-x-twitter',
        default => 'fa-solid fa-globe',
    };
    $defaultLabel = match ($net) {
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'github' => 'GitHub',
        'tiktok' => 'TikTok',
        'twitter' => 'X',
        default => 'Website',
    };
    $aria = filled($block->settings->label) ? $block->settings->label : $defaultLabel;
@endphp

<a href="{{ $block->settings->url }}" {!! $block->editorAttributes() !!}
    class="inline-flex h-12 w-12 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:border-accent hover:text-accent"
    target="_blank" rel="noopener noreferrer"
    aria-label="{{ $aria }}">
    <i class="{{ $icon }} text-lg" aria-hidden="true"></i>
</a>
