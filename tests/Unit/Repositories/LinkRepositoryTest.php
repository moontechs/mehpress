<?php

namespace Tests\Unit\Repositories;

use App\Models\Link;
use App\Models\Post;
use App\Models\Short;
use App\Repositories\LinkRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private LinkRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new LinkRepository;
    }

    public function test_update_links_list_creates_links_and_associates_with_post(): void
    {
        $post = Post::factory()->create();
        $urls = [
            'https://example.com',
            'https://test.org/page',
        ];

        $this->repository->updateLinksList($post, $urls);

        $this->assertDatabaseHas('links', ['url' => 'https://example.com']);
        $this->assertDatabaseHas('links', ['url' => 'https://test.org/page']);

        $this->assertCount(2, $post->links);
        $this->assertEquals($urls, $post->links->pluck('url')->toArray());
    }

    public function test_update_links_list_reuses_existing_links(): void
    {
        $post = Post::factory()->create();
        $existingLink = Link::factory()->create(['url' => 'https://example.com']);
        $urls = [
            'https://example.com',
            'https://test.org/page',
        ];

        $initialLinkCount = Link::count();

        $this->repository->updateLinksList($post, $urls);

        $this->assertEquals($initialLinkCount + 1, Link::count());

        $this->assertCount(2, $post->links);
        $this->assertEquals($urls, $post->links->pluck('url')->toArray());
    }

    public function test_update_links_list_handles_empty_url_array(): void
    {
        $post = Post::factory()->create();
        $urls = [];

        $this->repository->updateLinksList($post, $urls);

        $this->assertCount(0, $post->links);
    }

    public function test_update_links_list_syncs_links_replacing_previous_ones(): void
    {
        $post = Post::factory()->create();
        $initialUrls = ['https://example.com', 'https://test.org/page'];

        $this->repository->updateLinksList($post, $initialUrls);
        $this->assertCount(2, $post->links);

        $newUrls = ['https://example.com', 'https://newsite.com'];

        $this->repository->updateLinksList($post, $newUrls);

        $post->refresh();
        $this->assertCount(2, $post->links);
        $this->assertEquals($newUrls, $post->links->pluck('url')->toArray());

        $this->assertDatabaseHas('links', ['url' => 'https://test.org/page']);
    }

    public function test_update_links_list_works_with_short_model(): void
    {
        $short = Short::factory()->create();
        $urls = ['https://example.com', 'https://test.org/page'];

        $this->repository->updateLinksList($short, $urls);

        $this->assertCount(2, $short->links);
        $this->assertEquals($urls, $short->links->pluck('url')->toArray());
    }
}
