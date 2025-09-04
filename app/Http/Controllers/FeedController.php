<?php

namespace App\Http\Controllers;

use App\Business\BlogServiceInterface;
use App\Business\NavigationInterface;
use App\Constants;
use App\DTO\Navigation\Element;
use App\DTO\Navigation\Navigation;
use App\DTO\PostsFilter;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request, BlogServiceInterface $blogService, NavigationInterface $navigation)
    {
        $blog = $request->session()->get('blog');
        $feedType = $request->route()->getName();
        $postType = $this->getPostTypeFromFeedType($feedType);
        $tag = $request->input('tag');
        $language = $request->session()->get('language', $blog->default_language);
        $postsFilter = (new PostsFilter($postType, $tag, $language));

        $period = $request->input('period', $blogService->getLatestPostsPeriod($blog, $postsFilter));

        if ($period) {
            [$month, $year] = explode('-', $period);
        } else {
            [$month, $year] = [1, 2025];
        }

        $posts = $blogService->getPostsGroupedByMonthForPeriod($blog, $month, $year, $postsFilter);

        $navigationPreviousElement = new Element(
            'Past',
            ! $posts->isEmpty() ? $navigation->getPreviousFeedUrl($posts->first(), $postsFilter) : null
        );
        $navigationNextElement = new Element(
            'Future',
            ! $posts->isEmpty() ? $navigation->getNextFeedUrl($posts->first(), $postsFilter) : null
        );

        return view('default.feed', [
            'blog' => $blog,
            'posts' => $posts,
            'monthAndYear' => $posts->first()?->created_at->format('F Y') ?? null,
            'navigation' => new Navigation(
                previous: $navigationPreviousElement,
                next: $navigationNextElement
            ),
        ]);
    }

    public function getPostTypeFromFeedType(string $feedType): ?string
    {
        return match ($feedType) {
            'posts' => Constants::POST_TYPE,
            'shorts' => Constants::SHORT_POST_TYPE,
            default => null,
        };
    }
}
