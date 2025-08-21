<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Tests\TestCase;

class LinkRendererTest extends TestCase
{
    use RefreshDatabase;

    public function test_link_with_metadata_renders_as_preview(): void
    {
        // Create a link with metadata
        $link = Link::create([
            'url' => 'https://example.com/test',
            'metadata' => [
                'og:title' => 'Test Title',
                'og:description' => 'This is a test description',
                'og:image' => 'https://example.com/image.jpg',
                'og:site_name' => 'Example Site',
            ],
        ]);

        $markdown = '[Test Link](https://example.com/test)';
        $renderer = app(MarkdownRenderer::class);
        $html = $renderer->toHtml($markdown);

        // Should contain link preview elements instead of regular link
        $this->assertStringContainsString('Test Title', $html);
        $this->assertStringContainsString('This is a test description', $html);
        $this->assertStringContainsString('Example Site', $html);
    }

    public function test_link_without_metadata_renders_as_regular_link(): void
    {
        $markdown = '[Regular Link](https://example.com/no-metadata)';
        $renderer = app(MarkdownRenderer::class);
        $html = $renderer->toHtml($markdown);

        // Should render as regular link
        $this->assertStringContainsString('<a href="https://example.com/no-metadata">Regular Link</a>', $html);
    }
}
