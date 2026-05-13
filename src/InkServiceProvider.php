<?php

declare(strict_types=1);

namespace Relaticle\Ink;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InkServiceProvider extends PackageServiceProvider
{
    public static string $name = 'ink';

    public static string $viewNamespace = 'ink';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->discoversMigrations()
            ->runsMigrations()
            ->hasViews(static::$viewNamespace);
    }

    public function packageBooted(): void
    {
        Blade::componentNamespace('Relaticle\\Ink\\Components', 'ink');

        if (config('ink.features.public_routes')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
    }
}
