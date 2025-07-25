<?php

namespace App\Business;

use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use MoeMizrak\LaravelOpenrouter\DTO\ChatData;
use MoeMizrak\LaravelOpenrouter\DTO\ErrorData;
use MoeMizrak\LaravelOpenrouter\DTO\MessageData;
use MoeMizrak\LaravelOpenrouter\DTO\ResponseFormatData;
use MoeMizrak\LaravelOpenrouter\Facades\LaravelOpenRouter;

class Seo implements SeoInterface
{
    public function getDefaultStructure(): array
    {
        return [
            'title' => '',
            'link_rel' => 'canonical',
            'meta_name__description' => '',
            'meta_name__robots' => 'index, follow',
            'robots' => 'index, follow',
            'meta_property__og:title' => '',
            'meta_property__og:description' => '',
            'meta_property__og:url' => '',
            'meta_property__og:type' => 'article',
            'meta_name__twitter:card' => 'summary',
            'meta_name__twitter:title' => '',
            'meta_name__twitter:description' => '',
        ];
    }

    public function generateForPost(Post $post, bool $useAI = false): void
    {
        $generatedSeoTags = [];

        if ($useAI) {
            $generatedSeoTags = $this->generateUsingAI([
                'title' => $post->title,
                'description' => $post->description,
                'text' => $post->text,
                'url' => $post->getUrl(),
            ]);
        }

        $post->seo_tags = [
            'title' => $post->title,
            'meta_name__description' => $generatedSeoTags['description'] ?? $post->description,
            'link_rel' => 'canonical',
            'meta_name__robots' => 'index, follow',
            'robots' => 'index, follow',
            'meta_property__og:title' => $post->title,
            'meta_property__og:description' => $generatedSeoTags['description'] ?? $post->description,
            'meta_property__og:url' => $post->getUrl(),
            'meta_property__og:type' => 'article',
            'meta_name__twitter:card' => 'summary',
            'meta_name__twitter:title' => $post->title,
            'meta_name__twitter:description' => $generatedSeoTags['description'] ?? $post->description,
        ];

        $post->save();
    }

    protected function generateUsingAI(array $data): array
    {
        if (empty(env('OPENROUTER_API_KEY'))) {
            Log::info('OpenRouter API key is not set. Skipping SEO generation. Please, check your .env file or env variables');

            return [];
        }

        $prompt = 'You are an SEO expert. Generate a JSON object that contains a description of a given article'.
            "Title: {$data['title']}\n".
            "Description: {$data['description']}\n".
            "Text: {$data['text']}\n";

        $jsonSchema = [
            'name' => 'SeoTags',
            'strict' => true,
            'additionalProperties' => false,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'description' => [
                        'type' => 'string',
                        'description' => 'A brief description of the article for SEO purposes.',
                    ],
                ],
                'required' => [
                    'description',
                ],
            ],
        ];

        $responseFormat = new ResponseFormatData(type: 'json_schema', json_schema: $jsonSchema);
        $chatData = new ChatData(
            messages: [new MessageData(content: $prompt, role: 'user')],
            model: 'openai/gpt-4.1-mini',
            response_format: $responseFormat
        );

        $response = LaravelOpenRouter::chatRequest($chatData);

        if ($response instanceof ErrorData) {
            Log::error('SEO generation failed', [
                'prompt' => $prompt,
                'response' => $response->toArray(),
            ]);

            throw new \RuntimeException('SEO generation failed: '.$response->message);
        }

        $response = $response->toArray();

        $content = Arr::get($response, 'choices.0.message.content', '');

        $seoTags = json_decode((string) $content, true) ?: [];

        Log::debug('Generated SEO tags', [
            'prompt' => $prompt,
            'response' => $response,
            'seoTags' => $seoTags,
        ]);

        if (empty($seoTags['description'])) {
            Log::error('SEO generation returned empty tags', [
                'prompt' => $prompt,
                'response' => $response,
            ]);

            throw new \RuntimeException('SEO generation returned empty tags');
        }

        return $seoTags;
    }
}
