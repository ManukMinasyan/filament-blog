<?php

declare(strict_types=1);

return [
    'prefix' => 'blog',
    'author_model' => \App\Models\User::class,
    'per_page' => 12,
    'feed' => [
        'enabled' => true,
        'title' => null,
        'description' => null,
        'author_email' => null,
    ],
    'publisher' => [
        'name' => null,
        'url' => null,
        'logo' => null,
    ],
];
