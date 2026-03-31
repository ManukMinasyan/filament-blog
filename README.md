# manukminasyan/filament-blog

Headless blog package for Filament applications. Provides models, Filament admin, MCP tools, SEO components, and publishable UI components -- no routes or controllers included.

## Installation

```bash
composer require manukminasyan/filament-blog
```

Register the Filament plugin in your panel provider:

```php
->plugin(\ManukMinasyan\FilamentBlog\FilamentBlogPlugin::make())
```

Run migrations:

```bash
php artisan migrate
```

Publish config:

```bash
php artisan vendor:publish --tag=filament-blog-config
```

## Configuration

```php
// config/filament-blog.php
return [
    'prefix' => 'blog',
    'author_model' => \App\Models\User::class,
    'per_page' => 12,
    'feed' => [
        'enabled' => true,
        'title' => 'My Blog',
        'description' => 'Latest posts.',
        'author_email' => 'hello@example.com',
    ],
    'publisher' => [
        'name' => 'My Company',
        'url' => 'https://example.com',
        'logo' => 'images/logo.png',
    ],
];
```

## Frontend

This package does not register routes. Define your own routes, controllers, and page views. Use the provided Blade components:

### SEO Components

```blade
{{-- In <head> --}}
<x-blog::meta-tags :post="$post" />
<x-blog::feed-link />

{{-- In <body> --}}
<x-blog::structured-data :post="$post" />
```

### UI Components

```blade
<x-blog::post-header :post="$post" />
<x-blog::post-body :post="$post" />
<x-blog::post-card :post="$post" />
<x-blog::related-posts :posts="$relatedPosts" />
<x-blog::category-badge :category="$category" />
<x-blog::preview-banner :post="$post" :editUrl="$editUrl" />
```

### RSS Feed

```blade
{{-- In your feed route view --}}
<x-blog::feed :posts="$posts" />
```

Publish and customize views:

```bash
php artisan vendor:publish --tag=filament-blog-views
```

## Expected Route Names

The package checks for these route names when generating URLs:

- `blog.index` - Blog listing page
- `blog.show` - Single post page (parameter: `slug`)
- `blog.category` - Category page (parameter: `slug`)
- `blog.preview` - Draft preview (signed URL, parameter: `post`)
- `blog.feed` - RSS feed

If routes don't exist, the package gracefully falls back (returns `#` for URLs, skips sitemap entries).

## MCP Tools

The package includes 13 MCP tools for AI agent integration. Register them in your MCP server.

## Sitemap

Add blog URLs to your sitemap:

```php
use ManukMinasyan\FilamentBlog\BlogSitemapGenerator;

BlogSitemapGenerator::addToSitemap($sitemap);
```
