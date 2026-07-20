<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'video';

    protected $fillable = ['client_id', 'user_id', 'profile_id', 'title', 'video_name', 'is_extra'];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'title' => '',
            'video_name' => '',
            'is_extra' => 0,
        ];
    }

    protected function casts(): array
    {
        return ['is_extra' => 'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
