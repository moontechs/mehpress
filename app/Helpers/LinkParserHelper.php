<?php

namespace App\Helpers;

use App\Models\Post;
use App\Models\Short;
use App\Repositories\LinkRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LinkParserHelper implements LinkParserHelperInterface
{
    public function __construct(protected readonly LinkRepositoryInterface $linkRepository) {}

    public function extractUrls(string $text): array
    {
        $pattern = '/\b(https?:\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i';
        preg_match_all($pattern, $text, $matches);

        return array_unique(
            Arr::map($matches[0] ?? [], fn ($value) => Str::rtrim($value, '/')),
        );
    }

    /**
     * @param  Model|Post|Short  $model
     */
    public function updateModelLinks(Model $model, string $content): void
    {
        $urls = $this->extractUrls($content);

        if (empty($urls)) {
            return;
        }

        $this->linkRepository->updateLinksList($model, $urls);
    }
}
