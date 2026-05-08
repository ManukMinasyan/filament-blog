<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
