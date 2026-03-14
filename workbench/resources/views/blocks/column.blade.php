@schema([
    'name' => 'Column',
    'blocks' => [
        ['type' => '@theme'],
    ],
])

<div class="flex flex-col gap-4" {!! $block->editorAttributes() !!}>
    @blocks($block)
</div>
