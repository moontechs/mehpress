<?php

namespace Database\Factories;

use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
