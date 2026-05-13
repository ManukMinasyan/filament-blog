<?php

declare(strict_types=1);

use Livewire\Livewire;
use Relaticle\Ink\Livewire\BlogSearch;
use Relaticle\Ink\Models\Post;

it('renders matching posts when query is set', function () {
    Post::factory()->published()->create(['title' => 'Webhooks integration']);
    Post::factory()->published()->create(['title' => 'Forms tutorial']);

    Livewire::test(BlogSearch::class, ['query' => 'webhooks'])
        ->assertSee('Webhooks integration')
        ->assertDontSee('Forms tutorial');
});

it('shows empty state when no matches', function () {
    Post::factory()->published()->create(['title' => 'Webhooks']);

    Livewire::test(BlogSearch::class, ['query' => 'nope'])
        ->assertSee('No posts match');
});

it('starts with empty query and shows no results until typed', function () {
    Post::factory()->published()->create(['title' => 'Webhooks']);

    Livewire::test(BlogSearch::class)
        ->assertSet('query', '')
        ->assertDontSee('Webhooks');
});

it('updates results when query is set', function () {
    Post::factory()->published()->create(['title' => 'Webhooks integration']);

    Livewire::test(BlogSearch::class)
        ->set('query', 'webhooks')
        ->assertSet('query', 'webhooks')
        ->assertSee('Webhooks integration');
});
