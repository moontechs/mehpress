<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Constants;
use App\Models\Blog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->limit(100)
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('language')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tags')
                    ->badge()
                    ->searchable(),
                IconColumn::make('published')
                    ->sortable()
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('published'),
                Filter::make('draft')
                    ->label('Draft')
                    ->query(fn ($query) => $query->where('published', false)),
                Filter::make('type')
                    ->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(array_combine(Constants::POST_TYPES, Constants::POST_TYPES)),
                    ]),
                Filter::make('blog_id')
                    ->schema([
                        Select::make('blog_id')
                            ->label('Blog')
                            ->relationship('blog', 'name'),
                    ])
                    ->query(function ($query, array $data) {
                        if (! empty($data['blog_id'])) {
                            $query->where('blog_id', $data['blog_id']);
                        }
                    }),
                Filter::make('language')
                    ->schema([
                        Select::make('language')
                            ->label('Language')
                            ->options(function (Get $get) {
                                $allLanguages = Blog::query()
                                    ->pluck('languages')
                                    ->filter()
                                    ->flatMap(fn ($languages) => $languages ?? [])
                                    ->unique()
                                    ->values()
                                    ->all();

                                return array_combine($allLanguages, $allLanguages);
                            }),
                    ])
                    ->query(function ($query, array $data) {
                        if (! empty($data['language'])) {
                            $query->where('language', $data['language']);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
