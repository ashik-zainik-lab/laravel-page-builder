<?php

namespace Coderstm\PageBuilder\Providers;

use Coderstm\PageBuilder\Commands;
use Coderstm\PageBuilder\Facades;
use Coderstm\PageBuilder\Http\Middleware;
use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Registry;
use Coderstm\PageBuilder\Rendering;
use Coderstm\PageBuilder\Services;
use Coderstm\PageBuilder\Services\PageRegistry;
use Coderstm\PageBuilder\Services\PageRenderer;
use Coderstm\PageBuilder\Services\PageStorage;
use Coderstm\PageBuilder\Services\TemplateStorage;
use Coderstm\PageBuilder\Services\ThemeSettings;
use Coderstm\PageBuilder\Support;
use Coderstm\PageBuilder\Support\TemplateVariableResolver;
use Coderstm\PageBuilder\Support\WrapperParser;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class PageBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register page builder services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/pagebuilder.php',
            'pagebuilder'
        );

        // ─── Core services ───────────────────────────────────────

        $this->app->singleton('page-service', function ($app) {
            return $app->make(Services\PageService::class);
        });

        $this->app->singleton('theme', function ($app) {
            return new Services\Theme;
        });

        $this->app->singleton(Support\Mix::class);

        // ─── Schema extraction ───────────────────────────────────

        $this->app->singleton(Registry\SchemaExtractor::class);

        // ─── Registries ──────────────────────────────────────────

        $this->app->singleton(Registry\SectionRegistry::class, function ($app) {
            return new Registry\SectionRegistry($app->make(Registry\SchemaExtractor::class));
        });

        $this->app->singleton(Registry\BlockRegistry::class, function ($app) {
            return new Registry\BlockRegistry($app->make(Registry\SchemaExtractor::class));
        });

        $this->app->singleton(Registry\LayoutParser::class, function ($app) {
            return new Registry\LayoutParser($app->make(Registry\SectionRegistry::class));
        });

        // ─── Page services ───────────────────────────────────────

        $this->app->singleton(Services\PageCache::class);
        $this->app->singleton(PageRegistry::class);
        $this->app->singleton(Services\PageRevisionHistory::class);
        $this->app->singleton(PageStorage::class);
        $this->app->singleton(TemplateStorage::class);
        $this->app->singleton(ThemeSettings::class);

        // ─── Support utilities ───────────────────────────────────

        $this->app->singleton(WrapperParser::class);
        $this->app->singleton(TemplateVariableResolver::class);

        // ─── Rendering ──────────────────────────────────────────

        $this->app->singleton(Rendering\Renderer::class, function ($app) {
            return new Rendering\Renderer(
                $app->make(Registry\SectionRegistry::class),
                $app->make(Registry\BlockRegistry::class),
            );
        });

        $this->app->singleton(PageRenderer::class, function ($app) {
            return new PageRenderer(
                $app->make(Rendering\Renderer::class),
                $app->make(PageStorage::class),
                $app->make(WrapperParser::class),
                $app->make(Services\PageCache::class),
            );
        });
    }

    public function boot(): void
    {
        $stubSectionsPath = __DIR__.'/../../stubs/sections';
        $stubBlocksPath = __DIR__.'/../../stubs/blocks';

        // Register section paths from config.
        // In Orchestra Testbench, config('pagebuilder.sections') points to the
        // framework fixture path. Add package stubs as a fallback so demo-only
        // sections like `header` remain available in workbench.
        if ($sections = config('pagebuilder.sections')) {
            Facades\Section::add($sections);

            if (is_string($sections) && str_contains($sections, 'testbench-core') && is_dir($stubSectionsPath)) {
                Facades\Section::add($stubSectionsPath);
            }
        }

        // Register block paths from config.
        // Same Testbench fallback as sections above (for blocks like `nav-link`).
        if ($blocks = config('pagebuilder.blocks')) {
            Facades\Block::add($blocks);

            if (is_string($blocks) && str_contains($blocks, 'testbench-core') && is_dir($stubBlocksPath)) {
                Facades\Block::add($stubBlocksPath);
            }
        }

        // Set active theme
        if ($activeTheme = config('theme.active')) {
            Services\Theme::set($activeTheme);
        }

        // Register theme middleware
        Route::aliasMiddleware('theme', Middleware\ThemeMiddleware::class);

        if (! PageBuilder::$withoutRoutes) {
            PageBuilder::pageRoutes();
            PageBuilder::builderRoutes(config('pagebuilder.middleware', ['web']));
        }

        // Views & migrations
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'pagebuilder');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Publishable resources
        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views/vendor/pagebuilder'),
        ], 'pagebuilder-views');

        $this->publishes([
            __DIR__.'/../../config/pagebuilder.php' => config_path('pagebuilder.php'),
        ], 'pagebuilder-config');

        $this->publishes([
            __DIR__.'/../../dist' => public_path('pagebuilder'),
        ], 'pagebuilder-assets');

        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'pagebuilder-migrations');

        // Commands
        $this->commands([
            Commands\CreateTemplate::class,
            Commands\InstallPageBuilder::class,
            Commands\RegeneratePages::class,
            Commands\ThemeLink::class,
        ]);

        // Share $theme globally with all Blade views
        View::share('theme', $this->app->make(ThemeSettings::class));

        // ─── Blade directives & precompiler ──────────────────────
        Rendering\BladeDirectives::register();
        Rendering\BladeDirectives::registerPrecompiler();
    }
}
