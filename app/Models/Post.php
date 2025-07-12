<?php

namespace App\Models;

use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([PostObserver::class])]
class Post extends Model
{
    protected $fillable = [
        'title',
        'description',
        'text',
        'slug',
        'published',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
