<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Relaticle\Ink\Http\Controllers\BlogController;

$prefix = config('ink.prefix', 'ink');

Route::prefix($prefix)->middleware('web')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/preview/{post}', [BlogController::class, 'preview'])
        ->middleware('signed')
        ->name('blog.preview');
    Route::get('/feed', [BlogController::class, 'feed'])->name('blog.feed');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});
