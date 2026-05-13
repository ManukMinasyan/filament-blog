# Configuration

> Full configuration reference for Ink.

Publish the config file:

```bash [Terminal]
php artisan vendor:publish --tag=ink-config
```

## Full reference

```php [config/ink.php]
return [
    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    | The URI prefix for all blog routes when public-routes mode is enabled.
    | Used as: /{prefix}, /{prefix}/{slug}, /{prefix}/category/{slug}, etc.
    */
    'prefix' => 'blog',

    /*
    |--------------------------------------------------------------------------
    | Layout view
    |--------------------------------------------------------------------------
    | The Blade layout the shipped page views extend (when public-routes mode
    | is enabled). Must define a @yield('content') slot. Ignored in headless
    | mode.
    */
    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Author model
    |--------------------------------------------------------------------------
    | The Eloquent model used as the post author. Must have an integer or
    | string primary key matching the type of `users.id`.
    */
    'author_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Posts per page
    |--------------------------------------------------------------------------
    | Used by the index/category/tag listing pages.
    */
    'per_page' => 12,

    /*
    |--------------------------------------------------------------------------
    | Feature flags
    |--------------------------------------------------------------------------
    | All opt-in. Defaults match the headless install — flip flags to enable.
    */
    'features' => [
        // Register /blog, /blog/{slug}, /blog/category/{slug}, signed
        // /blog/preview/{post}. See: getting-started/public-routes-mode
        'public_routes' => false,

        // Register /blog/feed (RSS 2.0). Independent of public_routes —
        // when enabled alone, only the feed route is registered.
        'feed' => false,

        // Hint for the BlogSitemapGenerator. Today the helper works
        // regardless; the flag is reserved for an auto-discovery hook
        // tracked in the Phase 3 roadmap.
        'sitemap' => false,

        // Show TagResource in the admin nav, the multi-select tags field
        // on the Post form, and the /blog/tag/{slug} archive route.
        // See: essentials/tags
        'tags' => false,

        // Use SpatieMediaLibraryFileUpload for the featured-image field
        // when both this flag is on AND spatie/laravel-medialibrary is
        // installed. See: essentials/media-library
        'media_library' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema auto-emission
    |--------------------------------------------------------------------------
    | Detect FAQ and HowTo sections in post content and emit JSON-LD schema
    | automatically. FAQ detection looks for an `## FAQ` H2 followed by H3
    | question / paragraph answer pairs. HowTo detection looks for a `## Steps`
    | H2 followed by H3 step headings.
    */
    'schema' => [
        'faq_auto' => true,
        'howto_auto' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    | Defaults to a portable LIKE search across title/excerpt/content.
    | Override `callback` to use Postgres FTS, MySQL FULLTEXT, Scout, etc.
    */
    'search' => [
        'callback' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | RSS feed metadata
    |--------------------------------------------------------------------------
    */
    'feed' => [
        'title' => null,         // falls back to config('app.name')
        'description' => null,
        'author_email' => null,  // RSS <author> tag email
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON-LD publisher block
    |--------------------------------------------------------------------------
    | Used by <x-ink::structured-data> and the Post::getDynamicSEOData()
    | Article schema. Leave nulls to omit fields.
    */
    'publisher' => [
        'name' => null,    // Organization name
        'url' => null,     // Organization URL
        'logo' => null,    // Path used with asset()
    ],

    /*
    |--------------------------------------------------------------------------
    | Table names
    |--------------------------------------------------------------------------
    | Override if blog_posts/blog_categories collide with existing tables in
    | your application. Migrations and models pick these up.
    */
    'tables' => [
        'posts' => 'blog_posts',
        'categories' => 'blog_categories',
    ],
];
```

## Sitemap integration

Add blog URLs to your sitemap generation:

```php [GenerateSitemapCommand.php]
use Relaticle\Ink\BlogSitemapGenerator;
use Spatie\Sitemap\Sitemap;

$sitemap = Sitemap::create();
BlogSitemapGenerator::addToSitemap($sitemap);
$sitemap->writeToFile(public_path('sitemap.xml'));
```

The generator is route-aware — it only adds URLs for routes that exist in your application.

## Customizing views

Publish all Blade page + component views:

```bash [Terminal]
php artisan vendor:publish --tag=ink-views
```

Published files go to:

- `resources/views/vendor/blog/components/` — the publishable components used in headless mode
- `resources/views/vendor/blog/pages/` — the page views used in public-routes mode

Edit them to match your design system. Once published, the package no longer serves its own copies of those files.

## Customizing translations

```bash [Terminal]
php artisan vendor:publish --tag=ink-translations
```

(No translations ship by default; this tag exists for future localization.)
