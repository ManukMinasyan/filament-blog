<?php

declare(strict_types=1);

namespace Relaticle\Ink\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Relaticle\Ink\Models\Post;

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
        return view('ink::components.post-header');
    }
}
