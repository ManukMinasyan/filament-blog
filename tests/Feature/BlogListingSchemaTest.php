<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use RalphJSmit\Laravel\SEO\TagManager;
use Relaticle\Ink\InkServiceProvider;
use Relaticle\Ink\Models\Category;
use Relaticle\Ink\Models\Post;
use Relaticle\Ink\Models\Tag;

beforeEach(function () {
    config()->set('ink.features.public_routes', true);
    config()->set('ink.features.tags', true);
    config()->set('ink.layout', 'tests::layouts.empty');

    $this->app->register(InkServiceProvider::class, force: true);
    $this->app->getProvider(InkServiceProvider::class)->packageBooted();
    Route::getRoutes()->refreshNameLookups();

    $this->app->forgetInstance(TagManager::class);
});

it('emits Blog + CollectionPage JSON-LD on /blog', function () {
    Post::factory(2)->published()->create();

    $response = $this->get(route('blog.index'));

    $response->assertOk();
    $response->assertSee('"@type":"Blog"', escape: false);
    $response->assertSee('"@type":"CollectionPage"', escape: false);
});

it('emits CollectionPage JSON-LD on /blog/category/{slug}', function () {
    $category = Category::create(['name' => 'Guides', 'slug' => 'guides']);
    Post::factory(2)->published()->for($category)->create();

    $response = $this->get(route('blog.category', ['slug' => 'guides']));

    $response->assertOk();
    $response->assertSee('"@type":"CollectionPage"', escape: false);
});

it('emits CollectionPage JSON-LD on /blog/tag/{slug}', function () {
    $tag = Tag::factory()->create(['name' => 'Filament']);
    $post = Post::factory()->published()->create();
    $post->tags()->attach($tag);

    $response = $this->get(route('blog.tag', ['slug' => $tag->slug]));

    $response->assertOk();
    $response->assertSee('"@type":"CollectionPage"', escape: false);
});

it('does not emit listing schema on post detail pages', function () {
    $post = Post::factory()->published()->create();

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertOk();
    $response->assertDontSee('"@type":"CollectionPage"', escape: false);
    $response->assertDontSee('"@type":"Blog"', escape: false);
});
