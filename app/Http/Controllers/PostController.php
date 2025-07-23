<?php

namespace App\Http\Controllers;

use App\Business\BlogServiceInterface;
use App\Business\NavigationInterface;
use App\Constants;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __invoke(Request $request, string $slug, BlogServiceInterface $blogService, NavigationInterface $navigation)
    {
        $blog = $request->session()->get('blog');

        $post = $blogService->getPostBySlug($blog, $slug);

        // $navigationPrevious = $navigation->getPreviousPeriodFeedUrl($posts->first(), Constants::FEED);
        // $navigationNext = $navigation->getNextPeriodFeedUrl($posts->first(), Constants::FEED);

        return view('default.post', [
            'blog' => $blog,
            'post' => $post,
            // 'monthAndYear' => $posts->first()->created_at->format('F Y') ?? null,
            // 'navigationPrevious' => $navigationPrevious,
            // 'navigationNext' => $navigationNext,
        ]);
    }
}
