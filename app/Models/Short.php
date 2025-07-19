<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperShort
 */
class Short extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'tags',
        'slug',
        'seo_tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'seo_tags' => 'array',
    ];

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class);
    }

    public function getUrl(): string
    {
        return config('app.url').'/shorts/'.$this->slug;
    }
}
