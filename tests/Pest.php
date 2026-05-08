<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ManukMinasyan\FilamentBlog\Tests\TestCase;
use Orchestra\Testbench\Factories\UserFactory;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/** Seed a default test user the package expects via author_id. */
function testUser(): User
{
    /** @var User $user */
    $user = (new UserFactory)->create([
        'name' => 'Test Author',
        'email' => 'author@example.test',
    ]);

    return $user;
}
