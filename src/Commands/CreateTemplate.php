<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class CreateTemplate extends Command
{
    protected $signature = 'pagebuilder:template
                            {name=page : Template name (without .json), e.g. "page.alternate"}
                            {--force : Overwrite the template file if it already exists}
                            {--layout=page : Layout value (use "false" for no layout)}
                            {--wrapper= : Optional wrapper selector, e.g. main#page-main.container}';

    protected $description = 'Create a new page builder template JSON file.';

    public function handle(): int
    {
        $name = $this->normalizeTemplateName((string) $this->argument('name'));

        if (! $this->isValidTemplateName($name)) {
            $this->components->error('Invalid template name. Use letters, numbers, dots, dashes, or underscores.');

            return self::INVALID;
        }

        $templatesPath = (string) config('pagebuilder.templates', resource_path('views/templates'));
        File::ensureDirectoryExists($templatesPath);

        $filePath = rtrim($templatesPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$name.'.json';
        $force = (bool) $this->option('force');

        if (File::exists($filePath) && ! $force) {
            $this->components->error("Template already exists: {$filePath}");
            $this->line('Use <fg=cyan>--force</> to overwrite it.');

            return self::FAILURE;
        }

        $payload = [
            'sections' => [
                'main' => [
                    'type' => 'page-content',
                ],
            ],
            'order' => ['main'],
        ];

        $layout = $this->parseLayoutOption((string) $this->option('layout'));
        if ($layout !== null) {
            $payload['layout'] = $layout;
        }

        $wrapper = trim((string) $this->option('wrapper'));
        if ($wrapper !== '') {
            $payload['wrapper'] = $wrapper;
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            $this->components->error('Failed to generate template JSON.');

            return self::FAILURE;
        }

        File::put($filePath, $json.PHP_EOL);

        $this->components->info("Template created: {$filePath}");

        return self::SUCCESS;
    }

    private function normalizeTemplateName(string $name): string
    {
        $normalized = strtolower(trim($name));

        if (str_ends_with($normalized, '.json')) {
            $normalized = substr($normalized, 0, -5);
        }

        return $normalized !== '' ? $normalized : 'page';
    }

    private function isValidTemplateName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $name)
            && ! str_contains($name, '..');
    }

    /**
     * @return string|false|null
     */
    private function parseLayoutOption(string $layout): string|false|null
    {
        $value = trim($layout);

        if ($value === '') {
            return null;
        }

        if (in_array(strtolower($value), ['false', 'none', 'null'], true)) {
            return false;
        }

        return $value;
    }
}
