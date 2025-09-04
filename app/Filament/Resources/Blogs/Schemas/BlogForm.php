<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Constants;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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

                Section::make('Languages')
                    ->columnSpanFull()
                    ->schema([
                        TagsInput::make('languages')
                            ->label('Available Languages')
                            ->placeholder('Add language locales (e.g., en_US, pt_BR, es_ES)')
                            ->suggestions(['en_US', 'en_GB', 'es_ES', 'es_MX', 'pt_BR', 'pt_PT', 'fr_FR', 'de_DE', 'it_IT', 'ja_JP', 'zh_CN', 'ru_RU', 'ko_KR'])
                            ->helperText('Enter locale codes (language_COUNTRY) that will be available for this blog')
                            ->rules(['required', 'array', 'min:1'])
                            ->nestedRecursiveRules(['regex:/^[a-z]{2}_[A-Z]{2}$/'])
                            ->required(),
                        Select::make('default_language')
                            ->label('Default Language')
                            ->placeholder('Select default language')
                            ->options(fn ($get) => array_combine($get('languages') ?? ['en_US'], $get('languages') ?? ['en_US']))
                            ->live()
                            ->rules(['required', 'regex:/^[a-z]{2}_[A-Z]{2}$/'])
                            ->required(),
                    ])->columns(2),

                Section::make('Navigation')
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

                Section::make('Footer')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('footer')->schema([
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
