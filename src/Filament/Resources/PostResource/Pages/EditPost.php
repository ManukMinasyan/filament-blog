<?php

declare(strict_types=1);

namespace Relaticle\Ink\Filament\Resources\PostResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Relaticle\Ink\Filament\Resources\PostResource;

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
