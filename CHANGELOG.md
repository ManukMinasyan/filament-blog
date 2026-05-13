# Changelog

All notable changes to this project will be documented in this file.

## [2.1.0] - Unreleased

### Added
- `Relaticle\Ink\Support\BlogListingSeo` helper for building per-page `SEOData` for listings. Headless consumers can call `BlogListingSeo::forIndex/forCategory/forTag` from their own controllers.
- Auto-emit `FAQPage` JSON-LD on post pages when content contains an `## FAQ` H2 followed by `### Question?` / answer-paragraph pairs. Controlled by `schema.faq_auto` config (default `true`).
- Auto-emit `HowTo` JSON-LD on post pages when content contains a `## Steps` H2 followed by `### Step Name` / paragraph pairs. Opt-in via `schema.howto_auto` config (default `false`).
- `Relaticle\Ink\Support\SchemaExtractor` helper for FAQ + HowTo HTML parsing.
- Auto-emit `Blog` and `CollectionPage` JSON-LD on listing routes (`blog.index` emits both; `blog.category` and `blog.tag` emit `CollectionPage`).
- Numbered, aria-labeled pagination view at `ink::pagination.blog`. Listing pages (index/category/tag) use it by default. Publish via `php artisan vendor:publish --tag=ink-views` to customize.
- `wire:navigate` on the `<x-ink::post-card>` post-link and pagination links for SPA-feel navigation in Livewire 4 hosts. No-op when Livewire is not present.
- `Post::search($term)` query scope. Defaults to a portable LIKE search across title/excerpt/content. Override via `search.callback` config for Postgres FTS, MySQL FULLTEXT, Scout, etc.
- The shipped `/blog` route now honors `?q=` for search. The existing `BlogListingSeo::forIndex(searchQuery:)` call ensures `noindex,follow` is set on search result URLs.
- `BlogSearch` Livewire component (`<livewire:blog::search />`) with URL-synced `?q=` query, 400ms debounce, and empty state. Theme-agnostic — publish the view at `resources/views/vendor/ink/livewire/blog-search.blade.php` to restyle.

### Fixed
- `BlogSitemapGenerator` now includes `/blog/tag/{slug}` URLs when the tags feature is enabled and the tag has published posts.
- Listing routes (`/blog`, `/blog/category/{slug}`, `/blog/tag/{slug}`) now emit per-page canonical URLs and page-aware titles. Previously every paginated page canonicalized to page 1, causing Google to treat pages 2+ as duplicate content.
- Shipped `show` and `preview` routes now call `seo()->for($post)` so post-attached SEO (BlogPosting + BreadcrumbList + FAQPage JSON-LD) actually renders in public-routes mode. Previously the schema only worked for consumers who overrode the controller.

## [2.0.0] - Unreleased

### Changed (BREAKING)
- **Package renamed** from `manukminasyan/filament-blog` to `relaticle/ink`
- **PHP namespace** changed from `ManukMinasyan\FilamentBlog\` to `Relaticle\Ink\`
- **Service provider** renamed: `FilamentBlogServiceProvider` → `InkServiceProvider`
- **Filament plugin** renamed: `FilamentBlogPlugin` → `InkPlugin`
- **Config file** renamed: `config/filament-blog.php` → `config/ink.php`. Use `config('ink.X')` instead of `config('filament-blog.X')`.
- **Publish tags** renamed: `filament-blog-config` → `ink-config`, `filament-blog-views` → `ink-views`, `filament-blog-migrations` → `ink-migrations`, `filament-blog-translations` → `ink-translations`
- **View component prefix** renamed: `<x-blog::post-card>` → `<x-ink::post-card>` (all components affected)
- **View namespace** renamed: `view('blog::X')` → `view('ink::X')`

### Unchanged (compatibility-preserving)
- Database table names (`blog_posts`, `blog_categories`, `blog_tags`, `blog_post_tag`) — no data migration required
- Route names (`blog.index`, `blog.show`, `blog.category`, `blog.preview`, `blog.feed`, `blog.tag`) — public API contract preserved
- URL prefix default (still `/blog`, configurable via `config('ink.prefix')`)
- All public model/component APIs and method signatures

### Migration
```bash
composer remove manukminasyan/filament-blog
composer require relaticle/ink:^2.0
```

See [UPGRADING.md](UPGRADING.md) for the full sed recipe.

## [1.0.1] - 2026-04-01

### Fixed
- Detect user ID column type for `author_id` foreign key (supports ULID/UUID)

## [1.0.0] - 2026-04-01

### Added
- Initial release
- Post and Category Eloquent models with SEO, slugs, soft deletes
- PostStatus enum (Draft/Published)
- Filament PostResource with markdown editor, status toggle, SEO section
- Filament CategoryResource with post count
- 13 MCP tools for blog post and category CRUD
- 10 publishable Blade components (meta-tags, structured-data, feed-link, feed, post-card, post-header, post-body, related-posts, category-badge, preview-banner)
- BlogSitemapGenerator with route-aware URL generation
- Dark mode support in all UI components
- Route-aware `Post::getUrl()` with fallback
