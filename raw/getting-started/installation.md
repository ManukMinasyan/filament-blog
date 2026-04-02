# Installation

> Install Filament Blog in your Laravel application.

## Requirements

- **PHP:** 8.4+
- **Laravel:** 12+
- **Filament:** 5.x
- **Database:** PostgreSQL, MySQL, SQLite

## Quick Setup

<steps>

### Install Package

```bash [Terminal]
composer require manukminasyan/filament-blog
```

<alert type="info">

For private repositories, add the VCS repository to your `composer.json` first:

```json [composer.json]
"repositories": [
    {"type": "vcs", "url": "git@github.com:ManukMinasyan/filament-blog.git"}
]
```

</alert>

### Run Migrations

```bash [Terminal]
php artisan migrate
```

This creates `blog_posts` and `blog_categories` tables.

### Register Filament Plugin

```php [AppPanelProvider.php]
use ManukMinasyan\FilamentBlog\FilamentBlogPlugin;

$panel->plugins([
    FilamentBlogPlugin::make(),
]);
```

### Publish Config

```bash [Terminal]
php artisan vendor:publish --tag=filament-blog-config
```

### Configure

```php [config/filament-blog.php]
return [
    'prefix' => 'blog',
    'author_model' => \App\Models\User::class,
    'per_page' => 12,
    'feed' => [
        'enabled' => true,
        'title' => 'My Engineering Blog',
        'description' => 'Latest posts from the team.',
        'author_email' => 'hello@example.com',
    ],
    'publisher' => [
        'name' => 'My Company',
        'url' => 'https://example.com',
        'logo' => 'images/logo.png',
    ],
];
```

</steps>

**Done!** Visit your Filament panel to see the Blog section with Posts and Categories.

## Morph Map

If your application enforces morph maps, add the blog models:

```php [AppServiceProvider.php]
Relation::enforceMorphMap([
    // ...existing entries
    'blog_post' => \ManukMinasyan\FilamentBlog\Models\Post::class,
    'blog_category' => \ManukMinasyan\FilamentBlog\Models\Category::class,
]);
```

## SEO Migration

The package requires `ralphjsmit/laravel-seo`. Publish its migration:

```bash [Terminal]
php artisan vendor:publish --tag=seo-migrations
php artisan migrate
```
