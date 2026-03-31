<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ManukMinasyan\FilamentBlog\Models\Post;

class PostHeader extends Component
{
    public int $readTime;

    public function __construct(
        public Post $post,
    ) {
        $this->readTime = (int) ceil(str_word_count(strip_tags($post->content)) / 200);
    }

    public function render(): View
    {
        return view('blog::components.post-header');
    }
}
