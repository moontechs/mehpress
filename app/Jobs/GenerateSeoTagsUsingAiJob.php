<?php

namespace App\Jobs;

use App\Business\SeoInterface;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSeoTagsUsingAiJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly string $type, public readonly int $id) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $seo = app()->make(SeoInterface::class);
        $post = Post::findOrFail($this->id);
        $seo->generateForPost($post, true);
    }
}
