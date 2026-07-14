<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logo extends Model
{
    protected $table = 'logo';

    protected $fillable = ['client_id', 'user_id', 'profile_id', 'logo_name', 'is_temp'];

    protected function casts(): array
    {
        return ['is_temp' => 'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
