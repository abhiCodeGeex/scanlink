<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    protected $table = 'participant';

    protected $primaryKey = 'participant_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'participant_id', 'profile_id', 'name', 'employer_cmp',
        'due_date', 'participated_date', 'is_participated',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'participated_date' => 'date',
            'is_participated' => 'boolean',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
