<?php

namespace App\Business;

use App\Models\Blog;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BlogService implements BlogServiceInterface
{
    public function getLatestPostsPeriod(Blog $blog, ?string $type = null): ?string
    {
        $latestPost = $blog->posts()
            ->when($type, function (Builder $builder) use ($type) {
                return $builder->where('type', $type);
            })->latest()->first();

        if (! $latestPost) {
            return null;
        }

        $month = $latestPost->created_at->format('m');
        $year = $latestPost->created_at->format('Y');

        return "{$month}-{$year}";
    }

    public function getPostsGroupedByMonthForPeriod(Blog $blog, string $month, string $year, ?string $type = null): Collection
    {
        return $blog->posts()
            ->where('published', true)
            ->when($type, function (Builder $query) use ($type) {
                $query->where('type', '=', $type);
            })
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPostFromPreviousPeriod(Post $post, bool $sameType = false): ?Post
    {
        $currentMonth = $post->created_at->month;
        $currentYear = $post->created_at->year;

        return $post->blog
            ->posts()
            ->where('published', true)
            ->where(function (Builder $query) use ($currentMonth, $currentYear) {
                $query->whereYear('created_at', '<=', $currentYear)
                    ->whereMonth('created_at', '<', $currentMonth);
            })
            ->when($sameType, function (Builder $query) use ($post) {
                $query->where('type', $post->type);
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getPostFromNextPeriod(Post $post, bool $sameType = false): ?Post
    {
        $currentMonth = $post->created_at->month;
        $currentYear = $post->created_at->year;

        return $post->blog
            ->posts()
            ->where('published', true)
            ->where(function (Builder $query) use ($currentMonth, $currentYear) {
                $query->whereYear('created_at', '>=', $currentYear)
                    ->whereMonth('created_at', '>', $currentMonth);
            })
            ->when($sameType, function (Builder $query) use ($post) {
                $query->where('type', $post->type);
            })
            ->orderBy('created_at')
            ->first();
    }

    public function getPostBySlug(Blog $blog, string $slug): ?Post
    {
        return $blog->posts()
            ->where('slug', $slug)
            ->first();
    }
}
