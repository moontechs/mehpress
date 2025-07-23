<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Constants;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('description')
                    ->columnSpanFull(),
                TextInput::make('host')->unique(ignoreRecord: true)
                    ->columnSpanFull()
                    ->required(),
                Textarea::make('logo_svg')
                    ->columnSpanFull(),

                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('navigation')->schema([
                            TextInput::make('label')
                                ->required(),
                            Select::make('type')
                                ->selectablePlaceholder(false)
                                ->default(Constants::NAVIGATION_LINK_TYPE)
                                ->options(array_combine(Constants::NAVIGATION_BUTTON_TYPES, Constants::NAVIGATION_BUTTON_TYPES)),
                            TextInput::make('url')
                                ->required(),
                        ])->columns(3),
                    ]),
            ]);
    }
}
