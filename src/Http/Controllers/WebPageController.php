<?php

declare(strict_types=1);

namespace Coderstm\PageBuilder\Http\Controllers;

use Coderstm\PageBuilder\Facades\Page;
use Illuminate\Routing\Controller;

class WebPageController extends Controller
{
    public function pages(string $slug): mixed
    {
        return Page::render($slug);
    }
}
