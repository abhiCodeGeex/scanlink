<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    protected $table = 'video';

    protected $fillable = ['client_id', 'user_id', 'profile_id', 'title', 'video_name', 'is_extra'];

    protected function casts(): array
    {
        return ['is_extra' => 'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
