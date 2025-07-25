<?php

namespace App\Business;

use App\Constants;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class Navigation implements NavigationInterface
{
    public function __construct(protected readonly BlogServiceInterface $blogService) {}

    public function getPreviousFeedUrl(Model $model, ?string $type, array $queryParams = []): ?string
    {
        if (! $model instanceof Post) {
            throw new \InvalidArgumentException('Model must be an instance of Post.');
        }

        $tag = $queryParams['tag'] ?? null;
        $previousPost = $this->blogService->getPostFromPreviousPeriod($model, $type !== Constants::FEED, $tag);

        if (! $previousPost) {
            return null;
        }

        $params = array_merge(['period' => $previousPost->created_at->format('m-Y')], $queryParams);

        return route($this->getRouteName($type), $params);
    }

    public function getNextFeedUrl(Model $model, ?string $type, array $queryParams = []): ?string
    {
        if (! $model instanceof Post) {
            throw new \InvalidArgumentException('Model must be an instance of Post.');
        }

        $tag = $queryParams['tag'] ?? null;
        $nextPost = $this->blogService->getPostFromNextPeriod($model, $type !== Constants::FEED, $tag);

        if (! $nextPost) {
            return null;
        }

        $params = array_merge(['period' => $nextPost->created_at->format('m-Y')], $queryParams);

        return route($this->getRouteName($type), $params);
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
