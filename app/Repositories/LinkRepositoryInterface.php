<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Short;
use Illuminate\Database\Eloquent\Model;

interface LinkRepositoryInterface
{
    /**
     * @param  Model|Post|Short  $model
     */
    public function updateLinksList(Model $model, array $urls): void;
}
