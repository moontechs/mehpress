<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Helpers\Slug\Slug;
use App\Helpers\Tag\Tag;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true)
                    ->live()
                    ->partiallyRenderComponentsAfterStateUpdated(['slug'])
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Slug::getForPost($state)))
                    ->required(),
                TextInput::make('slug')
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('description')
                    ->columnSpanFull(),
                MarkdownEditor::make('text')
                    ->required()
                    ->columnSpanFull(),
                TagsInput::make('tags')
                    ->suggestions(Tag::getSuggestions())
                    ->columnSpanFull(),
                Toggle::make('published')
                    ->default(false),
            ]);
    }
}
