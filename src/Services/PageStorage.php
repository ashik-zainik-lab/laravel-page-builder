<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Services;

use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Support\PageData;
use Illuminate\Support\Facades\File;

/**
 * Handles loading and persisting page JSON data to disk.
 */
class PageStorage
{
    public function __construct(
        protected readonly PageCache $pageCache,
        protected readonly PageRevisionHistory $revisionHistory,
    ) {}

    /**
     * Load and decode a page JSON file by slug.
     */
    public function load(string $slug): ?PageData
    {
        $filePath = config('pagebuilder.pages').'/'.$slug.'.json';

        if (! File::exists($filePath)) {
            return null;
        }

        $data = json_decode(File::get($filePath), true);

        if (! is_array($data)) {
            return null;
        }

        return PageData::fromArray($data);
    }

    /**
     * Persist a page's JSON data to disk, creating the pages directory if needed.
     *
     * @param  bool  $writeRevisionSnapshot  When true, the previous file contents are kept in revision history.
     */
    public function save(string $slug, array|PageData $data, bool $writeRevisionSnapshot = true): bool
    {
        $pagesPath = config('pagebuilder.pages');

        if (! File::isDirectory($pagesPath)) {
            File::makeDirectory($pagesPath, 0755, true);
        }

        $filePath = $pagesPath.'/'.$slug.'.json';
        $directory = dirname($filePath);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $payload = $data instanceof PageData ? $data->toArray() : $data;

        // Strip DB-only fields — title and meta are persisted to the database, not the JSON file.
        // Except for preserved slugs (like home), which don't have a database record.
        if (! PageBuilder::isPreservedPage($slug)) {
            unset($payload['title'], $payload['meta']);
        }

        // $filePath already defined above
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (
            $writeRevisionSnapshot
            && config('pagebuilder.revision_history.enabled', true)
            && File::exists($filePath)
        ) {
            $previous = File::get($filePath);

            if (is_string($previous) && $previous !== $json) {
                $this->revisionHistory->pushSnapshot($slug, $previous);
            }
        }

        $result = File::put($filePath, $json) !== false;

        if ($result) {
            $this->pageCache->forget($slug);
        }

        return $result;
    }

    /**
     * Delete a page JSON file and related revision snapshots.
     */
    public function delete(string $slug): bool
    {
        $pagesPath = (string) config('pagebuilder.pages');
        $filePath = $pagesPath.'/'.$slug.'.json';
        $safeSlug = str_replace(['/', '\\'], '__', $slug);
        $historyPath = rtrim($pagesPath, '/').'/.history/'.$safeSlug;

        $deletedJson = ! File::exists($filePath) || File::delete($filePath);
        $deletedHistory = ! File::isDirectory($historyPath) || File::deleteDirectory($historyPath);

        if ($deletedJson && $deletedHistory) {
            $this->pageCache->forget($slug);
        }

        return $deletedJson && $deletedHistory;
    }
}
