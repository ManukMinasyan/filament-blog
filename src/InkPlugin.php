<?php

declare(strict_types=1);

namespace Relaticle\Ink;

use Filament\Contracts\Plugin;
use Filament\Panel;

class InkPlugin implements Plugin
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
        return 'ink';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__.'/Filament/Resources',
            for: 'Relaticle\\Ink\\Filament\\Resources',
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
