@schema([
    'name' => 'Footer',
    'settings' => [
        ['id' => 'copyright_text', 'type' => 'text', 'label' => 'Copyright Text', 'default' => ''],
        ['id' => 'privacy_url', 'type' => 'url', 'label' => 'Privacy Policy URL', 'default' => '/privacy'],
        ['id' => 'terms_url', 'type' => 'url', 'label' => 'Terms of Service URL', 'default' => '/terms'],
        ['id' => 'facebook_url', 'type' => 'url', 'label' => 'Facebook URL', 'default' => ''],
        ['id' => 'instagram_url', 'type' => 'url', 'label' => 'Instagram URL', 'default' => ''],
        ['id' => 'twitter_url', 'type' => 'url', 'label' => 'X (Twitter) URL', 'default' => ''],
    ],
    'blocks' => [
        [
            'type' => 'footer-column',
            'name' => 'Column',
            'limit' => 4,
            'settings' => [
                ['id' => 'title', 'type' => 'text', 'label' => 'Heading', 'default' => 'Column'],
            ],
            'blocks' => [
                [
                    'type' => 'footer-link',
                    'name' => 'Link',
                    'settings' => [
                        ['id' => 'label', 'type' => 'text', 'label' => 'Label', 'default' => 'Link'],
                        ['id' => 'url', 'type' => 'url', 'label' => 'URL', 'default' => '#'],
                    ],
                ],
            ],
        ],
    ],
    'presets' => [
        [
            'name' => 'Default Footer',
            'blocks' => [
                [
                    'type' => 'footer-column',
                    'settings' => ['title' => 'Company'],
                    'blocks' => [
                        ['type' => 'footer-link', 'settings' => ['label' => 'About', 'url' => '#']],
                        ['type' => 'footer-link', 'settings' => ['label' => 'Careers', 'url' => '#']],
                        ['type' => 'footer-link', 'settings' => ['label' => 'Blog', 'url' => '#']],
                    ],
                ],
                [
                    'type' => 'footer-column',
                    'settings' => ['title' => 'Support'],
                    'blocks' => [
                        ['type' => 'footer-link', 'settings' => ['label' => 'Help Center', 'url' => '#']],
                        ['type' => 'footer-link', 'settings' => ['label' => 'Contact Us', 'url' => '#']],
                    ],
                ],
            ],
        ],
    ],
])

@php
    $socials = array_filter([
        ['icon' => 'fa-brands fa-facebook-f', 'url' => $section->settings->facebook_url],
        ['icon' => 'fa-brands fa-instagram',  'url' => $section->settings->instagram_url],
        ['icon' => 'fa-brands fa-x-twitter',  'url' => $section->settings->twitter_url],
    ], fn ($s) => !empty($s['url']));

    $copyright = $section->settings->copyright_text ?: '&copy; ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.';
@endphp

<footer {!! $section->editorAttributes() !!} class="bg-gray-900 text-gray-400">
    <div class="container mx-auto px-4 py-12">

        @if ($section->blocks->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
                @foreach ($section->blocks as $block)
                    @if ($block->type === 'footer-column')
                        <div {!! $block->editorAttributes() !!}>
                            <h6 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                                {{ $block->settings->title }}
                            </h6>
                            <ul class="space-y-3">
                                @foreach ($block->blocks as $child)
                                    @if ($child->type === 'footer-link')
                                        <li {!! $child->editorAttributes() !!}>
                                            <a href="{{ $child->settings->url }}"
                                                class="text-sm hover:text-white transition-colors">
                                                {{ $child->settings->label }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm">{!! $copyright !!}</p>
            <div class="flex items-center gap-6 text-sm">
                @if ($section->settings->privacy_url)
                    <a href="{{ $section->settings->privacy_url }}" class="hover:text-white transition-colors">Privacy Policy</a>
                @endif
                @if ($section->settings->terms_url)
                    <a href="{{ $section->settings->terms_url }}" class="hover:text-white transition-colors">Terms of Service</a>
                @endif
                @foreach ($socials as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener"
                        class="hover:text-white transition-colors">
                        <i class="{{ $social['icon'] }}"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
