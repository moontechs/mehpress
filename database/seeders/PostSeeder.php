<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (config('app.env') === 'production') {
            return;
        }

        foreach (Blog::all() as $blog) {
            for ($month = 1; $month <= 5; $month++) {
                for ($i = 1; $i <= 5; $i++) {
                    Post::create(
                        [
                            'title' => fake()->sentence(10),
                            'description' => fake()->sentence(20),
                            'slug' => fake()->slug(),
                            'text' => $this->fakeMarkdown(),
                            'type' => 'post',
                            'blog_id' => $blog->id,
                            'published' => true,
                            'tags' => $i % 2 === 0 ? [
                                fake()->word(),
                                fake()->word(),
                                fake()->word(),
                                'same',
                                'post',
                            ] : null,
                            'language' => 'en_US',
                            'created_at' => fake()->dateTimeBetween(
                                startDate: now()->month($month)->startOfMonth(),
                                endDate: now()->month($month)->endOfMonth(),
                            ),

                        ]
                    );
                    Post::create(
                        [
                            'text' => fake()->sentence(20).' #short #same #'.fake()->word(),
                            'type' => 'short',
                            'blog_id' => $blog->id,
                            'published' => true,
                            'language' => 'en_US',
                            'created_at' => fake()->dateTimeBetween(
                                startDate: now()->month($month)->startOfMonth(),
                                endDate: now()->month($month)->endOfMonth(),
                            ),
                        ]
                    );
                }
            }
        }
    }

    private function fakeMarkdown(): string
    {
        $markdown = [
            '## '.fake()->sentence(3),
            fake()->paragraph(3),
            '### '.fake()->sentence(3),
            fake()->paragraph(3),
            '#### '.fake()->sentence(3),
            fake()->paragraph(3),
            '##### '.fake()->sentence(3),
        ];

        return implode(PHP_EOL, $markdown);
    }
}
