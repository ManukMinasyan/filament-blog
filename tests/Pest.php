<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use ManukMinasyan\FilamentBlog\Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');
