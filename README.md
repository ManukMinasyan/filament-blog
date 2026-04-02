# Filament Blog

Headless blog package for Filament applications. Provides models, Filament admin, MCP tools, SEO components, and publishable UI components — no routes or controllers included.

## Features

- **Filament Admin** — Full PostResource and CategoryResource with markdown editor, draft/published toggle, SEO fields, and featured images
- **SEO Components** — Meta tags, Open Graph, Twitter Cards, JSON-LD structured data, RSS feed, canonical URLs
- **13 MCP Tools** — Full CRUD for posts and categories via Model Context Protocol
- **Publishable UI Components** — Post card, header, body, related posts, category badge, preview banner — all with dark mode
- **No Routes** — Define your own routes, controllers, and page layouts
- **Sitemap Generator** — Route-aware sitemap integration via spatie/laravel-sitemap

## Requirements

- PHP 8.4+
- Laravel 12+
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

## Documentation

**[Read the full documentation →](https://manukminasyan.github.io/filament-blog/)**

- [Installation](https://manukminasyan.github.io/filament-blog/getting-started/installation)
- [Frontend Setup](https://manukminasyan.github.io/filament-blog/getting-started/frontend-setup)
- [Blade Components](https://manukminasyan.github.io/filament-blog/essentials/blade-components)
- [Filament Admin](https://manukminasyan.github.io/filament-blog/essentials/filament-admin)
- [MCP Tools](https://manukminasyan.github.io/filament-blog/essentials/mcp-tools)
- [Configuration](https://manukminasyan.github.io/filament-blog/essentials/configuration)

## Quick Example

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
    <x-blog::related-posts :posts="$relatedPosts" />
</x-your-layout>
```

## License

MIT
