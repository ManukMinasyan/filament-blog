# Public Routes & Drop-in Replacement (Phase 1) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make `manukminasyan/filament-blog` a drop-in replacement for the Tapix and FilaForms internal blog packages by adding opt-in public routes (controller + page views), bulk publishing actions, MCP markdown sanitization, reading-time / related-posts wiring, plus a real test suite and CI.

**Architecture:** Feature flags live in `config/filament-blog.php` (not on the Filament plugin) so public routes register at the service-provider level — independently of any Filament panel boot. Headless behavior remains the default (`features.public_routes = false` would mean exactly today's behavior). Filament panel concerns stay on the plugin (resource discovery, MCP tool registration). Two-layer architecture: Core (always on) + Plus (opt-in via config).

**Tech Stack:** PHP 8.3+ · Laravel 12 · Filament v5 · Pest v3 · Spatie Laravel Package Tools · Spatie Sluggable · Ralph J Smit Laravel SEO · Spatie Markdown.

---

## File Structure

**Created:**
- `src/Http/Controllers/BlogController.php` — public controller (index/show/category/preview/feed)
- `src/Http/Controllers/BaseBlogController.php` — *not created*; we keep one controller, no abstraction
- `routes/web.php` — public route file (loaded conditionally by service provider)
- `resources/views/layouts/blog.blade.php` — wrapper that extends host layout
- `resources/views/pages/index.blade.php`
- `resources/views/pages/show.blade.php`
- `resources/views/pages/category.blade.php`
- `resources/views/pages/preview.blade.php`
- `resources/views/pages/feed.blade.php` — RSS 2.0 page (re-uses existing `<x-blog::feed>` component)
- `resources/views/pages/_post-content.blade.php` — shared partial used by show + preview
- `tests/TestCase.php` — Orchestra Testbench base
- `tests/Pest.php` — pest bootstrap
- `tests/Feature/PublicRoutesTest.php`
- `tests/Feature/PostResourceBulkActionsTest.php`
- `tests/Feature/Mcp/CreatePostToolTest.php`
- `tests/Feature/PostModelTest.php` — reading time, related posts
- `phpunit.xml.dist`
- `pint.json`
- `.github/workflows/tests.yml`

**Modified:**
- `config/filament-blog.php` — add `features` array, `layout`, `tables` sections, fill default `feed` metadata
- `src/FilamentBlogServiceProvider.php` — read config flags, conditionally register routes
- `src/Filament/Resources/PostResource.php` — add bulk publish/unpublish/schedule actions
- `src/Mcp/Tools/CreatePostTool.php` — markdown sanitization
- `src/Mcp/Tools/UpdatePostTool.php` — markdown sanitization
- `src/Models/Post.php` — `readingTime()` accessor, `relatedPosts()` query
- `src/Components/RelatedPosts.php` — call new model method, expose `$relatedPosts` to view
- `composer.json` — require-dev: pestphp/pest, orchestra/testbench, laravel/pint, larastan/larastan
- `README.md` — document new flags + plugin builder additions
- `docs/content/1.getting-started/2.frontend-setup.md` — describe public-routes mode

---

## Setup

### Task 0: Setup branch and verify clean state

**Files:** none (git plumbing only)

- [ ] **Step 1: Confirm branch and clean state**

```bash
cd /tmp/filament-blog   # or your local clone
git status
git branch --show-current
```

Expected: branch `feat/public-routes-phase-1`, clean working tree.

- [ ] **Step 2: Pull main to be sure we're up to date**

```bash
git fetch origin
git log --oneline origin/main -3
```

Expected: see latest main commits; if branch is behind, rebase.

---

## Setup: tests + CI

### Task 1: Add test infrastructure

**Files:**
- Create: `composer.json` (modify)
- Create: `tests/TestCase.php`
- Create: `tests/Pest.php`
- Create: `phpunit.xml.dist`
- Create: `pint.json`

- [ ] **Step 1: Add dev dependencies**

```bash
cd /tmp/filament-blog
composer require --dev pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 \
    orchestra/testbench:^9.0 laravel/pint:^1.14 larastan/larastan:^3.0 --no-update
composer update --no-scripts
```

Expected: `composer.json` gets a `require-dev` block; `composer.lock` updated; vendor populated.

- [ ] **Step 2: Create `tests/TestCase.php`**

```php
<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Livewire\LivewireServiceProvider;
use ManukMinasyan\FilamentBlog\FilamentBlogServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RalphJSmit\Laravel\SEO\SEOServiceProvider as RalphSEOServiceProvider;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            FilamentServiceProvider::class,
            ActionsServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            LivewireServiceProvider::class,
            RalphSEOServiceProvider::class,
            FilamentBlogServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('view.paths', [__DIR__.'/Fixtures/views']);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // minimal users table for the author FK
        $this->app['db']->connection()->getSchemaBuilder()
            ->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamps();
            });
    }
}
```

- [ ] **Step 3: Create `tests/Pest.php`**

```php
<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use ManukMinasyan\FilamentBlog\Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
```

- [ ] **Step 4: Create `phpunit.xml.dist`**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://getcomposer.org/xsd/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         processIsolation="false"
         stopOnFailure="false"
         cacheDirectory=".phpunit.cache"
         executionOrder="random"
         backupGlobals="false"
         backupStaticProperties="false"
         beStrictAboutOutputDuringTests="true"
         failOnNotice="true"
         failOnRisky="true"
         failOnWarning="true">
    <testsuites>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

- [ ] **Step 5: Create `pint.json`**

```json
{
    "preset": "laravel",
    "rules": {
        "declare_strict_types": true,
        "ordered_imports": { "sort_algorithm": "alpha" },
        "no_unused_imports": true
    }
}
```

- [ ] **Step 6: Create `tests/Fixtures/views/.gitkeep`**

```bash
mkdir -p /tmp/filament-blog/tests/Fixtures/views
touch /tmp/filament-blog/tests/Fixtures/views/.gitkeep
```

- [ ] **Step 7: Smoke-run pest**

```bash
cd /tmp/filament-blog
vendor/bin/pest --version
```

Expected: prints Pest version, no error.

- [ ] **Step 8: Commit**

```bash
git add composer.json composer.lock tests/TestCase.php tests/Pest.php tests/Fixtures/views/.gitkeep phpunit.xml.dist pint.json
git commit -m "test: add Pest + Testbench scaffolding and Pint config"
```

---

### Task 2: Add CI workflow

**Files:**
- Create: `.github/workflows/tests.yml`

- [ ] **Step 1: Write the workflow**

```yaml
name: Tests

on:
  push:
    branches: [main]
  pull_request:
  workflow_call:

permissions:
  contents: read

jobs:
  tests:
    runs-on: ubuntu-latest

    strategy:
      fail-fast: false
      matrix:
        php: ['8.3', '8.4']
        laravel: ['^12.0']

    name: PHP ${{ matrix.php }} - Laravel ${{ matrix.laravel }}

    steps:
      - name: Checkout
        uses: actions/checkout@v6

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite, bcmath, soap, intl, gd, exif, iconv
          coverage: none

      - name: Install dependencies
        run: composer update --prefer-dist --no-interaction --no-progress

      - name: Run tests
        run: vendor/bin/pest --ci

  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v6
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.4', coverage: none }
      - run: composer update --prefer-dist --no-interaction --no-progress
      - run: vendor/bin/pint --test
```

- [ ] **Step 2: Commit**

```bash
git add .github/workflows/tests.yml
git commit -m "ci: add tests + lint workflow"
```

---

## Config evolution

### Task 3: Add `features`, `layout`, `tables` to config

**Files:**
- Modify: `config/filament-blog.php` (full rewrite — short file)

- [ ] **Step 1: Replace the config file**

Replace `config/filament-blog.php` contents with:

```php
<?php

declare(strict_types=1);

return [
    'prefix' => 'blog',

    'layout' => 'layouts.app',

    'author_model' => \App\Models\User::class,

    'per_page' => 12,

    'features' => [
        'public_routes' => false,
        'feed'          => false,
        'sitemap'       => false,
    ],

    'feed' => [
        'title' => null,
        'description' => null,
        'author_email' => null,
    ],

    'publisher' => [
        'name' => null,
        'url' => null,
        'logo' => null,
    ],

    'tables' => [
        'posts' => 'blog_posts',
        'categories' => 'blog_categories',
    ],
];
```

Note: defaults are `false` so existing installs keep their headless behavior unchanged.

- [ ] **Step 2: Commit**

```bash
git add config/filament-blog.php
git commit -m "feat(config): add features array, layout, tables sections"
```

---

## Public routes (TDD)

### Task 4: Failing test for public index route

**Files:**
- Create: `tests/Feature/PublicRoutesTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

declare(strict_types=1);

use ManukMinasyan\FilamentBlog\Models\Category;
use ManukMinasyan\FilamentBlog\Models\Post;

beforeEach(function () {
    config()->set('filament-blog.features.public_routes', true);
    config()->set('filament-blog.layout', 'tests::layouts.empty');
});

test('public index route returns published posts when feature enabled', function () {
    $post = Post::factory()->published()->create(['title' => 'Hello world']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSeeText('Hello world');
});

test('public index route is not registered when feature disabled', function () {
    config()->set('filament-blog.features.public_routes', false);

    expect(\Illuminate\Support\Facades\Route::has('blog.index'))->toBeFalse();
});
```

- [ ] **Step 2: Run the test to verify it fails**

```bash
cd /tmp/filament-blog
vendor/bin/pest tests/Feature/PublicRoutesTest.php
```

Expected: FAIL — `route('blog.index')` not defined; route helper throws.

---

### Task 5: Test fixtures (factory + layout view)

**Files:**
- Create: `database/factories/PostFactory.php`
- Create: `database/factories/CategoryFactory.php`
- Create: `tests/Fixtures/views/layouts/empty.blade.php`

- [ ] **Step 1: Create CategoryFactory**

```php
<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ManukMinasyan\FilamentBlog\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
        ];
    }
}
```

- [ ] **Step 2: Create PostFactory**

```php
<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence(),
            'featured_image' => null,
            'category_id' => null,
            'author_id' => null,
            'status' => PostStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Published,
            'published_at' => now()->subMinute(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDay(),
        ]);
    }
}
```

- [ ] **Step 3: Wire factory autoload**

In `composer.json`, ensure the factories namespace is autoloaded — add this `autoload-dev`:

```json
"autoload-dev": {
    "psr-4": {
        "ManukMinasyan\\FilamentBlog\\Tests\\": "tests/",
        "ManukMinasyan\\FilamentBlog\\Database\\Factories\\": "database/factories/"
    }
}
```

Then:

```bash
composer dump-autoload
```

- [ ] **Step 4: Make Post and Category use factories**

In `src/Models/Post.php`, ensure `use HasFactory;` is present and add the static `newFactory()` if HasFactory cannot resolve namespace:

```php
protected static function newFactory(): \ManukMinasyan\FilamentBlog\Database\Factories\PostFactory
{
    return \ManukMinasyan\FilamentBlog\Database\Factories\PostFactory::new();
}
```

Same for `Category.php`:

```php
protected static function newFactory(): \ManukMinasyan\FilamentBlog\Database\Factories\CategoryFactory
{
    return \ManukMinasyan\FilamentBlog\Database\Factories\CategoryFactory::new();
}
```

- [ ] **Step 5: Create the empty layout fixture**

`tests/Fixtures/views/layouts/empty.blade.php`:

```blade
<!doctype html>
<html><head><title>{{ $title ?? 'Blog' }}</title></head>
<body>@yield('content')</body>
</html>
```

- [ ] **Step 6: Register the fixture views path in TestCase**

In `tests/TestCase.php` `defineEnvironment()`, replace the `view.paths` line with a registered namespace:

```php
$app['view']->addNamespace('tests', __DIR__.'/Fixtures/views');
```

(Remove the previous `$app['config']->set('view.paths', ...)` line.)

- [ ] **Step 7: Commit**

```bash
git add database/factories/ tests/Fixtures/views/ src/Models/ tests/TestCase.php composer.json composer.lock
git commit -m "test: add Post + Category factories and fixture layout"
```

---

### Task 6: BlogController + index page

**Files:**
- Create: `src/Http/Controllers/BlogController.php`
- Create: `routes/web.php`
- Create: `resources/views/pages/index.blade.php`

- [ ] **Step 1: Create the controller (index only first)**

```php
<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ManukMinasyan\FilamentBlog\Models\Post;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) config('filament-blog.per_page', 12);

        $posts = Post::query()
            ->with(['category', 'author', 'seo'])
            ->published()
            ->latest('published_at')
            ->paginate($perPage);

        return view('blog::pages.index', [
            'posts' => $posts,
        ]);
    }
}
```

- [ ] **Step 2: Create the routes file**

`routes/web.php`:

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use ManukMinasyan\FilamentBlog\Http\Controllers\BlogController;

$prefix = config('filament-blog.prefix', 'blog');

Route::prefix($prefix)->middleware('web')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
});
```

- [ ] **Step 3: Wire route loading in service provider**

In `src/FilamentBlogServiceProvider.php`, replace `packageBooted()` with:

```php
public function packageBooted(): void
{
    Blade::componentNamespace('ManukMinasyan\\FilamentBlog\\Components', 'blog');

    if (config('filament-blog.features.public_routes')) {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
```

- [ ] **Step 4: Create the index view**

`resources/views/pages/index.blade.php`:

```blade
@extends(config('filament-blog.layout', 'layouts.app'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ config('filament-blog.feed.title') ?? 'Blog' }}</h1>

    <div class="space-y-8">
        @forelse ($posts as $post)
            <x-blog::post-card :post="$post" />
        @empty
            <p class="text-gray-500">No posts yet.</p>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $posts->links() }}
    </div>
</div>
@endsection
```

- [ ] **Step 5: Run the test**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php
```

Expected: PASS for "public index route returns published posts when feature enabled" and "public index route is not registered when feature disabled".

- [ ] **Step 6: Commit**

```bash
git add src/Http/Controllers/BlogController.php routes/web.php src/FilamentBlogServiceProvider.php resources/views/pages/index.blade.php
git commit -m "feat: add public blog routes and index page (config-gated)"
```

---

### Task 7: Show page (single post)

**Files:**
- Modify: `tests/Feature/PublicRoutesTest.php`
- Modify: `src/Http/Controllers/BlogController.php`
- Modify: `routes/web.php`
- Create: `resources/views/pages/show.blade.php`
- Create: `resources/views/pages/_post-content.blade.php`

- [ ] **Step 1: Add failing tests**

Append to `tests/Feature/PublicRoutesTest.php`:

```php
test('public show route returns the post by slug', function () {
    $post = Post::factory()->published()->create([
        'title' => 'My Post',
        'slug' => 'my-post',
        'content' => 'Hello body content',
    ]);

    $this->get(route('blog.show', 'my-post'))
        ->assertOk()
        ->assertSeeText('My Post')
        ->assertSeeText('Hello body content');
});

test('public show 404s on draft post', function () {
    Post::factory()->create(['slug' => 'unpublished']);

    $this->get(route('blog.show', 'unpublished'))->assertNotFound();
});

test('public show 404s on scheduled (future) post', function () {
    Post::factory()->scheduled()->create(['slug' => 'tomorrow']);

    $this->get(route('blog.show', 'tomorrow'))->assertNotFound();
});
```

- [ ] **Step 2: Run — expect fail**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php --filter="public show"
```

Expected: 3 failures (`route('blog.show')` not defined).

- [ ] **Step 3: Add `show` controller action**

In `src/Http/Controllers/BlogController.php`, add:

```php
public function show(string $slug): View
{
    $post = Post::query()
        ->with(['category', 'author', 'seo'])
        ->where('slug', $slug)
        ->published()
        ->firstOrFail();

    $related = $post->relatedPosts(limit: 3)->get();

    return view('blog::pages.show', [
        'post' => $post,
        'relatedPosts' => $related,
    ]);
}
```

(`relatedPosts()` will be added on the Post model in Task 11; this controller method is fine to ship now.)

Add `use Illuminate\Http\Response;` if needed (currently not).

- [ ] **Step 4: Add the route**

In `routes/web.php`, inside the existing prefix group, add:

```php
Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
```

- [ ] **Step 5: Create the show view**

`resources/views/pages/show.blade.php`:

```blade
@extends(config('filament-blog.layout', 'layouts.app'))

@section('content')
<article class="max-w-2xl mx-auto px-4 py-12 prose dark:prose-invert">
    <x-blog::post-header :post="$post" />

    @include('blog::pages._post-content', ['post' => $post])

    <x-blog::related-posts :post="$post" :relatedPosts="$relatedPosts" />
</article>
@endsection
```

- [ ] **Step 6: Create the shared content partial**

`resources/views/pages/_post-content.blade.php`:

```blade
<div class="post-body">
    <x-blog::post-body :post="$post" />
</div>
```

- [ ] **Step 7: Run tests**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php
```

Expected: all 5 PASS.

- [ ] **Step 8: Commit**

```bash
git add tests/Feature/PublicRoutesTest.php src/Http/Controllers/BlogController.php routes/web.php resources/views/pages/
git commit -m "feat: add public show route and view"
```

---

### Task 8: Category archive route

**Files:**
- Modify: `tests/Feature/PublicRoutesTest.php`
- Modify: `src/Http/Controllers/BlogController.php`
- Modify: `routes/web.php`
- Create: `resources/views/pages/category.blade.php`

- [ ] **Step 1: Failing test**

Append to `tests/Feature/PublicRoutesTest.php`:

```php
test('public category route lists posts in that category', function () {
    $cat = Category::factory()->create(['slug' => 'news', 'name' => 'News']);
    $included = Post::factory()->published()->create([
        'title' => 'In category',
        'category_id' => $cat->id,
    ]);
    $excluded = Post::factory()->published()->create(['title' => 'Out of category']);

    $this->get(route('blog.category', 'news'))
        ->assertOk()
        ->assertSeeText('In category')
        ->assertDontSeeText('Out of category');
});
```

- [ ] **Step 2: Verify failure**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php --filter="category route"
```

Expected: FAIL.

- [ ] **Step 3: Add controller action**

In `BlogController`, add:

```php
public function category(string $slug): View
{
    $category = \ManukMinasyan\FilamentBlog\Models\Category::where('slug', $slug)->firstOrFail();
    $perPage = (int) config('filament-blog.per_page', 12);

    $posts = Post::query()
        ->with(['category', 'author', 'seo'])
        ->where('category_id', $category->id)
        ->published()
        ->latest('published_at')
        ->paginate($perPage);

    return view('blog::pages.category', [
        'category' => $category,
        'posts' => $posts,
    ]);
}
```

- [ ] **Step 4: Add route (BEFORE the `{slug}` show route, because `/category/{slug}` is more specific)**

In `routes/web.php`, change the order so it reads:

```php
Route::prefix($prefix)->middleware('web')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});
```

- [ ] **Step 5: Create the view**

`resources/views/pages/category.blade.php`:

```blade
@extends(config('filament-blog.layout', 'layouts.app'))

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-2">{{ $category->name }}</h1>
    <p class="text-gray-500 mb-8">Posts filed under <x-blog::category-badge :category="$category" /></p>

    <div class="space-y-8">
        @forelse ($posts as $post)
            <x-blog::post-card :post="$post" />
        @empty
            <p class="text-gray-500">No posts in this category yet.</p>
        @endforelse
    </div>

    <div class="mt-12">{{ $posts->links() }}</div>
</div>
@endsection
```

- [ ] **Step 6: Run all public route tests**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php
```

Expected: all PASS.

- [ ] **Step 7: Commit**

```bash
git add tests/Feature/PublicRoutesTest.php src/Http/Controllers/BlogController.php routes/web.php resources/views/pages/category.blade.php
git commit -m "feat: add public category archive route"
```

---

### Task 9: Signed preview route

**Files:**
- Modify: `tests/Feature/PublicRoutesTest.php`
- Modify: `src/Http/Controllers/BlogController.php`
- Modify: `routes/web.php`
- Create: `resources/views/pages/preview.blade.php`

- [ ] **Step 1: Failing tests**

Append:

```php
test('preview route renders draft when signature valid', function () {
    $post = Post::factory()->create([
        'title' => 'Draft preview',
        'slug' => 'draft-preview',
    ]);

    $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'blog.preview', now()->addHour(), ['post' => $post->id]
    );

    $this->get($url)
        ->assertOk()
        ->assertSeeText('Draft preview')
        ->assertSee('noindex', false);
});

test('preview route 403s without signature', function () {
    $post = Post::factory()->create();

    $this->get(route('blog.preview', $post))->assertForbidden();
});
```

- [ ] **Step 2: Add controller action**

```php
public function preview(\ManukMinasyan\FilamentBlog\Models\Post $post): View
{
    return view('blog::pages.preview', [
        'post' => $post->loadMissing(['category', 'author', 'seo']),
    ]);
}
```

- [ ] **Step 3: Add route with `signed` middleware**

```php
Route::get('/preview/{post}', [BlogController::class, 'preview'])
    ->middleware('signed')
    ->name('blog.preview');
```

(Add it before the `{slug}` route.)

- [ ] **Step 4: Create the preview view**

`resources/views/pages/preview.blade.php`:

```blade
@extends(config('filament-blog.layout', 'layouts.app'))

@section('content')
<article class="max-w-2xl mx-auto px-4 py-12 prose dark:prose-invert">
    <x-blog::preview-banner />
    <x-blog::post-header :post="$post" />
    @include('blog::pages._post-content', ['post' => $post])
</article>
@endsection
```

The existing `<x-blog::preview-banner>` component should already push a `noindex,nofollow` meta tag — confirm with:

```bash
grep -n "noindex" /tmp/filament-blog/src/Components/PreviewBanner.php /tmp/filament-blog/resources/views/components/preview-banner.blade.php 2>&1
```

If no `noindex` is emitted, add a simple `<meta name="robots" content="noindex, nofollow">` directly in the preview view above the article.

- [ ] **Step 5: Run tests**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php
```

Expected: all PASS.

- [ ] **Step 6: Commit**

```bash
git add tests/Feature/PublicRoutesTest.php src/Http/Controllers/BlogController.php routes/web.php resources/views/pages/preview.blade.php
git commit -m "feat: add signed preview route for drafts"
```

---

### Task 10: RSS feed route

**Files:**
- Modify: `tests/Feature/PublicRoutesTest.php`
- Modify: `src/Http/Controllers/BlogController.php`
- Modify: `routes/web.php`
- Modify: `src/FilamentBlogServiceProvider.php`
- Create: `resources/views/pages/feed.blade.php`

- [ ] **Step 1: Failing tests**

Append:

```php
test('feed route returns RSS XML when feed feature enabled', function () {
    config()->set('filament-blog.features.feed', true);

    $post = Post::factory()->published()->create(['title' => 'Hello feed']);

    $response = $this->get(route('blog.feed'));
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
    expect($response->getContent())->toContain('<rss');
    expect($response->getContent())->toContain('Hello feed');
});

test('feed route is not registered when feed feature disabled', function () {
    config()->set('filament-blog.features.feed', false);

    expect(\Illuminate\Support\Facades\Route::has('blog.feed'))->toBeFalse();
});
```

- [ ] **Step 2: Verify failure**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php --filter="feed"
```

Expected: FAIL.

- [ ] **Step 3: Add controller action**

```php
public function feed(): \Illuminate\Http\Response
{
    $posts = Post::query()
        ->with(['author', 'seo'])
        ->published()
        ->latest('published_at')
        ->limit(20)
        ->get();

    return response()
        ->view('blog::pages.feed', ['posts' => $posts])
        ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
}
```

- [ ] **Step 4: Add the route, gated by separate feature flag**

In `routes/web.php`, conditionally register the feed:

```php
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use ManukMinasyan\FilamentBlog\Http\Controllers\BlogController;

$prefix = config('filament-blog.prefix', 'blog');

Route::prefix($prefix)->middleware('web')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/preview/{post}', [BlogController::class, 'preview'])
        ->middleware('signed')
        ->name('blog.preview');

    if (config('filament-blog.features.feed')) {
        Route::get('/feed', [BlogController::class, 'feed'])->name('blog.feed');
    }

    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});
```

- [ ] **Step 5: Create the feed view**

`resources/views/pages/feed.blade.php`:

```blade
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>{{ config('filament-blog.feed.title') ?? config('app.name') }}</title>
    <link>{{ url('/') }}</link>
    <description>{{ config('filament-blog.feed.description') ?? '' }}</description>
    <language>en</language>
    <atom:link href="{{ route('blog.feed') }}" rel="self" type="application/rss+xml" />
    @foreach ($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ \Illuminate\Support\Facades\Route::has('blog.show') ? route('blog.show', $post->slug) : url('/blog/'.$post->slug) }}</link>
            <guid isPermaLink="true">{{ \Illuminate\Support\Facades\Route::has('blog.show') ? route('blog.show', $post->slug) : url('/blog/'.$post->slug) }}</guid>
            <pubDate>{{ $post->published_at?->toRfc822String() }}</pubDate>
            <description><![CDATA[{{ $post->excerpt }}]]></description>
            @if (config('filament-blog.feed.author_email'))
                <author>{{ config('filament-blog.feed.author_email') }}</author>
            @endif
        </item>
    @endforeach
</channel>
</rss>
```

- [ ] **Step 6: Run tests**

```bash
vendor/bin/pest tests/Feature/PublicRoutesTest.php
```

Expected: all PASS.

- [ ] **Step 7: Commit**

```bash
git add tests/Feature/PublicRoutesTest.php src/Http/Controllers/BlogController.php routes/web.php resources/views/pages/feed.blade.php
git commit -m "feat: add RSS feed route gated by features.feed"
```

---

## Filament resource enhancements

### Task 11: Bulk publish/unpublish/schedule actions

**Files:**
- Create: `tests/Feature/PostResourceBulkActionsTest.php`
- Modify: `src/Filament/Resources/PostResource.php`

- [ ] **Step 1: Failing test**

```php
<?php

declare(strict_types=1);

use Filament\Actions\Testing\TestAction;
use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages\ListPosts;
use ManukMinasyan\FilamentBlog\Models\Post;
use function Pest\Livewire\livewire;

beforeEach(function () {
    // Authenticate a fake user — the resource doesn't enforce policies, but
    // Filament tables expect a user in the auth guard for some assertions.
});

test('bulk publish action sets status and published_at', function () {
    $posts = Post::factory()->count(3)->create();

    livewire(ListPosts::class)
        ->callAction(TestAction::make('publish')->table(records: $posts))
        ->assertNotified();

    foreach ($posts->fresh() as $post) {
        expect($post->status)->toBe(PostStatus::Published);
        expect($post->published_at)->not->toBeNull();
    }
});

test('bulk unpublish action reverts status to draft and clears published_at', function () {
    $posts = Post::factory()->published()->count(2)->create();

    livewire(ListPosts::class)
        ->callAction(TestAction::make('unpublish')->table(records: $posts))
        ->assertNotified();

    foreach ($posts->fresh() as $post) {
        expect($post->status)->toBe(PostStatus::Draft);
        expect($post->published_at)->toBeNull();
    }
});

test('bulk schedule action sets future published_at', function () {
    $posts = Post::factory()->count(2)->create();
    $when = now()->addWeek()->format('Y-m-d H:i:s');

    livewire(ListPosts::class)
        ->callAction(TestAction::make('schedule')->table(records: $posts), data: [
            'published_at' => $when,
        ])
        ->assertNotified();

    foreach ($posts->fresh() as $post) {
        expect($post->status)->toBe(PostStatus::Published);
        expect($post->published_at?->format('Y-m-d H:i:s'))->toBe($when);
    }
});
```

- [ ] **Step 2: Verify the tests fail**

```bash
vendor/bin/pest tests/Feature/PostResourceBulkActionsTest.php
```

Expected: 3 failures — actions don't exist.

- [ ] **Step 3: Add actions to PostResource**

In `src/Filament/Resources/PostResource.php`, locate the `BulkActionGroup::make([...])` block inside `table()` and add new entries (do not remove existing ones):

```php
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
// add the imports if not already present

// inside the BulkActionGroup::make([...])->...
BulkAction::make('publish')
    ->label('Publish')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->requiresConfirmation()
    ->action(function (\Illuminate\Support\Collection $records): void {
        $records->each(fn (Post $post) => $post->forceFill([
            'status' => PostStatus::Published,
            'published_at' => $post->published_at ?? now(),
        ])->save());

        Notification::make()->success()->title('Posts published')->send();
    })
    ->deselectRecordsAfterCompletion(),

BulkAction::make('unpublish')
    ->label('Unpublish')
    ->icon('heroicon-o-eye-slash')
    ->requiresConfirmation()
    ->action(function (\Illuminate\Support\Collection $records): void {
        $records->each(fn (Post $post) => $post->forceFill([
            'status' => PostStatus::Draft,
            'published_at' => null,
        ])->save());

        Notification::make()->success()->title('Posts unpublished')->send();
    })
    ->deselectRecordsAfterCompletion(),

BulkAction::make('schedule')
    ->label('Schedule')
    ->icon('heroicon-o-calendar')
    ->schema([
        DateTimePicker::make('published_at')
            ->label('Publish at')
            ->required()
            ->minDate(now()),
    ])
    ->action(function (array $data, \Illuminate\Support\Collection $records): void {
        $records->each(fn (Post $post) => $post->forceFill([
            'status' => PostStatus::Published,
            'published_at' => $data['published_at'],
        ])->save());

        Notification::make()->success()->title('Posts scheduled')->send();
    })
    ->deselectRecordsAfterCompletion(),
```

- [ ] **Step 4: Run the tests**

```bash
vendor/bin/pest tests/Feature/PostResourceBulkActionsTest.php
```

Expected: all 3 PASS.

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/PostResourceBulkActionsTest.php src/Filament/Resources/PostResource.php
git commit -m "feat(admin): add bulk publish/unpublish/schedule actions"
```

---

## MCP markdown sanitization

### Task 12: Sanitize markdown in CreatePostTool

**Files:**
- Create: `tests/Feature/Mcp/CreatePostToolTest.php`
- Modify: `src/Mcp/Tools/CreatePostTool.php`

- [ ] **Step 1: Failing test**

```php
<?php

declare(strict_types=1);

use Laravel\Mcp\Server\Request;
use ManukMinasyan\FilamentBlog\Mcp\Tools\CreatePostTool;
use ManukMinasyan\FilamentBlog\Models\Post;

test('CreatePostTool strips HTML and unsafe links from content', function () {
    $user = (object) ['is_admin' => true];

    // shape the request: this depends on Laravel/MCP's Request — adjust the fake to match
    $request = mock(Request::class);
    $request->shouldReceive('user')->andReturn($user);
    $request->shouldReceive('validated')->andReturn([
        'title' => 'Test',
        'slug' => 'test',
        'content' => '<script>alert(1)</script>**ok** [click](javascript:alert(1))',
        'excerpt' => null,
        'category_id' => null,
        'author_id' => null,
        'status' => 'draft',
        'published_at' => null,
    ]);
    $request->shouldReceive('user->tokenCan')->with('posts:create')->andReturnTrue();

    $tool = new CreatePostTool;
    $tool->handle($request);

    $post = Post::firstWhere('slug', 'test');
    expect($post)->not->toBeNull();
    expect($post->content)
        ->not->toContain('<script>')
        ->not->toContain('javascript:');
});
```

- [ ] **Step 2: Verify failure**

```bash
vendor/bin/pest tests/Feature/Mcp/CreatePostToolTest.php
```

Expected: FAIL — content contains `<script>` and/or `javascript:`.

- [ ] **Step 3: Patch CreatePostTool**

In `src/Mcp/Tools/CreatePostTool.php`, find the line where `$validated['content']` is read and inserted into the new Post. Replace the content assignment with a sanitized variant:

```php
use Illuminate\Support\Str;

// ... inside handle(), after $validated = $request->validated():
$validated['content'] = Str::markdown(
    $validated['content'] ?? '',
    [
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ],
);
```

- [ ] **Step 4: Run test**

```bash
vendor/bin/pest tests/Feature/Mcp/CreatePostToolTest.php
```

Expected: PASS.

- [ ] **Step 5: Apply same patch to UpdatePostTool**

Same one-line addition in `src/Mcp/Tools/UpdatePostTool.php`. Add a similar test in the same `tests/Feature/Mcp/` folder targeting `UpdatePostTool`.

- [ ] **Step 6: Commit**

```bash
git add tests/Feature/Mcp/ src/Mcp/Tools/CreatePostTool.php src/Mcp/Tools/UpdatePostTool.php
git commit -m "fix(mcp): sanitize markdown in Create/UpdatePostTool (strip HTML, block unsafe links)"
```

---

## Post model: reading time + related posts

### Task 13: Reading time accessor

**Files:**
- Create: `tests/Feature/PostModelTest.php`
- Modify: `src/Models/Post.php`

- [ ] **Step 1: Failing test**

```php
<?php

declare(strict_types=1);

use ManukMinasyan\FilamentBlog\Models\Post;

test('readingTime computes minutes from content word count', function () {
    $post = Post::factory()->create([
        'content' => str_repeat('word ', 600), // ~3 min at 200 wpm
    ]);

    expect($post->readingTime())->toBe(3);
});

test('readingTime is at least 1 minute for any content', function () {
    $post = Post::factory()->create(['content' => 'short']);

    expect($post->readingTime())->toBe(1);
});
```

- [ ] **Step 2: Verify failure**

```bash
vendor/bin/pest tests/Feature/PostModelTest.php --filter="readingTime"
```

Expected: FAIL — method does not exist.

- [ ] **Step 3: Add the method to `src/Models/Post.php`**

```php
public function readingTime(int $wordsPerMinute = 200): int
{
    $words = str_word_count((string) $this->content);

    return max(1, (int) ceil($words / $wordsPerMinute));
}
```

- [ ] **Step 4: Run tests**

```bash
vendor/bin/pest tests/Feature/PostModelTest.php
```

Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/PostModelTest.php src/Models/Post.php
git commit -m "feat(model): add readingTime accessor"
```

---

### Task 14: Related posts query + wire RelatedPosts component

**Files:**
- Modify: `tests/Feature/PostModelTest.php`
- Modify: `src/Models/Post.php`
- Modify: `src/Components/RelatedPosts.php`
- Modify: `resources/views/components/related-posts.blade.php`

- [ ] **Step 1: Failing test**

Append to `tests/Feature/PostModelTest.php`:

```php
test('relatedPosts returns same-category published posts excluding self', function () {
    $cat = \ManukMinasyan\FilamentBlog\Models\Category::factory()->create();
    $self = Post::factory()->published()->create(['category_id' => $cat->id]);
    $a = Post::factory()->published()->create(['category_id' => $cat->id]);
    $b = Post::factory()->published()->create(['category_id' => $cat->id]);
    $other = Post::factory()->published()->create(['category_id' => null]);

    $related = $self->relatedPosts(limit: 5)->get();

    expect($related->pluck('id')->all())
        ->toContain($a->id)
        ->toContain($b->id)
        ->not->toContain($self->id)
        ->not->toContain($other->id);
});

test('relatedPosts returns empty when post has no category', function () {
    $self = Post::factory()->published()->create(['category_id' => null]);
    Post::factory()->published()->count(3)->create();

    expect($self->relatedPosts()->get())->toBeEmpty();
});
```

- [ ] **Step 2: Verify failure**

```bash
vendor/bin/pest tests/Feature/PostModelTest.php --filter="relatedPosts"
```

Expected: FAIL.

- [ ] **Step 3: Implement on Post model**

Add to `src/Models/Post.php`:

```php
public function relatedPosts(int $limit = 3): \Illuminate\Database\Eloquent\Builder
{
    return static::query()
        ->published()
        ->where('id', '!=', $this->getKey())
        ->when(
            $this->category_id,
            fn ($q) => $q->where('category_id', $this->category_id),
            fn ($q) => $q->whereRaw('1 = 0'), // no category → no related
        )
        ->latest('published_at')
        ->limit($limit);
}
```

- [ ] **Step 4: Wire the component**

Modify `src/Components/RelatedPosts.php` so it accepts a `$relatedPosts` collection (passed in by the controller in Task 7) — or computes it from `$post` if not provided. Suggested:

```php
public function __construct(
    public Post $post,
    public ?\Illuminate\Support\Collection $relatedPosts = null,
) {
    $this->relatedPosts ??= $post->relatedPosts()->get();
}
```

Update the matching `resources/views/components/related-posts.blade.php` to iterate `$relatedPosts`.

- [ ] **Step 5: Run all model tests**

```bash
vendor/bin/pest tests/Feature/PostModelTest.php
```

Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add tests/Feature/PostModelTest.php src/Models/Post.php src/Components/RelatedPosts.php resources/views/components/related-posts.blade.php
git commit -m "feat(model): add relatedPosts() query + wire RelatedPosts component"
```

---

## Documentation

### Task 15: README + frontend setup docs

**Files:**
- Modify: `README.md`
- Modify: `docs/content/1.getting-started/2.frontend-setup.md`

- [ ] **Step 1: Add a "Public routes" section to README**

In the README's existing structure, add a section after the Quickstart:

```markdown
## Public-routes mode (opt-in)

By default this package is fully headless: no routes, no controllers, no
forced views. Your app owns all rendering.

To get a working blog at `/blog` without writing any controllers, flip the
feature flag:

```php
// config/filament-blog.php
'features' => [
    'public_routes' => true,   // /blog, /blog/{slug}, /blog/category/{slug}, /blog/preview/{post}
    'feed'          => true,   // adds /blog/feed (RSS 2.0)
],

'layout' => 'layouts.app',     // your host layout to extend
```

Routes register at the service-provider level — no Filament panel boot is
required, so the public site keeps working for guests who never touch the
admin.

Publish the views if you want to customize them:

```bash
php artisan vendor:publish --tag=filament-blog-views
```
```

- [ ] **Step 2: Update frontend-setup docs**

Replace or augment `docs/content/1.getting-started/2.frontend-setup.md` with a "Mode A: opt-in routes" / "Mode B: write your own controllers" split. Use the same example from the README but with prose framing. The existing "write your own routes" content stays as Mode B.

- [ ] **Step 3: Commit**

```bash
git add README.md docs/content/1.getting-started/2.frontend-setup.md
git commit -m "docs: document public-routes mode and feature flags"
```

---

## Final verification

### Task 16: Full test + lint run

**Files:** none

- [ ] **Step 1: Run the whole test suite**

```bash
cd /tmp/filament-blog
vendor/bin/pest
```

Expected: all tests PASS, no skipped/risky.

- [ ] **Step 2: Run Pint**

```bash
vendor/bin/pint --test
```

Expected: no style violations. If any, run `vendor/bin/pint` to fix and commit:

```bash
vendor/bin/pint
git diff --quiet || git commit -am "style: pint"
```

- [ ] **Step 3: Confirm clean working tree**

```bash
git status --short
```

Expected: clean.

---

### Task 17: Push branch and open PR

**Files:** none (git/gh)

- [ ] **Step 1: Push the branch**

```bash
git push -u origin feat/public-routes-phase-1
```

- [ ] **Step 2: Open a PR with a structured body**

```bash
gh pr create --base main --title "feat: opt-in public routes (drop-in for Tapix/FilaForms blog) [Phase 1]" --body "$(cat <<'EOF'
## Summary

Phase 1 of the strategy laid out in `.context/blog-package-comparison.md` (in the demo workspace). Makes `manukminasyan/filament-blog` a drop-in replacement for the Tapix and FilaForms internal blog packages while keeping the headless-by-default behavior intact.

## What's new

- **Public routes mode** (opt-in via `config('filament-blog.features.public_routes') = true`)
  - `BlogController` + `routes/web.php` registering `blog.index`, `blog.show`, `blog.category`, `blog.preview` (signed)
  - Service provider loads routes at boot — no Filament panel needed
- **RSS feed** (opt-in via `features.feed`) — `/blog/feed` returns RSS 2.0
- **Layout config** (`'layout' => 'layouts.app'`) for the page views to extend
- **Bulk publish/unpublish/schedule actions** in `PostResource`
- **MCP markdown sanitization** — `CreatePostTool` and `UpdatePostTool` now strip HTML and disallow unsafe links
- **`Post::readingTime()`** accessor and **`Post::relatedPosts()`** query, with `<x-blog::related-posts>` wired to use them
- **Test infrastructure** — Pest 3 + Orchestra Testbench, Pint config, GitHub Actions workflow
- **README / docs** updated with the new mode

## Backwards compatibility

All new feature flags default to `false`. Existing installs continue to behave exactly as before — fully headless, no routes registered, no view changes. Upgrading is a no-op until the host opts in.

## Test plan

- [x] `vendor/bin/pest` — all green
- [x] `vendor/bin/pint --test` — clean
- [x] Tested with feature flags off → no routes registered (Route::has returns false)
- [x] Tested with feature flags on → all 5 public routes work, RSS feed serves XML, signed-only preview enforced
EOF
)"
```

Expected: PR URL printed.

- [ ] **Step 3: Watch CI**

```bash
gh pr checks --watch
```

Expected: all checks pass.

---

## Self-review notes

- **Spec coverage:** every "Must-have to reach feature parity (Core)" bullet from the strategy doc Section 4 is mapped to a task (Tasks 6–10 cover routes/controllers/views, Task 11 covers bulk actions, Task 12 covers MCP sanitization, Tasks 13–14 cover reading time + related posts, Task 15 covers docs, Task 1 covers the layout-config option implicitly via Task 3's config update).
- **Plus features (tags, MediaLibrary, tenancy, comments, broken-link tracking)** are explicitly **out of scope** for this plan — they belong to Phase 3 separate plans, as documented in the strategy file.
- **Sitemap auto-hook**: `features.sitemap` flag is in the config but not wired in this plan. The existing `BlogSitemapGenerator` helper is sufficient for hosts to call manually. A future task can add a Spatie sitemap auto-discovery if there's demand. Documented in the README that the helper exists.
- **MCP test fakes:** the test in Task 12 mocks `Laravel\Mcp\Server\Request`. If `laravel/mcp` is not in `require-dev` yet, the test will fail to autoload — add `composer require --dev laravel/mcp` in Task 1 before the others. (Confirm during execution; if the package is already a hard dep, no action needed.)
