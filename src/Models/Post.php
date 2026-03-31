<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Models;

use ManukMinasyan\FilamentBlog\Database\Factories\PostFactory;
use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasFactory;
    use HasSEO;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if ($post->isDirty('content')) {
                Cache::forget("post-rendered:{$post->id}");
            }
        });
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<Model, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(config('filament-blog.author_model'), 'author_id');
    }

    /** @return array{label: string, color: string} */
    public function displayStatus(): array
    {
        if ($this->status === PostStatus::Draft) {
            return ['label' => 'Draft', 'color' => 'gray'];
        }

        if ($this->published_at?->isFuture()) {
            return ['label' => 'Scheduled', 'color' => 'warning'];
        }

        return ['label' => 'Published', 'color' => 'success'];
    }

    #[Scope]
    protected function draft(Builder $query): void
    {
        $query->where('status', PostStatus::Draft);
    }

    #[Scope]
    protected function scheduled(Builder $query): void
    {
        $query
            ->where('status', PostStatus::Published)
            ->where('published_at', '>', now());
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query
            ->where('status', PostStatus::Published)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function toHtml(): string
    {
        return Cache::rememberForever(
            "post-rendered:{$this->id}",
            fn (): string => app(MarkdownRenderer::class)->toHtml($this->content),
        );
    }

    public function getUrl(): string
    {
        if ($this->status === PostStatus::Published && ! $this->published_at?->isFuture()) {
            return Route::has('blog.show')
                ? route('blog.show', $this->slug)
                : '#';
        }

        return Route::has('blog.preview')
            ? URL::temporarySignedRoute('blog.preview', now()->addHour(), ['post' => $this])
            : '#';
    }

    public function renderedContent(): string
    {
        return $this->processInternalLinks($this->toHtml());
    }

    private function processInternalLinks(string $html): string
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST);

        if (! $host) {
            return $html;
        }

        return preg_replace_callback(
            '/<a\s([^>]*href="https?:\/\/'.preg_quote($host, '/').'(?=["\/?#])[^"]*"[^>]*)>/i',
            function (array $matches): string {
                $attrs = $matches[1];

                $attrs = preg_replace('/\s*target="_blank"/', '', $attrs);
                $attrs = preg_replace('/\s+nofollow\b|\bnofollow\s+|\bnofollow\b/', '', $attrs);
                $attrs = preg_replace('/rel="\s*"/', '', $attrs);
                $attrs = preg_replace('/\s{2,}/', ' ', $attrs);

                return '<a '.trim($attrs).'>';
            },
            $html,
        ) ?? $html;
    }

    public function getDynamicSEOData(): SEOData
    {
        $schema = SchemaCollection::initialize()
            ->addArticle(function (ArticleSchema $article): ArticleSchema {
                $article->type = 'BlogPosting';

                return $article->markup(function (Collection $markup): Collection {
                    $publisherConfig = config('filament-blog.publisher');

                    if (! $publisherConfig['name']) {
                        return $markup;
                    }

                    return $markup->put('publisher', [
                        '@type' => 'Organization',
                        'name' => $publisherConfig['name'],
                        'url' => $publisherConfig['url'],
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => asset($publisherConfig['logo']),
                        ],
                    ]);
                });
            })
            ->addBreadcrumbs(function (BreadcrumbListSchema $breadcrumbs): BreadcrumbListSchema {
                $crumbs = ['Home' => url('/')];

                if (Route::has('blog.index')) {
                    $crumbs['Blog'] = route('blog.index');
                }

                if ($this->category && Route::has('blog.category')) {
                    $crumbs[$this->category->name] = route('blog.category', $this->category->slug);
                }

                return $breadcrumbs->prependBreadcrumbs($crumbs);
            });

        return new SEOData(
            title: $this->title,
            description: $this->excerpt,
            author: $this->author?->name,
            image: $this->featured_image ? asset("storage/{$this->featured_image}") : null,
            published_time: $this->published_at,
            modified_time: $this->updated_at,
            articleBody: Str::limit(
                trim(preg_replace('/\s+/', ' ', strip_tags($this->content))),
                200,
            ),
            section: $this->category?->name,
            type: 'article',
            schema: $schema,
        );
    }
}
