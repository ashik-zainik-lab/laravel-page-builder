<?php

use Coderstm\PageBuilder\Http\Controllers\AssetController;
use Coderstm\PageBuilder\Http\Controllers\PageBuilderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Page Builder Routes
|--------------------------------------------------------------------------
|
| These routes handle the page builder API endpoints.
| Used by the React editor for CRUD operations and section rendering.
| Preview is handled via the real page URLs with ?pb-editor=1 query param.
|
*/

Route::prefix('pagebuilder')->as('pagebuilder.')->group(function () {
    Route::get('pages', [PageBuilderController::class, 'pages'])->name('pages');
    Route::get('page/{slug}/revisions', [PageBuilderController::class, 'pageRevisions'])
        ->where('slug', '.*')
        ->name('page.revisions');
    Route::post('page/{slug}/revisions/{revisionId}/restore', [PageBuilderController::class, 'restorePageRevision'])
        ->where('slug', '.*')
        ->where('revisionId', '[0-9]{14}_[a-f0-9]{8}')
        ->name('page.revisions.restore');
    Route::delete('page/{slug}', [PageBuilderController::class, 'deletePage'])
        ->where('slug', '.*')
        ->name('page.delete');
    Route::get('page/{slug?}', [PageBuilderController::class, 'page'])->where('slug', '.*')->defaults('slug', 'home')->name('page');
    Route::post('render-section', [PageBuilderController::class, 'renderSection'])->name('render-section');
    Route::post('render-block', [PageBuilderController::class, 'renderBlock'])->name('render-block');
    Route::post('save-page', [PageBuilderController::class, 'savePage'])->name('save-page');
    Route::get('templates', [PageBuilderController::class, 'templates'])->name('templates');
    Route::post('templates', [PageBuilderController::class, 'createTemplate'])->name('templates.create');

    // Theme settings
    Route::get('theme-settings', [PageBuilderController::class, 'themeSettings'])->name('theme-settings');
    Route::post('theme-settings', [PageBuilderController::class, 'saveThemeSettings'])->name('theme-settings.save');

    // Asset management
    Route::get('assets', [AssetController::class, 'index'])->name('assets');
    Route::post('assets/upload', [AssetController::class, 'upload'])->name('assets.upload');
});

// Redirect to home page builder editor if accessing /pagebuilder without slug
Route::redirect('pagebuilder', 'pagebuilder/home', 301)->name('pagebuilder.index');

// Editor routes (Blade layout)
Route::get('pagebuilder/{slug?}', [PageBuilderController::class, 'editor'])->where('slug', '.*')->name('pagebuilder.editor');
