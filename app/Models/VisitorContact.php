<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorContact extends Model
{
    protected $table = 'user_info';

    public $timestamps = false;

    protected $fillable = ['profile_id', 'user_name', 'user_email', 'user_mobile', 'entry_date'];

    protected function casts(): array
    {
        return [
            'entry_date' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
