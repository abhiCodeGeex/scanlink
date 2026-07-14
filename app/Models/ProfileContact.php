<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileContact extends Model
{
    protected $table = 'profile_contact';

    protected $fillable = ['profile_id', 'name_company', 'telephone', 'datestamp'];

    protected function casts(): array
    {
        return ['datestamp' => 'datetime'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
