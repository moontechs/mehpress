<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Business\SeoInterface;
use App\Constants;
use App\Jobs\GenerateSeoTagsUsingAiJob;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ShortPostForm
{
    public static function configure(Schema $schema): Schema
    {
        $seo = app()->make(SeoInterface::class);

        return $schema
            ->components([
                TextInput::make('title')
                    ->default(fn (Get $get) => Str::random(20))
                    ->hidden(),
                TextInput::make('slug')
                    ->default(fn (Get $get) => Str::random(20))
                    ->hidden(),
                Select::make('blog_id')
                    ->relationship('blog', 'name')
                    ->selectablePlaceholder(false)
                    ->afterStateHydrated(function (Set $set, ?string $state) {
                        $blogId = request()->query('blog_id');
                        if ($blogId && ! $state) {
                            $set('blog_id', $blogId);
                        }
                    })->hidden(),
                Select::make('type')
                    ->options(array_combine(Constants::POST_TYPES, Constants::POST_TYPES))
                    ->default(Constants::SHORT_POST_TYPE)
                    ->hidden(),
                Toggle::make('published')
                    ->default(true)
                    ->hidden(),

                MarkdownEditor::make('text')
                    ->columnSpanFull()
                    ->required(),
                Grid::make()->columnSpanFull()->schema([
                    KeyValue::make('seo_tags')
                        ->label('SEO')
                        ->columnSpanFull()
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->default($seo->getDefaultStructure()),
                    Actions::make([
                        Action::make('generate_seo_tags')
                            ->label('Request SEO Tags AI generation')
                            ->tooltip('All tags will be generated automatically and rewritten')
                            ->requiresConfirmation()
                            ->action(fn (Set $set, Get $get) => GenerateSeoTagsUsingAiJob::dispatch($get('type'), $get('id')))
                            ->visible(fn (Get $get) => $get('id') !== null)
                            ->outlined(),
                    ]),
                ]),
            ]);
    }
}
