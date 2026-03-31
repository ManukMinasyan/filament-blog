<?php

namespace ManukMinasyan\FilamentBlog\Filament\Resources\CategoryResource\Pages;

use ManukMinasyan\FilamentBlog\Filament\Resources\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }
}
