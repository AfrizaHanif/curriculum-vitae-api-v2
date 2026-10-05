<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome', [
    'appName' => config('app.name', 'Curriculum Vitae API'),
    'laravelVersion' => app()->version(),
    'phpVersion' => PHP_VERSION,
    'baseUrl' => url('/'),
])->name('home');
