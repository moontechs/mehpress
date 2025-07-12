<?php

namespace App\Observers;

use App\Constants;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void
    {
        if (! empty($post->tags)) {
            Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        }
    }

    public function updating(Post $post): void
    {
        if ($post->isDirty(['tags'])) {
            Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        }
    }

    public function updated(Post $post): void
    {
        //
    }

    public function deleted(Post $post): void
    {
        //
    }

    public function restored(Post $post): void
    {
        //
    }

    public function forceDeleted(Post $post): void
    {
        //
    }
}
