<?php

declare(strict_types=1);

namespace Relaticle\Ink\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Relaticle\Ink\Models\Category;

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
