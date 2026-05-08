<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use ManukMinasyan\FilamentBlog\Filament\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
