<?php

namespace App\Providers;

use App\Business\LinkParser;
use App\Business\LinkParserInterface;
use App\Business\Seo;
use App\Business\SeoInterface;
use App\Business\Tag;
use App\Business\TagInterface;
use App\Repositories\LinkRepository;
use App\Repositories\LinkRepositoryInterface;
use App\Repositories\TagRepository;
use App\Repositories\TagRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TagRepositoryInterface::class,
            TagRepository::class
        );
        $this->app->bind(
            LinkRepositoryInterface::class,
            LinkRepository::class
        );

        $this->app->bind(
            TagInterface::class,
            Tag::class
        );
        $this->app->bind(
            LinkParserInterface::class,
            LinkParser::class
        );
        $this->app->bind(
            SeoInterface::class,
            Seo::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
