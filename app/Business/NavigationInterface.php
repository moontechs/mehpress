<?php

namespace App\Business;

use Illuminate\Database\Eloquent\Model;

interface NavigationInterface
{
    public function getPreviousFeedUrl(Model $model, ?string $type, array $queryParams = []): ?string;

    public function getNextFeedUrl(Model $model, ?string $type, array $queryParams = []): ?string;
}
