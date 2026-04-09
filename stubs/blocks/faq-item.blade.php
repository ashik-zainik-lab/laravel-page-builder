@schema([
    'name' => 'FAQ item',
    'settings' => [
        ['id' => 'question', 'type' => 'text', 'label' => 'Question', 'default' => 'What is included in my plan?'],
        ['id' => 'answer', 'type' => 'richtext', 'label' => 'Answer', 'default' => '<p>Everything listed for your tier on the pricing page. You can upgrade or downgrade at any time.</p>'],
    ],
    'presets' => [
        ['name' => 'FAQ item'],
    ],
])

<details class="group py-4" {!! $block->editorAttributes() !!}>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left font-semibold text-gray-900 hover:text-accent">
        <span>{{ $block->settings->question }}</span>
        <span class="text-gray-400 transition-transform duration-200 group-open:rotate-180" aria-hidden="true">▼</span>
    </summary>
    <div class="mt-3 max-w-none pb-2 text-gray-600 prose prose-gray">
        {!! $block->settings->answer !!}
    </div>
</details>
