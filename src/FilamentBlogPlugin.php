<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentBlogPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'filament-blog';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__.'/Filament/Resources',
            for: 'ManukMinasyan\\FilamentBlog\\Filament\\Resources',
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
