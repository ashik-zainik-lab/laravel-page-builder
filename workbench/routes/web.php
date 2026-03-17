<?php

use Illuminate\Support\Facades\Route;

// Custom route that bypasses PageService entirely — used to verify that
// @sections() renders with default layout values even without __pb_layout.
Route::get('/custom-route', fn () => view('pages.custom-blade'));
