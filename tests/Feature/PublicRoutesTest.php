<?php

declare(strict_types=1);

use ManukMinasyan\FilamentBlog\Models\Category;
use ManukMinasyan\FilamentBlog\Models\Post;

beforeEach(function () {
    config()->set('filament-blog.features.public_routes', true);
    config()->set('filament-blog.layout', 'tests::layouts.empty');
});

test('public index route returns published posts when feature enabled', function () {
    Post::factory()->published()->create(['title' => 'Hello world']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSeeText('Hello world');
});

test('public index route is not registered when feature disabled', function () {
    config()->set('filament-blog.features.public_routes', false);

    expect(\Illuminate\Support\Facades\Route::has('blog.index'))->toBeFalse();
});
