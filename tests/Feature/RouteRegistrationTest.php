<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Tests\Feature;

use Coderstm\PageBuilder\Facades\Page;
use Coderstm\PageBuilder\Services\PageRegistry;
use Coderstm\PageBuilder\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class RouteRegistrationTest extends TestCase
{
    public function test_pages_routes_are_registered_from_registry(): void
    {
        // 1. Put some pages into the registry
        app(PageRegistry::class)->put([
            'test-page' => ['title' => 'Test Page', 'slug' => 'test-page'],
        ]);

        // 2. Trigger route registration
        Page::routes();

        $routes = Route::getRoutes();
        $routes->refreshNameLookups();

        $this->assertTrue($routes->hasNamedRoute('pages.test-page'));
        $this->assertSame(url('/test-page'), route('pages.test-page'));
    }
}
