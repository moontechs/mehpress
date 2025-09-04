<?php

namespace App\Business;

use App\DTO\PostsFilter;
use App\Models\Blog;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BlogService implements BlogServiceInterface
{
    public function getLatestPostsPeriod(Blog $blog, ?PostsFilter $filter = null): ?string
    {
        $latestPost = $blog->posts()
            ->when($filter->type, function (Builder $builder) use ($filter) {
                return $builder->where('type', $filter->type);
            })
            ->when($filter->tag, function (Builder $builder) use ($filter) {
                return $builder->whereJsonContains('tags', $filter->tag);
            })
            ->when($filter->language, function (Builder $builder) use ($filter) {
                return $builder->where('language', $filter->language);
            })
            ->latest()->first();

        if (! $latestPost) {
            return null;
        }

        $month = $latestPost->created_at->format('m');
        $year = $latestPost->created_at->format('Y');

        return "{$month}-{$year}";
    }

    public function getPostsGroupedByMonthForPeriod(Blog $blog, string $month, string $year, ?PostsFilter $filter = null): Collection
    {
        return $blog->posts()
            ->where('published', true)
            ->when($filter->type, function (Builder $query) use ($filter) {
                $query->where('type', '=', $filter->type);
            })
            ->when($filter->tag, function (Builder $query) use ($filter) {
                $query->whereJsonContains('tags', $filter->tag);
            })
            ->when($filter->language, function (Builder $query) use ($filter) {
                $query->where('language', $filter->language);
            })
            ->whereMonth('created_at', '=', $month)
            ->whereYear('created_at', '=', $year)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPostFromPreviousPeriod(Post $post, ?PostsFilter $filter = null): ?Post
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
            ->when($filter->type, function (Builder $query) use ($filter) {
                $query->where('type', $filter->type);
            })
            ->when($filter?->tag, function (Builder $query) use ($filter) {
                $query->whereJsonContains('tags', $filter->tag);
            })
            ->when($filter?->language, function (Builder $query) use ($filter) {
                $query->where('language', $filter->language);
            })
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getPostFromNextPeriod(Post $post, ?PostsFilter $filter = null): ?Post
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
            ->when($filter->type, function (Builder $query) use ($filter) {
                $query->where('type', $filter->type);
            })
            ->when($filter?->tag, function (Builder $query) use ($filter) {
                $query->whereJsonContains('tags', $filter->tag);
            })
            ->when($filter?->language, function (Builder $query) use ($filter) {
                $query->where('language', $filter->language);
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

    public function getFileUrlForBlog(int $blogId, string $filePath): ?string
    {
        $blog = Blog::find($blogId);
        if (! $blog) {
            return null;
        }

        return sprintf('%s/storage/%s', $blog->host, $filePath);
    }
}
