<?php

namespace Tests\Unit\Repositories;

use App\Constants;
use App\Repositories\TagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TagRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TagRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TagRepository;

        Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
    }

    public function test_get_unique_returns_unique_tags_from_posts_and_shorts(): void
    {
        DB::table('posts')->insert([
            'title' => 'Post 1',
            'slug' => 'post-1',
            'text' => 'Content',
            'tags' => json_encode(['laravel', 'php']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('posts')->insert([
            'title' => 'Post 2',
            'slug' => 'post-2',
            'text' => 'Content',
            'tags' => json_encode(['laravel', 'testing']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shorts')->insert([
            'slug' => 'short-1',
            'text' => 'Content',
            'tags' => json_encode(['vuejs', 'laravel']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tags = $this->repository->getUnique();

        $this->assertCount(4, $tags);
        $this->assertContains('laravel', $tags);
        $this->assertContains('php', $tags);
        $this->assertContains('testing', $tags);
        $this->assertContains('vuejs', $tags);
    }

    public function test_get_unique_returns_empty_array_when_no_tags_exist(): void
    {
        DB::table('posts')->insert([
            'title' => 'Post 1',
            'slug' => 'post-1',
            'text' => 'Content',
            'tags' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shorts')->insert([
            'slug' => 'short-1',
            'text' => 'Content',
            'tags' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tags = $this->repository->getUnique();

        $this->assertEmpty($tags);
    }

    public function test_get_unique_uses_cache(): void
    {
        DB::table('posts')->insert([
            'title' => 'Post 1',
            'slug' => 'post-1',
            'text' => 'Content',
            'tags' => json_encode(['laravel', 'php']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->repository->getUnique();

        DB::table('posts')->insert([
            'title' => 'Post 2',
            'slug' => 'post-2',
            'text' => 'Content',
            'tags' => json_encode(['new-tag']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cachedTags = $this->repository->getUnique();

        $this->assertCount(2, $cachedTags);
        $this->assertContains('laravel', $cachedTags);
        $this->assertContains('php', $cachedTags);
        $this->assertNotContains('new-tag', $cachedTags);

        Cache::forget(Constants::CACHE_UNIQUE_TAGS_KEY);
        $freshTags = $this->repository->getUnique();

        $this->assertCount(3, $freshTags);
        $this->assertContains('new-tag', $freshTags);
    }
}
