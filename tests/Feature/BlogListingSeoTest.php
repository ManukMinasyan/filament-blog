<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Relaticle\Ink\InkServiceProvider;
use Relaticle\Ink\Models\Category;
use Relaticle\Ink\Models\Post;
use Relaticle\Ink\Models\Tag;
use Relaticle\Ink\Support\BlogListingSeo;

beforeEach(function () {
    config()->set('ink.features.public_routes', true);
    config()->set('ink.features.tags', true);
    config()->set('ink.per_page', 2);
    config()->set('ink.layout', 'tests::layouts.empty');

    $this->app->register(InkServiceProvider::class, force: true);
    $this->app->getProvider(InkServiceProvider::class)->packageBooted();
    Route::getRoutes()->refreshNameLookups();
});

it('sets a self-canonical on the blog index page 1', function () {
    Post::factory(3)->published()->create();

    $response = $this->get('/blog');

    $response->assertOk();
    $response->assertSee('<link rel="canonical" href="'.url('/blog').'"', escape: false);
});

it('sets a page-aware canonical and title on /blog?page=2', function () {
    Post::factory(5)->published()->create();

    $response = $this->get('/blog?page=2');

    $response->assertOk();
    $response->assertSee('<link rel="canonical" href="'.url('/blog?page=2').'"', escape: false);
    $response->assertSee('Page 2', escape: false);
});

it('sets a category-aware canonical on /blog/category/{slug}', function () {
    $category = Category::factory()->create(['name' => 'Guides', 'slug' => 'guides']);
    Post::factory(3)->published()->create(['category_id' => $category->id]);

    $response = $this->get('/blog/category/guides');

    $response->assertOk();
    $response->assertSee('<link rel="canonical" href="'.url('/blog/category/guides').'"', escape: false);
});

it('sets a tag-aware canonical on /blog/tag/{slug}', function () {
    $tag = Tag::factory()->create(['name' => 'Filament']);
    $post = Post::factory()->published()->create();
    $post->tags()->attach($tag);

    $response = $this->get('/blog/tag/'.$tag->slug);

    $response->assertOk();
    $response->assertSee('<link rel="canonical" href="'.url('/blog/tag/'.$tag->slug).'"', escape: false);
});

it('builds an SEOData object headless consumers can use directly', function () {
    $data = BlogListingSeo::forIndex(page: 3);

    expect($data->title)->toContain('Page 3');
    expect((string) $data->url)->toBe(url('/blog?page=3'));
});

it('marks search result pages as noindex via headless helper', function () {
    $data = BlogListingSeo::forIndex(searchQuery: 'laravel');

    expect($data->robots)->toBe('noindex,follow');
});

it('builds category SEOData with page-aware url', function () {
    $category = Category::factory()->create(['name' => 'News', 'slug' => 'news']);

    $data = BlogListingSeo::forCategory($category, page: 2);

    expect($data->title)->toContain('News');
    expect($data->title)->toContain('Page 2');
    expect((string) $data->url)->toBe(url('/blog/category/news?page=2'));
});

it('builds tag SEOData with page-aware url', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel']);

    $data = BlogListingSeo::forTag($tag, page: 2);

    expect($data->title)->toContain($tag->name);
    expect($data->title)->toContain('Page 2');
    expect((string) $data->url)->toBe(url('/blog/tag/'.$tag->slug.'?page=2'));
});

it('emits noindex on /blog?q=searchterm', function () {
    Post::factory()->published()->create(['title' => 'Webhooks post']);
    $response = $this->get('/blog?q=webhooks');
    $response->assertOk();
    $response->assertSee('noindex', escape: false);
});

it('filters results by q parameter', function () {
    Post::factory()->published()->create(['title' => 'Webhooks tutorial']);
    Post::factory()->published()->create(['title' => 'Forms guide']);

    $response = $this->get('/blog?q=webhooks');
    $response->assertOk();
    $response->assertSee('Webhooks tutorial');
    $response->assertDontSee('Forms guide');
});
