<?php

namespace App\Filament\Resources\Shorts\Schemas;

use App\Helpers\SlugHelper;
use App\Helpers\TagHelperInterface;
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
        $tagHelper = app()->make(TagHelperInterface::class);

        return $schema
            ->components([
                MarkdownEditor::make('text')
                    ->columnSpanFull()
                    ->live()
                    ->partiallyRenderComponentsAfterStateUpdated(['slug', 'tags'])
                    ->afterStateUpdated(function (Set $set, ?string $state) use ($tagHelper) {
                        $set('slug', SlugHelper::getForShort($state));
                        $set('tags', Arr::flatten($tagHelper->parseFromText($state)));
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
