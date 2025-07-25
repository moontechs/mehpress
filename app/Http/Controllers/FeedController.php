<?php

namespace App\Http\Controllers;

use App\Business\BlogServiceInterface;
use App\Business\NavigationInterface;
use App\Constants;
use App\DTO\Navigation\Element;
use App\DTO\Navigation\Navigation;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __invoke(Request $request, BlogServiceInterface $blogService, NavigationInterface $navigation)
    {
        $blog = $request->session()->get('blog');
        $feedType = $request->route()->getName();
        $postType = $this->getPostTypeFromFeedType($feedType);
        $tag = $request->input('tag');

        $period = $request->input('period', $blogService->getLatestPostsPeriod($blog, $postType, $tag));

        [$month, $year] = explode('-', $period);
        $posts = $blogService->getPostsGroupedByMonthForPeriod($blog, $month, $year, $postType, $tag);

        if ($posts->isEmpty()) {
            abort(404);
        }

        $queryParams = $tag ? ['tag' => $tag] : [];

        $navigationPreviousElement = new Element(
            'Past',
            $navigation->getPreviousFeedUrl($posts->first(), $postType, $queryParams
            ));
        $navigationNextElement = new Element(
            'Future',
            $navigation->getNextFeedUrl($posts->first(), $postType, $queryParams
            ));

        return view('default.feed', [
            'blog' => $blog,
            'posts' => $posts,
            'monthAndYear' => $posts->first()->created_at->format('F Y') ?? null,
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
