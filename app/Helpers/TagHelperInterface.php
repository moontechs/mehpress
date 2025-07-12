<?php

namespace App\Helpers;

interface TagHelperInterface
{
    public function parseFromText(?string $text): array;

    public function getSuggestions(): array;
}
