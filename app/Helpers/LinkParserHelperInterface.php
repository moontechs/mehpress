<?php

namespace App\Helpers;

use App\Models\Post;
use App\Models\Short;
use Illuminate\Database\Eloquent\Model;

interface LinkParserHelperInterface
{
    public function extractUrls(string $text): array;

    /**
     * @param  Model|Post|Short  $model
     */
    public function updateModelLinks(Model $model, string $content): void;
}
