<?php

namespace Tests\Unit\Business;

use App\Business\SlugHelper;
use Tests\TestCase;

class SlugHelperTest extends TestCase
{
    public function test_get_for_post_creates_proper_slug(): void
    {
        $title = 'This is a Test Post Title!';
        $slug = SlugHelper::getForPost($title);
        $this->assertEquals('this-is-a-test-post-title', $slug);
    }

    public function test_get_for_post_handles_special_characters(): void
    {
        $title = 'Test & Special @ Characters #';
        $slug = SlugHelper::getForPost($title);
        $this->assertEquals('test-special-at-characters', $slug);
    }

    public function test_get_for_short_creates_slug_with_random_number(): void
    {
        $title = 'This is a Test Short Title';
        $slug = SlugHelper::getForShort($title);
        $this->assertStringStartsWith('this-is-a-test-short-title-', $slug);

        preg_match('/^this-is-a-test-short-title-(\d{3})$/', $slug, $matches);
        $this->assertArrayHasKey(1, $matches);
        $this->assertGreaterThanOrEqual(100, (int) $matches[1]);
        $this->assertLessThanOrEqual(999, (int) $matches[1]);
    }

    public function test_get_for_short_truncates_long_titles(): void
    {
        $longTitle = 'This is an extremely long title that should be truncated because it exceeds the maximum length for a slug in the system by quite a bit';
        $slug = SlugHelper::getForShort($longTitle);

        $slugBase = substr($slug, 0, strrpos($slug, '-'));
        $this->assertLessThanOrEqual(40, strlen($slugBase));

        preg_match('/-(\d{3})$/', $slug, $matches);
        $this->assertArrayHasKey(1, $matches);
        $this->assertGreaterThanOrEqual(100, (int) $matches[1]);
        $this->assertLessThanOrEqual(999, (int) $matches[1]);
    }
}
