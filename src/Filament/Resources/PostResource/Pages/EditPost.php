<?php

namespace ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages;

use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource;
use Filament\Resources\Pages\EditRecord;

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
