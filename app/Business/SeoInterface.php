<?php

namespace App\Business;

use App\Models\Post;

interface SeoInterface
{
    public function getDefaultStructure(): array;

    public function generateForPost(Post $post, bool $useAI = false): void;
}
