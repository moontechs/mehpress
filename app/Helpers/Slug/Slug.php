<?php

namespace App\Helpers\Slug;

use Illuminate\Support\Str;

class Slug
{
    public static function getForPost(string $string): string
    {
        return Str::slug($string);
    }

    public static function getForShort(string $string): string
    {
        return Str::slug(Str::substr($string, 0, 40)).'-'.rand(100, 999);
    }
}
