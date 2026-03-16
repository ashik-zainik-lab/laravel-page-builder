@schema([
    'name' => 'Page Content',
    'settings' => [
        ['id' => 'max_width', 'type' => 'select', 'label' => 'Max Width', 'default' => '4xl', 'options' => [
            ['value' => 'full',  'label' => 'Full'],
            ['value' => 'sm',    'label' => 'SM'],
            ['value' => 'md',    'label' => 'MD'],
            ['value' => 'lg',    'label' => 'LG'],
            ['value' => 'xl',    'label' => 'XL'],
            ['value' => '2xl',   'label' => '2XL'],
            ['value' => '4xl',   'label' => '4XL'],
            ['value' => '5xl',   'label' => '5XL'],
            ['value' => '7xl',   'label' => '7XL'],
        ]],
        ['id' => 'padding_top',    'type' => 'select', 'label' => 'Padding Top',    'default' => 'md', 'options' => [
            ['value' => 'none', 'label' => 'None'], ['value' => 'xs', 'label' => 'XS'], ['value' => 'sm', 'label' => 'SM'],
            ['value' => 'md',   'label' => 'MD'],   ['value' => 'lg', 'label' => 'LG'], ['value' => 'xl', 'label' => 'XL'],
        ]],
        ['id' => 'padding_bottom', 'type' => 'select', 'label' => 'Padding Bottom', 'default' => 'md', 'options' => [
            ['value' => 'none', 'label' => 'None'], ['value' => 'xs', 'label' => 'XS'], ['value' => 'sm', 'label' => 'SM'],
            ['value' => 'md',   'label' => 'MD'],   ['value' => 'lg', 'label' => 'LG'], ['value' => 'xl', 'label' => 'XL'],
        ]],
    ],
    'presets' => [
        ['name' => 'Page Content'],
    ],
])

@php
    $ptMap = ['none' => 'pt-0', 'xs' => 'pt-4',  'sm' => 'pt-8',  'md' => 'pt-12', 'lg' => 'pt-16', 'xl' => 'pt-24'];
    $pbMap = ['none' => 'pb-0', 'xs' => 'pb-4',  'sm' => 'pb-8',  'md' => 'pb-12', 'lg' => 'pb-16', 'xl' => 'pb-24'];
    $mwMap = ['full' => 'max-w-full', 'sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg',
              'xl' => 'max-w-xl', '2xl' => 'max-w-2xl', '4xl' => 'max-w-4xl', '5xl' => 'max-w-5xl', '7xl' => 'max-w-7xl'];

    $pt = $ptMap[$section->settings->padding_top]    ?? 'pt-12';
    $pb = $pbMap[$section->settings->padding_bottom] ?? 'pb-12';
    $mw = $mwMap[$section->settings->max_width]      ?? 'max-w-4xl';
@endphp

<section {!! $section->editorAttributes() !!} class="{{ $pt }} {{ $pb }}">
    <div class="{{ $mw }} mx-auto px-4 prose prose-invert max-w-none">
        {!! $page?->content ?? '' !!}
    </div>
</section>
