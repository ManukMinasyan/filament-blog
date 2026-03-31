<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ManukMinasyan\FilamentBlog\Models\Category;

class CategoryBadge extends Component
{
    public function __construct(
        public Category $category,
        public bool $linked = true,
    ) {}

    public function render(): View
    {
        return view('blog::components.category-badge');
    }
}
