<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperBlog
 */
class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'host',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
