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
