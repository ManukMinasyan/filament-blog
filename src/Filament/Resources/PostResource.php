<?php

namespace ManukMinasyan\FilamentBlog\Filament\Resources;

use ManukMinasyan\FilamentBlog\Enums\PostStatus;
use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages\CreatePost;
use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages\EditPost;
use ManukMinasyan\FilamentBlog\Filament\Resources\PostResource\Pages\ListPosts;
use ManukMinasyan\FilamentBlog\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use RalphJSmit\Filament\SEO\SEO;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                MarkdownEditor::make('content')
                                    ->required()
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'strike', 'link'],
                                        ['heading'],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['table', 'attachFiles'],
                                        ['undo', 'redo'],
                                    ])
                                    ->columnSpanFull(),

                                Textarea::make('excerpt')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Status')
                            ->afterHeader([
                                Action::make('view')
                                    ->icon('heroicon-o-eye')
                                    ->color('gray')
                                    ->url(fn (Post $record): string => $record->getUrl())
                                    ->openUrlInNewTab()
                                    ->visible(fn (?Post $record): bool => $record !== null),
                            ])
                            ->schema([
                                Toggle::make('status')
                                    ->label('Published')
                                    ->default(false)
                                    ->afterStateHydrated(fn ($component, $record) => $component->state($record?->status === PostStatus::Published))
                                    ->dehydrateStateUsing(fn (bool $state): string => $state ? PostStatus::Published->value : PostStatus::Draft->value),

                                DateTimePicker::make('published_at'),
                            ]),

                        Section::make('Details')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required(),
                                    ]),

                                Select::make('author_id')
                                    ->relationship('author', 'name')
                                    ->default(fn () => auth()->id())
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Section::make('Featured Image')
                            ->schema([
                                FileUpload::make('featured_image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('blog'),
                            ]),

                        Section::make('SEO')
                            ->schema([
                                SEO::make(['title', 'description']),
                            ])
                            ->collapsible()
                            ->persistCollapsed(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->state(fn (Post $record): string => $record->displayStatus()['label'])
                    ->color(fn (Post $record): string => $record->displayStatus()['color']),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                TrashedFilter::make(),
            ])
            ->recordUrl(fn (Post $record): string => static::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Post $record): string => $record->getUrl())
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
