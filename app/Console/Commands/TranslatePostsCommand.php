<?php

namespace App\Console\Commands;

use App\Business\TranslationService;
use App\Models\Blog;
use App\Models\Post;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TranslatePostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-posts
                            {--blog-id= : Specific blog ID to translate posts for}
                            {--dry-run : Show what would be translated without actually doing it}
                            {--force : Force translation even if target post already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate all posts in the default blog language to other available languages using AI';

    public function __construct(protected TranslationService $translationService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting post translation process...');

        $blogs = $this->getBlogs();

        if ($blogs->isEmpty()) {
            $this->error('No blogs found to process.');

            return 1;
        }

        $totalTranslated = 0;
        $totalSkipped = 0;
        $totalErrors = 0;

        foreach ($blogs as $blog) {
            $this->info("Processing blog: {$blog->name} (ID: {$blog->id})");

            $result = $this->processBlog($blog);
            $totalTranslated += $result['translated'];
            $totalSkipped += $result['skipped'];
            $totalErrors += $result['errors'];
        }

        $this->newLine();
        $this->info('Translation process completed!');
        $this->table(['Status', 'Count'], [
            ['Translated', $totalTranslated],
            ['Skipped', $totalSkipped],
            ['Errors', $totalErrors],
        ]);

        return $totalErrors > 0 ? 1 : 0;
    }

    protected function getBlogs(): Collection
    {
        if ($blogId = $this->option('blog-id')) {
            $blog = Blog::find($blogId);

            return $blog ? collect([$blog]) : collect();
        }

        return Blog::all();
    }

    protected function processBlog(Blog $blog): array
    {
        $translated = 0;
        $skipped = 0;
        $errors = 0;

        $defaultLanguage = $blog->default_language;
        $availableLanguages = $blog->languages ?: [];

        if (empty($defaultLanguage)) {
            $this->warn("Blog '{$blog->name}' has no default language set. Skipping.");

            return ['translated' => 0, 'skipped' => 1, 'errors' => 0];
        }

        if (empty($availableLanguages)) {
            $this->warn("Blog '{$blog->name}' has no additional languages configured. Skipping.");

            return ['translated' => 0, 'skipped' => 1, 'errors' => 0];
        }

        $targetLanguages = array_filter($availableLanguages, fn ($lang) => $lang !== $defaultLanguage);

        if (empty($targetLanguages)) {
            $this->warn("Blog '{$blog->name}' has no target languages for translation. Skipping.");

            return ['translated' => 0, 'skipped' => 1, 'errors' => 0];
        }

        $this->comment("Default language: {$defaultLanguage}");
        $this->comment('Target languages: '.implode(', ', $targetLanguages));

        // Get posts in default language that don't have a parent (original posts only)
        $originalPosts = Post::where('blog_id', $blog->id)
            ->whereNull('parent_id')
            ->where('published', '=', true)
            ->where(function ($query) use ($defaultLanguage) {
                $query->where('language', $defaultLanguage)
                    ->orWhereNull('language');
            })
            ->get();

        if ($originalPosts->isEmpty()) {
            $this->warn("No original posts found in default language for blog '{$blog->name}'. Skipping.");

            return ['translated' => 0, 'skipped' => 1, 'errors' => 0];
        }

        $this->comment("Found {$originalPosts->count()} original posts in default language");

        foreach ($originalPosts as $post) {
            foreach ($targetLanguages as $targetLanguage) {
                if ($this->shouldSkipTranslation($post, $targetLanguage)) {
                    $skipped++;

                    continue;
                }

                if ($this->option('dry-run')) {
                    $this->line("Would translate: '{$post->title}' (ID: {$post->id}) to {$targetLanguage}");

                    continue;
                }

                try {
                    $this->info("Translating post '{$post->title}' (ID: {$post->id}) to {$targetLanguage}...");

                    $translatedData = $this->translationService->translatePost($post, $targetLanguage);

                    if (empty($translatedData)) {
                        $this->warn("Translation service returned empty data for post ID {$post->id}");
                        $skipped++;

                        continue;
                    }

                    $translatedPost = $this->translationService->createTranslatedPost($post, $translatedData, $targetLanguage);

                    $this->comment("✓ Successfully created translated post (ID: {$translatedPost->id}) with parent_id: {$post->id}");
                    $translated++;

                } catch (ClientException $e) {
                    Log::error('Failed to translate post due to API error', [
                        'post_id' => $post->id,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage(),
                        'response' => $e->getResponse()?->getBody()?->getContents(),
                    ]);
                    $this->error("✗ API error translating post ID {$post->id}: {$e->getMessage()}");
                    $errors++;
                } catch (\Exception $e) {
                    Log::error('Failed to translate post', [
                        'post_id' => $post->id,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("✗ Error translating post ID {$post->id}: {$e->getMessage()}");
                    $errors++;
                }
            }
        }

        return ['translated' => $translated, 'skipped' => $skipped, 'errors' => $errors];
    }

    protected function shouldSkipTranslation(Post $post, string $targetLanguage): bool
    {
        if ($this->option('force')) {
            return false;
        }

        // Check if translation already exists using parent_id relationship
        $existingTranslation = Post::where('parent_id', $post->id)
            ->where('language', $targetLanguage)
            ->exists();

        if ($existingTranslation) {
            $this->comment("Skipping translation of '{$post->title}' to {$targetLanguage} - translation already exists");

            return true;
        }

        return false;
    }
}
