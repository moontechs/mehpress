<?php

namespace App\Models;

use App\Observers\PostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([PostObserver::class])]
/**
 * @mixin IdeHelperPost
 */
class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'text',
        'slug',
        'published',
        'tags',
        'seo_tags',
        'type',
        'blog_id',
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
        return $this->blog->host.'/posts/'.$this->slug;
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}
