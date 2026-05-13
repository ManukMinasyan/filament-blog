<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Relaticle\Ink\BlogSitemapGenerator;
use Relaticle\Ink\InkServiceProvider;
use Relaticle\Ink\Models\Post;
use Relaticle\Ink\Models\Tag;
use Spatie\Sitemap\Sitemap;

beforeEach(function () {
    config()->set('ink.features.public_routes', true);
    config()->set('ink.features.tags', true);
    config()->set('ink.layout', 'tests::layouts.empty');

    $this->app->register(InkServiceProvider::class, force: true);
    $this->app->getProvider(InkServiceProvider::class)->packageBooted();
    Route::getRoutes()->refreshNameLookups();
});

test('adds tag URLs only when blog.tag route exists and the tag has published posts', function () {
    $usedTag = Tag::factory()->create(['name' => 'Used Tag']);
    $emptyTag = Tag::factory()->create(['name' => 'Empty Tag']);

    $post = Post::factory()->published()->create();
    $post->tags()->attach($usedTag);

    $sitemap = BlogSitemapGenerator::addToSitemap(Sitemap::create());

    $urls = collect($sitemap->getTags())->map(fn ($tag) => $tag->url)->all();

    expect($urls)
        ->toContain(route('blog.tag', $usedTag->slug))
        ->not->toContain(route('blog.tag', $emptyTag->slug));
});

test('omits all tag URLs when tags feature is off', function () {
    config()->set('ink.features.tags', false);

    $tag = Tag::factory()->create(['name' => 'Used Tag']);
    $post = Post::factory()->published()->create();
    $post->tags()->attach($tag);

    $sitemap = BlogSitemapGenerator::addToSitemap(Sitemap::create());
    $urls = collect($sitemap->getTags())->map(fn ($tag) => $tag->url)->all();

    expect($urls)->not->toContain(route('blog.tag', $tag->slug));
});
