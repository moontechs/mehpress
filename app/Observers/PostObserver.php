<?php

namespace App\Observers;

use App\Business\LinkParser;
use App\Business\SeoInterface;
use App\Business\SlugHelper;
use App\Business\TagInterface;
use App\Constants;
use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PostObserver
{
    public function __construct(
        protected readonly LinkParser $linkParser,
        protected readonly TagInterface $tag,
        protected readonly SeoInterface $seo
    ) {}

    public function created(Post $post): void
    {
        if (! empty($post->tags)) {
            Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        }

        if ($post->text) {
            $this->linkParser->updateModelLinks($post, $post->text);
        }

        $this->seo->generateForPost($post);
    }

    public function creating(Post $post): void
    {
        if ($post->type === Constants::SHORT_POST_TYPE) {
            $this->updateShortPostFields($post);
        }
    }

    public function updating(Post $post): void
    {
        if ($post->type === Constants::SHORT_POST_TYPE && $post->isDirty('text')) {
            $this->updateShortPostFields($post);
        }

        if ($post->isDirty(['tags'])) {
            Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        }
    }

    public function updated(Post $post): void
    {
        if ($post->wasChanged('text')) {
            $this->linkParser->updateModelLinks($post, $post->text);
        }
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

    private function updateShortPostFields(Post $post): void
    {
        $firstSentence = strtok($post->text, '.!?');
        $firstSentence = Str::of($firstSentence)
            ->replaceMatches('/#\w+/', '')
            ->trim(); // remove hashtags

        $post->title = $firstSentence;
        $post->slug = SlugHelper::getForShort($firstSentence);
        $post->description = $firstSentence;
        $post->tags = Arr::flatten($this->tag->parseFromText($post->text));
    }
}
