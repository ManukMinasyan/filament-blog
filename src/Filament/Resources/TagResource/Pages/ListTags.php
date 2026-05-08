<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Filament\Resources\TagResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use ManukMinasyan\FilamentBlog\Filament\Resources\TagResource;

class ListTags extends ListRecords
{
    protected static string $resource = TagResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }
}
