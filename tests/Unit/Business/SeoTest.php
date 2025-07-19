<?php

namespace Tests\Unit\Business;

use App\Business\Seo;
use App\Models\Blog;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_default_structure_returns_expected_defaults(): void
    {
        $seo = new Seo;
        $expected = [
            'title' => '',
            'meta_name__description' => '',
            'meta_name__robots' => 'index, follow',
            'robots' => 'index, follow',
            'meta_property__og:title' => '',
            'meta_property__og:description' => '',
            'meta_property__og:url' => '',
            'meta_property__og:type' => 'article',
            'meta_name__twitter:card' => 'summary',
            'meta_name__twitter:title' => '',
            'meta_name__twitter:description' => '',
        ];

        $this->assertEquals($expected, $seo->getDefaultStructure());
    }

    public function test_generate_for_post_sets_seo_tags_without_ai(): void
    {
        $host = 'https://example.com';
        $blog = Blog::factory()->create(['host' => $host]);

        $post = Post::factory()
            ->for($blog)
            ->create([
                'title' => 'Test Title',
                'description' => 'Test Description',
                'slug' => 'test-title',
                'text' => 'Some content for testing.',
            ]);

        $seo = new Seo;
        $seo->generateForPost($post, false);

        $post->refresh();

        $this->assertIsArray($post->seo_tags);
        $this->assertEquals('Test Title', $post->seo_tags['title']);
        $this->assertEquals('Test Description', $post->seo_tags['meta_name__description']);
        $this->assertEquals('index, follow', $post->seo_tags['meta_name__robots']);
        $this->assertEquals('Test Title', $post->seo_tags['meta_property__og:title']);
        $this->assertEquals($host.'/posts/test-title', $post->seo_tags['meta_property__og:url']);
        $this->assertEquals('article', $post->seo_tags['meta_property__og:type']);
        $this->assertEquals('summary', $post->seo_tags['meta_name__twitter:card']);
        $this->assertEquals('Test Description', $post->seo_tags['meta_name__twitter:description']);
    }

    public function test_generate_for_post_with_ai_flag_uses_description_when_no_api_key(): void
    {
        $host = 'https://example.com';
        $blog = Blog::factory()->create(['host' => $host]);

        $post = Post::factory()
            ->for($blog)
            ->create([
                'title' => 'AI Title',
                'description' => 'AI Description',
                'slug' => 'ai-title',
                'text' => 'AI content here.',
            ]);

        putenv('OPENROUTER_API_KEY');

        $fakeResponse = Mockery::mock();
        $fakeResponse->shouldReceive('toArray')->andReturn([
            'choices' => [[
                'message' => ['content' => json_encode(['description' => 'Generated AI Description'])],
            ]],
        ]);
        $this->app->instance('laravel-openrouter', new class($fakeResponse)
        {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }

            public function chatRequest($chatData)
            {
                return $this->response;
            }
        });

        $seo = new Seo;
        $seo->generateForPost($post, true);

        $post->refresh();

        $this->assertEquals('AI Description', $post->seo_tags['meta_name__description']);
        $this->assertEquals('AI Description', $post->seo_tags['meta_property__og:description']);
        $this->assertEquals('AI Description', $post->seo_tags['meta_name__twitter:description']);
    }

    public function test_generate_for_post_with_ai_flag_uses_generated_description_when_api_key_present(): void
    {
        $host = 'https://example.com';
        $blog = Blog::factory()->create(['host' => $host]);

        $post = Post::factory()
            ->for($blog)
            ->create([
                'title' => 'AI Title',
                'description' => 'Original Description',
                'slug' => 'ai-title',
                'text' => 'Some AI content.',
            ]);

        putenv('OPENROUTER_API_KEY=mykey');

        $fakeResponse = Mockery::mock();
        $fakeResponse->shouldReceive('toArray')->andReturn([
            'choices' => [[
                'message' => ['content' => json_encode(['description' => 'Generated AI Description'])],
            ]],
        ]);
        $this->app->instance('laravel-openrouter', new class($fakeResponse)
        {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }

            public function chatRequest($chatData)
            {
                return $this->response;
            }
        });

        $seo = new Seo;
        $seo->generateForPost($post, true);

        $post->refresh();
        $this->assertEquals('Generated AI Description', $post->seo_tags['meta_name__description']);
        $this->assertEquals('Generated AI Description', $post->seo_tags['meta_property__og:description']);
        $this->assertEquals('Generated AI Description', $post->seo_tags['meta_name__twitter:description']);
    }
}
