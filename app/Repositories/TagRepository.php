<?php

namespace App\Repositories;

use App\Constants;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TagRepository implements TagRepositoryInterface
{
    public function getUnique(): array
    {
        if (Cache::has(Constants::CACHE_UNIQUE_TAGS_KEY)) {
            return Cache::get(Constants::CACHE_UNIQUE_TAGS_KEY);
        }

        $tags = DB::select('
            SELECT DISTINCT value AS tag
            FROM (
                SELECT json_each.value
                FROM posts, json_each(posts.tags)
            ) AS all_tags
        ');

        $tags = array_column($tags, 'tag');
        Cache::forever(Constants::CACHE_UNIQUE_TAGS_KEY, $tags);

        return $tags;
    }
}
