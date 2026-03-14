<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Tests;

use Coderstm\PageBuilder\PageBuilder;
use Coderstm\PageBuilder\Tests\Stubs\PageStub;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected function defineEnvironment($app): void
    {
        PageBuilder::usePageModel(PageStub::class);
    }
}
