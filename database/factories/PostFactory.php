<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Models\Post;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence(),
            'featured_image' => null,
            'category_id' => null,
            'author_id' => fn () => $this->resolveAuthorId(),
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

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDay(),
        ]);
    }

    /**
     * Resolve a user id, creating one if needed. Uses the configured author
     * model when available, falls back to Testbench's User during tests.
     */
    protected function resolveAuthorId(): int|string
    {
        $authorModel = (string) config('filament-blog.author_model', User::class);

        if (class_exists($authorModel) && method_exists($authorModel, 'factory')) {
            return $authorModel::factory()->create()->getKey();
        }

        // Fallback: insert a row directly into users
        $id = \DB::table('users')->insertGetId([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }
}
