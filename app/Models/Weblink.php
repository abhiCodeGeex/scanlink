<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weblink extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'weblink';

    /** Live dump has created_at only. */
    public const UPDATED_AT = null;

    protected $fillable = [
        'profile_id', 'link_button', 'link_button_text',
        'link_button_url', 'link_button_color', 'link_button_align',
    ];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'link_button' => 0,
            'link_button_text' => '',
            'link_button_url' => '',
            'link_button_color' => '008901',
            'link_button_align' => 'left',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $link): void {
            if (empty($link->created_at)) {
                $link->created_at = now();
            }
        });

        $touchProfile = function (self $link): void {
            if ($link->profile_id) {
                Profile::query()->whereKey($link->profile_id)->update([
                    'updated_at' => now(),
                ]);
            }
        };

        static::saved($touchProfile);
        static::deleted($touchProfile);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
