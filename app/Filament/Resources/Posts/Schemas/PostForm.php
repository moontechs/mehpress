<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Business\BlogService;
use App\Business\SeoInterface;
use App\Business\SlugHelper;
use App\Business\TagInterface;
use App\Constants;
use App\Models\Blog;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        $tag = app()->make(TagInterface::class);
        $seo = app()->make(SeoInterface::class);

        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true)
                    ->live(debounce: 1000)
                    ->partiallyRenderComponentsAfterStateUpdated(['slug', 'tags'])
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('slug', SlugHelper::getForPost($state));
                    })
                    ->required(),
                TextInput::make('slug')
                    ->columnSpanFull()
                    ->unique(ignoreRecord: true)
                    ->required(),

                Grid::make()->columnSpanFull()->schema([
                    Select::make('blog_id')
                        ->relationship('blog', 'name')
                        ->selectablePlaceholder(false)
                        ->default(fn (Get $get) => Blog::first()?->id ?? 1)
                        ->live()
                        ->afterStateHydrated(function (Set $set, ?string $state) {
                            $blogId = request()->query('blog_id');
                            if ($blogId && ! $state) {
                                $set('blog_id', $blogId);
                            }
                        }),
                    Select::make('type')
                        ->options(array_combine(Constants::POST_TYPES, Constants::POST_TYPES))
                        ->default(Constants::POST_TYPE),
                    Select::make('language')
                        ->label('Language')
                        ->options(function (Get $get) {
                            $blogId = $get('blog_id');
                            if (! $blogId) {
                                return [];
                            }
                            $blog = Blog::find($blogId);
                            if (! $blog || ! $blog->languages) {
                                return [];
                            }

                            return array_combine($blog->languages, $blog->languages);
                        })
                        ->default(function (Get $get) {
                            $blogId = $get('blog_id');
                            if (! $blogId) {
                                return null;
                            }
                            $blog = Blog::find($blogId);

                            return $blog?->default_language;
                        })
                        ->live()
                        ->rules(['required', 'regex:/^[a-z]{2}_[A-Z]{2}$/'])
                        ->required(),
                ])->columns(3),
                TextInput::make('description')
                    ->columnSpanFull(),
                MarkdownEditor::make('text')
                    ->toolbarButtons([
                        ['bold', 'italic', 'strike', 'link'],
                        ['heading'],
                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                        ['table', 'attachFiles'],
                        ['undo', 'redo'],
                    ])
                    ->fileAttachmentsDisk('public')
                    ->getFileAttachmentUrlUsing(function (string $file, Get $get, BlogService $blogService) {
                        return $blogService->getFileUrlForBlog($get('blog_id'), $file);
                    })
                    ->required()
                    ->columnSpanFull(),
                TagsInput::make('tags')
                    ->suggestions($tag->getSuggestions())
                    ->columnSpanFull(),

                Grid::make()->columnSpanFull()->schema([
                    KeyValue::make('seo_tags')
                        ->label('SEO')
                        ->columnSpanFull()
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->default($seo->getDefaultStructure()),
                    Actions::make([
                        Action::make('restore_seo_tags')
                            ->label('Restore default SEO tags')
                            ->tooltip('All tags will be generated automatically and rewritten')
                            ->requiresConfirmation()
                            ->action(fn (Set $set, Get $get) => $set('seo_tags', $seo->getDefaultStructure()))
                            ->visible(fn (Get $get) => $get('id') !== null)
                            ->outlined(),
                    ]),
                ]),

                Toggle::make('published')
                    ->default(false),
            ]);
    }
}
