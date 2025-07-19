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

    protected $fillable = ['url'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
