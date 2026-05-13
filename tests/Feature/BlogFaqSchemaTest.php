<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use RalphJSmit\Laravel\SEO\TagManager;
use Relaticle\Ink\InkServiceProvider;
use Relaticle\Ink\Models\Post;
use Relaticle\Ink\Support\SchemaExtractor;

beforeEach(function () {
    config()->set('ink.features.public_routes', true);
    config()->set('ink.layout', 'tests::layouts.empty');

    $this->app->register(InkServiceProvider::class, force: true);
    $this->app->getProvider(InkServiceProvider::class)->packageBooted();
    Route::getRoutes()->refreshNameLookups();

    // The shipped package controller does not call seo()->for() in show().
    // Consumers override the controller to do so (see Post::getDynamicSEOData
    // for the article + FAQ schema wiring). We simulate that here by binding
    // a fresh TagManager and pointing it at the post we want to render.
    $this->app->forgetInstance(TagManager::class);
});

function renderSeoForPost(Post $post): string
{
    seo()->for($post);

    return (string) seo();
}

it('emits FAQPage JSON-LD when content has a ## FAQ section', function () {
    $content = <<<'MD'
    Intro paragraph.

    ## FAQ

    ### Does the package support FAQ schema?
    Yes, automatically detected from headings.

    ### Is it opt-in?
    Yes, via config.
    MD;

    $post = Post::factory()->published()->create(['content' => $content]);

    $output = renderSeoForPost($post);

    expect($output)->toContain('FAQPage');
    expect($output)->toContain('Question');
    expect($output)->toContain('Does the package support FAQ schema?');
});

it('omits FAQPage when the schema.faq_auto config is disabled', function () {
    config()->set('ink.schema.faq_auto', false);

    $post = Post::factory()->published()->create([
        'content' => "## FAQ\n\n### Q?\n\nA.",
    ]);

    $output = renderSeoForPost($post);

    expect($output)->not->toContain('FAQPage');
});

it('omits FAQPage when no FAQ section is present', function () {
    $post = Post::factory()->published()->create([
        'content' => 'Just a post with no FAQ heading.',
    ]);

    $output = renderSeoForPost($post);

    expect($output)->not->toContain('FAQPage');
});

it('extracts FAQ entities directly from rendered HTML', function () {
    $html = '<h2>FAQ</h2><h3>Q1?</h3><p>A1</p><h3>Q2?</h3><p>A2</p>';

    $entities = SchemaExtractor::extractFaqEntities($html);

    expect($entities)->toHaveCount(2);
    expect($entities[0]['name'])->toBe('Q1?');
    expect($entities[0]['acceptedAnswer']['text'])->toBe('A1');
});

it('returns empty array when HTML has no FAQ section', function () {
    $entities = SchemaExtractor::extractFaqEntities(
        '<h2>Not FAQ</h2><p>Text</p>'
    );

    expect($entities)->toBe([]);
});
