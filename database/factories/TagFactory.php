<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ManukMinasyan\FilamentBlog\Models\Tag;

/** @extends Factory<Tag> */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
