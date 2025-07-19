<?php

namespace Tests\Unit\Business;

use App\Business\Tag;
use App\Repositories\TagRepositoryInterface;
use Mockery;
use Tests\TestCase;

class TagHelperHelperTest extends TestCase
{
    public function test_can_parse_hashtags_from_text(): void
    {
        $tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $tagHelper = new Tag($tagRepository);

        $text = 'This is a post with #laravel and #php hashtags. #testing is important.';

        $tags = $tagHelper->parseFromText($text);

        $this->assertEquals(['laravel', 'php', 'testing'], $tags);
    }

    public function test_returns_unique_hashtags_only(): void
    {
        $tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $tagHelper = new Tag($tagRepository);

        $text = 'This is a post with #laravel and #php hashtags. #laravel is mentioned twice.';

        $tags = $tagHelper->parseFromText($text);

        $this->assertEquals(['laravel', 'php'], $tags);
    }

    public function test_returns_empty_array_when_no_hashtags_found(): void
    {
        $tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $tagHelper = new Tag($tagRepository);

        $text = 'This is a post with no hashtags.';

        $tags = $tagHelper->parseFromText($text);

        $this->assertEmpty($tags);
    }

    public function test_handles_null_text_input(): void
    {
        $tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $tagHelper = new Tag($tagRepository);

        $tags = $tagHelper->parseFromText(null);

        $this->assertEmpty($tags);
    }

    public function test_get_suggestions_returns_tags_from_repository(): void
    {
        $tagRepository = Mockery::mock(TagRepositoryInterface::class);
        $tagHelper = new Tag($tagRepository);

        $expectedTags = ['laravel', 'php', 'testing'];

        $tagRepository->shouldReceive('getUnique')
            ->once()
            ->andReturn($expectedTags);

        $tags = $tagHelper->getSuggestions();

        $this->assertEquals($expectedTags, $tags);
    }
}
