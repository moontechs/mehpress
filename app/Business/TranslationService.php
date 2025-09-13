<?php

namespace App\Business;

use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MoeMizrak\LaravelOpenrouter\DTO\ChatData;
use MoeMizrak\LaravelOpenrouter\DTO\ErrorData;
use MoeMizrak\LaravelOpenrouter\DTO\MessageData;
use MoeMizrak\LaravelOpenrouter\DTO\ResponseFormatData;
use MoeMizrak\LaravelOpenrouter\Facades\LaravelOpenRouter;

class TranslationService
{
    public function translatePost(Post $originalPost, string $targetLanguage): array
    {
        if (empty(config('services.openrouter.api_key'))) {
            Log::info('OpenRouter API key is not set. Skipping translation. Please check your .env file or env variables');

            return [];
        }

        $prompt = $this->buildTranslationPrompt($originalPost, $targetLanguage);

        $jsonSchema = [
            'name' => 'TranslatedPost',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'title' => [
                        'type' => 'string',
                        'description' => 'Translated title of the post.',
                    ],
                    'description' => [
                        'type' => 'string',
                        'description' => 'Translated description of the post.',
                    ],
                    'text' => [
                        'type' => 'string',
                        'description' => 'Translated markdown content. URLs, code blocks, and image paths must remain unchanged.',
                    ],
                ],
                'required' => [
                    'title',
                    'description',
                    'text',
                ],
                'additionalProperties' => false,
            ],
        ];

        $responseFormat = new ResponseFormatData(type: 'json_schema', json_schema: $jsonSchema);
        $chatData = new ChatData(
            messages: [new MessageData(content: $prompt, role: 'user')],
            model: 'openai/gpt-4o-mini',
            response_format: $responseFormat
        );

        $response = LaravelOpenRouter::chatRequest($chatData);

        if ($response instanceof ErrorData) {
            Log::error('Translation failed', [
                'original_post_id' => $originalPost->id,
                'target_language' => $targetLanguage,
                'prompt' => $prompt,
                'response' => $response->toArray(),
            ]);

            throw new \RuntimeException('Translation failed: '.$response->message);
        }

        $response = $response->toArray();
        $content = Arr::get($response, 'choices.0.message.content', '');
        $translatedData = json_decode($content, true) ?: [];

        Log::debug('Post translated successfully', [
            'original_post_id' => $originalPost->id,
            'target_language' => $targetLanguage,
            'translated_data' => $translatedData,
        ]);

        if (empty($translatedData['title']) || empty($translatedData['text'])) {
            Log::error('Translation returned empty content', [
                'original_post_id' => $originalPost->id,
                'target_language' => $targetLanguage,
                'response' => $response,
            ]);

            throw new \RuntimeException('Translation returned empty content');
        }

        return $translatedData;
    }

    protected function buildTranslationPrompt(Post $originalPost, string $targetLanguage): string
    {
        return <<<TXT
                You are a professional translator specialized in blog content translation.
                Translate the following blog post to {$targetLanguage}.

                IMPORTANT INSTRUCTIONS:
                1. Translate ONLY the text content - do NOT translate:
                   - URLs and links
                   - Image paths and filenames
                   - Code blocks and inline code
                   - Quoted text (markdown blockquotes)
                   - HTML tags
                   - Markdown syntax characters
                   - Names of people, products, brands, or proper nouns
                2. Preserve the original markdown formatting structure
                3. Keep the same tone and style as the original
                4. Maintain all hyperlinks and image references exactly as they are
                5. Translate tags to appropriate terms in the target language

                Original Post Data:
                Title: {$originalPost->title}
                Description: {$originalPost->description}
                Content (Markdown):
                {$originalPost->text}

                Return the translation as a JSON object with the specified schema."
            TXT;
    }

    public function createTranslatedPost(Post $originalPost, array $translatedData, string $targetLanguage): Post
    {
        $translatedPost = new Post([
            'title' => $translatedData['title'],
            'description' => $translatedData['description'],
            'text' => $translatedData['text'],
            'slug' => $this->generateUniqueSlug($translatedData['title'], $originalPost->blog_id),
            'tags' => $translatedData['tags'] ?? [],
            'published' => $originalPost->published,
            'type' => $originalPost->type,
            'blog_id' => $originalPost->blog_id,
            'language' => $targetLanguage,
            'parent_id' => $originalPost->id,
        ]);

        $translatedPost->save();

        // Copy linked relations if they exist
        if ($originalPost->links()->exists()) {
            $translatedPost->links()->sync($originalPost->links()->pluck('links.id'));
        }

        return $translatedPost;
    }

    protected function generateUniqueSlug(string $title, int $blogId): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Post::where('slug', $slug)->where('blog_id', $blogId)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
