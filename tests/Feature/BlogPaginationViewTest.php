<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Relaticle\Ink\InkServiceProvider;
use Relaticle\Ink\Models\Post;

beforeEach(function () {
    config()->set('ink.features.public_routes', true);
    config()->set('ink.per_page', 2);
    config()->set('ink.layout', 'tests::layouts.empty');

    $this->app->register(InkServiceProvider::class, force: true);
    $this->app->getProvider(InkServiceProvider::class)->packageBooted();
    Route::getRoutes()->refreshNameLookups();
});

it('renders numbered pagination with aria-label on the blog index', function () {
    Post::factory(5)->published()->create();

    $response = $this->get('/blog');

    $response->assertOk();
    $response->assertSee('aria-label="Blog pagination"', escape: false);
    $response->assertSee('aria-label="Go to page 2"', escape: false);
});

it('marks the current page with aria-current', function () {
    Post::factory(5)->published()->create();

    $response = $this->get('/blog?page=2');

    $response->assertOk();
    $response->assertSee('aria-current="page"', escape: false);
});
