<?php

namespace App\Models;

use App\Models\Concerns\FillsLegacyNotNullDefaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogoExtra extends Model
{
    use FillsLegacyNotNullDefaults;

    protected $table = 'logo_extra';

    protected $fillable = ['client_id', 'user_id', 'profile_id', 'logo_name', 'logo_url', 'is_temp'];

    /**
     * @return array<string, mixed>
     */
    protected static function legacyNotNullDefaults(): array
    {
        return [
            'logo_name' => '',
            'logo_url' => '',
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
