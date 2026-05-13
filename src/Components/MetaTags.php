<?php

declare(strict_types=1);

namespace Relaticle\Ink\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Relaticle\Ink\Models\Post;

class MetaTags extends Component
{
    public function __construct(
        public Post $post,
    ) {}

    public function render(): View
    {
        return view('ink::components.meta-tags');
    }
}
