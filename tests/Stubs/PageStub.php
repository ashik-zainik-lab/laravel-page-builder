<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

/**
 * Lightweight Page stub used in tests to avoid loading the real Page model
 * which depends on spatie/laravel-sluggable (not a test dependency).
 *
 * The ServiceProvider calls `$pageModel::observe(PageObserver::class)` during
 * boot — this stub receives the observer registration without requiring any
 * third-party traits.
 */
class PageStub extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'parent',
        'title',
        'slug',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_active',
        'template',
        'metadata',
        'content',
    ];
}
