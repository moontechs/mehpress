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
                'footer' => [
                    ['label' => 'English', 'type' => 'link', 'url' => '/language/en_US'],
                    ['label' => 'Deutsch', 'type' => 'link', 'url' => '/language/de_DE'],
                    ['label' => 'Español', 'type' => 'link', 'url' => '/language/es_ES'],
                    ['label' => 'Português', 'type' => 'link', 'url' => '/language/pt_PT'],
                    ['label' => 'Русский', 'type' => 'link', 'url' => '/language/ru_RU'],
                ],
                'languages' => ['en_US', 'de_DE', 'es_ES', 'pt_PT', 'ru_RU'],
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
