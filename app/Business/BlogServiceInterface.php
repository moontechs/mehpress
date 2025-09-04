<?php

namespace App\Business;

use App\DTO\PostsFilter;
use App\Models\Blog;
use App\Models\Post;
use Illuminate\Support\Collection;

interface BlogServiceInterface
{
    public function getLatestPostsPeriod(Blog $blog, ?PostsFilter $filter = null): ?string;

    public function getPostsGroupedByMonthForPeriod(Blog $blog, string $month, string $year, ?PostsFilter $filter = null): Collection;

    public function getPostFromPreviousPeriod(Post $post, ?PostsFilter $filter = null): ?Post;

    public function getPostFromNextPeriod(Post $post, ?PostsFilter $filter = null): ?Post;

    public function getPostBySlug(Blog $blog, string $slug);

    public function getFileUrlForBlog(int $blogId, string $filePath): ?string;
}
