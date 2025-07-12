<?php

namespace App\Models;

use App\Observers\ShortObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ShortObserver::class])]
class Short extends Model
{
    protected $fillable = [
        'text',
        'tags',
        'slug',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
