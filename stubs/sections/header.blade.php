@schema([
    'name' => 'Header',
    'settings' => [
        ['id' => 'logo_text', 'type' => 'text', 'label' => 'Logo text', 'default' => 'My Site'],
        ['id' => 'logo_url', 'type' => 'url', 'label' => 'Logo URL', 'default' => '/'],
        ['id' => 'enabled', 'type' => 'checkbox', 'label' => 'Show header', 'default' => true],
        ['id' => 'sticky', 'type' => 'checkbox', 'label' => 'Sticky header', 'default' => true],
        ['id' => 'cta_label', 'type' => 'text', 'label' => 'CTA label', 'default' => 'Get Started'],
        ['id' => 'cta_url', 'type' => 'url', 'label' => 'CTA URL', 'default' => '#'],
    ],
    'blocks' => [
        ['type' => 'nav-link'],
        ['type' => 'nav-menu'],
    ],
    'presets' => [
        [
            'name' => 'Default header',
            'settings' => [
                'logo_text' => 'My Site',
                'logo_url' => '/',
                'enabled' => true,
                'sticky' => true,
                'cta_label' => 'Get Started',
                'cta_url' => '#',
            ],
            'blocks' => [
                ['type' => 'nav-link', 'settings' => ['label' => 'Home', 'url' => '/']],
                [
                    'type' => 'nav-menu',
                    'settings' => ['label' => 'Services', 'url' => '/services'],
                    'blocks' => [
                        ['type' => 'nav-sub-link', 'settings' => ['label' => 'Web Design', 'url' => '/services/web-design']],
                        ['type' => 'nav-sub-link', 'settings' => ['label' => 'SEO', 'url' => '/services/seo']],
                        ['type' => 'nav-sub-link', 'settings' => ['label' => 'Marketing', 'url' => '/services/marketing']],
                    ],
                ],
                ['type' => 'nav-link', 'settings' => ['label' => 'Contact', 'url' => '/contact']],
            ],
        ],
    ],
])

@php
    $stickyClass = $section->settings->sticky ? 'sticky top-0 z-50' : '';
@endphp

@if ($section->settings->enabled)
    <header {!! $section->editorAttributes() !!}
        class="{{ $stickyClass }} w-full border-b border-gray-200 bg-white/90 backdrop-blur-md">
        <div class="container mx-auto flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <a href="{{ $section->settings->logo_url }}" class="text-xl font-bold text-gray-900">
                    {{ $section->settings->logo_text }}
                </a>
            </div>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-600">
                @blocks($section)
            </nav>

            <div class="flex items-center gap-3">
                @if (filled($section->settings->cta_label))
                    <a href="{{ $section->settings->cta_url }}"
                        class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        {{ $section->settings->cta_label }}
                    </a>
                @endif
            </div>
        </div>
    </header>
@endif
