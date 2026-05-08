<?php

declare(strict_types=1);

use ManukMinasyan\FilamentBlog\FilamentBlogServiceProvider;
use ManukMinasyan\FilamentBlog\Models\Post;

beforeEach(function () {
    config()->set('filament-blog.features.public_routes', true);
    config()->set('filament-blog.layout', 'tests::layouts.empty');

    // Re-boot the package so routes register with the just-set config flag.
    $this->app->register(FilamentBlogServiceProvider::class, force: true);
    $this->app->getProvider(FilamentBlogServiceProvider::class)->packageBooted();
});

test('public index route returns published posts when feature enabled', function () {
    Post::factory()->published()->create(['title' => 'Hello world']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSeeText('Hello world');
});

test('public index route is not registered when feature disabled', function () {
    config()->set('filament-blog.features.public_routes', false);

    // Simulate fresh boot with feature off
    $this->refreshApplication();
    config()->set('filament-blog.features.public_routes', false);

    expect(\Illuminate\Support\Facades\Route::has('blog.index'))->toBeFalse();
});
