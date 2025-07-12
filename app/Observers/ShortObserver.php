<?php

namespace App\Observers;

use App\Constants;
use App\Models\Short;
use Illuminate\Support\Facades\Cache;

class ShortObserver
{
    public function created(Short $short): void
    {
        if (! empty($short->tags)) {
            Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        }
    }

    public function updating(Short $short): void
    {
        if ($short->isDirty(['tags'])) {
            Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        }
    }

    public function updated(Short $short): void
    {
        //
    }

    public function deleted(Short $short): void
    {
        //
    }

    public function restored(Short $short): void
    {
        //
    }

    public function forceDeleted(Short $short): void
    {
        //
    }
}
