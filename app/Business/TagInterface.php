<?php

namespace App\Business;

interface TagInterface
{
    public function parseFromText(?string $text): array;

    public function getSuggestions(): array;
}
