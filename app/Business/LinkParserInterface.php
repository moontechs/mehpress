<?php

namespace App\Business;

use App\Models\Post;

interface LinkParserInterface
{
    public function extractUrls(string $text): array;

    public function updateModelLinks(Post $post, string $content): void;
}
