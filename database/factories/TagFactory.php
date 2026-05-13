<?php

declare(strict_types=1);

namespace Relaticle\Ink\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Relaticle\Ink\Models\Tag;

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
