<?php

namespace App\DTO\Navigation;

class Element
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $url,
    ) {}
}
