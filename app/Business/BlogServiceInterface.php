<?php

namespace App\Business;

use App\Models\Blog;
use App\Models\Post;
use Illuminate\Support\Collection;

interface BlogServiceInterface
{
    public function getLatestPostsPeriod(Blog $blog, ?string $type = null): ?string;

    public function getPostsGroupedByMonthForPeriod(Blog $blog, string $month, string $year, ?string $type = null): Collection;

    public function getPostFromPreviousPeriod(Post $post, bool $sameType = false): ?Post;

    public function getPostFromNextPeriod(Post $post, bool $sameType = false): ?Post;

    public function getPostBySlug(Blog $blog, string $slug);
}
