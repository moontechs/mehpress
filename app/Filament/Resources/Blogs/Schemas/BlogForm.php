<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Forms\Components\TextInput;
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
            ]);
    }
}
