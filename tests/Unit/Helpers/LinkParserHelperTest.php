<?php

namespace Tests\Unit\Helpers;

use App\Helpers\LinkParserHelper;
use App\Models\Post;
use App\Models\Short;
use App\Repositories\LinkRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class LinkParserHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_extract_urls_from_text(): void
    {
        $linkRepository = Mockery::mock(LinkRepositoryInterface::class);
        $linkParser = new LinkParserHelper($linkRepository);

        $text = 'Check out these websites: https://example.com and http://test.org/page and also https://github.com/';

        $urls = $linkParser->extractUrls($text);

        $this->assertEquals([
            'https://example.com',
            'http://test.org/page',
            'https://github.com',
        ], $urls);
    }

    public function test_extract_urls_returns_empty_array_when_no_urls_in_text(): void
    {
        $linkRepository = Mockery::mock(LinkRepositoryInterface::class);
        $linkParser = new LinkParserHelper($linkRepository);

        $text = 'This text contains no URLs';

        $urls = $linkParser->extractUrls($text);

        $this->assertEmpty($urls);
    }

    public function test_update_model_links_calls_repository_with_extracted_urls(): void
    {
        $linkRepository = Mockery::mock(LinkRepositoryInterface::class);
        $linkParser = new LinkParserHelper($linkRepository);

        $post = Post::factory()->create();
        $content = 'Check out https://example.com';

        $linkRepository->shouldReceive('updateLinksList')
            ->once()
            ->with($post, ['https://example.com']);

        $linkParser->updateModelLinks($post, $content);
    }

    public function test_update_model_links_does_nothing_when_no_urls_found(): void
    {
        $linkRepository = Mockery::mock(LinkRepositoryInterface::class);
        $linkParser = new LinkParserHelper($linkRepository);

        $post = Post::factory()->create();
        $content = 'No URLs here';

        $linkRepository->shouldReceive('updateLinksList')->never();

        $linkParser->updateModelLinks($post, $content);
    }

    public function test_can_handle_both_post_and_short_models(): void
    {
        $linkRepository = Mockery::mock(LinkRepositoryInterface::class);
        $linkParser = new LinkParserHelper($linkRepository);

        $post = Post::factory()->create();
        $short = Short::factory()->create();
        $content = 'Check out https://example.com and https://test.com';

        $linkRepository->shouldReceive('updateLinksList')
            ->once()
            ->with($post, ['https://example.com', 'https://test.com']);

        $linkRepository->shouldReceive('updateLinksList')
            ->once()
            ->with($short, ['https://example.com', 'https://test.com']);

        $linkParser->updateModelLinks($post, $content);
        $linkParser->updateModelLinks($short, $content);
    }
}
