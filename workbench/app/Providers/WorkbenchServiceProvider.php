<?php

namespace Workbench\App\Providers;

use Coderstm\PageBuilder\Facades\Theme;
use Illuminate\Support\ServiceProvider;

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
    }
}
