<?php

namespace App\Repositories;

use App\Models\Link;
use App\Models\Post;
use App\Models\Short;
use Illuminate\Database\Eloquent\Model;

class LinkRepository implements LinkRepositoryInterface
{
    /**
     * @param  Model|Post|Short  $model
     */
    public function updateLinksList(Model $model, array $urls): void
    {
        $linkIds = [];

        foreach ($urls as $url) {
            $link = Link::firstOrCreate(['url' => $url]);
            $linkIds[] = $link->id;
        }

        $model->links()->sync($linkIds);
    }
}
