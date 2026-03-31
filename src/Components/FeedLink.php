<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class FeedLink extends Component
{
    public bool $hasFeedRoute;

    public function __construct()
    {
        $this->hasFeedRoute = Route::has('blog.feed');
    }

    public function render(): View
    {
        return view('blog::components.feed-link');
    }

    public function shouldRender(): bool
    {
        return $this->hasFeedRoute && config('filament-blog.feed.enabled', true);
    }
}
