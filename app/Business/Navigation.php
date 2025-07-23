<?php

namespace App\Business;

use App\Constants;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class Navigation implements NavigationInterface
{
    public function __construct(protected readonly BlogServiceInterface $blogService) {}

    public function getPreviousFeedUrl(Model $model, ?string $type): ?string
    {
        if (! $model instanceof Post) {
            throw new \InvalidArgumentException('Model must be an instance of Post.');
        }

        $previousPost = $this->blogService->getPostFromPreviousPeriod($model, $type !== Constants::FEED);

        if (! $previousPost) {
            return null;
        }

        return route($this->getRouteName($type), ['period' => $previousPost->created_at->format('m-Y')]);
    }

    public function getNextFeedUrl(Model $model, ?string $type): ?string
    {
        if (! $model instanceof Post) {
            throw new \InvalidArgumentException('Model must be an instance of Post.');
        }

        $nextPost = $this->blogService->getPostFromNextPeriod($model, $type !== Constants::FEED);

        if (! $nextPost) {
            return null;
        }

        return route($this->getRouteName($type), ['period' => $nextPost->created_at->format('m-Y')]);
    }

    private function getRouteName(?string $feedType): string
    {
        return match ($feedType) {
            Constants::POST_TYPE => 'posts',
            Constants::SHORT_POST_TYPE => 'shorts',
            Constants::LINK => 'links',
            Constants::QUOTE => 'quotes',
            default => 'feed',
        };
    }
}
