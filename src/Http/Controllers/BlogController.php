<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ManukMinasyan\FilamentBlog\Models\Category;
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

    public function show(string $slug): View
    {
        $post = Post::query()
            ->with(['category', 'author', 'seo'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedPosts = $post->relatedPosts(limit: 3)->get();

        return view('blog::pages.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $perPage = (int) config('filament-blog.per_page', 12);

        $posts = Post::query()
            ->with(['category', 'author', 'seo'])
            ->where('category_id', $category->id)
            ->published()
            ->latest('published_at')
            ->paginate($perPage);

        return view('blog::pages.category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }

    public function preview(Post $post): View
    {
        return view('blog::pages.preview', [
            'post' => $post->loadMissing(['category', 'author', 'seo']),
        ]);
    }
}
