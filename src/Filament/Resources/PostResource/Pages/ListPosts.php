<?php

namespace ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages;

use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource;
use ManukMinasyan\FilamentBlog\Models\Post;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }

    public function getTabs(): array
    {
        return [
            'drafts' => Tab::make('Drafts')
                ->badge(Post::query()->draft()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->draft()),

            'scheduled' => Tab::make('Scheduled')
                ->badge(Post::query()->scheduled()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->scheduled()),

            'published' => Tab::make('Published')
                ->badge(Post::query()->published()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->published()),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'drafts';
    }
}
