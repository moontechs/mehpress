<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ExecuteBlogCronCommand extends Command
{
    protected $signature = 'app:blog:execute-cron {blog?} {--dry-run : Show commands without executing} {--timeout=600 : Command timeout in seconds}';

    protected $description = 'Execute ordered cron commands for blogs';

    public function handle(): int
    {
        $blogParam = $this->argument('blog');
        $isDryRun = $this->option('dry-run');
        $timeout = (int) $this->option('timeout');

        $blogs = $this->getBlogs($blogParam);

        if ($blogs->isEmpty()) {
            $this->error('No blogs found or no blogs have cron commands configured.');

            return CommandAlias::FAILURE;
        }

        $this->info("Processing {$blogs->count()} blog(s)...");

        foreach ($blogs as $blog) {
            $this->processBlog($blog, $isDryRun, $timeout);
        }

        $this->info('✅ All blog cron commands processed successfully!');

        return CommandAlias::SUCCESS;
    }

    private function getBlogs(?string $blogParam)
    {
        if ($blogParam) {
            $blog = is_numeric($blogParam)
                ? Blog::find($blogParam)
                : Blog::where('name', $blogParam)->first();

            if (! $blog) {
                $this->error("Blog not found: {$blogParam}");

                return collect();
            }

            return collect([$blog]);
        }

        return Blog::whereNotNull('cron_commands')
            ->where('cron_commands', '!=', '[]')
            ->get()
            ->filter(fn ($blog) => ! empty($blog->cron_commands));
    }

    private function processBlog(Blog $blog, bool $isDryRun, int $timeout): void
    {
        $commands = $blog->getFormattedCronCommands();

        if (empty($commands)) {
            $this->warn("⚠️  Blog '{$blog->name}' has no cron commands configured.");

            return;
        }

        $this->newLine();
        $this->info("🔄 Processing blog: {$blog->name} ({$blog->id})");
        $this->info('📋 Found '.count($commands).' command(s) to execute');

        foreach ($commands as $index => $item) {
            $commandNumber = $index + 1;
            $fullCommand = $item['command'];
            $description = $item['description'];

            $this->newLine();
            $this->info("📝 [{$commandNumber}] {$fullCommand}");

            if ($description) {
                $this->comment("    → {$description}");
            }

            if ($isDryRun) {
                $this->comment("    💨 [DRY RUN] Would execute: php artisan {$fullCommand}");

                continue;
            }

            $this->executeCommand($fullCommand, $timeout, $commandNumber);
        }
    }

    private function executeCommand(string $command): void
    {
        $startTime = microtime(true);

        try {
            $this->comment('    ⏳ Executing...');

            $parts = $this->parseCommandString($command);
            $commandName = array_shift($parts);
            $arguments = $this->parseArguments($parts);

            $exitCode = Artisan::call($commandName, $arguments);

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            if ($exitCode === 0) {
                $this->info("    ✅ Completed in {$duration}ms");

                $output = trim(Artisan::output());
                if (! empty($output)) {
                    $this->line('    📄 Output:');
                    foreach (explode("\n", $output) as $line) {
                        $this->line('       '.$line);
                    }
                }
            } else {
                $this->error("    ❌ Command failed (exit code: {$exitCode})");

                $output = trim(Artisan::output());
                if (! empty($output)) {
                    $this->error('    🚨 Command output:');
                    foreach (explode("\n", $output) as $line) {
                        $this->error('       '.$line);
                    }
                }
            }

        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $this->error("    ❌ Command failed after {$duration}ms: ".$e->getMessage());
        }
    }

    private function parseCommandString(string $command): array
    {
        return str_getcsv($command, ' ');
    }

    private function parseArguments(array $parts): array
    {
        $arguments = [];
        $positionalIndex = 0;

        foreach ($parts as $part) {
            if (str_starts_with($part, '--')) {
                // Handle --option=value or --flag
                if (str_contains($part, '=')) {
                    [$key, $value] = explode('=', $part, 2);
                    $arguments['--'.ltrim($key, '-')] = $this->parseValue($value);
                } else {
                    $arguments['--'.ltrim($part, '-')] = true;
                }
            } elseif (str_starts_with($part, '-')) {
                // Handle short options like -v
                $arguments['-'.ltrim($part, '-')] = true;
            } else {
                // Handle positional arguments with numeric keys
                $arguments[$positionalIndex] = $part;
                $positionalIndex++;
            }
        }

        return $arguments;
    }

    private function parseValue(string $value): mixed
    {
        if (in_array(strtolower($value), ['true', '1', 'yes', 'on'])) {
            return true;
        }

        if (in_array(strtolower($value), ['false', '0', 'no', 'off'])) {
            return false;
        }

        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }
}
