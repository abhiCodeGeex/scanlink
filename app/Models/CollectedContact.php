<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectedContact extends Model
{
    protected $table = 'contacts';

    public $timestamps = false;

    protected $fillable = [
        'id_profile',
        'name',
        'surname',
        'mobile',
        'email',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'id_profile');
    }
}
