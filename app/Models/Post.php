<?php

namespace App\Models;

use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([PostObserver::class])]
/**
 * @mixin IdeHelperPost
 */
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

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class);
    }
}
