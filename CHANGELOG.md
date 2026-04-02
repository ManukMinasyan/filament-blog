# Changelog

All notable changes to this project will be documented in this file.

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
