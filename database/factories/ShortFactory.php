<?php

namespace Database\Factories;

use App\Models\Short;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShortFactory extends Factory
{
    protected $model = Short::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->slug(),
            'text' => fake()->paragraph(),
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
