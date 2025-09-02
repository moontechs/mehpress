<?php

namespace App\Business;

use App\DTO\PostsFilter;
use Illuminate\Database\Eloquent\Model;

interface NavigationInterface
{
    public function getPreviousFeedUrl(Model $model, PostsFilter $filter): ?string;

    public function getNextFeedUrl(Model $model, PostsFilter $filter): ?string;
}
