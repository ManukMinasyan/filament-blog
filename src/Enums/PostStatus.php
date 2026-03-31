<?php

declare(strict_types=1);

namespace ManukMinasyan\FilamentBlog\Enums;

use Filament\Support\Contracts\HasLabel;

enum PostStatus: string implements HasLabel
{
    case Draft = 'draft';
    case Published = 'published';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
        };
    }
}
