<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Blog;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class BlogsWidget extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Blog::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('host')
                    ->searchable(),
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
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('create_post')
                    ->label('Create Post')
                    ->button()
                    ->color('gray')
                    ->url(fn (Blog $record): string => PostResource::getUrl('create', [
                        'blog_id' => $record->id,
                    ])),
                Action::make('create_short')
                    ->label('Create Short')
                    ->button()
                    ->color('gray')
                    ->url(fn (Blog $record): string => PostResource::getUrl('create', [
                        'blog_id' => $record->id,
                        'type' => 'short',
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
