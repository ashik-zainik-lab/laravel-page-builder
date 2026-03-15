{{--
    Contact Form section (schema)
--}}

@schema([
    'name' => 'Contact Form',
    'settings' => [
        [
            'id' => 'eyebrow',
            'type' => 'text',
            'label' => 'Eyebrow',
            'default' => 'Get In Touch',
        ],
        [
            'id' => 'title',
            'type' => 'textarea',
            'label' => 'Title',
            'default' => 'Contact Us',
        ],
        [
            'id' => 'submit_label',
            'type' => 'text',
            'label' => 'Submit button label',
            'default' => 'Send Message',
        ],
    ],
    'blocks' => [
        [
            'type' => 'contact-info',
            'name' => 'Contact Info',
            'blocks' => [
                [
                    'type' => 'item',
                    'name' => 'Item',
                    'settings' => [
                        [
                            'id' => 'icon',
                            'type' => 'icon_fa',
                            'label' => 'Icon',
                            'default' => 'fas fa-location-dot',
                        ],
                        [
                            'id' => 'label',
                            'type' => 'text',
                            'label' => 'Label',
                            'default' => 'Our Location',
                        ],
                        [
                            'id' => 'value',
                            'type' => 'richtext',
                            'label' => 'Value',
                            'default' => '123 Fitness Street, Muscle City, MC 45678',
                        ],
                    ],
                ],
            ],
        ],
        [
            'type' => 'socials',
            'name' => 'Social Button',
            'blocks' => [
                [
                    'type' => 'social',
                    'name' => 'Social',
                    'settings' => [
                        [
                            'id' => 'icon',
                            'type' => 'icon_fa',
                            'label' => 'Icon',
                            'default' => 'fa-brands fa-facebook-f',
                        ],
                        [
                            'id' => 'url',
                            'type' => 'url',
                            'label' => 'URL',
                            'default' => '#',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'presets' => [
        [
            'name' => 'Contact Form',
            'blocks' => [
                [
                    'type' => 'contact-info',
                    'blocks' => [['type' => 'item', 'settings' => ['icon' => 'fas fa-location-dot', 'label' => 'Our Location', 'value' => '123 Fitness Street, Muscle City, MC 45678']], ['type' => 'item', 'settings' => ['icon' => 'fas fa-phone', 'label' => 'Phone Number', 'value' => '+1 (555) 123-4567']], ['type' => 'item', 'settings' => ['icon' => 'fas fa-envelope', 'label' => 'Email Address', 'value' => 'info@yourgym.com']], ['type' => 'item', 'settings' => ['icon' => 'fas fa-clock', 'label' => 'Working Hours', 'value' => 'Mon–Fri: 5 AM – 11 PM<br>Sat–Sun: 7 AM – 9 PM']]],
                ],
                [
                    'type' => 'socials',
                    'blocks' => [['type' => 'social', 'settings' => ['icon' => 'fa-brands fa-facebook-f', 'url' => '#']], ['type' => 'social', 'settings' => ['icon' => 'fa-brands fa-instagram', 'url' => '#']], ['type' => 'social', 'settings' => ['icon' => 'fa-brands fa-twitter', 'url' => '#']]],
                ],
            ],
        ],
    ],
])

<section {!! $section->editorAttributes() !!} id="contact" class="py-16 lg:py-24 bg-bg-darker">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            @if ($section->settings->eyebrow)
                <span
                    class="text-xs font-bold uppercase tracking-[0.25em] text-accent">{{ $section->settings->eyebrow }}</span>
            @endif
            <h2 class="text-2xl lg:text-3xl font-bold text-white mt-2">{!! $section->settings->title !!}</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-5">
                @foreach ($section->blocks as $block)
                    @if ($block->type === 'contact-info')
                        <div {!! $block->editorAttributes() !!} class="space-y-6">
                            @foreach ($block->blocks as $item)
                                <div {!! $item->editorAttributes() !!} class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center shrink-0">
                                        <i class="{{ $item->settings->icon ?? 'fas fa-circle-info' }} text-accent"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-white font-semibold text-sm">
                                            {{ $item->settings->label ?? '' }}</h6>
                                        <p class="text-text-body text-sm mt-1">{!! $item->settings->value ?? '' !!}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($block->type === 'socials')
                        <div {!! $block->editorAttributes() !!} class="flex items-center gap-3 mt-6">
                            @foreach ($block->blocks as $social)
                                <a {!! $social->editorAttributes() !!} href="{{ $social->settings->url ?? '#' }}"
                                    class="w-9 h-9 rounded-full border border-border-dark flex items-center justify-center text-text-body hover:bg-accent hover:border-accent hover:text-white transition-colors">
                                    <i class="{{ $social->settings->icon ?? 'fa-brands fa-globe' }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="lg:col-span-7">
                <form action="/contact" method="POST" class="space-y-4" novalidate="novalidate">
                    @csrf
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token_{{ $section->id }}">
                    @if (session('success'))
                        <div class="p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-lg text-sm"
                            role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @error('recaptcha_token')
                        <div class="p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg text-sm"
                            role="alert">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input name="name" type="text" value="{{ old('name') }}"
                                placeholder="{{ __('Your Name') }}" required
                                class="w-full px-4 py-3 bg-bg-card border border-border-dark rounded-lg text-white placeholder-text-body focus:border-accent focus:ring-1 focus:ring-accent outline-none transition-colors @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input name="email" type="email" value="{{ old('email') }}"
                                placeholder="{{ __('Your Email') }}" required
                                class="w-full px-4 py-3 bg-bg-card border border-border-dark rounded-lg text-white placeholder-text-body focus:border-accent focus:ring-1 focus:ring-accent outline-none transition-colors @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <input name="phone" type="tel" value="{{ old('phone') }}"
                            placeholder="{{ __('Phone Number') }}"
                            class="w-full px-4 py-3 bg-bg-card border border-border-dark rounded-lg text-white placeholder-text-body focus:border-accent focus:ring-1 focus:ring-accent outline-none transition-colors @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <textarea name="message" rows="5" placeholder="{{ __('Leave a comment or query') }}" required
                            class="w-full px-4 py-3 bg-bg-card border border-border-dark rounded-lg text-white placeholder-text-body focus:border-accent focus:ring-1 focus:ring-accent outline-none transition-colors resize-none @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors">
                        {{ $section->settings->submit_label }}
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
<script>
    (function() {
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('recaptcha.site_key') }}').then(function(token) {
                document.getElementById('recaptcha_token_{{ $section->id }}').value = token;
            });
        });
    })();
</script>
