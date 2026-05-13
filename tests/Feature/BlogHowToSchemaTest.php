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
    config()->set('ink.schema.howto_auto', true);

    $this->app->register(InkServiceProvider::class, force: true);
    $this->app->getProvider(InkServiceProvider::class)->packageBooted();
    Route::getRoutes()->refreshNameLookups();

    $this->app->forgetInstance(TagManager::class);
});

it('emits HowTo JSON-LD when content has a ## Steps section and howto_auto is on', function () {
    $content = <<<'MD'
    Setup overview.

    ## Steps

    ### Install the package
    Run `composer require relaticle/ink`.

    ### Publish the config
    Run `php artisan vendor:publish --tag=ink-config`.
    MD;

    $post = Post::factory()->published()->create(['content' => $content]);

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertOk();
    $response->assertSee('HowTo', escape: false);
    $response->assertSee('HowToStep', escape: false);
    $response->assertSee('"position":1', escape: false);
    $response->assertSee('"position":2', escape: false);
    $response->assertSee('Install the package', escape: false);
});

it('omits HowTo when howto_auto is off (default)', function () {
    config()->set('ink.schema.howto_auto', false);

    $post = Post::factory()->published()->create([
        'content' => "## Steps\n\n### A\n\nDo a.\n\n### B\n\nDo b.",
    ]);

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertOk();
    $response->assertDontSee('"@type":"HowTo"', escape: false);
});

it('omits HowTo when no Steps section is present', function () {
    $post = Post::factory()->published()->create([
        'content' => 'Just a post with no Steps heading.',
    ]);

    $response = $this->get(route('blog.show', $post->slug));

    $response->assertOk();
    $response->assertDontSee('"@type":"HowTo"', escape: false);
});

it('extracts HowTo steps directly from rendered HTML', function () {
    $html = '<h2>Steps</h2><h3>First</h3><p>Do this.</p><h3>Second</h3><p>Do that.</p>';
    $steps = SchemaExtractor::extractHowToSteps($html);

    expect($steps)->toHaveCount(2);
    expect($steps[0]['position'])->toBe(1);
    expect($steps[0]['name'])->toBe('First');
    expect($steps[0]['text'])->toBe('Do this.');
    expect($steps[1]['position'])->toBe(2);
});
