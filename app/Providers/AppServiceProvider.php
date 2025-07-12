<?php

namespace App\Providers;

use App\Helpers\LinkParserHelper;
use App\Helpers\LinkParserHelperInterface;
use App\Helpers\TagHelperHelper;
use App\Helpers\TagHelperInterface;
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
            TagHelperInterface::class,
            TagHelperHelper::class
        );
        $this->app->bind(
            LinkParserHelperInterface::class,
            LinkParserHelper::class
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
