<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use ManukMinasyan\FilamentBlog\Models\Category;
use ManukMinasyan\FilamentBlog\Models\Post;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class BlogSitemapGenerator
{
    public static function addToSitemap(Sitemap $sitemap): Sitemap
    {
        if (! Route::has('blog.index')) {
            return $sitemap;
        }

        $sitemap->add(
            Url::create(route('blog.index'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8)
        );

        if (Route::has('blog.category')) {
            Category::query()
                ->select(['id', 'slug'])
                ->whereHas('posts', fn (Builder $query) => $query->published())
                ->each(function (Category $category) use ($sitemap): void {
                    $sitemap->add(
                        Url::create(route('blog.category', $category->slug))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                });
        }

        if (Route::has('blog.show')) {
            Post::query()
                ->select(['id', 'slug', 'published_at', 'updated_at'])
                ->published()
                ->latest('published_at')
                ->each(function (Post $post) use ($sitemap): void {
                    $sitemap->add(
                        Url::create(route('blog.show', $post->slug))
                            ->setLastModificationDate($post->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                });
        }

        return $sitemap;
    }
}
