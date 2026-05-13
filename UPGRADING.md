# Upgrading from `manukminasyan/filament-blog` to `relaticle/ink`

This package was renamed from `manukminasyan/filament-blog` to `relaticle/ink` at version `2.0.0`.

## What changed

| Before | After |
|---|---|
| `manukminasyan/filament-blog` | `relaticle/ink` |
| `ManukMinasyan\FilamentBlog\` | `Relaticle\Ink\` |
| `FilamentBlogServiceProvider` | `InkServiceProvider` |
| `FilamentBlogPlugin` | `InkPlugin` |
| `config/filament-blog.php` | `config/ink.php` |
| `config('filament-blog.X')` | `config('ink.X')` |
| `<x-blog::post-card>` etc. | `<x-ink::post-card>` etc. |
| `view('blog::pages.show')` | `view('ink::pages.show')` |
| `--tag=filament-blog-config` | `--tag=ink-config` |
| `--tag=filament-blog-views` | `--tag=ink-views` |
| `--tag=filament-blog-migrations` | `--tag=ink-migrations` |
| `--tag=filament-blog-translations` | `--tag=ink-translations` |

## What did NOT change

- Database tables stay `blog_posts`, `blog_categories`, `blog_tags`, `blog_post_tag` — **no data migration required**
- Route names stay `blog.index`, `blog.show`, `blog.category`, `blog.preview`, `blog.feed`, `blog.tag`
- URL prefix default stays `/blog` (override via `config('ink.prefix')`)
- All public model methods, component APIs, MCP tool signatures

## Upgrade steps

### 1. Swap the composer dependency

```bash
composer remove manukminasyan/filament-blog
composer require relaticle/ink:^2.0
```

### 2. Update imports and references

From your project root, run:

```bash
# PHP namespaces and class names
find app -type f -name '*.php' -exec perl -i -pe '
  s|ManukMinasyan\\FilamentBlog|Relaticle\\Ink|g;
  s|FilamentBlogServiceProvider|InkServiceProvider|g;
  s|FilamentBlogPlugin|InkPlugin|g;
' {} +

# Config calls
find app config -type f \( -name '*.php' -o -name '*.blade.php' \) -exec perl -i -pe "
  s|config\('filament-blog\\.|config('ink.|g;
  s|config\(\"filament-blog\\.|config(\"ink.|g;
" {} +

# Blade components and view namespace
find resources -type f -name '*.blade.php' -exec perl -i -pe '
  s|<x-blog::|<x-ink::|g;
  s|</x-blog::|</x-ink::|g;
  s|view\(["'"'"']blog::|view("ink::|g;
' {} +
```

### 3. Republish config (optional — only if you'd published the old one)

If you'd published `config/filament-blog.php`, either:

- **Keep your edits**: `git mv config/filament-blog.php config/ink.php` (config keys are the same)
- **Start fresh**: delete `config/filament-blog.php` and run `php artisan vendor:publish --tag=ink-config`

### 4. Re-publish views (optional)

If you'd published views to `resources/views/vendor/blog/`, rename to `resources/views/vendor/ink/`:

```bash
git mv resources/views/vendor/blog resources/views/vendor/ink
```

### 5. Run tests

Your existing tests should pass without changes (route names, DB tables, model APIs all preserved).

## Need help?

Open an issue at https://github.com/relaticle/ink/issues
