<?php

namespace App\Business;

use App\Constants;
use App\DTO\PostsFilter;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class Navigation implements NavigationInterface
{
    public function __construct(protected readonly BlogServiceInterface $blogService) {}

    public function getPreviousFeedUrl(Model $model, PostsFilter $filter): ?string
    {
        if (! $model instanceof Post) {
            throw new \InvalidArgumentException('Model must be an instance of Post.');
        }

        $previousPost = $this->blogService->getPostFromPreviousPeriod($model, $filter);

        if (! $previousPost) {
            return null;
        }

        $params = array_merge(['period' => $previousPost->created_at->format('m-Y')], $this->filterToQueryParams($filter));

        return route($this->getRouteName($filter->type), $params);
    }

    public function getNextFeedUrl(Model $model, PostsFilter $filter): ?string
    {
        if (! $model instanceof Post) {
            throw new \InvalidArgumentException('Model must be an instance of Post.');
        }

        $nextPost = $this->blogService->getPostFromNextPeriod($model, $filter);

        if (! $nextPost) {
            return null;
        }

        $params = array_merge(['period' => $nextPost->created_at->format('m-Y')], $this->filterToQueryParams($filter));

        return route($this->getRouteName($filter->type), $params);
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

    private function filterToQueryParams(PostsFilter $filter): array
    {
        $queryParams = [];
        if ($filter->tag) {
            $queryParams['tag'] = $filter->tag;
        }

        return $queryParams;
    }
}
