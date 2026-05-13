<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Relaticle\Ink\Models\Post;

it('matches posts by title via the search scope', function () {
    Post::factory()->published()->create(['title' => 'Webhooks in Laravel', 'content' => '']);
    Post::factory()->published()->create(['title' => 'Forms in Filament', 'content' => '']);

    $results = Post::query()->published()->search('webhooks')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->title)->toBe('Webhooks in Laravel');
});

it('matches by excerpt and content via the default LIKE strategy', function () {
    Post::factory()->published()->create([
        'title' => 'Unrelated',
        'excerpt' => 'A post about queues',
        'content' => '...',
    ]);
    Post::factory()->published()->create([
        'title' => 'Unrelated 2',
        'excerpt' => '',
        'content' => 'Discussion of webhooks here.',
    ]);

    expect(Post::query()->published()->search('queues')->count())->toBe(1);
    expect(Post::query()->published()->search('webhooks')->count())->toBe(1);
});

it('returns the unfiltered query when term is empty or whitespace', function () {
    Post::factory(3)->published()->create();

    expect(Post::query()->published()->search('')->count())->toBe(3);
    expect(Post::query()->published()->search('   ')->count())->toBe(3);
});

it('delegates to the configured callback when search.callback is set', function () {
    Post::factory()->published()->create(['title' => 'matches']);
    Post::factory()->published()->create(['title' => 'no']);

    config()->set('ink.search.callback', function (Builder $query, string $term) {
        $query->where('title', $term);
    });

    expect(Post::query()->published()->search('matches')->count())->toBe(1);
});
