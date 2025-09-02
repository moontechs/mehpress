<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (config('app.env') === 'production') {
            return;
        }

        $blogs = [
            [
                'name' => 'MehPress',
                'host' => 'http://127.0.0.1:8000',
                'description' => 'Not exactly WordPress... more like MehPress',
                'navigation' => [
                    ['label' => 'Feed', 'type' => 'link', 'url' => '/'],
                    ['label' => 'Posts', 'type' => 'link', 'url' => '/posts'],
                    ['label' => 'Shorts', 'type' => 'link', 'url' => '/shorts'],
                ],
                'languages' => ['en_US', 'de_DE', 'fr_FR', 'es_ES', 'it_IT', 'pt_PT', 'ru_RU'],
                'default_language' => 'en_US',
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::updateOrCreate(
                ['name' => $blog['name']],
                $blog,
            );
        }
    }
}
