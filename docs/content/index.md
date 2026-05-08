---
seo:
  title: Filament Blog - Headless Blog Package
  description: Headless-by-default blog package for Filament. Opt-in public routes, tags taxonomy, MediaLibrary integration, MCP tools, and SEO components — your app stays in control.
---

::u-page-hero
#title
Filament Blog

#description
Headless-by-default blog for Filament 5 — opt into public routes, RSS feed, tags taxonomy, and MediaLibrary integration with config flags. Your app stays in control.

#links
  :::u-button
  ---
  color: neutral
  size: xl
  to: /getting-started/installation
  trailing-icon: i-lucide-arrow-right
  ---
  Get started
  :::

  :::u-button
  ---
  color: neutral
  icon: simple-icons:github
  size: xl
  to: https://github.com/ManukMinasyan/filament-blog
  variant: outline
  ---
  GitHub
  :::
::

::u-page-section
#title
Two install modes

#features
  :::u-page-feature
  ---
  icon: i-lucide-route-off
  ---
  #title
  Headless (default)
  
  #description
  No routes, no controllers, no forced views. Use the Blade components, write your own routing. Your app owns the frontend.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-zap
  ---
  #title
  Public-routes mode (opt-in)
  
  #description
  Flip a config flag — get `/blog`, `/blog/{slug}`, `/blog/category/{slug}`, signed `/blog/preview/{post}`, and optional `/blog/feed`. No Filament panel boot needed.
  :::
::

::u-page-section
#title
What's in the box

#features
  :::u-page-feature
  ---
  icon: i-lucide-layout-dashboard
  ---
  #title
  Filament Admin Panel
  
  #description
  PostResource and CategoryResource with markdown editor, draft/scheduled UX, SEO fields, featured images, and bulk publish/unpublish/schedule actions.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-search
  ---
  #title
  SEO Components
  
  #description
  Meta tags, Open Graph, Twitter Cards, JSON-LD `BlogPosting`, RSS feed, canonical URLs — all publishable Blade components.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-bot
  ---
  #title
  13 MCP Tools
  
  #description
  Full CRUD for posts and categories via Model Context Protocol with Sanctum ability gating and markdown sanitization (HTML stripped, unsafe links blocked).
  :::

  :::u-page-feature
  ---
  icon: i-lucide-hash
  ---
  #title
  Tags taxonomy (opt-in)
  
  #description
  Many-to-many `blog_post_tag` table, `TagResource` admin UI, public archive at `/blog/tag/{slug}` — all behind a single config flag.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-image
  ---
  #title
  MediaLibrary integration (opt-in)
  
  #description
  When the flag is on and `spatie/laravel-medialibrary` is installed, featured-image uploads use `SpatieMediaLibraryFileUpload`. Falls back gracefully if the package isn't installed.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-paintbrush
  ---
  #title
  Publishable UI Components
  
  #description
  Post-card, post-header, post-body, related-posts, and more — Tailwind + dark mode out of the box. Publish to fully customize.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-map
  ---
  #title
  Sitemap Generator
  
  #description
  Route-aware sitemap helper that automatically adds blog index, categories, tags, and individual posts.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-clock
  ---
  #title
  Reading time + related posts
  
  #description
  `Post::readingTime()` and `Post::relatedPosts()` helpers ship with the model. No extra queries to wire.
  :::
::
