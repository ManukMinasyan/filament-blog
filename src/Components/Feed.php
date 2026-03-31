<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use ManukMinasyan\FilamentBlog\Models\Post;

class Feed extends Component
{
    /** @param Collection<int, Post> $posts */
    public function __construct(
        public Collection $posts,
    ) {}

    public function render(): View
    {
        return view('blog::components.feed');
    }
}
