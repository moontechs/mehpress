<?php

namespace App\Models;

use App\Observers\ShortObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([ShortObserver::class])]
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
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class);
    }
}
