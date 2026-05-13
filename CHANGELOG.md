# Changelog

All notable changes to this project will be documented in this file.

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
