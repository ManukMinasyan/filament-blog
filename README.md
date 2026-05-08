# Filament Blog

Headless blog package for Filament applications. Provides models, Filament admin, MCP tools, SEO components, publishable UI components, and an **opt-in public-routes mode** for hosts that just want a working blog without writing controllers.

## Features

- **Filament Admin** — PostResource and CategoryResource with markdown editor, draft/published/scheduled UX, SEO fields, featured images, and bulk publish/unpublish/schedule actions
- **SEO Components** — Meta tags, Open Graph, Twitter Cards, JSON-LD structured data, RSS feed, canonical URLs
- **13 MCP Tools** — Full CRUD for posts and categories via Model Context Protocol, with markdown sanitization (HTML stripped, unsafe links blocked)
- **Publishable UI Components** — Post card, header, body, related posts, category badge, preview banner — all with dark mode
- **Two install modes**
  - **Headless (default)** — define your own routes/controllers, use the Blade components
  - **Public-routes mode (opt-in)** — flip a config flag, get `/blog`, `/blog/{slug}`, `/blog/category/{slug}`, signed `/blog/preview/{post}`, and optional `/blog/feed`
- **Tags taxonomy** (opt-in via `features.tags`) — many-to-many `blog_post_tag` table, `TagResource` admin UI, public archive at `/blog/tag/{slug}`
- **MediaLibrary integration** (opt-in via `features.media_library`) — when both the flag is on AND `spatie/laravel-medialibrary` is installed, the featured-image upload uses `SpatieMediaLibraryFileUpload` instead of the plain `FileUpload`. Falls back gracefully if MediaLibrary isn't installed.
- **Sitemap Generator** — Route-aware sitemap integration via spatie/laravel-sitemap
- **Reading-time + related-posts** helpers on the Post model

## Requirements

- PHP 8.4+
- Laravel 12+ or 13
- Filament 5.x

## Installation

```bash
composer require manukminasyan/filament-blog
```

Register the plugin and run migrations:

```php
// AppPanelProvider.php
->plugin(\ManukMinasyan\FilamentBlog\FilamentBlogPlugin::make())
```

```bash
php artisan migrate
```

## Public-routes mode (opt-in)

By default this package is fully headless: no routes, no controllers, no forced views. Your app owns all rendering.

To get a working blog at `/blog` without writing any controllers, flip the feature flag:

```php
// config/filament-blog.php
'features' => [
    'public_routes' => true,   // /blog, /blog/{slug}, /blog/category/{slug}, /blog/preview/{post}
    'feed'          => true,   // adds /blog/feed (RSS 2.0)
    'tags'          => true,   // /blog/tag/{slug}, TagResource in admin
    'media_library' => true,   // SpatieMediaLibraryFileUpload (requires spatie/laravel-medialibrary)
],

'layout' => 'layouts.app',     // your host layout to extend
```

Routes register at the service-provider level — no Filament panel boot is required, so the public site keeps working for guests who never touch the admin.

Publish the views if you want to customize them:

```bash
php artisan vendor:publish --tag=filament-blog-views
```

## Documentation

**[Read the full documentation →](https://manukminasyan.github.io/filament-blog/)**

- [Installation](https://manukminasyan.github.io/filament-blog/getting-started/installation)
- [Frontend Setup](https://manukminasyan.github.io/filament-blog/getting-started/frontend-setup)
- [Blade Components](https://manukminasyan.github.io/filament-blog/essentials/blade-components)
- [Filament Admin](https://manukminasyan.github.io/filament-blog/essentials/filament-admin)
- [MCP Tools](https://manukminasyan.github.io/filament-blog/essentials/mcp-tools)
- [Configuration](https://manukminasyan.github.io/filament-blog/essentials/configuration)

## Quick Example (headless)

```blade
{{-- In your blog show page --}}
<x-your-layout>
    @push('head')
        <x-blog::meta-tags :post="$post" />
        <x-blog::feed-link />
    @endpush

    <x-blog::structured-data :post="$post" />
    <x-blog::post-header :post="$post" />
    <x-blog::post-body :post="$post" />
    <x-blog::related-posts :post="$post" />
</x-your-layout>
```

## License

MIT
