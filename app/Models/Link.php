<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperLink
 */
class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
