<?php

namespace App\Business;

use App\Models\Blog;
use App\Models\Post;
use App\Models\Short;
use App\Repositories\LinkRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LinkParser implements LinkParserInterface
{
    public function __construct(protected readonly LinkRepositoryInterface $linkRepository) {}

    public function extractUrls(string $text): array
    {
        $hosts = Blog::select('host')->get()->pluck('host')->toArray();

        $pattern = '/\b(https?:\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/i';
        preg_match_all($pattern, $text, $matches);

        return array_unique(
            Arr::where(
                Arr::map($matches[0] ?? [], fn ($value) => Str::rtrim($value, '/')),
                fn ($value) => Str::startsWith($value, $hosts) === false,
            ),
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
