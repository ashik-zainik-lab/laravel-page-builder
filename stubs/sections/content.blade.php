@schema([
    'name' => 'Content',
    'blocks' => [
        ['type' => 'row'],
        ['type' => '@theme'],
    ],
    'presets' => [
        [
            'name' => 'Content',
            'blocks' => [
                [
                    'type' => 'row',
                    'settings' => ['columns' => '2', 'gap' => '6'],
                    'blocks' => [
                        ['type' => 'column'],
                        ['type' => 'column'],
                    ],
                ],
            ],
        ],
    ],
])

<section {!! $section->editorAttributes() !!} class="py-16">
    <div class="container mx-auto px-4">
        @blocks($section)
    </div>
</section>
