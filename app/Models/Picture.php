<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Picture extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'picture';

    protected $fillable = ['client_id', 'user_id', 'profile_id', 'txt_footer', 'picture_name', 'is_temp'];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'txt_footer' => '',
            'picture_name' => '',
            'is_temp' => 0,
        ];
    }

    protected function casts(): array
    {
        return ['is_temp' => 'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
