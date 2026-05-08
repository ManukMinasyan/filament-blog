<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages;

use Filament\Resources\Pages\EditRecord;
use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
