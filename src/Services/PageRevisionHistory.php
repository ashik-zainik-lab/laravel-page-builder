<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Services;

use Illuminate\Support\Facades\File;

/**
 * Stores rolling JSON snapshots of each page before overwrite (Framer-style revisions).
 *
 * Files live under `{pages}/.history/{slug-safe}/` as `{id}.json`.
 */
final class PageRevisionHistory
{
    /**
     * @return list<array{id: string, saved_at: int, saved_at_iso: string}>
     */
    public function list(string $slug): array
    {
        $dir = $this->directoryForSlug($slug);

        if (! File::isDirectory($dir)) {
            return [];
        }

        $items = [];

        foreach (File::files($dir) as $file) {
            $path = $file->getPathname();
            if (! str_ends_with($path, '.json')) {
                continue;
            }

            $id = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            if (! $this->isValidRevisionId($id)) {
                continue;
            }

            $mtime = @filemtime($path) ?: time();

            $items[] = [
                'id' => $id,
                'saved_at' => $mtime,
                'saved_at_iso' => date('c', $mtime),
            ];
        }

        usort($items, static fn (array $a, array $b): int => strcmp($b['id'], $a['id']));

        return $items;
    }

    public function read(string $slug, string $revisionId): ?string
    {
        if (! $this->isValidRevisionId($revisionId)) {
            return null;
        }

        $path = $this->directoryForSlug($slug).'/'.$revisionId.'.json';

        if (! File::exists($path)) {
            return null;
        }

        $raw = File::get($path);

        return is_string($raw) ? $raw : null;
    }

    public function pushSnapshot(string $slug, string $previousRawJson): void
    {
        if (! config('pagebuilder.revision_history.enabled', true)) {
            return;
        }

        $max = (int) config('pagebuilder.revision_history.max', 10);

        if ($max <= 0) {
            return;
        }

        $dir = $this->directoryForSlug($slug);

        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $id = $this->makeUniqueId();
        $path = $dir.'/'.$id.'.json';

        File::put($path, $previousRawJson);

        $this->prune($slug, $max);
    }

    private function prune(string $slug, int $max): void
    {
        $items = $this->list($slug);

        if (count($items) <= $max) {
            return;
        }

        $dir = $this->directoryForSlug($slug);
        $toRemove = array_slice($items, $max);

        foreach ($toRemove as $row) {
            $path = $dir.'/'.$row['id'].'.json';

            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    private function makeUniqueId(): string
    {
        return now()->format('YmdHis').'_'.bin2hex(random_bytes(4));
    }

    private function directoryForSlug(string $slug): string
    {
        $base = rtrim((string) config('pagebuilder.pages'), '/');

        return $base.'/.history/'.$this->slugToSafePath($slug);
    }

    private function slugToSafePath(string $slug): string
    {
        return str_replace(['/', '\\'], '__', $slug);
    }

    private function isValidRevisionId(string $id): bool
    {
        return $id !== '' && (bool) preg_match('/^[0-9]{14}_[a-f0-9]{8}$/', $id);
    }
}
