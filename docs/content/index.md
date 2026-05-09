---
seo:
  title: Filament Blog — Headless blog package for Filament
  description: Drop a blog into any Filament 5 app. Ships admin, SEO, MCP tools, and Blade components — bring your own routes or opt into the built-in ones with a config flag.
---

::u-page-hero
#title
A blog you can drop into any Filament app

#description
Ships the admin, SEO components, MCP tools, and Blade components. Bring your own routes for full control — or flip a flag and get `/blog` out of the box.

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
  Source on GitHub
  :::
::

::u-page-section
#title
Two ways to install

#description
Headless by default. Opt in to the built-in routes when you want a working blog without writing controllers.

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
  Public-routes mode
  
  #description
  Flip one config flag and get `/blog`, single posts, category and tag archives, signed previews, and an RSS feed. No Filament panel boot needed.
  :::
::

::u-page-section
#title
Five-minute install

#description
Add the package, run migrations, register the plugin. That's the whole admin path. Public routes are one config flag away.

```bash [Terminal]
composer require manukminasyan/filament-blog
php artisan migrate
```

```php [AppPanelProvider.php]
use ManukMinasyan\FilamentBlog\FilamentBlogPlugin;

$panel->plugins([
    FilamentBlogPlugin::make(),
]);
```

```php [config/filament-blog.php]
'features' => [
    'public_routes' => true,   // /blog, /blog/{slug}, signed /blog/preview/{post}
    'feed'          => true,   // /blog/feed (RSS 2.0)
    'tags'          => true,   // TagResource + /blog/tag/{slug}
    'media_library' => false,  // SpatieMediaLibraryFileUpload (optional)
],
```
::

::u-page-section
#title
What's included

#features
  :::u-page-feature
  ---
  icon: i-lucide-layout-dashboard
  ---
  #title
  Filament admin
  
  #description
  Posts, categories, tags. Markdown editor, draft and scheduled UX, SEO fields, bulk publish actions.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-search
  ---
  #title
  SEO baked in
  
  #description
  Meta tags, Open Graph, Twitter Cards, JSON-LD `BlogPosting`, RSS feed, and a sitemap helper — all publishable.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-bot
  ---
  #title
  MCP tools for AI
  
  #description
  13 Model Context Protocol tools so AI agents can write and publish posts. Sanctum-gated and markdown-sanitized.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-hash
  ---
  #title
  Tags taxonomy
  
  #description
  Many-to-many tags with admin UI and a public archive at `/blog/tag/{slug}`. All behind one config flag.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-image
  ---
  #title
  MediaLibrary ready
  
  #description
  Featured-image uploads switch to `SpatieMediaLibraryFileUpload` when you install the package and flip the flag.
  :::

  :::u-page-feature
  ---
  icon: i-lucide-paintbrush
  ---
  #title
  Tailwind components
  
  #description
  Post card, header, body, related posts, preview banner. Dark mode out of the box. Publish and customize.
  :::
::

::u-page-section
#title
Built for teams replacing internal blogs

#description
The schema and admin shape match what Tapix and FilaForms ship internally — so swapping in is a `composer remove` away. No data migration, no template rewrites unless you want them.

```bash [Terminal]
composer remove tapix/blog        # or filaforms/blog
composer require manukminasyan/filament-blog
```
::
