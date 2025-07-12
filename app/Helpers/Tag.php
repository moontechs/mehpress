<?php

namespace App\Helpers;

use App\Repositories\TagRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Tag
{
    public static function parseFromText(?string $text): array
    {
        $matches = [];
        preg_match_all('/#\w+/', $text ?? '', $matches);

        return array_unique(
            Arr::map(
                Arr::flatten($matches),
                fn ($value) => Str::replace('#', '', $value)
            )
        );
    }

    public static function getSuggestions(): array
    {
        return TagRepository::getUnique();
    }
}
