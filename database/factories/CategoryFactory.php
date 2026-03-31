<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Database\Factories;

use ManukMinasyan\FilamentBlog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
