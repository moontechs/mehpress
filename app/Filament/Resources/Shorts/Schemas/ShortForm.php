<?php

namespace App\Filament\Resources\Shorts\Schemas;

use App\Helpers\Slug;
use App\Helpers\Tag;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

class ShortForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                MarkdownEditor::make('text')
                    ->columnSpanFull()
                    ->live()
                    ->partiallyRenderComponentsAfterStateUpdated(['slug', 'tags'])
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', Slug::getForShort($state));
                        $set('tags', Arr::flatten(Tag::parseFromText($state)));
                    })
                    ->required(),
                TextInput::make('slug')
                    ->hint('It will be created automatically from a text')
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TagsInput::make('tags')
                    ->hint('It will be added automatically from a text')
                    ->columnSpanFull(),
            ]);
    }
}
