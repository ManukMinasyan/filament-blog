<?php

declare(strict_types=1);

namespace Relaticle\Ink\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Relaticle\Ink\Models\Post;

class RelatedPosts extends Component
{
    /** @param Collection<int, Post> $posts */
    public function __construct(
        public Collection $posts,
    ) {}

    public function render(): View
    {
        return view('ink::components.related-posts');
    }

    public function shouldRender(): bool
    {
        return $this->posts->isNotEmpty();
    }
}
