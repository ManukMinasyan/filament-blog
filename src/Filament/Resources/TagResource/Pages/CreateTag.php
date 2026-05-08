<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Filament\Resources\TagResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use ManukMinasyan\FilamentBlog\Filament\Resources\TagResource;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
