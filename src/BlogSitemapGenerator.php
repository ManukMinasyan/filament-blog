<?php

declare(strict_types=1);

namespace Relaticle\Ink;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Relaticle\Ink\Models\Category;
use Relaticle\Ink\Models\Post;
use Relaticle\Ink\Models\Tag;
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

        if (Route::has('blog.tag') && config('ink.features.tags', false)) {
            Tag::query()
                ->select(['id', 'slug'])
                ->whereHas('posts', fn (Builder $query) => $query->published())
                ->each(function (Tag $tag) use ($sitemap): void {
                    $sitemap->add(
                        Url::create(route('blog.tag', $tag->slug))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.5)
                    );
                });
        }

        return $sitemap;
    }
}
