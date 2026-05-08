<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ManukMinasyan\FilamentBlog\Models\Post;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) config('filament-blog.per_page', 12);

        $posts = Post::query()
            ->with(['category', 'author', 'seo'])
            ->published()
            ->latest('published_at')
            ->paginate($perPage);

        return view('blog::pages.index', [
            'posts' => $posts,
        ]);
    }
}
