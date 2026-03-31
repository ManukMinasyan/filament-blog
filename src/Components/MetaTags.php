<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ManukMinasyan\FilamentBlog\Models\Post;

class MetaTags extends Component
{
    public function __construct(
        public Post $post,
    ) {}

    public function render(): View
    {
        return view('blog::components.meta-tags');
    }
}
