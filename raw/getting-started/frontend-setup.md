# Frontend Setup (headless)

> Build your own blog frontend using the package's Blade components.

<alert type="info">

**Want a working blog without writing controllers?** See [Public-routes mode](/getting-started/public-routes-mode) — flip a config flag and you're done. This page covers the **headless mode** for hosts who want full control.

</alert>

In headless mode the package ships **no routes, no controllers, no page views**. You wire your own routing and use the provided Blade components.

## Create routes

```php [routes/web.php]
use App\Http\Controllers\BlogController;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/feed', [BlogController::class, 'feed'])->name('feed');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('tag');
    Route::get('/preview/{post}', [BlogController::class, 'preview'])
        ->name('preview')->middleware('signed');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});
```

The route names matter — the package's URL helpers and SEO components check for them via `Route::has(...)` and fall back gracefully when missing.

## Create controller

```php [app/Http/Controllers/BlogController.php]
use ManukMinasyan\FilamentBlog\Models\Category;
use ManukMinasyan\FilamentBlog\Models\Post;
use ManukMinasyan\FilamentBlog\Models\Tag;

final readonly class BlogController
{
    public function index(): View
    {
        $posts = Post::query()
            ->published()
            ->with(['category', 'author', 'seo'])
            ->latest('published_at')
            ->paginate(config('filament-blog.per_page', 12));

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with(['category', 'author', 'seo'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = $post->relatedPosts()->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $posts = Post::query()
            ->where('category_id', $category->id)
            ->published()
            ->with(['category', 'author', 'seo'])
            ->latest('published_at')
            ->paginate(config('filament-blog.per_page', 12));

        return view('blog.category', compact('category', 'posts'));
    }

    public function tag(string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = Post::query()
            ->whereHas('tags', fn ($q) => $q->where('blog_tags.id', $tag->id))
            ->published()
            ->with(['category', 'author', 'seo'])
            ->latest('published_at')
            ->paginate(config('filament-blog.per_page', 12));

        return view('blog.tag', compact('tag', 'posts'));
    }

    public function preview(Post $post): View
    {
        return view('blog.preview', ['post' => $post->loadMissing(['category', 'author', 'seo'])]);
    }

    public function feed(): Response
    {
        $posts = Post::query()
            ->published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->limit(20)
            ->get();

        return response()
            ->view('blog.feed', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
```

## Create views

Use the package's Blade components inside your own page templates:

```blade [resources/views/blog/show.blade.php]
<x-your-layout>
    @push('head')
        <x-blog::meta-tags :post="$post" />
        <x-blog::feed-link />
    @endpush

    <x-blog::structured-data :post="$post" />

    <x-blog::post-header :post="$post" />
    <x-blog::post-body :post="$post" />
    <x-blog::related-posts :posts="$relatedPosts" />
</x-your-layout>
```

```blade [resources/views/blog/index.blade.php]
<x-your-layout>
    @foreach($posts as $post)
        <x-blog::post-card :post="$post" />
    @endforeach

    {{ $posts->links() }}
</x-your-layout>
```

```blade [resources/views/blog/feed.blade.php]
<x-blog::feed :posts="$posts" />
```

## Helpers on the Post model

Useful in your views:

```php
$post->readingTime();       // int — minutes
$post->relatedPosts(3);     // Builder of same-category, published, !=$this->id
$post->getUrl();            // route('blog.show', $post->slug) if registered, else fallback
```

## Expected route names

The package checks for these names when generating URLs. If a route is missing, the helper returns `#`:

<table>
<thead>
  <tr>
    <th>
      Name
    </th>
    
    <th>
      Purpose
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <code>
        blog.index
      </code>
    </td>
    
    <td>
      Listing page
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        blog.show
      </code>
    </td>
    
    <td>
      Single post (<code>
        slug
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        blog.category
      </code>
    </td>
    
    <td>
      Category archive (<code>
        slug
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        blog.tag
      </code>
    </td>
    
    <td>
      Tag archive (<code>
        slug
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        blog.preview
      </code>
    </td>
    
    <td>
      Signed draft preview (<code>
        post
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        blog.feed
      </code>
    </td>
    
    <td>
      RSS feed
    </td>
  </tr>
</tbody>
</table>

## Promote to public-routes mode any time

If writing all this gets old, flip the flag in config and delete your controller — the package will register equivalent routes automatically. See [Public-routes mode](/getting-started/public-routes-mode).
