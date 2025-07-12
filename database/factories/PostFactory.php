<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'text' => fake()->paragraphs(3, true),
            'tags' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withTags(array $tags): self
    {
        return $this->state(fn (array $attributes) => [
            'tags' => json_encode($tags),
        ]);
    }
}
