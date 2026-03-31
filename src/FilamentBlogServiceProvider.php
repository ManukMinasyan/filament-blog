<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentBlogServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-blog';

    public static string $viewNamespace = 'blog';

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
        Blade::componentNamespace('ManukMinasyan\\FilamentBlog\\Components', 'blog');
    }
}
