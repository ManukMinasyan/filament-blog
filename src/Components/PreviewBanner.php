<?php

declare(strict_types=1);

namespace Relaticle\Ink\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Relaticle\Ink\Models\Post;

class PreviewBanner extends Component
{
    public function __construct(
        public Post $post,
        public ?string $editUrl = null,
    ) {}

    public function render(): View
    {
        return view('ink::components.preview-banner');
    }
}
