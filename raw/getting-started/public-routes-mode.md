# Public-routes mode

> Get a working blog at /blog without writing controllers — opt in via config flags.

The package ships an **opt-in public-routes mode**. Flip a flag in config and you get:

<table>
<thead>
  <tr>
    <th>
      Route
    </th>
    
    <th>
      Name
    </th>
    
    <th>
      Notes
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <code>
        GET /blog
      </code>
    </td>
    
    <td>
      <code>
        blog.index
      </code>
    </td>
    
    <td>
      Paginated post listing
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GET /blog/{slug}
      </code>
    </td>
    
    <td>
      <code>
        blog.show
      </code>
    </td>
    
    <td>
      Single post (only published)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GET /blog/category/{slug}
      </code>
    </td>
    
    <td>
      <code>
        blog.category
      </code>
    </td>
    
    <td>
      Category archive (paginated)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GET /blog/preview/{post}
      </code>
    </td>
    
    <td>
      <code>
        blog.preview
      </code>
    </td>
    
    <td>
      Signed-only draft preview, with <code>
        noindex,nofollow
      </code>
      
       meta
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GET /blog/feed
      </code>
    </td>
    
    <td>
      <code>
        blog.feed
      </code>
    </td>
    
    <td>
      RSS 2.0 feed (gated by <code>
        features.feed
      </code>
      
      )
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GET /blog/tag/{slug}
      </code>
    </td>
    
    <td>
      <code>
        blog.tag
      </code>
    </td>
    
    <td>
      Tag archive (gated by <code>
        features.tags
      </code>
      
      )
    </td>
  </tr>
</tbody>
</table>

## Enable it

```php [config/filament-blog.php]
'features' => [
    'public_routes' => true,
    'feed'          => true,   // optional, enables /blog/feed
    'tags'          => true,   // optional, enables /blog/tag/{slug}
],

'layout' => 'layouts.app',     // your host layout the page views extend
```

That's it. The service provider registers the routes at boot — no Filament panel boot is required, so the public site keeps working for guests who never touch the admin.

## Required: a host layout

The page views extend the layout you set in `'layout'`. It must define a `@yield('content')` slot. A minimal example:

```blade [resources/views/layouts/app.blade.php]
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @stack('head')
</head>
<body class="bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    @yield('content')
</body>
</html>
```

If your layout uses a different slot mechanism (e.g. Blade components with `{{ $slot }}`), publish the page views and adapt them:

```bash [Terminal]
php artisan vendor:publish --tag=filament-blog-views
```

## Customizing pages

The shipped pages live at:

- `resources/views/vendor/blog/pages/index.blade.php`
- `resources/views/vendor/blog/pages/show.blade.php`
- `resources/views/vendor/blog/pages/category.blade.php`
- `resources/views/vendor/blog/pages/preview.blade.php`
- `resources/views/vendor/blog/pages/tag.blade.php`
- `resources/views/vendor/blog/pages/feed.blade.php`

Edit them freely — once published, the package no longer serves its own copies.

## Custom prefix

Change `'prefix' => 'blog'` in config. All routes pick up the new prefix.

## Disabling individual pieces

Each feature flag is independent:

```php
'features' => [
    'public_routes' => true,
    'feed'          => false,   // no RSS feed
    'tags'          => false,   // no tag archive (admin still works if registered)
],
```

When a flag is off, requests to that path return **404** (not registered). `Route::has(...)` returns true (the route is defined) but the controller calls `abort_unless($flag, 404)`. That's a deliberate choice so route ordering stays predictable and you can probe the route name without crashing.

## Mode comparison

<table>
<thead>
  <tr>
    <th>
      
    </th>
    
    <th>
      Headless
    </th>
    
    <th>
      Public-routes
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      Routes registered
    </td>
    
    <td>
      None
    </td>
    
    <td>
      All 6
    </td>
  </tr>
  
  <tr>
    <td>
      Controllers
    </td>
    
    <td>
      You write
    </td>
    
    <td>
      Shipped
    </td>
  </tr>
  
  <tr>
    <td>
      Views
    </td>
    
    <td>
      Components only
    </td>
    
    <td>
      Full pages
    </td>
  </tr>
  
  <tr>
    <td>
      Custom domain logic
    </td>
    
    <td>
      Yes (any)
    </td>
    
    <td>
      Limited to view overrides
    </td>
  </tr>
  
  <tr>
    <td>
      Effort to ship a blog
    </td>
    
    <td>
      ~2 hours
    </td>
    
    <td>
      ~5 minutes
    </td>
  </tr>
</tbody>
</table>

If you outgrow public-routes mode, flip the flag back to `false` and write your own controllers — see [Frontend Setup (headless)](/getting-started/frontend-setup).
