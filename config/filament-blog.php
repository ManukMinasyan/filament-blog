<?php

declare(strict_types=1);

return [
    'prefix' => 'blog',

    'layout' => 'layouts.app',

    'author_model' => \App\Models\User::class,

    'per_page' => 12,

    'features' => [
        'public_routes' => false,
        'feed'          => false,
        'sitemap'       => false,
    ],

    'feed' => [
        'title' => null,
        'description' => null,
        'author_email' => null,
    ],

    'publisher' => [
        'name' => null,
        'url' => null,
        'logo' => null,
    ],

    'tables' => [
        'posts' => 'blog_posts',
        'categories' => 'blog_categories',
    ],
];
