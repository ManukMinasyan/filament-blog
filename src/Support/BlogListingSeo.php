<?php

declare(strict_types=1);

namespace Relaticle\Ink\Support;

use RalphJSmit\Laravel\SEO\Support\SEOData;
use Relaticle\Ink\Models\Category;
use Relaticle\Ink\Models\Tag;

final class BlogListingSeo
{
    public static function forIndex(int $page = 1, ?string $searchQuery = null): SEOData
    {
        $base = route('blog.index');
        $title = $page > 1 ? "Blog — Page {$page}" : 'Blog';
        $description = 'Latest posts from the blog.';
        $url = $page > 1 ? "{$base}?page={$page}" : $base;
        $robots = $searchQuery !== null && $searchQuery !== '' ? 'noindex,follow' : null;

        return new SEOData(
            title: $title,
            description: $description,
            url: $url,
            robots: $robots,
        );
    }

    public static function forCategory(Category $category, int $page = 1): SEOData
    {
        $base = route('blog.category', $category->slug);
        $title = $page > 1 ? "{$category->name} — Page {$page}" : $category->name;
        $description = "Posts in {$category->name}.";
        $url = $page > 1 ? "{$base}?page={$page}" : $base;

        return new SEOData(
            title: $title,
            description: $description,
            url: $url,
        );
    }

    public static function forTag(Tag $tag, int $page = 1): SEOData
    {
        $base = route('blog.tag', $tag->slug);
        $title = $page > 1 ? "{$tag->name} — Page {$page}" : $tag->name;
        $description = "Posts tagged {$tag->name}.";
        $url = $page > 1 ? "{$base}?page={$page}" : $base;

        return new SEOData(
            title: $title,
            description: $description,
            url: $url,
        );
    }
}
