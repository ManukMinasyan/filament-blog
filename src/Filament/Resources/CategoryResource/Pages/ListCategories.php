<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use ManukMinasyan\FilamentBlog\Filament\Resources\CategoryResource;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }
}
