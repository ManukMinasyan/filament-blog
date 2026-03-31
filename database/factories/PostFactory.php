<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Database\Factories;

use App\Models\User;
use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Models\Category;
use ManukMinasyan\FilamentBlog\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'excerpt' => fake()->sentence(),
            'category_id' => Category::factory(),
            'author_id' => User::factory(),
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(rand(1, 30)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }
}
