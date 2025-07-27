<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Kovah\HtmlMeta\Exceptions\InvalidUrlException;
use Kovah\HtmlMeta\Exceptions\UnreachableUrlException;
use Kovah\HtmlMeta\Facades\HtmlMeta;

class FetchLinkMetadataCommand extends Command
{
    protected $signature = 'app:links:fetch-metadata
                            {--force : Refetch metadata for links that already have it}
                            {--chunk=50 : Number of links to process in each chunk}';

    protected $description = 'Fetch metadata for links in the database';

    public function handle(): int
    {
        $force = $this->option('force');
        $chunkSize = (int) $this->option('chunk');

        $query = Link::query();

        if (! $force) {
            $query->whereNull('metadata')
                ->orWhere('metadata', '=', '');
        }

        $totalLinks = $query->count();

        if ($totalLinks === 0) {
            $this->info('No links to process.');

            return self::SUCCESS;
        }

        $this->info("Processing {$totalLinks} links...");

        $processed = 0;
        $updated = 0;
        $failed = 0;

        $query->chunk($chunkSize, function ($links) use (&$processed, &$updated, &$failed) {
            foreach ($links as $link) {
                $this->info("Processing: {$link->url}");

                try {
                    $metaTags = HtmlMeta::forUrl($link->url)->getMeta();
                } catch (InvalidUrlException $e) {
                    Log::error('Invalid URL encountered', [
                        'url' => $link->url,
                        'error' => $e->getMessage(),
                    ]);
                } catch (UnreachableUrlException $e) {
                    Log::error('Unreachable URL encountered', [
                        'url' => $link->url,
                        'error' => $e->getMessage(),
                    ]);
                }

                if (! empty($metaTags)) {
                    $link->update(['metadata' => $metaTags]);
                    $updated++;
                    $this->info("✓ Updated metadata for: {$link->url}");
                } else {
                    Log::warning('Empty meta tags', [
                        'url' => $link->url,
                    ]);
                    $failed++;
                    $this->warn("✗ Failed to fetch metadata for: {$link->url}");
                }

                $processed++;

                usleep(rand(100000, 400000));
            }
        });

        $this->info("Completed processing {$processed} links:");
        $this->info("- Updated: {$updated}");
        $this->info("- Failed: {$failed}");

        return self::SUCCESS;
    }
}
