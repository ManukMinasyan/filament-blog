<?php

declare(strict_types=1);

namespace Relaticle\Ink\Filament\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\Ink\Filament\Resources\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
