<?php

namespace App\DTO;

class PostsFilter
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $tag,
        public readonly ?string $language,
    ) {}
}
