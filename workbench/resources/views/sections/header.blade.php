@schema([
    'name' => 'Header',
    'settings' => [
        ['id' => 'title', 'type' => 'text', 'label' => 'Title', 'default' => 'My Site'],
        ['id' => 'logo_url', 'type' => 'url', 'label' => 'Logo URL', 'default' => '/'],
        ['id' => 'nav_1_label', 'type' => 'text', 'label' => 'Nav 1 label', 'default' => 'Home'],
        ['id' => 'nav_1_url', 'type' => 'url', 'label' => 'Nav 1 URL', 'default' => '/'],
        ['id' => 'nav_2_label', 'type' => 'text', 'label' => 'Nav 2 label', 'default' => 'Features'],
        ['id' => 'nav_2_url', 'type' => 'url', 'label' => 'Nav 2 URL', 'default' => '/features'],
        ['id' => 'nav_3_label', 'type' => 'text', 'label' => 'Nav 3 label', 'default' => 'About'],
        ['id' => 'nav_3_url', 'type' => 'url', 'label' => 'Nav 3 URL', 'default' => '/about'],
        ['id' => 'cta_label', 'type' => 'text', 'label' => 'CTA label', 'default' => 'Get Started'],
        ['id' => 'cta_url', 'type' => 'url', 'label' => 'CTA URL', 'default' => '#'],
    ],
    'blocks' => [
        ['type' => 'nav-link'],
    ],
    'presets' => [
        [
            'name' => 'My Site Header',
            'settings' => [
                'title' => 'My Site Header',
                'logo_url' => '/',
                'nav_1_label' => 'Home',
                'nav_1_url' => '/',
                'nav_2_label' => 'Features',
                'nav_2_url' => '/features',
                'nav_3_label' => 'About',
                'nav_3_url' => '/about',
                'cta_label' => 'Get Started',
                'cta_url' => '#',
            ],
            'blocks' => [
                ['type' => 'nav-link', 'settings' => ['label' => 'Home', 'url' => '/']],
                ['type' => 'nav-link', 'settings' => ['label' => 'Features', 'url' => '/features']],
                ['type' => 'nav-link', 'settings' => ['label' => 'About', 'url' => '/about']],
            ],
        ],
    ],
])

@php
    $hasBlockNav = !empty($section->order ?? []);
@endphp

<header {!! $section->editorAttributes() !!}
    class="sticky top-0 z-50 w-full border-b border-border-dark bg-bg-dark/80 backdrop-blur-md">
    <div class="container mx-auto flex h-16 items-center justify-between px-4">
        <div class="flex items-center gap-4">
            <a href="{{ $section->settings->logo_url }}" class="text-xl font-bold text-white">
                {{ $section->settings->title }}
            </a>
        </div>
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-text-body">
            @if ($hasBlockNav)
                @blocks($section)
            @else
                @if (filled($section->settings->nav_1_label))
                    <a href="{{ $section->settings->nav_1_url }}" class="hover:text-accent transition-colors">
                        {{ $section->settings->nav_1_label }}
                    </a>
                @endif
                @if (filled($section->settings->nav_2_label))
                    <a href="{{ $section->settings->nav_2_url }}" class="hover:text-accent transition-colors">
                        {{ $section->settings->nav_2_label }}
                    </a>
                @endif
                @if (filled($section->settings->nav_3_label))
                    <a href="{{ $section->settings->nav_3_url }}" class="hover:text-accent transition-colors">
                        {{ $section->settings->nav_3_label }}
                    </a>
                @endif
            @endif
        </nav>
        <div class="flex items-center gap-4">
            @if (filled($section->settings->cta_label))
                <a href="{{ $section->settings->cta_url }}"
                    class="bg-accent hover:bg-accent-hover text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    {{ $section->settings->cta_label }}
                </a>
            @endif
        </div>
    </div>
</header>
