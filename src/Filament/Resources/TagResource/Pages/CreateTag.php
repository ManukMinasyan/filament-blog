<?php

declare(strict_types=1);

namespace Relaticle\Ink\Filament\Resources\TagResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\Ink\Filament\Resources\TagResource;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
