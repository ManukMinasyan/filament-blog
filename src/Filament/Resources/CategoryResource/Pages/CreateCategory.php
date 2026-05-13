<?php

declare(strict_types=1);

namespace Relaticle\Ink\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\Ink\Filament\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
