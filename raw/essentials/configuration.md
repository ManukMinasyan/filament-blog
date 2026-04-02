# Configuration

> Full configuration reference for Filament Blog.

Publish the config file:

```bash [Terminal]
php artisan vendor:publish --tag=filament-blog-config
```

## Full Reference

```php [config/filament-blog.php]
return [
    // Route prefix for blog URLs
    'prefix' => 'blog',

    // Model class for blog post authors
    'author_model' => \App\Models\User::class,

    // Posts per page in listings
    'per_page' => 12,

    // RSS feed configuration
    'feed' => [
        'enabled' => true,
        'title' => null,        // Falls back to app.name + " Blog"
        'description' => null,
        'author_email' => null,  // Used in RSS <author> tags
    ],

    // Publisher info for JSON-LD structured data
    'publisher' => [
        'name' => null,    // Organization name
        'url' => null,     // Organization URL
        'logo' => null,    // Path to logo (used with asset())
    ],
];
```

## Sitemap Integration

Add blog URLs to your sitemap generation:

```php [GenerateSitemapCommand.php]
use ManukMinasyan\FilamentBlog\BlogSitemapGenerator;
use Spatie\Sitemap\Sitemap;

$sitemap = Sitemap::create();
BlogSitemapGenerator::addToSitemap($sitemap);
$sitemap->writeToFile(public_path('sitemap.xml'));
```

The generator is route-aware — it only adds URLs for routes that exist in your application.

## Customizing Views

Publish all Blade component views:

```bash [Terminal]
php artisan vendor:publish --tag=filament-blog-views
```

Published views go to `resources/views/vendor/blog/components/`. Edit them to match your design system.
