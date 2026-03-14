<?php

namespace Coderstm\PageBuilder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasFactory, HasSlug;

    protected $logIgnore = [
        'metadata',
    ];

    protected $fillable = [
        'parent',
        'title',
        'slug',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_active',
        'template', // layout template
        'metadata',
        'content',
    ];

    protected $casts = ['is_active' => 'boolean', 'metadata' => 'json'];

    protected $appends = ['url'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug')->preventOverwrite();
    }

    public static function findBySlug(string $slug): static
    {
        return static::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function getUrlAttribute()
    {
        $path = $this->slug;
        $parent = $this->parent;
        if ($parent) {
            $path = $parent.'/'.$path;
        }

        return url($path);
    }
}
