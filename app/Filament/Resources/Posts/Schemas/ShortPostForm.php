<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Business\BlogService;
use App\Constants;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ShortPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('blog_id')
                    ->relationship('blog', 'name')
                    ->selectablePlaceholder(false)
                    ->default(fn () => request()->query('blog_id'))
                    ->required()
                    ->hidden(),
                Select::make('type')
                    ->options(array_combine(Constants::POST_TYPES, Constants::POST_TYPES))
                    ->default(Constants::SHORT_POST_TYPE)
                    ->afterStateHydrated(function (Set $set, ?string $state) {
                        $type = request()->query('type');
                        if ($type && ! $state && $type === Constants::SHORT_POST_TYPE) {
                            $set('type', Constants::SHORT_POST_TYPE);
                        }
                    })
                    ->hidden(),
                Toggle::make('published')
                    ->default(true)
                    ->hidden(),

                MarkdownEditor::make('text')
                    ->fileAttachmentsDisk('public')
                    ->getFileAttachmentUrlUsing(function (string $file, Get $get, BlogService $blogService) {
                        return $blogService->getFileUrlForBlog($get('blog_id'), $file);
                    })
                    ->autofocus()
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
