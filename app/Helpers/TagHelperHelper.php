<?php

namespace App\Helpers;

use App\Repositories\TagRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TagHelperHelper implements TagHelperInterface
{
    public function __construct(protected readonly TagRepositoryInterface $repository) {}

    public function parseFromText(?string $text): array
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

    public function getSuggestions(): array
    {
        return $this->repository->getUnique();
    }
}
