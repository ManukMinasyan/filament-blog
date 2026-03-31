<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Models;

use ManukMinasyan\FilamentBlog\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /** @return HasMany<Post, $this> */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
