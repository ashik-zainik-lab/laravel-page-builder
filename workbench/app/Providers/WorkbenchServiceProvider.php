<?php

namespace Workbench\App\Providers;

use Coderstm\PageBuilder\Facades\Theme;
use Coderstm\PageBuilder\PageBuilder;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\Page;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Theme::set('default');

        PageBuilder::usePageModel(Page::class);
    }
}
