<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use ManukMinasyan\FilamentBlog\Http\Controllers\BlogController;

$prefix = config('filament-blog.prefix', 'blog');

Route::prefix($prefix)->middleware('web')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
});
