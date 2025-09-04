<?php

namespace App\Console\Commands;

use App\Business\SeoInterface;
use App\Models\Post;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSeoTagsUsingAi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seo:generate-seo-tags {--use-ai: Use AI to generate description} {--force : Force regeneration of SEO tags even if ai_generated is true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO tags using AI for all posts that have ai_generated=false or missing in seo_tags';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting SEO tags generation for posts...');

        $posts = Post::whereRaw($this->option('force')
            ? '1 = 1'
            : "
            seo_tags IS NULL
            OR JSON_EXTRACT(seo_tags, '$.ai_generated') = 'false'
            OR JSON_EXTRACT(seo_tags, '$.ai_generated') IS NULL
        "
        )->get();

        if ($posts->isEmpty()) {
            $this->info('No posts found that need SEO tag generation.');

            return;
        }

        $this->info("Found {$posts->count()} posts that need SEO tag generation.");

        $seo = app()->make(SeoInterface::class);

        $posts->each(function (Post $post) use ($seo) {
            $this->info("Processing post ID {$post->id}: {$post->title}");

            try {
                $seo->generateForPost($post, $this->option('use-ai'));
                $this->comment("✓ Successfully generated SEO tags for post ID {$post->id}");
            } catch (ClientException $e) {
                Log::error('Failed to generate SEO tags', [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                    'response' => $e->getResponse()->getBody()->getContents(),
                ]);
                $this->error("✗ Failed to generate SEO tags for post ID {$post->id}: {$e->getMessage()}");
            } catch (\Exception $e) {
                Log::error('Failed to generate SEO tags', ['post_id' => $post->id, 'error' => $e->getMessage()]);
                $this->error("✗ Failed to generate SEO tags for post ID {$post->id}: {$e->getMessage()}");
            }
        });

        $this->comment("Processed {$posts->count()} posts.");
    }
}
