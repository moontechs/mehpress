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
        'logo_svg',
        'navigation',
        'footer',
        'languages',
        'default_language',
        'cron_commands',
    ];

    protected $casts = [
        'navigation' => 'array',
        'footer' => 'array',
        'languages' => 'array',
        'cron_commands' => 'array',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function getCronCommandsList(): array
    {
        return collect($this->cron_commands)
            ->pluck('command')
            ->filter()
            ->toArray();
    }

    public function getFormattedCronCommands(): array
    {
        return collect($this->cron_commands)
            ->map(function ($item) {
                if (empty($item['command'])) {
                    return null;
                }

                $command = $item['command'];

                if (! empty($item['arguments'])) {
                    $command .= ' '.trim($item['arguments']);
                }

                return [
                    'command' => $command,
                    'description' => $item['description'] ?? null,
                    'original' => $item,
                ];
            })
            ->filter()
            ->toArray();
    }

    public function getCronCommandStrings(): array
    {
        return collect($this->getFormattedCronCommands())
            ->pluck('command')
            ->toArray();
    }
}
